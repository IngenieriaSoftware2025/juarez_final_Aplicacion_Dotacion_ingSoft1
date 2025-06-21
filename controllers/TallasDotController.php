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
        hasPermission(['ADMIN', 'GUARDALMACEN', 'USUARIO']);
        HistorialActividadesController::registrarActividad('/tallasDot', 'Acceso al módulo de tallas de dotación', 1);
        $router->render('tallasDot/index', []);
    }

    public static function obtenerPrendasAPI()
    {
        hasPermissionApi(['ADMIN', 'GUARDALMACEN', 'USUARIO']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/tallasDot/prendas', 'Consulta de prendas para tallas', 1);
            
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
            HistorialActividadesController::registrarError('/tallasDot/prendas', 'Error al consultar prendas: ' . $e->getMessage(), 1);
            
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
        hasPermissionApi(['ADMIN', 'GUARDALMACEN']);
        getHeadersApi();
    
        HistorialActividadesController::registrarActividad('/tallasDot/guardar', 'Intento de guardar nueva talla de dotación', 1, ['datos_enviados' => $_POST]);
        
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
                HistorialActividadesController::registrarActividad('/tallasDot/guardar', 'Talla de dotación guardada exitosamente con ID: ' . $resultado['id'], 1, ['id_generado' => $resultado['id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Talla registrada correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/tallasDot/guardar', 'Error al registrar talla de dotación', 1, ['resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar la talla',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/tallasDot/guardar', 'Excepción al guardar talla: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
        hasPermissionApi(['ADMIN', 'GUARDALMACEN', 'USUARIO']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/tallasDot/buscar', 'Búsqueda de tallas de dotación', 1);
            
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
            HistorialActividadesController::registrarError('/tallasDot/buscar', 'Error al buscar tallas: ' . $e->getMessage(), 1);
            
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
        hasPermissionApi(['ADMIN', 'GUARDALMACEN']);
        getHeadersApi();
        
        HistorialActividadesController::registrarActividad('/tallasDot/modificar', 'Intento de modificar talla de dotación', 1, ['datos_enviados' => $_POST]);
        
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
                HistorialActividadesController::registrarActividad('/tallasDot/modificar', 'Talla de dotación modificada exitosamente - ID: ' . $_POST['talla_id'], 1, ['id_modificado' => $_POST['talla_id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Talla modificada correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/tallasDot/modificar', 'Error al modificar talla de dotación', 1, ['id_talla' => $_POST['talla_id'], 'resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar la talla',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/tallasDot/modificar', 'Excepción al modificar talla: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
        hasPermissionApi(['ADMIN', 'GUARDALMACEN']);
        getHeadersApi();
        
        $id = $_GET['id'] ?? null;
        
        HistorialActividadesController::registrarActividad('/tallasDot/eliminar', 'Intento de eliminar talla de dotación - ID: ' . $id, 1, ['id_a_eliminar' => $id]);
        
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
                HistorialActividadesController::registrarActividad('/tallasDot/eliminar', 'Talla de dotación eliminada exitosamente - ID: ' . $id, 1, ['id_eliminado' => $id]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Talla eliminada correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/tallasDot/eliminar', 'No se pudo eliminar la talla - ID: ' . $id, 1, ['id_talla' => $id, 'resultado' => $resultado]);
                
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar la talla',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/tallasDot/eliminar', 'Excepción al eliminar talla: ' . $e->getMessage(), 1, ['id_talla' => $id]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}