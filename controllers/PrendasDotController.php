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
        hasPermission(['ADMIN', 'GUARDALMACEN', 'USUARIO']);
        HistorialActividadesController::registrarActividad('/prendasDot', 'Acceso al módulo de prendas de dotación', 1);
        $router->render('prendasDot/index', []);
    }

    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN', 'GUARDALMACEN']);
        getHeadersApi();
    
        HistorialActividadesController::registrarActividad('/prendasDot/guardar', 'Intento de guardar nueva prenda de dotación', 1, ['datos_enviados' => $_POST]);
        
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
                HistorialActividadesController::registrarActividad('/prendasDot/guardar', 'Prenda de dotación guardada exitosamente con ID: ' . $resultado['id'], 1, ['id_generado' => $resultado['id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Prenda registrada correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/prendasDot/guardar', 'Error al registrar prenda de dotación', 1, ['resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar la prenda',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/prendasDot/guardar', 'Excepción al guardar prenda: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
            HistorialActividadesController::registrarActividad('/prendasDot/buscar', 'Búsqueda de prendas de dotación', 1);
            
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
            HistorialActividadesController::registrarError('/prendasDot/buscar', 'Error al buscar prendas: ' . $e->getMessage(), 1);
            
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
        
        HistorialActividadesController::registrarActividad('/prendasDot/modificar', 'Intento de modificar prenda de dotación', 1, ['datos_enviados' => $_POST]);
        
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
                HistorialActividadesController::registrarActividad('/prendasDot/modificar', 'Prenda de dotación modificada exitosamente - ID: ' . $_POST['prenda_id'], 1, ['id_modificado' => $_POST['prenda_id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Prenda modificada correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/prendasDot/modificar', 'Error al modificar prenda de dotación', 1, ['id_prenda' => $_POST['prenda_id'], 'resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar la prenda',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/prendasDot/modificar', 'Excepción al modificar prenda: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
        
        HistorialActividadesController::registrarActividad('/prendasDot/eliminar', 'Intento de eliminar prenda de dotación - ID: ' . $id, 1, ['id_a_eliminar' => $id]);
        
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
                HistorialActividadesController::registrarActividad('/prendasDot/eliminar', 'Prenda de dotación eliminada exitosamente - ID: ' . $id, 1, ['id_eliminado' => $id]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Prenda eliminada correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/prendasDot/eliminar', 'No se pudo eliminar la prenda - ID: ' . $id, 1, ['id_prenda' => $id, 'resultado' => $resultado]);
                
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar la prenda',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/prendasDot/eliminar', 'Excepción al eliminar prenda: ' . $e->getMessage(), 1, ['id_prenda' => $id]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}