<?php
require_once "./models/Logs.php";
require_once "./middlewares/middleware.php";

use Middleware as Middleware;
use App\Models\Logs as Logs;
class LogsController
{
    /**
     * Guarda un log de la accion
     * @param $datosUsr son los datos del usuario que esta realizando la accion
     * @param $entidad es la entidad a la que esta afectando: Usuarios, Pedidos, Productos, etc
     * @param $accion es la accion realizada: Loguo, Alta, Baja, Modificacion
     * @param $arrayDetalle Array multidimensional para detallar la accion [Accion: [detalle], Accion2: [detalle2]]
     * @return void
     */
    public static function CargarUno($datosUsr, string $entidad, string $accion, array $arrayDetalle)
    {
        $cadena ="";
        try {
            if (isset($datosUsr)&& isset($entidad) && isset($accion)&& isset($arrayDetalle)) {
                foreach ($arrayDetalle as $keyUno => $valueUno) {
                    if (is_array($valueUno)) {
                        $cont=0;
                            foreach ($valueUno as $valueDos) {
                                $cont++;
                                $cadena.=$valueDos;
                                if ($cont < (count($valueUno))) {
                                    $cadena.=", ";
                                }
                            }
                    } else {
                        $cadena.=$valueUno;
                    }
                    if ($keyUno < (count($arrayDetalle)-1) && !is_array($arrayDetalle[$keyUno+1])) {
                        $cadena.=" - ";
                    }
                }
                $log = new Logs();
                $log->idUsuario = $datosUsr->idUsuario;
                $log->horaRegistro =  date("H:i:s");
                $log->idUsuario =  $datosUsr->idUsuario;
                $log->entidad =  $entidad;
                $log->accion =  $accion;
                $log->detalle = $cadena;
                $log->save();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
