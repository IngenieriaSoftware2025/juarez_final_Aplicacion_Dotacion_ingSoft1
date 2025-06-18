<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\PrendasDot;

class PrendasDotController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('prendasDot/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();
    
        $_POST['prenda_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['prenda_nombre']))));
        
        $cantidad_nombre = strlen($_POST['prenda_nombre']);
        
        if ($cantidad_nombre < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de la prenda debe tener mas de 2 caracteres'
            ]);
            exit;
        }
        
        $_POST['prenda_desc'] = ucfirst(strtolower(trim(htmlspecialchars($_POST['prenda_desc']))));
        
        if (strlen($_POST['prenda_desc']) < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La descripcion debe tener al menos 5 caracteres'
            ]);
            exit;
        }

        $prendaExistente = PrendasDot::fetchFirst("SELECT * FROM jjjc_dot_img WHERE prenda_nombre = '{$_POST['prenda_nombre']}'");
        if ($prendaExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe una prenda con ese nombre'
            ]);
            exit;
        }
        
        try {
            $prenda = new PrendasDot($_POST);
            $resultado = $prenda->crear();

            if($resultado['resultado'] == 1){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Prenda registrada correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar la prenda',
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
    
    public static function buscarAPI()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT prenda_id, prenda_nombre, prenda_desc, prenda_fecha_crea, prenda_situacion 
                    FROM jjjc_dot_img 
                    WHERE prenda_situacion = 1";
            
            $prendas = PrendasDot::fetchArray($sql);
            
            if (!empty($prendas)) {
                foreach ($prendas as &$p) {
                    if (!empty($p['prenda_fecha_crea'])) {
                        $p['prenda_fecha_crea'] = date('d/m/Y', strtotime($p['prenda_fecha_crea']));
                    }
                }
            }
            
            if (!empty($prendas)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Prendas encontradas: ' . count($prendas),
                    'data' => $prendas
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron prendas',
                    'data' => []
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
    
    public static function modificarAPI()
    {
        getHeadersApi();
        
        if (empty($_POST['prenda_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de la prenda es requerido'
            ]);
            exit;
        }

        $_POST['prenda_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['prenda_nombre']))));
        $_POST['prenda_desc'] = ucfirst(strtolower(trim(htmlspecialchars($_POST['prenda_desc']))));
        
        $prendaExistente = PrendasDot::fetchFirst("SELECT * FROM jjjc_dot_img WHERE prenda_nombre = '{$_POST['prenda_nombre']}' AND prenda_id != {$_POST['prenda_id']}");
        if ($prendaExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otra prenda con ese nombre'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_dot_img SET 
                    prenda_nombre = '{$_POST['prenda_nombre']}',
                    prenda_desc = '{$_POST['prenda_desc']}'
                    WHERE prenda_id = {$_POST['prenda_id']}";
            
            $resultado = PrendasDot::getDB()->exec($sql);

            if($resultado >= 0){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Prenda modificada correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar la prenda',
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
                'mensaje' => 'ID de la prenda es requerido'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_dot_img SET prenda_situacion = 0 WHERE prenda_id = $id AND prenda_situacion = 1";
            $resultado = PrendasDot::getDB()->exec($sql);
            
            if($resultado > 0){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Prenda eliminada correctamente',
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar la prenda',
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