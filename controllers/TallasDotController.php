<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\TallasDot;
use Model\PrendasDot;

class TallasDotController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('tallasDot/index', []);
    }

    public static function obtenerPrendasAPI()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT prenda_id, prenda_nombre, prenda_desc 
                    FROM jjjc_dot_img 
                    WHERE prenda_situacion = 1
                    ORDER BY prenda_nombre ASC";
            
            $prendas = PrendasDot::fetchArray($sql);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Prendas encontradas',
                'data' => $prendas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public static function guardarAPI()
    {
        getHeadersApi();
    
        if (empty($_POST['talla_prenda_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La prenda es requerida'
            ]);
            exit;
        }
        
        if (empty($_POST['talla_nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de la talla es requerido'
            ]);
            exit;
        }
        
        $_POST['talla_nombre'] = trim(htmlspecialchars($_POST['talla_nombre']));
        $_POST['talla_desc'] = trim(htmlspecialchars($_POST['talla_desc']));

        $tallaExistente = TallasDot::fetchFirst("SELECT * FROM jjjc_tallas_dot WHERE talla_nombre = '{$_POST['talla_nombre']}' AND talla_prenda_id = {$_POST['talla_prenda_id']}");
        if ($tallaExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe esta talla para la prenda seleccionada'
            ]);
            exit;
        }
        
        try {
            $talla = new TallasDot($_POST);
            $resultado = $talla->crear();

            if($resultado['resultado'] == 1){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Talla registrada correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar la talla',
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
            $sql = "SELECT t.talla_id, t.talla_nombre, t.talla_desc, 
                           p.prenda_nombre, t.talla_prenda_id
                    FROM jjjc_tallas_dot t
                    LEFT JOIN jjjc_dot_img p ON t.talla_prenda_id = p.prenda_id
                    WHERE t.talla_situacion = 1
                    ORDER BY p.prenda_nombre ASC, t.talla_nombre ASC";
            
            $tallas = TallasDot::fetchArray($sql);
            
            if (!empty($tallas)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Tallas encontradas: ' . count($tallas),
                    'data' => $tallas
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron tallas',
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
        
        if (empty($_POST['talla_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de la talla es requerido'
            ]);
            exit;
        }

        $_POST['talla_nombre'] = trim(htmlspecialchars($_POST['talla_nombre']));
        $_POST['talla_desc'] = trim(htmlspecialchars($_POST['talla_desc']));
        
        $tallaExistente = TallasDot::fetchFirst("SELECT * FROM jjjc_tallas_dot WHERE talla_nombre = '{$_POST['talla_nombre']}' AND talla_prenda_id = {$_POST['talla_prenda_id']} AND talla_id != {$_POST['talla_id']}");
        if ($tallaExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otra talla con ese nombre para esta prenda'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_tallas_dot SET 
                    talla_nombre = '{$_POST['talla_nombre']}',
                    talla_desc = '{$_POST['talla_desc']}',
                    talla_prenda_id = {$_POST['talla_prenda_id']}
                    WHERE talla_id = {$_POST['talla_id']}";
            
            $resultado = TallasDot::getDB()->exec($sql);

            if($resultado >= 0){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Talla modificada correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar la talla',
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
                'mensaje' => 'ID de la talla es requerido'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_tallas_dot SET talla_situacion = 0 WHERE talla_id = $id AND talla_situacion = 1";
            $resultado = TallasDot::getDB()->exec($sql);
            
            if($resultado > 0){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Talla eliminada correctamente',
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar la talla',
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