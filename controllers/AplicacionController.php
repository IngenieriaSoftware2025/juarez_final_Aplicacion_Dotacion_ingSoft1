<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Aplicacion;

class AplicacionController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('aplicaciones/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        
        // Sanitizar nombre largo
        $_POST['app_nombre_largo'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_largo']))));
        
        $cantidad_largo = strlen($_POST['app_nombre_largo']);
        
        if ($cantidad_largo < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre largo debe tener más de 4 caracteres'
            ]);
            exit;
        }
        
        // Sanitizar nombre mediano
        $_POST['app_nombre_mediano'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_mediano']))));
        
        $cantidad_mediano = strlen($_POST['app_nombre_mediano']);
        
        if ($cantidad_mediano < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre mediano debe tener más de 2 caracteres'
            ]);
            exit;
        }
        
        // Sanitizar nombre corto
        $_POST['app_nombre_corto'] = strtoupper(trim(htmlspecialchars($_POST['app_nombre_corto'])));
        
        $cantidad_corto = strlen($_POST['app_nombre_corto']);
        
        if ($cantidad_corto < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre corto debe tener más de 1 caracteres'
            ]);
            exit;
        }
        
        // Verificar si el nombre corto ya existe
        $appExistente = Aplicacion::where('app_nombre_corto', $_POST['app_nombre_corto']);
        if (!empty($appExistente)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe una aplicación con ese nombre corto'
            ]);
            exit;
        }
        
        $_POST['app_fecha_creacion'] = '';
        
        $aplicacion = new Aplicacion($_POST);
        $resultado = $aplicacion->crear();

        if($resultado['resultado'] == 1){
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Aplicación registrada correctamente',
            ]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al registrar la aplicación',
            ]);
            exit;
        }
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        
        try {
             // Usar consulta para la fecha
            $sql = "SELECT app_id, app_nombre_largo, app_nombre_mediano, app_nombre_corto, 
                           app_fecha_creacion, app_situacion 
                    FROM jjjc_aplicacion 
                    WHERE app_situacion = 1";
            
            $aplicaciones = Aplicacion::fetchArray($sql);
            
            // Formatear fechas
            if (!empty($aplicaciones)) {
                foreach ($aplicaciones as &$aplicacion) {
                    if (!empty($aplicacion['app_fecha_creacion'])) {
                        $aplicacion['app_fecha_creacion'] = date('d/m/Y', strtotime($aplicacion['app_fecha_creacion']));
                    }
                }
            }
            
            if (!empty($aplicaciones)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Aplicaciones encontradas: ' . count($aplicaciones),
                    'data' => $aplicaciones
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron aplicaciones',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar aplicaciones: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        
        if (empty($_POST['app_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de la aplicación es requerido'
            ]);
            exit;
        }
        
        $_POST['app_nombre_largo'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_largo']))));
        $_POST['app_nombre_mediano'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_mediano']))));
        $_POST['app_nombre_corto'] = strtoupper(trim(htmlspecialchars($_POST['app_nombre_corto'])));
        
        $appExistente = Aplicacion::fetchFirst("SELECT * FROM jjjc_aplicacion WHERE app_nombre_corto = '{$_POST['app_nombre_corto']}' AND app_id != {$_POST['app_id']}");
        if ($appExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otra aplicación con ese nombre corto'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_aplicacion SET 
                    app_nombre_largo = '{$_POST['app_nombre_largo']}',
                    app_nombre_mediano = '{$_POST['app_nombre_mediano']}',
                    app_nombre_corto = '{$_POST['app_nombre_corto']}'
                    WHERE app_id = {$_POST['app_id']}";
            
            $resultado = Aplicacion::getDB()->exec($sql);

            if($resultado >= 0){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Aplicación modificada correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar la aplicación',
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
                'mensaje' => 'ID de la aplicación es requerido'
            ]);
            exit;
        }
        
        try {
            // Cambiar situación a 0  NO eliminar físicamente
            $sql = "UPDATE jjjc_aplicacion SET app_situacion = 0 WHERE app_id = $id AND app_situacion = 1";
            $resultado = Aplicacion::getDB()->exec($sql);
            
            if($resultado > 0){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Aplicación eliminada correctamente (situación cambiada a inactiva)',
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar la aplicación (puede que ya esté eliminada)',
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
}