<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\InventarioDot;
use Model\PrendasDot;
use Model\TallasDot;

class InventarioDotController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        hasPermission(['ADMIN', 'GUARDALMACEN']);
        HistorialActividadesController::registrarActividad('/inventarioDot', 'Acceso al módulo de inventario de dotación', 1);
        $router->render('inventarioDot/index', []);
    }

    public static function obtenerPrendasAPI()
    {
        hasPermissionApi(['ADMIN', 'GUARDALMACEN']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/inventarioDot/prendas', 'Consulta de prendas para inventario', 1);
            
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
            HistorialActividadesController::registrarError('/inventarioDot/prendas', 'Error al consultar prendas: ' . $e->getMessage(), 1);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public static function obtenerTallasAPI()
    {
        hasPermissionApi(['ADMIN', 'GUARDALMACEN']);
        getHeadersApi();
        
        $prendaId = $_GET['prenda_id'] ?? null;
        
        if (!$prendaId) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de prenda requerido'
            ]);
            exit;
        }
        
        try {
            HistorialActividadesController::registrarActividad('/inventarioDot/tallas', 'Consulta de tallas para prenda ID: ' . $prendaId, 1, ['prenda_id' => $prendaId]);
            
            $sql = "SELECT talla_id, talla_nombre, talla_desc 
                    FROM jjjc_tallas_dot 
                    WHERE talla_prenda_id = $prendaId AND talla_situacion = 1
                    ORDER BY talla_nombre ASC";
            
            $tallas = TallasDot::fetchArray($sql);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tallas encontradas',
                'data' => $tallas
            ]);
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/inventarioDot/tallas', 'Error al consultar tallas: ' . $e->getMessage(), 1, ['prenda_id' => $prendaId]);
            
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
    
        HistorialActividadesController::registrarActividad('/inventarioDot/guardar', 'Intento de guardar nuevo inventario de dotación', 1, ['datos_enviados' => $_POST]);
        
        if (empty($_POST['inv_prenda_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La prenda es requerida'
            ]);
            exit;
        }
        
        if (empty($_POST['inv_talla_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La talla es requerida'
            ]);
            exit;
        }

        if (empty($_POST['inv_cant_total']) || $_POST['inv_cant_total'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad total debe ser mayor a 0'
            ]);
            exit;
        }
        
        $_POST['inv_lote'] = trim(htmlspecialchars($_POST['inv_lote']));
        $_POST['inv_observ'] = trim(htmlspecialchars($_POST['inv_observ']));
        $_POST['inv_cant_disp'] = $_POST['inv_cant_total']; 

        $inventarioExistente = InventarioDot::fetchFirst("SELECT * FROM jjjc_inv_dot WHERE inv_prenda_id = {$_POST['inv_prenda_id']} AND inv_talla_id = {$_POST['inv_talla_id']} AND inv_lote = '{$_POST['inv_lote']}'");
        if ($inventarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe inventario para esta combinación de prenda, talla y lote'
            ]);
            exit;
        }
        
        try {
            $inventario = new InventarioDot($_POST);
            $resultado = $inventario->crear();

            if($resultado['resultado'] == 1){
                HistorialActividadesController::registrarActividad('/inventarioDot/guardar', 'Inventario de dotación guardado exitosamente con ID: ' . $resultado['id'], 1, ['id_generado' => $resultado['id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Inventario registrado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/inventarioDot/guardar', 'Error al registrar inventario de dotación', 1, ['resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar el inventario',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/inventarioDot/guardar', 'Excepción al guardar inventario: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
        hasPermissionApi(['ADMIN', 'GUARDALMACEN']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/inventarioDot/buscar', 'Búsqueda de inventarios de dotación', 1);
            
            $sql = "SELECT i.inv_id, i.inv_cant_disp, i.inv_cant_total, i.inv_fecha_ing, 
                           i.inv_lote, i.inv_observ, i.inv_prenda_id, i.inv_talla_id,
                           p.prenda_nombre, t.talla_nombre
                    FROM jjjc_inv_dot i
                    LEFT JOIN jjjc_dot_img p ON i.inv_prenda_id = p.prenda_id
                    LEFT JOIN jjjc_tallas_dot t ON i.inv_talla_id = t.talla_id
                    WHERE i.inv_situacion = 1
                    ORDER BY p.prenda_nombre ASC, t.talla_nombre ASC, i.inv_lote ASC";
            
            $inventarios = InventarioDot::fetchArray($sql);
            
            if (!empty($inventarios)) {
                foreach ($inventarios as &$inv) {
                    if (!empty($inv['inv_fecha_ing'])) {
                        $inv['inv_fecha_ing'] = date('d/m/Y H:i', strtotime($inv['inv_fecha_ing']));
                    }
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Inventarios encontrados: ' . count($inventarios),
                    'data' => $inventarios
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron inventarios',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/inventarioDot/buscar', 'Error al buscar inventarios: ' . $e->getMessage(), 1);
            
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
        
        HistorialActividadesController::registrarActividad('/inventarioDot/modificar', 'Intento de modificar inventario de dotación', 1, ['datos_enviados' => $_POST]);
        
        if (empty($_POST['inv_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del inventario es requerido'
            ]);
            exit;
        }

        if (empty($_POST['inv_cant_total']) || $_POST['inv_cant_total'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad total debe ser mayor a 0'
            ]);
            exit;
        }

        $_POST['inv_lote'] = trim(htmlspecialchars($_POST['inv_lote']));
        $_POST['inv_observ'] = trim(htmlspecialchars($_POST['inv_observ']));
        
        try {
            $sql = "UPDATE jjjc_inv_dot SET 
                    inv_prenda_id = {$_POST['inv_prenda_id']},
                    inv_talla_id = {$_POST['inv_talla_id']},
                    inv_cant_total = {$_POST['inv_cant_total']},
                    inv_cant_disp = {$_POST['inv_cant_disp']},
                    inv_lote = '{$_POST['inv_lote']}',
                    inv_observ = '{$_POST['inv_observ']}'
                    WHERE inv_id = {$_POST['inv_id']}";
            
            $resultado = InventarioDot::getDB()->exec($sql);

            if($resultado >= 0){
                HistorialActividadesController::registrarActividad('/inventarioDot/modificar', 'Inventario de dotación modificado exitosamente - ID: ' . $_POST['inv_id'], 1, ['id_modificado' => $_POST['inv_id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Inventario modificado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/inventarioDot/modificar', 'Error al modificar inventario de dotación', 1, ['id_inventario' => $_POST['inv_id'], 'resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el inventario',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/inventarioDot/modificar', 'Excepción al modificar inventario: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
        
        HistorialActividadesController::registrarActividad('/inventarioDot/eliminar', 'Intento de eliminar inventario de dotación - ID: ' . $id, 1, ['id_a_eliminar' => $id]);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del inventario es requerido'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_inv_dot SET inv_situacion = 0 WHERE inv_id = $id AND inv_situacion = 1";
            $resultado = InventarioDot::getDB()->exec($sql);
            
            if($resultado > 0){
                HistorialActividadesController::registrarActividad('/inventarioDot/eliminar', 'Inventario de dotación eliminado exitosamente - ID: ' . $id, 1, ['id_eliminado' => $id]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Inventario eliminado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/inventarioDot/eliminar', 'No se pudo eliminar el inventario - ID: ' . $id, 1, ['id_inventario' => $id, 'resultado' => $resultado]);
                
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar el inventario',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/inventarioDot/eliminar', 'Excepción al eliminar inventario: ' . $e->getMessage(), 1, ['id_inventario' => $id]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}