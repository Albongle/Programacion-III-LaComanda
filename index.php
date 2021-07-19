<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Exception\NotFoundException;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . './vendor/autoload.php';
require_once "./middlewares/middleware.php";
require_once "./controllers/UsuarioController.php";
require_once "./controllers/ProductoController.php";
require_once "./controllers/MesaController.php";
require_once "./controllers/PedidoController.php";
require_once "./controllers/GestorDeArchivos.php";
require_once "./controllers/EncuestaController.php";
require_once "./controllers/FacturacionController.php";

date_default_timezone_set('America/Argentina/Buenos_Aires');


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();



// Instantiate App
$app = AppFactory::create();
$app->setBasePath("/LaComanda"); 
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
// Add error middleware
$app->addErrorMiddleware(true, true, true);


// Eloquent
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();


// login
$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioController::class . ':Login');


});

// usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(new Middleware("Admin"));
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/buscar/', \UsuarioController::class. ':TraerUno')->add(new Middleware("Admin"));
  $group->put('[/]', \UsuarioController::class. ':ModificarUno')->add(new Middleware("Admin"));
  $group->delete('[/]', \UsuarioController::class. ':BorrarUno')->add(new Middleware("Admin"));
  $group->get('/csv/descargar', \GestorDeArchivos::class . ':DescagarCSV')->add(new Middleware("Admin"));
  $group->post('/csv/cargar', \GestorDeArchivos::class . ':CargarCSV')->add(new Middleware("Admin"));  

});

//productos
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \ProductoController::class . ':CargarUno')->add(new Middleware("Socio"));
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/buscar/', \ProductoController::class. ':TraerUno');
  $group->put('[/]', \ProductoController::class. ':ModificarUno')->add(new Middleware("Socio"));
  $group->get('/csv/descargar', \GestorDeArchivos::class . ':DescagarCSV')->add(new Middleware("Socio"));
  $group->post('/csv/cargar', \GestorDeArchivos::class . ':CargarCSV')->add(new Middleware("Socio")); 

});

//mesas
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController::class . ':CargarUno'); 
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->put('[/]', \MesaController::class. ':ModificarUno')->add(new Middleware("Mozo"));
  $group->put('/cerrar', \MesaController::class. ':CerrarMesa')->add(new Middleware("Socio"));
  $group->get('/csv/descargar', \GestorDeArchivos::class . ':DescagarCSV')->add(new Middleware("Socio")); 
});
//pedidos
$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(new Middleware("Mozo")); 
    $group->get('/listar', \PedidoController::class . ':TraerTodos')->add(new Middleware("Socio"));
    $group->get('/usuario/buscar/', \PedidoController::class. ':TraerUno');
    $group->get('/empleados/buscar/', \PedidoController::class. ':TraerUno')->add(new Middleware("Empleado"));
    $group->put('[/]', \PedidoController::class . ':ModificarUno')->add(new Middleware("Empleado"));
    $group->delete('[/]', \PedidoController::class . ':BorrarUno')->add(new Middleware("Empleado"));
    $group->get('/csv/descargar', \GestorDeArchivos::class . ':DescagarCSV')->add(new Middleware("Socio"));

});

//facturar
$app->group('/facturar', function (RouteCollectorProxy $group) {
  $group->post('[/]', \FacturacionController::class . ':CargarUno')->add(new Middleware("Socio")); 


});

//encuestas
$app->group('/encuesta', function (RouteCollectorProxy $group) {
  $group->post('[/]', \EncuestaController::class . ':CargarUno'); 


});


//GestorDeConsultas
$app->group('/descargarPDF', function (RouteCollectorProxy $group) {

  $group->get('/empleados/ingresoAlSistema', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/empleados/operacionesPorSector', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/empleados/operacionesPorSectorYEmpleado', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/empleados/operacionesPorEmpleado', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/pedidos/masVendido', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/pedidos/menosVendido', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/pedidos/fueraDeTiempo', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/pedidos/cancelados', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/masUsadas', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/menosUsadas', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/masFacturo', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/menosFacturo', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/mayorFactura', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/menorFactura', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/fechaFacturacion', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/mejoresComentarios', \GestorDeArchivos::class . ':GenerarPDf');
  $group->get('/mesas/peoresComentarios', \GestorDeArchivos::class . ':GenerarPDf');

})->add(new Middleware("Admin"));




$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("La Comanda Alejandro Bongioanni");
  return $response;

});

$app->run();


?>