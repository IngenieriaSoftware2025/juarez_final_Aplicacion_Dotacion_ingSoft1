<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;

class EstadisticasDotController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        hasPermission(['ADMIN']);
        HistorialActividadesController::registrarActividad('/estadisticas', 'Acceso al módulo de estadísticas de dotación', 1);
        $router->render('estadisticas/index', []);
    }

    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/estadisticas/buscar', 'Consulta de estadísticas de dotación', 1);
            
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

            HistorialActividadesController::registrarActividad('/estadisticas/buscar', 'Estadísticas generadas exitosamente - Total dotaciones: ' . $totalDotaciones . ' - Stock disponible: ' . $totalStock, 1, ['totales_generados' => ['total_dotaciones' => $totalDotaciones, 'total_stock' => $totalStock]]);

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
            HistorialActividadesController::registrarError('/estadisticas/buscar', 'Error al generar estadísticas: ' . $error->getMessage(), 1);
            
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error: ' . $error->getMessage()
            ]);
        }
        exit;
    }

    public static function buscarActividadesAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/estadisticas/actividades', 'Consulta de estadísticas de actividades de usuarios', 1);

            $consultaEstados = "SELECT 
                                 CASE 
                                     WHEN historial_status = 1 THEN 'Exitosas'
                                     ELSE 'Con Errores'
                                 END as estado,
                                 COUNT(*) as cantidad
                               FROM jjjc_historial_act 
                               WHERE historial_situacion = 1
                               GROUP BY historial_status";
            
            $datosEstados = self::fetchArray($consultaEstados);

            $consultaUsuarios = "SELECT 
                                  u.usuario_nom1 || ' ' || u.usuario_ape1 as nombre_usuario,
                                  COUNT(h.historial_id) as total_actividades
                                FROM jjjc_historial_act h
                                INNER JOIN jjjc_usuario u ON h.historial_usuario_id = u.usuario_id
                                WHERE h.historial_situacion = 1
                                GROUP BY u.usuario_id, u.usuario_nom1, u.usuario_ape1
                                ORDER BY total_actividades DESC
                                LIMIT 5";
            
            $datosUsuarios = self::fetchArray($consultaUsuarios);

            $consultaApps = "SELECT 
                              a.app_nombre_largo as aplicacion,
                              COUNT(h.historial_id) as total_actividades
                            FROM jjjc_historial_act h
                            INNER JOIN jjjc_rutas r ON h.historial_ruta = r.ruta_id
                            INNER JOIN jjjc_aplicacion a ON r.ruta_app_id = a.app_id
                            WHERE h.historial_situacion = 1
                            GROUP BY a.app_nombre_largo
                            ORDER BY total_actividades DESC";
            
            $datosApps = self::fetchArray($consultaApps);

            echo json_encode([
                'exito' => true,
                'mensaje' => 'Estadísticas de actividades obtenidas correctamente',
                'estados' => $datosEstados,
                'usuarios' => $datosUsuarios,
                'aplicaciones' => $datosApps,
                'totales_actividades' => [
                    'total_actividades' => array_sum(array_column($datosEstados, 'cantidad')),
                    'total_usuarios_activos' => count($datosUsuarios),
                    'total_apps_usadas' => count($datosApps)
                ]
            ]);
            
        } catch (Exception $error) {
            HistorialActividadesController::registrarError('/estadisticas/actividades', 'Error al obtener estadísticas de actividades: ' . $error->getMessage(), 1);
            
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error: ' . $error->getMessage()
            ]);
        }
        exit;
    }
}