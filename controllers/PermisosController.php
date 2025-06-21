<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Permisos;
use Model\Aplicacion;

class PermisosController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        hasPermission(['ADMIN']);
        HistorialActividadesController::registrarActividad('/permisos', 'Acceso al módulo de permisos del sistema', 1);
        $router->render('permisos/index', []);
    }

    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        
        HistorialActividadesController::registrarActividad('/permisos/guardar', 'Intento de guardar nuevo permiso del sistema', 1, ['datos_enviados' => $_POST]);
        
        if (empty($_POST['permiso_app_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una aplicación'
            ]);
            exit;
        }
        
        $_POST['permiso_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['permiso_nombre']))));
        
        $cantidad_nombre = strlen($_POST['permiso_nombre']);
        
        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre del permiso debe tener más de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['permiso_clave'] = strtoupper(trim(htmlspecialchars($_POST['permiso_clave'])));
        
        if (strlen($_POST['permiso_clave']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Clave del permiso debe tener más de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['permiso_desc'] = ucfirst(strtolower(trim(htmlspecialchars($_POST['permiso_desc']))));
        
        if (strlen($_POST['permiso_desc']) < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Descripción debe tener más de 4 caracteres'
            ]);
            exit;
        }
        
        $permisoExistente = Permisos::fetchFirst("SELECT * FROM jjjc_permiso WHERE permiso_clave = '{$_POST['permiso_clave']}' AND permiso_app_id = {$_POST['permiso_app_id']}");
        if ($permisoExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe un permiso con esa clave para esta aplicación'
            ]);
            exit;
        }
        
        $_POST['permiso_fecha'] = '';
        
        try {
            $permiso = new Permisos($_POST);
            $resultado = $permiso->crear();

            if($resultado['resultado'] == 1){
                HistorialActividadesController::registrarActividad('/permisos/guardar', 'Permiso del sistema guardado exitosamente con ID: ' . $resultado['id'], 1, ['id_generado' => $resultado['id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso registrado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/permisos/guardar', 'Error al registrar permiso del sistema', 1, ['resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al registrar el permiso',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/permisos/guardar', 'Excepción al guardar permiso: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/permisos/buscar', 'Búsqueda de permisos del sistema', 1);
            
            $sql = "SELECT p.permiso_id, p.permiso_app_id, p.permiso_nombre, p.permiso_clave, 
                           p.permiso_desc, p.permiso_fecha, p.permiso_situacion, a.app_nombre_corto 
                    FROM jjjc_permiso p 
                    INNER JOIN jjjc_aplicacion a ON p.permiso_app_id = a.app_id 
                    WHERE p.permiso_situacion = 1 
                    ORDER BY p.permiso_id DESC";
            
            $permisos = Permisos::fetchArray($sql);
            
            if (!empty($permisos)) {
                foreach ($permisos as &$permiso) {
                    if (!empty($permiso['permiso_fecha'])) {
                        $permiso['permiso_fecha'] = date('d/m/Y', strtotime($permiso['permiso_fecha']));
                    }
                }
            }
            
            if (!empty($permisos)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permisos encontrados: ' . count($permisos),
                    'data' => $permisos
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron permisos',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/permisos/buscar', 'Error al buscar permisos: ' . $e->getMessage(), 1);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar permisos: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public static function modificarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        
        HistorialActividadesController::registrarActividad('/permisos/modificar', 'Intento de modificar permiso del sistema', 1, ['datos_enviados' => $_POST]);
        
        if (empty($_POST['permiso_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del permiso es requerido'
            ]);
            exit;
        }
        
        if (empty($_POST['permiso_app_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una aplicación'
            ]);
            exit;
        }
        
        $_POST['permiso_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['permiso_nombre']))));
        $_POST['permiso_clave'] = strtoupper(trim(htmlspecialchars($_POST['permiso_clave'])));
        $_POST['permiso_desc'] = ucfirst(strtolower(trim(htmlspecialchars($_POST['permiso_desc']))));
        
        $permisoExistente = Permisos::fetchFirst("SELECT * FROM jjjc_permiso WHERE permiso_clave = '{$_POST['permiso_clave']}' AND permiso_app_id = {$_POST['permiso_app_id']} AND permiso_id != {$_POST['permiso_id']}");
        if ($permisoExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otro permiso con esa clave para esta aplicación'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_permiso SET 
                    permiso_app_id = {$_POST['permiso_app_id']},
                    permiso_nombre = '{$_POST['permiso_nombre']}',
                    permiso_clave = '{$_POST['permiso_clave']}',
                    permiso_desc = '{$_POST['permiso_desc']}'
                    WHERE permiso_id = {$_POST['permiso_id']}";
            
            $resultado = Permisos::getDB()->exec($sql);

            if($resultado >= 0){
                HistorialActividadesController::registrarActividad('/permisos/modificar', 'Permiso del sistema modificado exitosamente - ID: ' . $_POST['permiso_id'], 1, ['id_modificado' => $_POST['permiso_id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso modificado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/permisos/modificar', 'Error al modificar permiso del sistema', 1, ['id_permiso' => $_POST['permiso_id'], 'resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el permiso',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/permisos/modificar', 'Excepción al modificar permiso: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public static function eliminarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        
        $id = $_GET['id'] ?? null;
        
        HistorialActividadesController::registrarActividad('/permisos/eliminar', 'Intento de eliminar permiso del sistema - ID: ' . $id, 1, ['id_a_eliminar' => $id]);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del permiso es requerido'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_permiso SET permiso_situacion = 0 WHERE permiso_id = $id AND permiso_situacion = 1";
            $resultado = Permisos::getDB()->exec($sql);
            
            if($resultado > 0){
                HistorialActividadesController::registrarActividad('/permisos/eliminar', 'Permiso del sistema eliminado exitosamente - ID: ' . $id, 1, ['id_eliminado' => $id]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso eliminado correctamente (situación cambiada a inactiva)',
                ]);
            } else {
                HistorialActividadesController::registrarError('/permisos/eliminar', 'No se pudo eliminar el permiso - ID: ' . $id, 1, ['id_permiso' => $id, 'resultado' => $resultado]);
                
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar el permiso (puede que ya esté eliminado)',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/permisos/eliminar', 'Excepción al eliminar permiso: ' . $e->getMessage(), 1, ['id_permiso' => $id]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public static function obtenerAplicacionesAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/permisos/aplicaciones', 'Consulta de aplicaciones para permisos', 1);
            
            $aplicaciones = Aplicacion::where('app_situacion', 1);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Aplicaciones encontradas',
                'data' => $aplicaciones
            ]);
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/permisos/aplicaciones', 'Error al consultar aplicaciones: ' . $e->getMessage(), 1);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener aplicaciones: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}