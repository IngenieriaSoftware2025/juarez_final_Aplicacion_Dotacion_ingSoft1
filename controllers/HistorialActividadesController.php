<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\HistorialActividades;
use Model\Rutas;

class HistorialActividadesController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        hasPermission(['ADMIN']);
        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /juarez_final_Aplicacion_Dotacion_ingSoft1/login');
            exit;
        }
        
        self::registrarActividad(
            '/historial',
            'Acceso al módulo de historial de actividades',
            1
        );
        
        $router->render('historial/index', []);
    }

    public static function registrarActividad($nombreRuta, $descripcionEjecucion, $aplicacionId = 1, $datosExtras = [])
    {
        try {
            if(isset($_SESSION['user_id'])) {
                
                $rutaId = self::obtenerOCrearRuta($nombreRuta, $descripcionEjecucion, $aplicacionId);
                
                if($rutaId) {
                    $infoContexto = [
                        'direccion_ip' => $_SERVER['REMOTE_ADDR'] ?? 'No disponible',
                        'agente_usuario' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'No disponible', 0, 200),
                        'metodo_solicitud' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                        'hora_servidor' => date('Y-m-d H:i:s')
                    ];
                    
                    if (!empty($datosExtras)) {
                        $infoContexto['datos_adicionales'] = $datosExtras;
                    }
                    
                    $descripcionCompleta = $descripcionEjecucion . ' | Info: ' . json_encode($infoContexto, JSON_UNESCAPED_UNICODE);
                    
                    $historial = new HistorialActividades([
                        'historial_usuario_id' => $_SESSION['user_id'],
                        'historial_fecha' => date('Y-m-d H:i'),
                        'historial_ruta' => $rutaId,
                        'historial_ejecucion' => $descripcionCompleta,
                        'historial_status' => 1,
                        'historial_situacion' => 1
                    ]);
                    
                    $resultado = $historial->crear();
                    
                    if($resultado['resultado'] == 0) {
                        error_log("Error al guardar historial: " . print_r($resultado, true));
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error en registrarActividad: " . $e->getMessage());
        }
    }

   
    public static function registrarError($nombreRuta, $mensajeError, $aplicacionId = 1, $detallesError = [])
    {
        try {
            if(isset($_SESSION['user_id'])) {
                
                $rutaId = self::obtenerOCrearRuta($nombreRuta, 'ERROR: ' . $mensajeError, $aplicacionId);
                
                if($rutaId) {
                    $infoError = [
                        'mensaje_error' => $mensajeError,
                        'direccion_ip' => $_SERVER['REMOTE_ADDR'] ?? 'No disponible',
                        'fecha_error' => date('Y-m-d H:i:s'),
                        'detalles' => $detallesError
                    ];
                    
                    $descripcionError = 'ERROR DEL SISTEMA: ' . json_encode($infoError, JSON_UNESCAPED_UNICODE);
                    
                    $historial = new HistorialActividades([
                        'historial_usuario_id' => $_SESSION['user_id'],
                        'historial_fecha' => date('Y-m-d H:i'),
                        'historial_ruta' => $rutaId,
                        'historial_ejecucion' => $descripcionError,
                        'historial_status' => 0, 
                        'historial_situacion' => 1
                    ]);
                    
                    $resultado = $historial->crear();
                }
            }
        } catch (Exception $e) {
            error_log("Error en registrarError: " . $e->getMessage());
        }
    }

    private static function obtenerOCrearRuta($nombreRuta, $descripcionRuta, $aplicacionId)
    {
        try {
            $sql = "SELECT ruta_id FROM jjjc_rutas WHERE ruta_nombre = '{$nombreRuta}' AND ruta_app_id = {$aplicacionId}";
            $resultados = Rutas::fetchArray($sql);
            
            if(!empty($resultados)) {
                return $resultados[0]['ruta_id'];
            } else {
                $nuevaRuta = new Rutas([
                    'ruta_app_id' => $aplicacionId,
                    'ruta_nombre' => $nombreRuta,
                    'ruta_descripcion' => $descripcionRuta,
                    'ruta_situacion' => 1
                ]);
                
                $resultado = $nuevaRuta->crear();
                if($resultado['resultado'] == 1) {
                    return $resultado['id'];
                }
            }
            return null;
        } catch (Exception $e) {
            error_log("Error en obtenerOCrearRuta: " . $e->getMessage());
            return null;
        }
    }

    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN']);
        try {
            $filtrosUsados = array_filter($_GET);
            self::registrarActividad(
                '/historial/buscarAPI',
                'Búsqueda en historial con filtros aplicados',
                1,
                ['filtros' => $filtrosUsados]
            );

            $fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
            $fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
            $usuarioId = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : null;
            $aplicacionId = isset($_GET['aplicacion_id']) ? $_GET['aplicacion_id'] : null;
            $rutaId = isset($_GET['ruta_id']) ? $_GET['ruta_id'] : null;

            $condiciones = ["h.historial_situacion = 1"];

            if ($fechaInicio) {
                $condiciones[] = "h.historial_fecha >= '{$fechaInicio} 00:00'";
            }

            if ($fechaFin) {
                $condiciones[] = "h.historial_fecha <= '{$fechaFin} 23:59'";
            }

            if ($usuarioId) {
                $condiciones[] = "h.historial_usuario_id = {$usuarioId}";
            }

            if ($aplicacionId) {
                $condiciones[] = "r.ruta_app_id = {$aplicacionId}";
            }

            if ($rutaId) {
                $condiciones[] = "h.historial_ruta = {$rutaId}";
            }

            $where = implode(" AND ", $condiciones);
            
            $sql = "SELECT 
                        h.historial_id,
                        h.historial_usuario_id,
                        u.usuario_nom1 || ' ' || u.usuario_ape1 as nombre_usuario,
                        h.historial_fecha,
                        h.historial_ruta,
                        r.ruta_nombre,
                        r.ruta_descripcion,
                        a.app_nombre_largo as aplicacion_nombre,
                        h.historial_ejecucion,
                        h.historial_status,
                        CASE 
                            WHEN h.historial_status = 1 THEN 'EXITOSO'
                            ELSE 'ERROR'
                        END as estado_ejecucion
                    FROM jjjc_historial_act h
                    INNER JOIN jjjc_rutas r ON h.historial_ruta = r.ruta_id
                    INNER JOIN jjjc_aplicacion a ON r.ruta_app_id = a.app_id
                    LEFT JOIN jjjc_usuario u ON h.historial_usuario_id = u.usuario_id
                    WHERE $where 
                    ORDER BY h.historial_fecha DESC, h.historial_id DESC
                    LIMIT 500";
            
            $datos = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Historial obtenido correctamente',
                'data' => $datos,
                'total_encontrados' => count($datos)
            ]);

        } catch (Exception $e) {
            self::registrarError(
                '/historial/buscarAPI',
                'Error al buscar historial: ' . $e->getMessage(),
                1,
                ['parametros_enviados' => $_GET]
            );

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el historial',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarUsuariosAPI()
    {
        hasPermissionApi(['ADMIN']);
        try {
            $sql = "SELECT DISTINCT h.historial_usuario_id, u.usuario_nom1 || ' ' || u.usuario_ape1 as usuario_nombre 
                    FROM jjjc_historial_act h
                    INNER JOIN jjjc_usuario u ON h.historial_usuario_id = u.usuario_id
                    WHERE h.historial_situacion = 1
                    ORDER BY u.usuario_nom1";
            $datos = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios obtenidos correctamente',
                'data' => $datos
            ]);

        } catch (Exception $e) {
            self::registrarError('/historial/buscarUsuariosAPI', 'Error al obtener usuarios: ' . $e->getMessage(), 1);
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Para visualizar el historial debe presionar el boton de busqueda',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarAplicacionesAPI()
    {
        hasPermissionApi(['ADMIN']);
        try {
            $sql = "SELECT DISTINCT a.app_id, a.app_nombre_largo as app_nombre 
                    FROM jjjc_aplicacion a
                    INNER JOIN jjjc_rutas r ON a.app_id = r.ruta_app_id
                    INNER JOIN jjjc_historial_act h ON r.ruta_id = h.historial_ruta
                    WHERE a.app_situacion = 1 AND h.historial_situacion = 1
                    ORDER BY a.app_nombre_largo";
            $datos = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Aplicaciones obtenidas correctamente',
                'data' => $datos
            ]);

        } catch (Exception $e) {
            self::registrarError('/historial/buscarAplicacionesAPI', 'Error al obtener aplicaciones: ' . $e->getMessage(), 1);
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las aplicaciones',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarRutasAPI()
    {
        hasPermissionApi(['ADMIN']);
        try {
            $aplicacionId = isset($_GET['aplicacion_id']) ? $_GET['aplicacion_id'] : null;
            
            $condiciones = ["r.ruta_situacion = 1"];
            
            if ($aplicacionId) {
                $condiciones[] = "r.ruta_app_id = {$aplicacionId}";
            }
            
            $where = implode(" AND ", $condiciones);
            
            $sql = "SELECT DISTINCT r.ruta_id, r.ruta_nombre, r.ruta_descripcion
                    FROM jjjc_rutas r
                    INNER JOIN jjjc_historial_act h ON r.ruta_id = h.historial_ruta
                    WHERE $where AND h.historial_situacion = 1
                    ORDER BY r.ruta_nombre";
            $datos = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Rutas obtenidas correctamente',
                'data' => $datos
            ]);

        } catch (Exception $e) {
            self::registrarError('/historial/buscarRutasAPI', 'Error al obtener rutas: ' . $e->getMessage(), 1);
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las rutas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}