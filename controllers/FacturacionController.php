<?php
require_once "./models/Facturacion.php";
require_once "./interfaces/IApiUsable.php";
require_once "./models/Mesa.php";
require_once "./controllers/LogsController.php";

use LogsController as LogsController;
use App\Models\Facturacion as Facturacion;
use App\Models\Mesa as Mesa;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FacturacionController implements IApiUsable
{
    public function CargarUno(Request $request, Response $response, $args)
    {
        try {
            $parametros=$request->getParsedBody();
            if (isset($parametros['idmesa']) && isset($parametros['importe'])) {
                $mesa = Mesa::find($parametros['idmesa']);
                if ($mesa) {
                    if ($mesa->estado == "Pagando") {
                        $facturacion = new Facturacion();
                        $facturacion->idMesa = $parametros['idmesa'];
                        $facturacion->importe = $parametros['importe'];
                        $usrLog=  Middleware::GetDatosUsuario($request);
                        LogsController::CargarUno($usrLog, "Facturacion", "Alta", array('Mesa Afectada: ', array($mesa->idMesas), ' Importe: ', array($facturacion->importe)));
                        $facturacion->save();
                        $payload =  json_encode(array("mensaje"=> "Factura generada con exito"));
                    } else {
                        $payload =  json_encode(array("mensaje"=> "Debe modificar el estado de la Mesa a Pagando"));
                    }
                } else {
                    $payload =  json_encode(array("mensaje"=> "Mesa no econtrada"));
                }
            }
            else
            {
                $payload =  json_encode(array("mensaje"=> "No se recibieron algunos de los parametros necesarios para el alta de la Factura, idmesa, importe"));
            }
        } catch (Exception $ex) {
            $payload =  json_encode(array("mensaje"=> $ex->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
