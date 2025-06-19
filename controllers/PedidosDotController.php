<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\PedidosDot;
use Model\PersonalDot;
use Model\PrendasDot;
use Model\TallasDot;

class PedidosDotController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        HistorialActividadesController::registrarActividad('/pedidosDot', 'Acceso al módulo de pedidos de dotación', 1);
        $router->render('pedidosDot/index', []);
    }

    public static function obtenerPersonalAPI()
    {
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/pedidosDot/personal', 'Consulta de personal para pedidos', 1);
            
            $sql = "SELECT per_id, per_nom1, per_nom2, per_ape1, per_ape2, per_puesto, per_area 
                    FROM jjjc_personal_dot 
                    WHERE per_situacion = 1
                    ORDER BY per_nom1 ASC, per_ape1 ASC";
            
            $personal = PersonalDot::fetchArray($sql);
            
            // Formatear nombres completos
            foreach ($personal as &$persona) {
                $persona['nombre_completo'] = trim($persona['per_nom1'] . ' ' . $persona['per_nom2'] . ' ' . $persona['per_ape1'] . ' ' . $persona['per_ape2']);
            }
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Personal encontrado',
                'data' => $personal
            ]);
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/pedidosDot/personal', 'Error al consultar personal: ' . $e->getMessage(), 1);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public static function obtenerPrendasAPI()
    {
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/pedidosDot/prendas', 'Consulta de prendas para pedidos', 1);
            
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
            HistorialActividadesController::registrarError('/pedidosDot/prendas', 'Error al consultar prendas: ' . $e->getMessage(), 1);
            
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
            HistorialActividadesController::registrarActividad('/pedidosDot/tallas', 'Consulta de tallas para prenda ID: ' . $prendaId, 1, ['prenda_id' => $prendaId]);
            
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
            HistorialActividadesController::registrarError('/pedidosDot/tallas', 'Error al consultar tallas: ' . $e->getMessage(), 1, ['prenda_id' => $prendaId]);
            
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
    
        HistorialActividadesController::registrarActividad('/pedidosDot/guardar', 'Intento de guardar nuevo pedido de dotación', 1, ['datos_enviados' => $_POST]);
        
        if (empty($_POST['ped_per_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El personal es requerido'
            ]);
            exit;
        }
        
        if (empty($_POST['ped_prenda_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La prenda es requerida'
            ]);
            exit;
        }
        
        if (empty($_POST['ped_talla_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La talla es requerida'
            ]);
            exit;
        }

        if (empty($_POST['ped_cant_sol']) || $_POST['ped_cant_sol'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad solicitada debe ser mayor a 0'
            ]);
            exit;
        }
        
        $_POST['ped_observ'] = trim(htmlspecialchars($_POST['ped_observ']));
        
        try {
            $pedido = new PedidosDot($_POST);
            $resultado = $pedido->crear();

            if($resultado['resultado'] == 1){
                HistorialActividadesController::registrarActividad('/pedidosDot/guardar', 'Pedido de dotación guardado exitosamente con ID: ' . $resultado['id'], 1, ['id_generado' => $resultado['id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Pedido registrado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/pedidosDot/guardar', 'Error al registrar pedido de dotación', 1, ['resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar el pedido',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/pedidosDot/guardar', 'Excepción al guardar pedido: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
            HistorialActividadesController::registrarActividad('/pedidosDot/buscar', 'Búsqueda de pedidos de dotación', 1);
            
            $sql = "SELECT p.ped_id, p.ped_cant_sol, p.ped_fecha_sol, p.ped_observ, p.ped_estado,
                           p.ped_per_id, p.ped_prenda_id, p.ped_talla_id,
                           TRIM(per.per_nom1 || ' ' || per.per_nom2 || ' ' || per.per_ape1 || ' ' || per.per_ape2) as nombre_completo,
                           per.per_puesto, per.per_area,
                           pr.prenda_nombre, t.talla_nombre
                    FROM jjjc_ped_dot p
                    LEFT JOIN jjjc_personal_dot per ON p.ped_per_id = per.per_id
                    LEFT JOIN jjjc_dot_img pr ON p.ped_prenda_id = pr.prenda_id
                    LEFT JOIN jjjc_tallas_dot t ON p.ped_talla_id = t.talla_id
                    WHERE p.ped_situacion = 1
                    ORDER BY p.ped_fecha_sol DESC";
            
            $pedidos = PedidosDot::fetchArray($sql);
            
            if (!empty($pedidos)) {
                // Formatear fechas
                foreach ($pedidos as &$ped) {
                    if (!empty($ped['ped_fecha_sol'])) {
                        $ped['ped_fecha_sol'] = date('d/m/Y H:i', strtotime($ped['ped_fecha_sol']));
                    }
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Pedidos encontrados: ' . count($pedidos),
                    'data' => $pedidos
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron pedidos',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/pedidosDot/buscar', 'Error al buscar pedidos: ' . $e->getMessage(), 1);
            
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
        
        HistorialActividadesController::registrarActividad('/pedidosDot/modificar', 'Intento de modificar pedido de dotación', 1, ['datos_enviados' => $_POST]);
        
        if (empty($_POST['ped_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del pedido es requerido'
            ]);
            exit;
        }

        if (empty($_POST['ped_cant_sol']) || $_POST['ped_cant_sol'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad solicitada debe ser mayor a 0'
            ]);
            exit;
        }

        $_POST['ped_observ'] = trim(htmlspecialchars($_POST['ped_observ']));
        
        try {
            $sql = "UPDATE jjjc_ped_dot SET 
                    ped_per_id = {$_POST['ped_per_id']},
                    ped_prenda_id = {$_POST['ped_prenda_id']},
                    ped_talla_id = {$_POST['ped_talla_id']},
                    ped_cant_sol = {$_POST['ped_cant_sol']},
                    ped_observ = '{$_POST['ped_observ']}',
                    ped_estado = '{$_POST['ped_estado']}'
                    WHERE ped_id = {$_POST['ped_id']}";
            
            $resultado = PedidosDot::getDB()->exec($sql);

            if($resultado >= 0){
                HistorialActividadesController::registrarActividad('/pedidosDot/modificar', 'Pedido de dotación modificado exitosamente - ID: ' . $_POST['ped_id'], 1, ['id_modificado' => $_POST['ped_id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Pedido modificado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/pedidosDot/modificar', 'Error al modificar pedido de dotación', 1, ['id_pedido' => $_POST['ped_id'], 'resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el pedido',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/pedidosDot/modificar', 'Excepción al modificar pedido: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
        
        HistorialActividadesController::registrarActividad('/pedidosDot/eliminar', 'Intento de eliminar pedido de dotación - ID: ' . $id, 1, ['id_a_eliminar' => $id]);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del pedido es requerido'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_ped_dot SET ped_situacion = 0 WHERE ped_id = $id AND ped_situacion = 1";
            $resultado = PedidosDot::getDB()->exec($sql);
            
            if($resultado > 0){
                HistorialActividadesController::registrarActividad('/pedidosDot/eliminar', 'Pedido de dotación eliminado exitosamente - ID: ' . $id, 1, ['id_eliminado' => $id]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Pedido eliminado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/pedidosDot/eliminar', 'No se pudo eliminar el pedido - ID: ' . $id, 1, ['id_pedido' => $id, 'resultado' => $resultado]);
                
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar el pedido',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/pedidosDot/eliminar', 'Excepción al eliminar pedido: ' . $e->getMessage(), 1, ['id_pedido' => $id]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}