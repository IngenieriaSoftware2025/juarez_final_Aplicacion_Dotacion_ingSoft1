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
        $router->render('pedidosDot/index', []);
    }

    public static function obtenerPersonalAPI()
    {
        getHeadersApi();
        
        try {
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
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Pedido registrado correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar el pedido',
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
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Pedido modificado correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el pedido',
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
                'mensaje' => 'ID del pedido es requerido'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_ped_dot SET ped_situacion = 0 WHERE ped_id = $id AND ped_situacion = 1";
            $resultado = PedidosDot::getDB()->exec($sql);
            
            if($resultado > 0){
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Pedido eliminado correctamente',
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar el pedido',
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