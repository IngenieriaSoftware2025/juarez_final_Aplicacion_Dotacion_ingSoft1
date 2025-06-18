<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;

class EstadisticasDotController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('estadisticas/index', []);
    }

    public static function buscarAPI()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        try {
            $consultaDotaciones = "SELECT 
                                    pr.prenda_nombre as nombre_prenda, 
                                    SUM(e.ent_cant_ent) as total_entregado
                                  FROM jjjc_ent_dot e
                                  INNER JOIN jjjc_inv_dot i ON e.ent_inv_id = i.inv_id
                                  INNER JOIN jjjc_dot_img pr ON i.inv_prenda_id = pr.prenda_id
                                  WHERE e.ent_situacion = 1
                                  GROUP BY pr.prenda_nombre
                                  ORDER BY total_entregado DESC";
            
            $datosDotaciones = self::fetchArray($consultaDotaciones);

            $consultaTallas = "SELECT 
                                t.talla_nombre as nombre_talla,
                                SUM(i.inv_cant_disp) as cantidad_disponible
                              FROM jjjc_inv_dot i
                              INNER JOIN jjjc_tallas_dot t ON i.inv_talla_id = t.talla_id
                              WHERE i.inv_situacion = 1 AND i.inv_cant_disp > 0
                              GROUP BY t.talla_nombre
                              ORDER BY cantidad_disponible DESC";
            
            $datosTallas = self::fetchArray($consultaTallas);

            $dotacionesParaGrafico = [];
            $totalDotaciones = 0;
            
            foreach ($datosDotaciones as $dotacion) {
                $dotacionesParaGrafico[] = [
                    'nombre' => $dotacion['nombre_prenda'],
                    'cantidad' => (int)$dotacion['total_entregado']
                ];
                $totalDotaciones += (int)$dotacion['total_entregado'];
            }

            $tallasParaGrafico = [];
            $totalStock = 0;
            
            foreach ($datosTallas as $talla) {
                $tallasParaGrafico[] = [
                    'nombre' => 'Talla ' . $talla['nombre_talla'],
                    'cantidad' => (int)$talla['cantidad_disponible']
                ];
                $totalStock += (int)$talla['cantidad_disponible'];
            }

            echo json_encode([
                'exito' => true,
                'mensaje' => 'Datos cargados correctamente',
                'dotaciones' => $dotacionesParaGrafico,
                'tallas' => $tallasParaGrafico,
                'totales' => [
                    'total_dotaciones' => $totalDotaciones,
                    'total_stock' => $totalStock,
                    'tipos_prendas' => count($dotacionesParaGrafico)
                ]
            ]);
            
        } catch (Exception $error) {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error: ' . $error->getMessage()
            ]);
        }
        exit;
    }
}