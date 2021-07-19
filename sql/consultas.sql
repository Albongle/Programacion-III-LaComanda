/*Detalle de ingreso al sistema*/
select logs.idUsuario, logs.accion, logs.detalle, logs.fechaCreacion, logs.horaRegistro
from comanda.logs inner join comanda.usuarios on logs.idUsuario
= usuarios.idUsuarios where usuarios.tipo =  "Empleado" and logs.accion = "Login";


/*Operaciones por sector*/
select usuarios.funcion,usuarios.sector,logs.entidad,
count(logs.accion) as operaciones
from comanda.logs inner join comanda.usuarios on logs.idUsuario
= usuarios.idUsuarios where usuarios.tipo =  "Empleado" and logs.accion != "Login" group by
usuarios.funcion,usuarios.sector,logs.entidad;

/*operaciones por sector y empleado*/
select usuarios.idUsuarios, usuarios.funcion,usuarios.sector,logs.entidad,
count(logs.accion) as operaciones
from comanda.logs inner join comanda.usuarios on logs.idUsuario
= usuarios.idUsuarios where usuarios.tipo =  "Empleado" and logs.accion != "Login" group by usuarios.idUsuarios,
usuarios.funcion,usuarios.sector,logs.entidad;

/*Operaciones de cada uno por separado*/
select usuarios.idUsuarios, usuarios.funcion,usuarios.sector,logs.entidad, logs.accion,
count(logs.accion) as operaciones
from comanda.logs inner join comanda.usuarios on logs.idUsuario
= usuarios.idUsuarios where usuarios.tipo =  "Empleado" and logs.accion != "Login" group by usuarios.idUsuarios,
usuarios.funcion,usuarios.sector,logs.entidad,logs.accion;



/*cantidad maxima Vendida*/
select cantidadVendida.idProducto,
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
group by pedidos.idProducto) as cuentaVendido) ;


/*cantidad mainima Vendida*/
select cantidadVendida.idProducto,
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
group by pedidos.idProducto) as cuentaVendido) ;


/*Pedidos fuera de tiempo*/
select * from comanda.pedidos where  horaFin > horaEstFin and pedidos.estado = "Completado";

/*Pedidos Cancelados*/
select * from comanda.pedidos where  pedidos.estado = "Cancelado";



/*Mesa mas Usada*/
select uso.idMesa, mesas.codigo, uso.cantidad from
(select idMesa, count(idFacturacion) cantidad from comanda.facturacion
group by idMesa) as uso inner join comanda.mesas
on mesas.idMesas =  uso.idMesa
where uso. cantidad = (select max(maximo.cantidad) from (select idMesa, count(idFacturacion) cantidad from comanda.facturacion
group by idMesa) as maximo) ;


/*Mesa menos usada*/
select uso.idMesa, mesas.codigo, uso.cantidad from
(select idMesa, count(idFacturacion) cantidad from comanda.facturacion
group by idMesa) as uso inner join comanda.mesas
on mesas.idMesas =  uso.idMesa
where uso. cantidad = (select min(minimo.cantidad) from (select idMesa, count(idFacturacion) cantidad from comanda.facturacion
group by idMesa) as minimo);

/*Mesa que mas facturo*/

select importes.idMesa,mesas.codigo, importes.sumImporte as total from (select idMesa, sum(importe) sumImporte from comanda.facturacion group by idMesa) as importes
inner join comanda.mesas on mesas.idMesas = importes.idMesa 
where importes.sumImporte =
(
select max(sumImporte.sumImporte) maxImporte from(select idMesa, sum(importe) sumImporte from comanda.facturacion
group by idMesa) as sumImporte
);

/*Mesa que menos facturo*/
select importes.idMesa,mesas.codigo, importes.sumImporte as total from (select idMesa, sum(importe) sumImporte from comanda.facturacion group by idMesa) as importes
inner join comanda.mesas on mesas.idMesas = importes.idMesa 
where importes.sumImporte =
(
select min(sumImporte.sumImporte) minImporte from(select idMesa, sum(importe) sumImporte from comanda.facturacion
group by idMesa) as sumImporte
);


/*Mesa mayor importe*/
select facturacion.idMesa,facturacion.fechaCreacion as fecha, mesas.codigo, facturacion.importe as maximo from comanda.facturacion
inner join comanda.mesas on facturacion.idMesa = mesas.idMesas
where importe = (select max(importe) from comanda.facturacion) group by
facturacion.idMesa,facturacion.fechaCreacion, mesas.codigo, facturacion.importe;

/*Mesa menor importe*/
select facturacion.idMesa,facturacion.fechaCreacion as fecha, mesas.codigo, facturacion.importe as minimo from comanda.facturacion
inner join comanda.mesas on facturacion.idMesa = mesas.idMesas
where importe = (select min(importe) from comanda.facturacion) group by
facturacion.idMesa,facturacion.fechaCreacion, mesas.codigo, facturacion.importe;


/*Facturacion entre 2 fechas dadas*/
select idMesa, mesas.codigo, sum(importe) as facturacion from comanda.facturacion
inner join comanda.mesas on mesas.idMesas = facturacion.idMesa
where facturacion.idMesa =  '2' and facturacion.fechaCreacion >= '2021-05-01' and facturacion.fechaCreacion <= '2021-05-31'
group by idMesa;


/*Calificacion maxima*/
select descripcion, fechaCreacion as fecha, valRestaurante as valoracion from
comanda.encuestas where valRestaurante = (select max(valRestaurante) calMaxima from comanda.encuestas);

/*Calificacion minima*/
select descripcion, fechaCreacion as fecha, valRestaurante as valoracion from
comanda.encuestas where valRestaurante = (select min(valRestaurante) calMin from comanda.encuestas);






