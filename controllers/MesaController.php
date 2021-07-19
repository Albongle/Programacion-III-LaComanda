<?php
require_once "./models/Mesa.php";
require_once "./models/Pedido.php";
require_once "./interfaces/IApiUsable.php";
require_once "./models/AutentificadorJWT.php";
require_once "./controllers/LogsController.php";
require_once "./middlewares/middleware.php";
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use LogsController as LogsController;
use Middleware as Middleware;
use App\Models\Pedido as Pedido;
use App\Models\Mesa as Mesa;

class MesaController implements IApiUsable
{
    public function TraerTodos(Request $request, Response $response, $args)
    {
        $lista = Mesa::all();
        $payload = json_encode(array("listaMesas" => $lista));
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno(Request $request, Response $response, $args)
    {
        // Buscamos por primary key
        $mesa =Mesa::find($args['id']);
        $payload = json_encode($mesa);
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function CargarUno(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        try {
            if (isset($parametros['idmesa'])) {
                $mesa=  new Mesa();
                $mesa->idMesas= $parametros['idmesa'];
                $mesa->codigo = $this->GenerarCodigoMesa("M".$parametros['idmesa']);
                $mesa->estado =  "Cerrada";
                $mesa->save();
                $usrLog=  Middleware::GetDatosUsuario($request);
                LogsController::CargarUno($usrLog, "Mesas", "Alta", array('Mesa Afectada: ', array($mesa->idMesas)));
                $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
            } else {
                $payload = json_encode(array("mensaje" => "No se recibio alguno de los parametros necesarios para el alta de Mesa", "Numero de Mesa"=>"idmesa"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => $ex->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response
    ->withHeader('Content-Type', 'application/json');
    }
    public function ModificarUno(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        try {
            if (isset($parametros['id']) && isset($parametros['estado'])) {
                $mesa =  Mesa::find($parametros['id']);
                if ($mesa) {
                    if (in_array($parametros['estado'], Mesa::ESTADO)) {
                        if ($parametros['estado'] == "Pagando") {
                            //Si el estado a cambiar es pagando, verifico antes que no tenga pedidos pendientes de procesar
                            $pedidosPendientes =  Pedido::where('idMesa', '=', $parametros['id'])->whereIn('estado', ['Iniciado','Preparacion'])->select('pedidos.idPedidos')->get();
                            if (count($pedidosPendientes)>0) {
                                $payload = json_encode(array("mensaje" => "Se cancelo la modificacion, dado que la mesa aun tiene pedidos pendientes de procesar"));
                            }
                        }
                        if (!isset($payload)) {
                            $mesa->update(['estado'=>$parametros['estado']]);
                            $mesa->save();
                            $usrLog=  Middleware::GetDatosUsuario($request);
                            LogsController::CargarUno($usrLog, "Mesas", "Modificacion", array('Mesa Afectada: ', array($mesa->idMesas), " Estado: ",array($mesa->estado)));
                            $payload = json_encode(array("mensaje" => "Se modfico el estado con exito"));
                        }
                    }
                    else {
                        $payload = json_encode(array("mensaje" => "Estado no permitido, solo se permite: Esperando, Pagando, Comiendo"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "No se recibidio algunos de los parametros necesarios para el cambio de estado"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => $ex->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response;
    }
    public function CerrarMesa(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        try {
            if (isset($parametros['id'])) {
                $mesa =  Mesa::find($parametros['id']);
                if ($mesa) {
                    $pedidosPendientes =  Pedido::where('idMesa', '=', $parametros['id'])->whereIn('estado', ['Iniciado','Preparacion'])->select('pedidos.idPedidos')->get();
                    if (count($pedidosPendientes)>0) {
                        $payload = json_encode(array("mensaje" => "Se cancelo el cierre de la mesa, dado que la mesa aun tiene pedidos pendientes de procesar"));
                    } else {
                        $mesa->update(['estado'=>'Cerrada']);
                        $mesa->save();
                        $usrLog=  Middleware::GetDatosUsuario($request);
                        LogsController::CargarUno($usrLog, "Mesas", "Modificacion", array('Mesa Afectada: ', array($mesa->idMesas), " Estado: ",array($mesa->estado)));
                        $payload = json_encode(array("mensaje" => "Mesa cerrada con exito"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "No se recibidio algunos de los parametros necesarios para el cierre"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => $ex->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response;
    }

    private function GenerarCodigoMesa($codigo)
    {
        $carateres =  str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

        while (strlen($codigo)<5) {
            $valor = rand(0, count($carateres)-1);
            $codigo.= $carateres[$valor];
        }
        return $codigo;
    }
}
