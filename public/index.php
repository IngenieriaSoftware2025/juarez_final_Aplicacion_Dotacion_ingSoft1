<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\PersonalDotController;
use Controllers\PrendasDotController;
use Controllers\TallasDotController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

// Rutas para el personal
$router->get('/personalDot', [PersonalDotController::class,'renderizarPagina']);
$router->post('/personalDot/guardar', [PersonalDotController::class,'guardarAPI']);
$router->post('/personalDot/buscar', [PersonalDotController::class,'buscarAPI']);
$router->post('/personalDot/modificar', [PersonalDotController::class,'modificarAPI']);
$router->get('/personalDot/eliminar', [PersonalDotController::class,'eliminarAPI']);

// Rutas para las prendas de la dotacion
$router->get('/prendasDot', [PrendasDotController::class,'renderizarPagina']);
$router->post('/prendasDot/guardar', [PrendasDotController::class,'guardarAPI']);
$router->post('/prendasDot/buscar', [PrendasDotController::class,'buscarAPI']);
$router->post('/prendasDot/modificar', [PrendasDotController::class,'modificarAPI']);
$router->get('/prendasDot/eliminar', [PrendasDotController::class,'eliminarAPI']);


// Rutas para las tallas de la dotacion
$router->get('/tallasDot', [TallasDotController::class,'renderizarPagina']);
$router->get('/tallasDot/prendas', [TallasDotController::class,'obtenerPrendasAPI']);
$router->post('/tallasDot/guardar', [TallasDotController::class,'guardarAPI']);
$router->post('/tallasDot/buscar', [TallasDotController::class,'buscarAPI']);
$router->post('/tallasDot/modificar', [TallasDotController::class,'modificarAPI']);
$router->get('/tallasDot/eliminar', [TallasDotController::class,'eliminarAPI']);





// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
