<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\PersonalDot;

class PersonalDotController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        HistorialActividadesController::registrarActividad('/personalDot', 'Acceso al módulo de personal de dotación', 1);
        $router->render('personalDot/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();
    
        HistorialActividadesController::registrarActividad('/personalDot/guardar', 'Intento de guardar nuevo personal de dotación', 1, ['datos_enviados' => $_POST]);
        
        $_POST['per_nom1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_nom1']))));
        
        $cantidad_nombre = strlen($_POST['per_nom1']);
        
        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Primer nombre debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['per_nom2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_nom2']))));
        
        $cantidad_nombre = strlen($_POST['per_nom2']);
        
        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Segundo nombre debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['per_ape1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_ape1']))));
        $cantidad_apellido = strlen($_POST['per_ape1']);
        
        if ($cantidad_apellido < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Primer apellido debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['per_ape2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_ape2']))));
        $cantidad_apellido2 = strlen($_POST['per_ape2']);
        
        if ($cantidad_apellido2 < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Segundo apellido debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['per_dpi'] = trim(htmlspecialchars($_POST['per_dpi']));
        
        if (strlen($_POST['per_dpi']) !== 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI debe tener exactamente 13 digitos'
            ]);
            exit;
        }
        
        if (!is_numeric($_POST['per_dpi'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI debe contener solo numeros'
            ]);
            exit;
        }
        
        $_POST['per_tel'] = trim(htmlspecialchars($_POST['per_tel']));
        
        if (!is_numeric($_POST['per_tel'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El telefono debe contener solo numeros'
            ]);
            exit;
        }
        
        if (strlen($_POST['per_tel']) < 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El telefono debe tener al menos 8 digitos'
            ]);
            exit;
        }
        
        $_POST['per_direc'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_direc']))));
        
        if (strlen($_POST['per_direc']) < 10) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La direccion debe tener al menos 10 caracteres'
            ]);
            exit;
        }
        
        $_POST['per_correo'] = filter_var($_POST['per_correo'], FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($_POST['per_correo'], FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico no es valido'
            ]);
            exit;
        }

        $correoExistente = PersonalDot::fetchFirst("SELECT * FROM jjjc_personal_dot WHERE per_correo = '{$_POST['per_correo']}'");
        if ($correoExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico ya esta registrado'
            ]);
            exit;
        }

        $dpiExistente = PersonalDot::fetchFirst("SELECT * FROM jjjc_personal_dot WHERE per_dpi = '{$_POST['per_dpi']}'");
        if ($dpiExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI ya esta registrado'
            ]);
            exit;
        }
        
        $_POST['per_puesto'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_puesto']))));
        
        if (strlen($_POST['per_puesto']) < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El puesto debe tener al menos 3 caracteres'
            ]);
            exit;
        }
        
        $_POST['per_area'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_area']))));
        
        if (strlen($_POST['per_area']) < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El area debe tener al menos 3 caracteres'
            ]);
            exit;
        }
        
        try {
            $personal = new PersonalDot($_POST);
            $resultado = $personal->crear();

            if($resultado['resultado'] == 1){
                HistorialActividadesController::registrarActividad('/personalDot/guardar', 'Personal de dotación guardado exitosamente con ID: ' . $resultado['id'], 1, ['id_generado' => $resultado['id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Personal registrado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/personalDot/guardar', 'Error al registrar personal de dotación', 1, ['resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar el personal',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/personalDot/guardar', 'Excepción al guardar personal: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
            HistorialActividadesController::registrarActividad('/personalDot/buscar', 'Búsqueda de personal de dotación', 1);
            
            $sql = "SELECT per_id, per_nom1, per_nom2, per_ape1, per_ape2, 
                           per_tel, per_direc, per_dpi, per_correo, per_puesto, per_area,
                           per_fecha_ing, per_situacion 
                    FROM jjjc_personal_dot 
                    WHERE per_situacion = 1";
            
            $personal = PersonalDot::fetchArray($sql);
            
            if (!empty($personal)) {
                foreach ($personal as &$p) {
                    if (!empty($p['per_fecha_ing'])) {
                        $p['per_fecha_ing'] = date('d/m/Y', strtotime($p['per_fecha_ing']));
                    }
                }
            }
            
            if (!empty($personal)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Personal encontrado: ' . count($personal),
                    'data' => $personal
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontro personal',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/personalDot/buscar', 'Error al buscar personal: ' . $e->getMessage(), 1);
            
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
        
        HistorialActividadesController::registrarActividad('/personalDot/modificar', 'Intento de modificar personal de dotación', 1, ['datos_enviados' => $_POST]);
        
        if (empty($_POST['per_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del personal es requerido'
            ]);
            exit;
        }

        $_POST['per_nom1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_nom1']))));
        $_POST['per_nom2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_nom2']))));
        $_POST['per_ape1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_ape1']))));
        $_POST['per_ape2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_ape2']))));
        $_POST['per_direc'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_direc']))));
        $_POST['per_puesto'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_puesto']))));
        $_POST['per_area'] = ucwords(strtolower(trim(htmlspecialchars($_POST['per_area']))));
        $_POST['per_correo'] = filter_var($_POST['per_correo'], FILTER_SANITIZE_EMAIL);
        
        try {
            $sql = "UPDATE jjjc_personal_dot SET 
                    per_nom1 = '{$_POST['per_nom1']}',
                    per_nom2 = '{$_POST['per_nom2']}',
                    per_ape1 = '{$_POST['per_ape1']}',
                    per_ape2 = '{$_POST['per_ape2']}',
                    per_dpi = '{$_POST['per_dpi']}',
                    per_tel = '{$_POST['per_tel']}',
                    per_direc = '{$_POST['per_direc']}',
                    per_correo = '{$_POST['per_correo']}',
                    per_puesto = '{$_POST['per_puesto']}',
                    per_area = '{$_POST['per_area']}'
                    WHERE per_id = {$_POST['per_id']}";
            
            $resultado = PersonalDot::getDB()->exec($sql);

            if($resultado >= 0){
                HistorialActividadesController::registrarActividad('/personalDot/modificar', 'Personal de dotación modificado exitosamente - ID: ' . $_POST['per_id'], 1, ['id_modificado' => $_POST['per_id']]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Personal modificado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/personalDot/modificar', 'Error al modificar personal de dotación', 1, ['id_personal' => $_POST['per_id'], 'resultado' => $resultado]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el personal',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/personalDot/modificar', 'Excepción al modificar personal: ' . $e->getMessage(), 1, ['datos_enviados' => $_POST]);
            
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
        
        HistorialActividadesController::registrarActividad('/personalDot/eliminar', 'Intento de eliminar personal de dotación - ID: ' . $id, 1, ['id_a_eliminar' => $id]);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID del personal es requerido'
            ]);
            exit;
        }
        
        try {
            $sql = "UPDATE jjjc_personal_dot SET per_situacion = 0 WHERE per_id = $id AND per_situacion = 1";
            $resultado = PersonalDot::getDB()->exec($sql);
            
            if($resultado > 0){
                HistorialActividadesController::registrarActividad('/personalDot/eliminar', 'Personal de dotación eliminado exitosamente - ID: ' . $id, 1, ['id_eliminado' => $id]);
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Personal eliminado correctamente',
                ]);
            } else {
                HistorialActividadesController::registrarError('/personalDot/eliminar', 'No se pudo eliminar el personal - ID: ' . $id, 1, ['id_personal' => $id, 'resultado' => $resultado]);
                
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar el personal',
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/personalDot/eliminar', 'Excepción al eliminar personal: ' . $e->getMessage(), 1, ['id_personal' => $id]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}