<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\AplicacionController;
use MVC\Router;
use Controllers\AppController;
use Controllers\AsigPermisosController;
use Controllers\EntregasDotController;
use Controllers\EstadisticasDotController;
use Controllers\HistorialActividadesController;
use Controllers\InventarioDotController;
use Controllers\LoginController;
use Controllers\PedidosDotController;
use Controllers\PermisosController;
use Controllers\PersonalDotController;
use Controllers\PrendasDotController;
use Controllers\TallasDotController;
use Controllers\UsuarioController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// Rutas de appcontroller
$router->get('/', [AppController::class,'index']);
$router->get('/inicio', [AppController::class,'inicio']);

// Rutas de Login
$router->get('/login', [LoginController::class,'renderizarPagina']);
$router->post('/login/iniciar', [LoginController::class,'login']);
$router->get('/logout', [LoginController::class,'logout']);



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

// Rutas para el inventario de la dotacion
$router->get('/inventarioDot', [InventarioDotController::class,'renderizarPagina']);
$router->get('/inventarioDot/prendas', [InventarioDotController::class,'obtenerPrendasAPI']);
$router->get('/inventarioDot/tallas', [InventarioDotController::class,'obtenerTallasAPI']);
$router->post('/inventarioDot/guardar', [InventarioDotController::class,'guardarAPI']);
$router->post('/inventarioDot/buscar', [InventarioDotController::class,'buscarAPI']);
$router->post('/inventarioDot/modificar', [InventarioDotController::class,'modificarAPI']);
$router->get('/inventarioDot/eliminar', [InventarioDotController::class,'eliminarAPI']);

// Rutas para los pedidos de la dotacion
$router->get('/pedidosDot', [PedidosDotController::class,'renderizarPagina']);
$router->get('/pedidosDot/personal', [PedidosDotController::class,'obtenerPersonalAPI']);
$router->get('/pedidosDot/prendas', [PedidosDotController::class,'obtenerPrendasAPI']);
$router->get('/pedidosDot/tallas', [PedidosDotController::class,'obtenerTallasAPI']);
$router->post('/pedidosDot/guardar', [PedidosDotController::class,'guardarAPI']);
$router->post('/pedidosDot/buscar', [PedidosDotController::class,'buscarAPI']);
$router->post('/pedidosDot/modificar', [PedidosDotController::class,'modificarAPI']);
$router->get('/pedidosDot/eliminar', [PedidosDotController::class,'eliminarAPI']);


//Rutas para usuarios
$router->get('/usuario', [UsuarioController::class,'renderizarPagina']);
$router->post('/usuario/guardar', [UsuarioController::class,'guardarAPI']);
$router->post('/usuario/buscar', [UsuarioController::class,'buscarAPI']);
$router->get('/usuario/imagen', [UsuarioController::class,'mostrarImagen']);


// Rutas para la entrega de dotacion
$router->get('/entregasDot', [EntregasDotController::class,'renderizarPagina']);
$router->get('/entregasDot/personal', [EntregasDotController::class,'obtenerPersonalAPI']);
$router->get('/entregasDot/pedidos', [EntregasDotController::class,'obtenerPedidosAPI']);
$router->get('/entregasDot/inventario', [EntregasDotController::class,'obtenerInventarioAPI']);
$router->get('/entregasDot/usuarios', [EntregasDotController::class,'obtenerUsuariosAPI']);
$router->post('/entregasDot/guardar', [EntregasDotController::class,'guardarAPI']);
$router->post('/entregasDot/buscar', [EntregasDotController::class,'buscarAPI']);
$router->post('/entregasDot/modificar', [EntregasDotController::class,'modificarAPI']);
$router->get('/entregasDot/eliminar', [EntregasDotController::class,'eliminarAPI']);

//Rutas para la Asignación de Permisos
$router->get('/asigPermisos', [AsigPermisosController::class,'renderizarPagina']);
$router->post('/asigPermisos/guardar', [AsigPermisosController::class,'guardarAPI']);
$router->post('/asigPermisos/buscar', [AsigPermisosController::class,'buscarAPI']);
$router->post('/asigPermisos/modificar', [AsigPermisosController::class,'modificarAPI']);
$router->get('/asigPermisos/eliminar', [AsigPermisosController::class,'eliminarAPI']);
$router->post('/asigPermisos/usuarios', [AsigPermisosController::class,'obtenerUsuariosAPI']);
$router->post('/asigPermisos/aplicaciones', [AsigPermisosController::class,'obtenerAplicacionesAPI']);
$router->get('/asigPermisos/permisos', [AsigPermisosController::class,'obtenerPermisosPorAppAPI']);

// RUTAS PARA ESTADÍSTICAS
$router->get('/estadisticas', [EstadisticasDotController::class, 'renderizarPagina']);
$router->get('/estadisticas/buscar', [EstadisticasDotController::class, 'buscarAPI']);
$router->get('/estadisticas/buscarActividades', [EstadisticasDotController::class,'buscarActividadesAPI']);

// RUTAS PARA APLICACIONES
$router->get('/aplicaciones', [AplicacionController::class,'renderizarPagina']);
$router->post('/aplicaciones/guardar', [AplicacionController::class,'guardarAPI']);
$router->post('/aplicaciones/buscar', [AplicacionController::class,'buscarAPI']);
$router->post('/aplicaciones/modificar', [AplicacionController::class,'modificarAPI']);
$router->get('/aplicaciones/eliminar', [AplicacionController::class,'eliminarAPI']);

// RUTAS PARA PERMISOS
$router->get('/permisos', [PermisosController::class,'renderizarPagina']);
$router->post('/permisos/guardar', [PermisosController::class,'guardarAPI']);
$router->post('/permisos/buscar', [PermisosController::class,'buscarAPI']);
$router->post('/permisos/modificar', [PermisosController::class,'modificarAPI']);
$router->get('/permisos/eliminar', [PermisosController::class,'eliminarAPI']);
$router->post('/permisos/aplicaciones', [PermisosController::class,'obtenerAplicacionesAPI']);

// Rutas para el Historial de Actividades
$router->get('/historial', [HistorialActividadesController::class,'renderizarPagina']);
$router->get('/historial/buscarAPI', [HistorialActividadesController::class,'buscarAPI']);
$router->get('/historial/buscarUsuariosAPI', [HistorialActividadesController::class,'buscarUsuariosAPI']);
$router->get('/historial/buscarAplicacionesAPI', [HistorialActividadesController::class,'buscarAplicacionesAPI']);
$router->get('/historial/buscarRutasAPI', [HistorialActividadesController::class,'buscarRutasAPI']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
