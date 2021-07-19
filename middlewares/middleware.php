<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

class Middleware
{
    private $filtro;

    public function __construct($filtro="Todos")
    {
        $this->filtro = $filtro;
    }

    
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $response =  new Response();
        $data = self::VerificarUsuario($request, $handler);
        $data = json_decode($data);
        if (isset($data->error)) {
            $response->getBody()->write($data->error);
        } else {
            switch ($this->filtro) {
                    case "Todos":
                    {
                        $response = $handler->handle($request);
                        break;
                    }
                    case "Mozo":
                        {
                           if ($data->token->tipo == "Admin" || ($data->token->tipo == "Empleado" && $data->token->funcion ==$this->filtro)) {
                                $response = $handler->handle($request);
                            } else {
                                $response->getBody()->write("No posee los permisos suficientes");
                            }
                            break;
                        }

                    default:
                    {
                        if ($data->token->tipo == "Admin" ||  ($data->token->tipo == $this->filtro)) { //dentro del token levanto el dato que me interesa para evaluar
                            $response = $handler->handle($request);
                        } else {
                            $response->getBody()->write("No posee los permisos suficientes");
                        }
                        break;
                    }
                }
        }
        return $response;
    }


    private static function VerificarUsuario(Request $request, RequestHandler $handler)
    {
        if (empty($request->getHeaderLine('Authorization'))) {
            $returnAux= array("error"=>"Falta el token de verificacion");
        } else {
            try {
                $datos = self::GetDatosUsuario($request);
                $returnAux = array("token"=>$datos);
            } catch (Exception $ex) {
                $returnAux= array("error"=>$ex->getMessage());
            }
        }
        return json_encode($returnAux);
    }
    /**
     * Funcion que retorna los datos del Usuario obtenidos en el Token
     * @param $request Solicitud recibida, donde se va a obtener el token
     * @return object 
     */
    public static function GetDatosUsuario(Request $request)
    {
        try {
            $parametrosToken = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $parametrosToken)[1]);
            AutentificadorJWT::VerificarToken($token);
            $datos = AutentificadorJWT::ObtenerData($token);
        } catch (Exception $ex) {
            throw $ex;
        }

        return $datos;
    }
}
