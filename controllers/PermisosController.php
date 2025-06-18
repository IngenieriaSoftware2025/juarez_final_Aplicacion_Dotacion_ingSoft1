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
        $router->render('permisos/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        
        // Validar aplicación
        if (empty($_POST['permiso_app_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una aplicación'
            ]);
            exit;
        }
        
        // Sanitizar nombre del permiso
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
        
        // Sanitizar clave del permiso
        $_POST['permiso_clave'] = strtoupper(trim(htmlspecialchars($_POST['permiso_clave'])));
        
        if (strlen($_POST['permiso_clave']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Clave del permiso debe tener más de 1 caracteres'
            ]);
            exit;
        }
        
        // Sanitizar descripción
        $_POST['permiso_desc'] = ucfirst(strtolower(trim(htmlspecialchars($_POST['permiso_desc']))));
        
        if (strlen($_POST['permiso_desc']) < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Descripción debe tener más de 4 caracteres'
            ]);
            exit;
        }
        
        // Verificar si la clave ya existe para esa aplicación
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
        
        $permiso = new Permisos($_POST);
        $resultado = $permiso->crear();

        if($resultado['resultado'] == 1){
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Permiso registrado correctamente',
            ]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al registrar el permiso',
            ]);
            exit;
        }
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT p.permiso_id, p.permiso_app_id, p.permiso_nombre, p.permiso_clave, 
                           p.permiso_desc, p.permiso_fecha, p.permiso_situacion, a.app_nombre_corto 
                    FROM permiso p 
                    INNER JOIN jjjc_aplicacion a ON p.permiso_app_id = a.app_id 
                    WHERE p.permiso_situacion = 1 
                    ORDER BY p.permiso_id DESC";
            
            $permisos = Permisos::fetchArray($sql);
            
            // Formatear fechas
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
        getHeadersApi();
        
        if (empty($_POST['permiso_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del permiso es requerido'
            ]);
            exit;
        }
        
        // Validar aplicación
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
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso modificado correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el permiso',
                ]);
            }
        } catch (Exception $e) {
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
        getHeadersApi();
        
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del permiso es requerido'
            ]);
            exit;
        }
        
        try {
            // Cambiar situación a 0 - NO eliminar físicamente
            $sql = "UPDATE jjjc_permiso SET permiso_situacion = 0 WHERE permiso_id = $id AND permiso_situacion = 1";
            $resultado = Permisos::getDB()->exec($sql);
            
            if($resultado > 0){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso eliminado correctamente (situación cambiada a inactiva)',
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar el permiso (puede que ya esté eliminado)',
                ]);
            }
        } catch (Exception $e) {
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
        getHeadersApi();
        
        try {
            $aplicaciones = Aplicacion::where('app_situacion', 1);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Aplicaciones encontradas',
                'data' => $aplicaciones
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener aplicaciones: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}