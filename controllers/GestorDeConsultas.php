<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as Capsule;

class GestorDeConsultas
{
    #region Empleados
    public static function IngresoAlSistema()
    {
        try {
            $detalle = Capsule::select('select logs.idUsuario, logs.accion, logs.detalle, logs.fechaCreacion, logs.horaRegistro
            from comanda.logs inner join comanda.usuarios on logs.idUsuario
            = usuarios.idUsuarios where usuarios.tipo =  "Empleado" and logs.accion = "Login"');
    
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }
        return $returnAux;
    }

    public static function OperacionesPorSector()
    {
        try {
            $detalle = Capsule::select('select usuarios.funcion,usuarios.sector,logs.entidad,
            count(logs.accion) as operaciones
            from comanda.logs inner join comanda.usuarios on logs.idUsuario
            = usuarios.idUsuarios where usuarios.tipo =  "Empleado" and logs.accion != "Login" group by
            usuarios.funcion,usuarios.sector,logs.entidad');
    
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }


        return $returnAux;
    }

    public static function OperacionesPorSectorYEmpleado()
    {
        try {
            $detalle = Capsule::select('select usuarios.idUsuarios, usuarios.funcion,usuarios.sector,logs.entidad,
            count(logs.accion) as operaciones
            from comanda.logs inner join comanda.usuarios on logs.idUsuario
            = usuarios.idUsuarios where usuarios.tipo =  "Empleado" and logs.accion != "Login" group by usuarios.idUsuarios,
            usuarios.funcion,usuarios.sector,logs.entidad');
    
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }


        return $returnAux;
    }

    public static function OperacionesPorSeparado()
    {
        try {
            $detalle = Capsule::select('select usuarios.idUsuarios, usuarios.funcion,usuarios.sector,logs.entidad, logs.accion,
            count(logs.accion) as operaciones
            from comanda.logs inner join comanda.usuarios on logs.idUsuario
            = usuarios.idUsuarios where usuarios.tipo =  "Empleado" and logs.accion != "Login" group by usuarios.idUsuarios,
            usuarios.funcion,usuarios.sector,logs.entidad,logs.accion');
    
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }
        return $returnAux;
    }
    #end region empleados

    #region Pedidos

    public static function ProductoMasVendido()
    {
        try {
            $detalle = Capsule::select('select cantidadVendida.idProducto,
            productos.tipo, productos.nombre, productos.sector, cantidadVendida.cantidad
            from(
            select pedidos.idProducto, sum(pedidos.cantidad) as cantidad
            from comanda.pedidos
            where pedidos.estado = "Completado"
            group by pedidos.idProducto) as cantidadVendida
            inner join comanda.productos on cantidadVendida.idProducto =  productos.idProductos
            where cantidadVendida.cantidad = (select max(cuentaVendido.cantidad) maximo from
            (select pedidos.idProducto, sum(pedidos.cantidad) as cantidad
            from comanda.pedidos
            where pedidos.estado = "Completado"
            group by pedidos.idProducto) as cuentaVendido)');
    
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }
        return $returnAux;
    }
    public static function ProductoMenosVendido()
    {
        try {
            $detalle = Capsule::select('select cantidadVendida.idProducto,
            productos.tipo, productos.nombre, productos.sector, cantidadVendida.cantidad
            from(
            select pedidos.idProducto, sum(pedidos.cantidad) as cantidad
            from comanda.pedidos
            where pedidos.estado = "Completado"
            group by pedidos.idProducto) as cantidadVendida
            inner join comanda.productos on cantidadVendida.idProducto =  productos.idProductos
            where cantidadVendida.cantidad = (select min(cuentaVendido.cantidad) minimo from
            (select pedidos.idProducto, sum(pedidos.cantidad) as cantidad
            from comanda.pedidos
            where pedidos.estado = "Completado"
            group by pedidos.idProducto) as cuentaVendido) ');
    
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }

        return $returnAux;
    }

    public static function PedidosFueraDeTiempo()
    {
        try {
            $detalle = Capsule::select('select * from comanda.pedidos where  horaFin > horaEstFin and pedidos.estado = "Completado"');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }
        return $returnAux;
    }

    public static function  PedidosCancelados()
    {
        try {
            $detalle = Capsule::select('select * from comanda.pedidos where  pedidos.estado = "Cancelado"');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }
        return $returnAux;
    }

    #end region pedidos

    #region Mesas

    public static function MesaMasUsada()
    {
        try {
            $detalle = Capsule::select('select uso.idMesa, mesas.codigo, uso.cantidad from
            (select idMesa, count(idFacturacion) cantidad from comanda.facturacion
            group by idMesa) as uso inner join comanda.mesas
            on mesas.idMesas =  uso.idMesa
            where uso. cantidad = (select max(maximo.cantidad) from (select idMesa, count(idFacturacion) cantidad from comanda.facturacion
            group by idMesa) as maximo)');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }
        return $returnAux;
    }

    
    public static function MesaMenosUsada()
    {
        try {
            $detalle = Capsule::select('select uso.idMesa, mesas.codigo, uso.cantidad from
            (select idMesa, count(idFacturacion) cantidad from comanda.facturacion
            group by idMesa) as uso inner join comanda.mesas
            on mesas.idMesas =  uso.idMesa
            where uso. cantidad = (select min(minimo.cantidad) from (select idMesa, count(idFacturacion) cantidad from comanda.facturacion
            group by idMesa) as minimo)');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }

        return $returnAux;
    }

    public static function MesaFacturaMayorImporte()
    {
        try {
            $detalle = Capsule::select('select facturacion.idMesa,facturacion.fechaCreacion as fecha, mesas.codigo, facturacion.importe as maximo from comanda.facturacion
            inner join comanda.mesas on facturacion.idMesa = mesas.idMesas
            where importe = (select max(importe) from comanda.facturacion) group by
            facturacion.idMesa,facturacion.fechaCreacion, mesas.codigo, facturacion.importe');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }


        return $returnAux;
    }

    public static function MesaFacturaMenorImporte()
    {
        try {
            $detalle = Capsule::select('select facturacion.idMesa,facturacion.fechaCreacion as fecha, mesas.codigo, facturacion.importe as minimo from comanda.facturacion
            inner join comanda.mesas on facturacion.idMesa = mesas.idMesas
            where importe = (select min(importe) from comanda.facturacion) group by
            facturacion.idMesa,facturacion.fechaCreacion, mesas.codigo, facturacion.importe');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }


        return $returnAux;
    }
    public static function MesaMasFacturo()
    {
        try {
            $detalle = Capsule::select('select importes.idMesa,mesas.codigo, importes.sumImporte as total from (select idMesa, sum(importe) sumImporte from comanda.facturacion group by idMesa) as importes
            inner join comanda.mesas on mesas.idMesas = importes.idMesa 
            where importes.sumImporte =
            (
            select max(sumImporte.sumImporte) maxImporte from(select idMesa, sum(importe) sumImporte from comanda.facturacion
            group by idMesa) as sumImporte
            )');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }

        return $returnAux;
    }

    public static function MesaMenosFacturo()
    {
        try {
            $detalle = Capsule::select('select importes.idMesa,mesas.codigo, importes.sumImporte as total from (select idMesa, sum(importe) sumImporte from comanda.facturacion group by idMesa) as importes
            inner join comanda.mesas on mesas.idMesas = importes.idMesa 
            where importes.sumImporte =
            (
            select min(sumImporte.sumImporte) minImporte from(select idMesa, sum(importe) sumImporte from comanda.facturacion
            group by idMesa) as sumImporte
            )');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }

        return $returnAux;
    }

    public static function FacturacionEntreFechas($fechaInicio, $fechaFin,$idMesa)
    {
        try {
            $detalle = Capsule::select('select idMesa, mesas.codigo, sum(importe) as facturacion from comanda.facturacion
            inner join comanda.mesas on mesas.idMesas = facturacion.idMesa
            where facturacion.idMesa ="'.$idMesa.'" and facturacion.fechaCreacion >= "'.$fechaInicio.'" and facturacion.fechaCreacion <= "'.$fechaFin.'" group by idMesa');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }

        return $returnAux;
    }

    #end region mesas

    #region Calificaciones
    public static function MejoresComentario()
    {
        try {
            $detalle = Capsule::select('select descripcion, fechaCreacion as fecha, valRestaurante as valoracion from
            comanda.encuestas where valRestaurante = (select max(valRestaurante) calMaxima from comanda.encuestas)');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }

        return $returnAux;
    }
    public static function PeoresComentario()
    {
        try {
            $detalle = Capsule::select('select descripcion, fechaCreacion as fecha, valRestaurante as valoracion from
            comanda.encuestas where valRestaurante = (select min(valRestaurante) calMin from comanda.encuestas)');
            $returnAux =  json_encode(array("resultado"=>$detalle));
        } catch (Exception $ex) {
            $returnAux = json_encode(array("error" => $ex->getMessage()));
        }
        return $returnAux;
    }


    #end region Calificaciones
}
