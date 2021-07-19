<?php
require_once "./models/Producto.php";
require_once "./models/Usuario.php";
require_once "./middlewares/middleware.php";
require_once "./controllers/LogsController.php";
require_once "./interfaces/IApiUsable.php";
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Producto as Producto;
use App\Models\Usuario as Usuario;
use LogsController as LogsController;
use Middleware as Middleware;


class ProductoController implements IApiUsable
{
    public function TraerTodos(Request $request, Response $response, $args)
    {
        $lista = Producto::all();
        $payload = json_encode(array("listaProductos" => $lista));
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno(Request $request, Response $response, $args)
    {
        $parametros =  $request->getQueryParams();
        if(isset($parametros['id']))
        {
            $producto = Producto::find($parametros['id']);
            $payload = json_encode($producto);
        }
        else if(isset($parametros['tipo'])){

            $productos =  Producto::where('tipo','=',$parametros['tipo'])->get();
            $payload = json_encode($productos);
        }
        else
        {
            $payload = json_encode(array("mensaje"=> "No se recibio ninguno de los parametros necesarios id o tipo"));
        }
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function CargarUno(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        try{
            if (isset($parametros['tipo']) && isset($parametros['nombre']) && isset($parametros['precio']) && isset($parametros['sector'])) {
                $productoAux = Producto::where('tipo','=', $parametros['tipo'])->where('nombre','=', $parametros['nombre'])->first();
                if ($productoAux) {
                    $payload = json_encode(array("mensaje" => "Producto ya existente"));
                } elseif (in_array($parametros['tipo'], Producto::TIPO) && in_array($parametros['sector'], Usuario::SECTOR)) {
                    $producto =  new Producto();
                    $producto->tipo = $parametros['tipo'];
                    $producto->nombre = $parametros['nombre'];
                    $producto->precio = $parametros ['precio'];
                    $producto->sector = $parametros['sector'];
                    $producto->save();
                    $usrLog=  Middleware::GetDatosUsuario($request);
                    LogsController::CargarUno($usrLog,"Productos","Alta",array('Producto Afectado: ', array($producto->idProductos)));
                    $payload = json_encode(array("mensaje" => "Producto creado con exito"));
                } else {
                    $payload = json_encode(array("mensaje" => "Tipo de producto o Sector no permitidos", "Tipos productos"=>"Comida, Bebidas, Postre","Sectores"=>"Tragos, Choperas, Candy Bar, Cocina"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "No se recibio alguno de los paramatros necesarios para el alta", "Parametros"=>"Tipo, Nombre, Precio, Sector"));
            }
        }
        catch(Exception $ex){
            $payload = json_encode(array("mensaje" => $ex->getMessage())); 
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    
    public function ModificarUno(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        try{
            if (isset($parametros['idproducto'])) {
                if ($producto = Producto::find($parametros['idproducto'])) {
                    $datos = self::ValidaCampos($parametros);
                    if (isset($datos)) {
                        foreach ($datos as $key => $value) {
                            $producto->$key = $value;
                        }
                        $usrLog=  Middleware::GetDatosUsuario($request);
                        LogsController::CargarUno($usrLog,"Productos","Modificacion",array('Producto Afectado: ', array($producto->idProductos), " Campos: ", $datos));
                        $producto->save();
                        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
                    } else {
                        $payload = json_encode(array("mensaje" => "No se encontro un paramatro a modificar"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Producto no encontrado"));
                }
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se recibio alguno de los parametros necesarios"));
            }
        }
        catch (Exception $ex)
        {
            $payload = json_encode(array("mensaje" => $ex->getMessage())); 
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    private static function ValidaCampos($array)
    {
        $datos =  array('tipo','nombre','sector','precio');
        $returnAux = null;

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (in_array($key, $datos)) {
                    $returnAux[$key]=$array[$key];
                }
            }
        }
        return  $returnAux;
    }


}
