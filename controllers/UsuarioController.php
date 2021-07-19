<?php
require_once "./models/Usuario.php";
require_once "./middlewares/middleware.php";
require_once "./controllers/LogsController.php";
require_once "./interfaces/IApiUsable.php";
require_once "./models/AutentificadorJWT.php";



use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario as Usuario;
use Middleware as Middlewre;
use AutentificadorJWT as AutentificadorJWT;
use LogsController as LogsController;
use Slim\Handlers\Strategies\RequestHandler;

class UsuarioController implements IApiUsable
{
    public function CargarUno(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();

        try {
            if (isset($parametros['nombre']) && isset($parametros['apellido'])&& isset($parametros['mail']) && isset($parametros['clave']) && isset($parametros['tipo'])) {
                if (!Usuario::where('mail', '=', $parametros['mail'])->first()) {
                    if (in_array($parametros['tipo'], Usuario::TIPO)) {
                        $usr =  new Usuario();
                        $usr->nombre = $parametros['nombre'];
                        $usr->apellido = $parametros['apellido'];
                        $usr->mail = $parametros['mail'];
                        $usr->clave = $parametros['clave'];
                        $usr->tipo =  $parametros['tipo'];
                        $usr->funcion = "NA";
                        $usr->sector = "NA";
                        switch (strtolower($parametros['tipo'])) {
                            case 'empleado':
                                {
                                    if (isset($parametros['funcion']) && in_array($parametros['funcion'], Usuario::FUNCION)) {
                                        $usr->funcion =  $parametros['funcion'];
                                        if (strtolower($parametros['funcion']) != 'mozo') {
                                            if (isset($parametros['sector']) && in_array($parametros['sector'], Usuario::SECTOR)) {
                                                $usr->sector =  $parametros['sector'];
                                            } else {
                                                $payload = json_encode(array("mensaje" => "Sector de usuario no recibido o permitido", "Tipos"=> "Tragos, Choperas, Candy Bar, Cocina"));
                                            }
                                        }
                                    } else {
                                        $payload = json_encode(array("mensaje" => "Funcion de usuario no recibida o permitida", "Tipos"=> "Bartender, Mozo, Cervecero, Cocinero"));
                                    }
                                    break;
                                }
                        }
                        if (!isset($payload)) {
                            $usr->estado="Activo";
                            $usr->save();
                            $usrLog=  Middleware::GetDatosUsuario($request);
                            LogsController::CargarUno($usrLog,"Usuarios","Alta",array('Usuario Afectado: ', array($usr->mail)));
                            $payload = json_encode(array("mensaje"=>"Usuario creado con Exito"));
                        }
                    } else {
                        $payload = json_encode(array("mensaje" => "Tipo de usuario no permitidos", "Tipos"=> "Empleado, Socio, Admin"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "mail existente en BD", "mail"=> $parametros['mail']));
                }
            } else {
                $payload = json_encode(array("mensaje" => "No se recibio alguno de los parametros necesarios para el alta de Usuario"));
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
            if (isset($parametros['mail'])) {
                // Conseguimos el objeto
                $usr = Usuario::where('mail', '=', $parametros['mail'])->first();
                // Si existe
                if ($usr !== null) {
                    $datos =  self::ValidaCampos($parametros);
    
                    if (isset($datos)) {
                        if ((isset($datos['estado']) && in_array($datos['estado'], Usuario::ESTADO)) || !isset($datos['estado'])) {
                            foreach ($datos as $key => $value) {
                                $usr->$key = $value;
                            }
                            $usr->save();
                            $usrLog=  Middleware::GetDatosUsuario($request);
                            LogsController::CargarUno($usrLog,"Usuarios","Modificacion",array('Usuario Afectado: ',array($usr->idUsuarios), " Campos Recibidos: ", $datos));
                            $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
                        } else {
                            $payload = json_encode(array("mensaje" => "El parametro a modificar es incorrecto", "Estados"=> "Activo, Suspendido, Baja"));
                        }
                    } else {
                        $payload = json_encode(array("mensaje" => "No se recibio ninguno de los parametros posibles de modificar",));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "Parametro mail necesario, no recibido"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => $ex->getMessage()));
        }

            
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function Login(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        try {
            if (isset($parametros['mail']) && isset($parametros['clave'])) {
                $usuario =  Usuario::Where('mail', '=', $parametros['mail'])->where('clave', '=', $parametros['clave'])->first();
                if ($usuario && $usuario->estado =="Activo") {
                    $datos = [
                        "idUsuario" => $usuario->idUsuarios,
                        "mail" => $usuario->mail,
                        "nombre"=> $usuario->nombre,
                        "apellido"=>$usuario->apellido,
                        "tipo" => $usuario->tipo,
                        "funcion" => $usuario->funcion,
                        "sector" => $usuario->sector,
                        "estado"=> $usuario->estado
                      ];
                    $payload =  AutentificadorJWT::CrearToken($datos);
                    LogsController::CargarUno(AutentificadorJWT::ObtenerData($payload),"Usuario","Login",array('Ingreso al Sistema'));
                } else {
                    $payload = json_encode(array("mensaje" => "No se pudo verirficar el usuario o la contraseña"));
                }
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => $ex->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    private static function ValidaCampos($array)
    {
        $datos =  array('nombre', 'mail', 'funcion', 'apellido','estado','sector', 'funcion','tipo');
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
    
    public function TraerTodos(Request $request, Response $response, $args)
    {
        $lista = Usuario::all();
        $payload = json_encode(array("listaUsuario" => $lista));
  
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    public function TraerUno(Request $request, Response $response, $args)
    {
        $parametros =  $request->getQueryParams();
        if (isset($parametros['id'])) {
            $usuario = Usuario::find($parametros['id']);
            $payload = json_encode($usuario);
        } elseif (isset($parametros['funcion'])) {
            $usuarios =  Usuario::where('funcion', '=', $parametros['funcion'])->get();
            $payload = json_encode($usuarios);
        } elseif (isset($parametros['tipo'])) {
            $usuarios =  Usuario::where('tipo', '=', $parametros['tipo'])->get();
            $payload = json_encode($usuarios);
        } else {
            $payload = json_encode(array("mensaje"=> "No se recibio ninguno de los parametros necesarios id, funcion o tipo"));
        }
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        try {
            if (isset($parametros['id'])) {
                $usr =  Usuario::find($parametros['id']);
                if ($usr) {
                    $usrLog=  Middleware::GetDatosUsuario($request);
                    LogsController::CargarUno($usrLog,"Usuarios","Baja",array('Usuario Afectado: ', array($usr->mail)));
                    $usr->update(['estado'=>'Baja']);
                    $usr->delete();
                    $payload = json_encode(array("mensaje" => "Usuario borrada con exito"));
                } else {
                    $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "No se recibidio algunos de los parametros necesarios para el borrado"));
            }
        } catch (Exception $ex) {
            $payload = json_encode(array("mensaje" => $ex->getMessage()));
        }


        $response->getBody()->write($payload);
        return $response;
    }
}
