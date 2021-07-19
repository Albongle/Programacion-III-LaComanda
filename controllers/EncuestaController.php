<?php
require_once "./models/Encuesta.php";
require_once "./models/Usuario.php";
require_once "./models/Mesa.php";
require_once "./interfaces/IApiUsable.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Encuesta as Encuesta;
use App\Models\Mesa as Mesa;
use App\Models\Usuario as Usuario;


class EncuestaController implements IApiUsable{


    public function CargarUno(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();

        try {
            if(isset($parametros['idmesa']) && isset($parametros['idmozo']) && isset($parametros['idcocinero']) && isset($parametros['descripcion']) && isset($parametros['valmesa'])
            && isset($parametros['valmozo']) && isset($parametros['valcocinero']) && isset($parametros['valrestaurante']) && isset($parametros['dni']) && isset($parametros['nombre'])){
                $mensaje =  array();
                $mozo =  Usuario::where('idUsuarios','=',$parametros['idmozo'])->first();
                $cocinero =  Usuario::where('idUsuarios','=',$parametros['idcocinero'])->first();
                $mesa =  Mesa::where('idMesas','=',$parametros['idmesa'])->first();
                if(!isset($mozo) || $mozo->funcion != "Mozo")
                {
                    $mensaje[]= array("Mozo" => "El id recibido corresponde con la funcion del empleado");
                }
                if(!isset($cocinero) || $cocinero->funcion != "Cocinero")
                {
                    $mensaje[]=array("Cocinero" => "El id recibido con la funcion del empleado");
                }
                if(!isset($mesa) || $mesa->estado != "Cerrada")
                {
                    $mensaje[]=array("Mesa" => "El id recibido no corresponde o aun no fue cerrada");
                }
                if(($parametros['valmesa']< 0 || $parametros['valmesa']>10)  || ($parametros['valmozo']< 0 || $parametros['valmozo']> 10)
                || ($parametros['valcocinero']< 0 ||$parametros['valcocinero']> 10) || ($parametros['valrestaurante']< 0 || $parametros['valrestaurante']> 10)){

                    $mensaje[]=array("Valoraciones" => "Las valoraciones deben ser entre 0 y 10");
                }
                if(strlen($parametros['descripcion'])> 66){
                    $mensaje[]=array("Descripcion" => "La descripcion de la puntuacion es muy larga, maximo 66 caracteres");
                }
                    

                if(count($mensaje)>0)
                {
                    $payload = json_encode(array("mensaje"=>$mensaje));
                }
                else
                {
                    $encuesta =  new Encuesta();
                    $encuesta->dni =$parametros['dni'];
                    $encuesta->nombreCliente =  $parametros['nombre'];
                    $encuesta->idMesa =$parametros['idmesa'];
                    $encuesta->idMozo = $parametros['idmozo'];
                    $encuesta->idCocinero =  $parametros['idcocinero'];
                    $encuesta->descripcion = $parametros['descripcion'];
                    $encuesta->valMesa = $parametros['valmesa'];
                    $encuesta->valMozo = $parametros['valmozo'];
                    $encuesta->valCocinero = $parametros['valcocinero'];
                    $encuesta->valRestaurante = $parametros['valrestaurante'];
                    $encuesta->save();
                    $payload = json_encode(array("mensaje"=>"Encuesta realizada con exito"));
                }
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se recibio alguno de los parametros necesarios", "parametros"=>"nombre, dni, idmesa, idmozo, idcodinero, descripcion, 
                valmesa, valmozo, valcocinero, valrestaurante"));
            }

        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => "Se produjo un error ". $ex->getMessage()));
        }
                
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }



}

?>

