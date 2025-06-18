<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\EntregasDot;
use Model\Usuario;
use Model\PedidosDot;
use Model\InventarioDot;
use Model\PersonalDot;

class EntregasDotController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('entregasDot/index', []);
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

    public static function obtenerPedidosAPI()
    {
        getHeadersApi();
        
        $personalId = $_GET['personal_id'] ?? null;
        
        if (!$personalId) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de personal requerido'
            ]);
            exit;
        }
        
        try {
            $sql = "SELECT p.ped_id, p.ped_cant_sol, p.ped_fecha_sol, p.ped_observ,
                           pr.prenda_nombre, t.talla_nombre, p.ped_prenda_id, p.ped_talla_id
                    FROM jjjc_ped_dot p
                    LEFT JOIN jjjc_dot_img pr ON p.ped_prenda_id = pr.prenda_id
                    LEFT JOIN jjjc_tallas_dot t ON p.ped_talla_id = t.talla_id
                    WHERE p.ped_per_id = $personalId 
                    AND p.ped_estado = 'APROBADO' 
                    AND p.ped_situacion = 1
                    ORDER BY p.ped_fecha_sol DESC";
            
            $pedidos = PedidosDot::fetchArray($sql);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Pedidos encontrados',
                'data' => $pedidos
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

    public static function obtenerInventarioAPI()
    {
        getHeadersApi();
        
        $prendaId = $_GET['prenda_id'] ?? null;
        $tallaId = $_GET['talla_id'] ?? null;
        
        if (!$prendaId || !$tallaId) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de prenda y talla requeridos'
            ]);
            exit;
        }
        
        try {
            $sql = "SELECT inv_id, inv_cant_disp, inv_cant_total, inv_lote, pr.prenda_nombre, t.talla_nombre
                    FROM jjjc_inv_dot i
                    LEFT JOIN jjjc_dot_img pr ON i.inv_prenda_id = pr.prenda_id
                    LEFT JOIN jjjc_tallas_dot t ON i.inv_talla_id = t.talla_id
                    WHERE i.inv_prenda_id = $prendaId 
                    AND i.inv_talla_id = $tallaId 
                    AND i.inv_cant_disp > 0 
                    AND i.inv_situacion = 1
                    ORDER BY i.inv_fecha_ing ASC";
            
            $inventario = InventarioDot::fetchArray($sql);
            
            if (empty($inventario)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay stock disponible para esta prenda y talla específica',
                    'data' => []
                ]);
                exit;
            }
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inventario disponible para ' . $inventario[0]['prenda_nombre'] . ' talla ' . $inventario[0]['talla_nombre'],
                'data' => $inventario
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
    
        if (empty($_POST['ent_per_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El personal es requerido'
            ]);
            exit;
        }
        
        if (empty($_POST['ent_ped_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El pedido es requerido'
            ]);
            exit;
        }
        
        if (empty($_POST['ent_inv_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El inventario es requerido'
            ]);
            exit;
        }

        if (empty($_POST['ent_cant_ent']) || $_POST['ent_cant_ent'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad entregada debe ser mayor a 0'
            ]);
            exit;
        }

        if (empty($_POST['ent_usuario_ent'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El personal que entrega es requerido'
            ]);
            exit;
        }
        
        $_POST['ent_observ'] = trim(htmlspecialchars($_POST['ent_observ']));
        
        try {
            // Verificar stock disponible
            $sqlStock = "SELECT inv_cant_disp FROM jjjc_inv_dot WHERE inv_id = {$_POST['ent_inv_id']}";
            $inventario = InventarioDot::fetchFirst($sqlStock);
            
            if (!$inventario || $inventario['inv_cant_disp'] < $_POST['ent_cant_ent']) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Stock insuficiente en inventario'
                ]);
                exit;
            }

            // Crear la entrega
            $entrega = new EntregasDot($_POST);
            $resultado = $entrega->crear();

            if($resultado['resultado'] == 1){
                // Actualizar stock del inventario
                $sqlActualizar = "UPDATE jjjc_inv_dot SET 
                                  inv_cant_disp = inv_cant_disp - {$_POST['ent_cant_ent']}
                                  WHERE inv_id = {$_POST['ent_inv_id']}";
                EntregasDot::getDB()->exec($sqlActualizar);

                // Verificar si se completó el pedido
                $sqlPedido = "SELECT ped_cant_sol FROM jjjc_ped_dot WHERE ped_id = {$_POST['ent_ped_id']}";
                $pedido = PedidosDot::fetchFirst($sqlPedido);
                
                $sqlEntregado = "SELECT SUM(ent_cant_ent) as total_entregado 
                                FROM jjjc_ent_dot 
                                WHERE ent_ped_id = {$_POST['ent_ped_id']} 
                                AND ent_situacion = 1";
                $totalEntregado = EntregasDot::fetchFirst($sqlEntregado);
                
                if ($totalEntregado['total_entregado'] >= $pedido['ped_cant_sol']) {
                    $sqlActualizarPedido = "UPDATE jjjc_ped_dot SET ped_estado = 'ENTREGADO' 
                                           WHERE ped_id = {$_POST['ent_ped_id']}";
                    PedidosDot::getDB()->exec($sqlActualizarPedido);
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Entrega registrada correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al registrar la entrega',
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
            $sql = "SELECT e.ent_id, e.ent_cant_ent, e.ent_fecha_ent, e.ent_observ,
                           e.ent_per_id, e.ent_ped_id, e.ent_inv_id, e.ent_usuario_ent,
                           TRIM(per.per_nom1 || ' ' || per.per_nom2 || ' ' || per.per_ape1 || ' ' || per.per_ape2) as nombre_personal,
                           per.per_puesto, per.per_area,
                           pr.prenda_nombre, t.talla_nombre,
                           TRIM(u.usuario_nom1 || ' ' || u.usuario_ape1) as nombre_usuario,
                           p.ped_cant_sol
                    FROM jjjc_ent_dot e
                    LEFT JOIN jjjc_personal_dot per ON e.ent_per_id = per.per_id
                    LEFT JOIN jjjc_ped_dot p ON e.ent_ped_id = p.ped_id
                    LEFT JOIN jjjc_inv_dot i ON e.ent_inv_id = i.inv_id
                    LEFT JOIN jjjc_dot_img pr ON i.inv_prenda_id = pr.prenda_id
                    LEFT JOIN jjjc_tallas_dot t ON i.inv_talla_id = t.talla_id
                    LEFT JOIN jjjc_usuario u ON e.ent_usuario_ent = u.usuario_id
                    WHERE e.ent_situacion = 1
                    ORDER BY e.ent_fecha_ent DESC";
            
            $entregas = EntregasDot::fetchArray($sql);
            
            if (!empty($entregas)) {
                // Formatear fechas
                foreach ($entregas as &$ent) {
                    if (!empty($ent['ent_fecha_ent'])) {
                        $ent['ent_fecha_ent'] = date('d/m/Y H:i', strtotime($ent['ent_fecha_ent']));
                    }
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Entregas encontradas: ' . count($entregas),
                    'data' => $entregas
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron entregas',
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
        
        if (empty($_POST['ent_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de la entrega es requerido'
            ]);
            exit;
        }

        if (empty($_POST['ent_cant_ent']) || $_POST['ent_cant_ent'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad entregada debe ser mayor a 0'
            ]);
            exit;
        }

        $_POST['ent_observ'] = trim(htmlspecialchars($_POST['ent_observ']));
        
        try {
            $sqlAnterior = "SELECT ent_cant_ent, ent_inv_id FROM jjjc_ent_dot WHERE ent_id = {$_POST['ent_id']}";
            $entregaAnterior = EntregasDot::fetchFirst($sqlAnterior);
            
            $diferencia = $_POST['ent_cant_ent'] - $entregaAnterior['ent_cant_ent'];
            
            if ($diferencia > 0) {
                $sqlStock = "SELECT inv_cant_disp FROM jjjc_inv_dot WHERE inv_id = {$entregaAnterior['ent_inv_id']}";
                $inventario = InventarioDot::fetchFirst($sqlStock);
                
                if ($inventario['inv_cant_disp'] < $diferencia) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Stock insuficiente para el aumento solicitado'
                    ]);
                    exit;
                }
            }

            $sql = "UPDATE jjjc_ent_dot SET 
                    ent_per_id = {$_POST['ent_per_id']},
                    ent_ped_id = {$_POST['ent_ped_id']},
                    ent_inv_id = {$_POST['ent_inv_id']},
                    ent_cant_ent = {$_POST['ent_cant_ent']},
                    ent_usuario_ent = {$_POST['ent_usuario_ent']},
                    ent_observ = '{$_POST['ent_observ']}'
                    WHERE ent_id = {$_POST['ent_id']}";
            
            $resultado = EntregasDot::getDB()->exec($sql);

            if($resultado >= 0){
                // Ajustar stock del inventario
                $sqlAjustar = "UPDATE jjjc_inv_dot SET 
                               inv_cant_disp = inv_cant_disp - ($diferencia)
                               WHERE inv_id = {$entregaAnterior['ent_inv_id']}";
                InventarioDot::getDB()->exec($sqlAjustar);

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Entrega modificada correctamente',
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar la entrega',
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
                'mensaje' => 'ID de la entrega es requerido'
            ]);
            exit;
        }
        
        try {
            // Obtener datos de la entrega para restaurar stock
            $sqlEntrega = "SELECT ent_cant_ent, ent_inv_id, ent_ped_id FROM jjjc_ent_dot WHERE ent_id = $id";
            $entrega = EntregasDot::fetchFirst($sqlEntrega);
            
            if (!$entrega) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Entrega no encontrada'
                ]);
                exit;
            }

            $sql = "UPDATE jjjc_ent_dot SET ent_situacion = 0 WHERE ent_id = $id AND ent_situacion = 1";
            $resultado = EntregasDot::getDB()->exec($sql);
            
            if($resultado > 0){
                // Restaurar stock al inventario
                $sqlRestaurar = "UPDATE jjjc_inv_dot SET 
                                inv_cant_disp = inv_cant_disp + {$entrega['ent_cant_ent']}
                                WHERE inv_id = {$entrega['ent_inv_id']}";
                InventarioDot::getDB()->exec($sqlRestaurar);

                // Revisar estado del pedido
                $sqlTotalEntregado = "SELECT SUM(ent_cant_ent) as total_entregado 
                                     FROM jjjc_ent_dot 
                                     WHERE ent_ped_id = {$entrega['ent_ped_id']} 
                                     AND ent_situacion = 1";
                $totalEntregado = EntregasDot::fetchFirst($sqlTotalEntregado);
                
                $sqlPedido = "SELECT ped_cant_sol FROM jjjc_ped_dot WHERE ped_id = {$entrega['ent_ped_id']}";
                $pedido = PedidosDot::fetchFirst($sqlPedido);
                
                if ($totalEntregado['total_entregado'] < $pedido['ped_cant_sol']) {
                    $sqlActualizarPedido = "UPDATE jjjc_ped_dot SET ped_estado = 'APROBADO' 
                                           WHERE ped_id = {$entrega['ent_ped_id']}";
                    PedidosDot::getDB()->exec($sqlActualizarPedido);
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Entrega eliminada correctamente',
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar la entrega',
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

    public static function obtenerUsuariosAPI()
    {
        getHeadersApi();
        
        try {
            // Usar la tabla de usuarios real
            $sql = "SELECT usuario_id, usuario_nom1, usuario_nom2, usuario_ape1, usuario_ape2 
                    FROM jjjc_usuario 
                    WHERE usuario_situacion = 1
                    ORDER BY usuario_nom1 ASC, usuario_ape1 ASC";
            
            $usuarios = Usuario::fetchArray($sql);
            
            // Formatear nombres completos
            foreach ($usuarios as &$usuario) {
                $usuario['nombre_completo'] = trim($usuario['usuario_nom1'] . ' ' . $usuario['usuario_nom2'] . ' ' . $usuario['usuario_ape1'] . ' ' . $usuario['usuario_ape2']);
            }
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios encontrados',
                'data' => $usuarios
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
}