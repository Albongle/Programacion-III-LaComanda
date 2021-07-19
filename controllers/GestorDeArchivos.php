<?php
require_once "./models/Producto.php";
require_once "./models/Usuario.php";
require_once "./models/Mesa.php";
require_once "./models/Pedido.php";
require_once "./controllers/LogsController.php";
require_once "./controllers/GestorDeConsultas.php";
require_once "./middlewares/middleware.php";
require_once "./models/Pdf.php";

use PDF as PDF;

use Middleware as Middleware;
use LogsController as LogsController;
use App\Models\Producto as Producto;
use App\Models\Usuario as Usuario;
use App\Models\Mesa as Mesa;
use App\Models\Pedido as Pedido;
use GestorDeConsultas as GestorDeConsultas;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GestorDeArchivos
{
    const RUTAS =  array('productos','usuarios','mesas','pedidos');
    public static function CargarCSV(Request $request, Response $response, $args)
    {
        $parametros =  $request->getUploadedFiles();
        
        try {
            if (isset($parametros['archivo'])) {
                $ruta =  $parametros['archivo']->getClientFileName();
                $extension = pathinfo($ruta, PATHINFO_EXTENSION);
    
                if ($extension == "csv") {
                    $destino =  "./archivos/loadTemp.csv";
                    $parametros['archivo']->moveTo($destino);
                    $existentes = 0;
                    $cantidad = 0;
                    $arrayIds =  array();
                    if (($archivo=fopen($destino, "r"))) {
                        $nombreArchivo = pathinfo($ruta, PATHINFO_FILENAME);
                        if (strtolower($nombreArchivo) =="productos") {
                            while (($datos = fgetcsv($archivo))) {
                                if (!Producto::where('tipo', '=', $datos[0])->where('nombre', '=', $datos[1])->first()) {
                                    $cantidad++;
                                    $producto =  new Producto();
                                    $producto->tipo = $datos[0];
                                    $producto->nombre = $datos[1];
                                    $producto->sector = $datos[2];
                                    $producto->precio = $datos[3];
                                    $producto->save();
                                    $arrayIds[]=$producto->idProductos;
                                } else {
                                    $existentes++;
                                }
                            }
                        } elseif (strtolower($nombreArchivo) =="usuarios") {
                            while (($datos = fgetcsv($archivo))) {
                                if (!Usuario::where('mail', '=', $datos[2])->first()) {
                                    $cantidad++;
                                    $usr =  new Usuario();
                                    $usr->nombre = $datos[0];
                                    $usr->apellido = $datos[1];
                                    $usr->mail = $datos[2];
                                    $usr->clave = $datos[3];
                                    $usr->tipo = $datos[4];
                                    $usr->funcion = $datos[5];
                                    $usr->sector = $datos[6];
                                    $usr->estado = "Activo";
                                    $usr->save();
                                    $arrayIds[]=$usr->idUsuarios;
                                } else {
                                    $existentes++;
                                }
                            }
                        } else {
                            $payload = json_encode(array("mensaje" => "Archivo no reconocido"));
                        }
                    }
                    if (isset($archivo) && !isset($payload)) {
                        if (!fclose($archivo)) {
                            $payload = json_encode(array("mensaje" => "Algo salio mal al cerrar el archivo"));
                            $response->getBody()->write($payload);
                            return $response
                            ->withHeader('Content-Type', 'application/json');
                        }
                        $payload = json_encode(array("mensaje" => "Informacion actualizada", "Resultado"=> "Existentes: " . $existentes."/ Cargados: ".$cantidad));
                        $usrLog=  Middleware::GetDatosUsuario($request);
                        LogsController::CargarUno($usrLog, ucfirst($nombreArchivo), "Carga CSV", array(ucfirst($nombreArchivo).' Afectados: ', $arrayIds));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Formato de archivo no permitido"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "No se recibio ningun archivo"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function DescagarCSV(Request $request, Response $response, $args)
    {
        $uri =  $request->getUri();
        $url =$uri->getPath();
        $arrOrigen = explode("/", $url);
        $origen = self::ValidaOrigen($arrOrigen, self::RUTAS);
        
        try {
            switch ($origen) {
            case "productos":
                {
                    $datos = Producto::all()->toArray();
                    break;
                }
            case "usuarios":
                {
                    //incluto las bajas
                    $datos = Usuario::withTrashed()->get()->toArray();
                    break;
                }
            case "mesas":
                {
                    $datos = Mesa::all()->toArray();
                    break;
                }
            case "pedidos":
                {
                    $datos = Pedido::all()->toArray();
                    break;
                }
            }

            $temp = "./archivos/downTemp.csv";
            if (count($datos)>0) {
                $archivo=fopen($temp, "w");
                foreach ($datos as $campos) {
                    fputcsv($archivo, $campos);
                }
                if (isset($archivo)) {
                    if (!fclose($archivo)) {
                        $payload = json_encode(array("Resultado" => "Algo salio mal al cerrar el archivo"));
                    } else {
                        header('Content-Type: text/csv');
                        header('Content-Disposition: attachment; filename='.$origen.'.csv');
                        readfile($temp);
                        return $response->withHeader('Content-Type', 'text/csv');
                    }
                }
            } else {
                $payload = json_encode(array("mensaje" => "No hay productos que obtener"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => $ex->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    private static function ValidaOrigen($array, $rutas)
    {
        $returnAux=null;
        foreach ($array as $key => $value) {
            if (in_array($value, $rutas)) {
                $returnAux =  $value;
                break;
            }
        }

        return $returnAux;
    }

    public function GenerarPDf(Request $request, Response $response, array $args)
    {
        $uri =  $request->getUri();
        $url =$uri->getPath();
        switch ($url) 
        {
            case "/LaComanda/descargarPDF/empleados/ingresoAlSistema":
                {
                    $respuesta=  json_decode(GestorDeConsultas::IngresoAlSistema());
                    break;
                }
            case "/LaComanda/descargarPDF/empleados/operacionesPorSector":
                {
                    $respuesta=  json_decode(GestorDeConsultas::OperacionesPorSector());
                    break;
                }
            case "/LaComanda/descargarPDF/empleados/operacionesPorSectorYEmpleado":
                {
                    $respuesta=  json_decode(GestorDeConsultas::OperacionesPorSectorYEmpleado());
                    break;
                }
            case "/LaComanda/descargarPDF/empleados/operacionesPorEmpleado":
                {
                    $respuesta=  json_decode(GestorDeConsultas::OperacionesPorSeparado());
                    break;
                }
            case "/LaComanda/descargarPDF/pedidos/masVendido":
                {
                    $respuesta=  json_decode(GestorDeConsultas::ProductoMasVendido());
                    break;
                }
            case "/LaComanda/descargarPDF/pedidos/menosVendido":
                {
                    $respuesta=  json_decode(GestorDeConsultas::ProductoMenosVendido());
                    break;
                }
            case "/LaComanda/descargarPDF/pedidos/fueraDeTiempo":
                {
                    $respuesta=  json_decode(GestorDeConsultas::PedidosFueraDeTiempo());
                    break;
                }
            case "/LaComanda/descargarPDF/pedidos/cancelados":
                {
                    $respuesta=  json_decode(GestorDeConsultas::PedidosCancelados());
                    break;
                }
            case "/LaComanda/descargarPDF/mesas/masUsadas":
                {
                    $respuesta=  json_decode(GestorDeConsultas::MesaMasUsada());
                    break;
                }
            case "/LaComanda/descargarPDF/mesas/menosUsadas":
                {
                    $respuesta=  json_decode(GestorDeConsultas::MesaMenosUsada());
                    break;
                }
            case "/LaComanda/descargarPDF/mesas/masFacturo":
                {
                    $respuesta=  json_decode(GestorDeConsultas::MesaMasFacturo());
                    break;
                }
            case"/LaComanda/descargarPDF/mesas/menosFacturo":
                {
                    $respuesta=  json_decode(GestorDeConsultas::MesaMenosFacturo());
                    break;
                }
            case "/LaComanda/descargarPDF/mesas/mayorFactura":
                {
                    $respuesta=  json_decode(GestorDeConsultas::MesaFacturaMayorImporte());
                    break;
                }
            case"/LaComanda/descargarPDF/mesas/menorFactura":
                {

                    $respuesta=  json_decode(GestorDeConsultas::MesaFacturaMenorImporte());
                    break;
                }
            case "/LaComanda/descargarPDF/mesas/fechaFacturacion":
                {
                    $parametros =  $request->getQueryParams();
                    if (isset($parametros['fechainicio']) && isset($parametros['fechafin']) && isset($parametros['idmesa'])) {
                        $respuesta=  json_decode(GestorDeConsultas::FacturacionEntreFechas($parametros['fechainicio'], $parametros['fechafin'], $parametros['idmesa']));
                    }
                    break;
                }
            case "/LaComanda/descargarPDF/mesas/mejoresComentarios":
                {
                    $respuesta=  json_decode(GestorDeConsultas::MejoresComentario());
                    break;
                }
            case "/LaComanda/descargarPDF/mesas/peoresComentarios":
                {
                    $respuesta=  json_decode(GestorDeConsultas::PeoresComentario());
                    break;
                }
            default:
            {
                break;
            }
                    
            
        }

        $arrayDatos = array();
        $arrayEncabezados = array();
        $flag =  false;
        if (isset($respuesta) && count($respuesta->resultado)>0) {
            $datos = $respuesta->resultado;
            foreach ($datos as $key => $value) {
                $arrayDatos[]=$value;
                if(!$flag)
                {
                    foreach ($value as $key => $value) {
                        $arrayEncabezados[]=$key;
                    }
                    $flag=true;
                }
               
            }
  
            try {
                $pdf = new PDF();
                $pdf->AliasNbPages();
                $pdf->AddPage('L');
                $pdf->SetFont('Times', '', 12);
                $pdf->FancyTable($arrayEncabezados, $arrayDatos);
                $pdf->Output();
                $payload = json_encode(array("Resultado" => "Descargado"));
                $response->getBody()->write($payload);
    
                return $response
                    ->withHeader('Content-Type', 'application/pdf');
            } catch (Exception $ex) {
                $error = $ex->getMessage();
                $datosError = json_encode(array("Error" => $error));
                $response->getBody()->write($datosError);
                return $response->withHeader('Content-Type', 'application/json');
            }
        } 
        else 
        {
            $payload = json_encode(array("mensaje" => "Sin registro de informacion"));
        }
      
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
