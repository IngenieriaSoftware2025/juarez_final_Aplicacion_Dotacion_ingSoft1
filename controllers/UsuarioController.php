<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Usuario;

class UsuarioController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        hasPermission(['ADMIN']);
        HistorialActividadesController::registrarActividad('/usuario', 'Acceso al módulo de usuarios del sistema', 1);
        $router->render('usuario/index', []);
    }

    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
    
        HistorialActividadesController::registrarActividad('/usuario/guardar', 'Intento de guardar nuevo usuario del sistema', 1, ['datos_enviados' => array_merge($_POST, ['archivo_subido' => isset($_FILES['usuario_fotografia']) ? $_FILES['usuario_fotografia']['name'] : 'No'])]);
        
        $_POST['usuario_nom1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom1']))));
        
        $cantidad_nombre = strlen($_POST['usuario_nom1']);
        
        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Primer nombre debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['usuario_nom2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom2']))));
        
        $cantidad_nombre = strlen($_POST['usuario_nom2']);
        
        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Segundo nombre debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['usuario_ape1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape1']))));
        $cantidad_apellido = strlen($_POST['usuario_ape1']);
        
        if ($cantidad_apellido < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Primer apellido debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['usuario_ape2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape2']))));
        $cantidad_apellido = strlen($_POST['usuario_ape2']);
        
        if ($cantidad_apellido < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Segundo apellido debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['usuario_tel'] = filter_var($_POST['usuario_tel'], FILTER_SANITIZE_NUMBER_INT);
        if (strlen($_POST['usuario_tel']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El telefono debe de tener 8 numeros'
            ]);
            exit;
        }
        
        $_POST['usuario_direc'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_direc']))));
        
        $_POST['usuario_dpi'] = filter_var($_POST['usuario_dpi'], FILTER_VALIDATE_INT);
        if (strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos del DPI debe de ser igual a 13'
            ]);
            exit;
        }
        
        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico no es valido'
            ]);
            exit;
        }

        $usuarioExistente = Usuario::fetchFirst("SELECT * FROM jjjc_usuario WHERE usuario_correo = '{$_POST['usuario_correo']}'");
        if ($usuarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico ya está registrado'
            ]);
            exit;
        }

        $dpiExistente = Usuario::fetchFirst("SELECT * FROM jjjc_usuario WHERE usuario_dpi = '{$_POST['usuario_dpi']}'");
        if ($dpiExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI ya está registrado'
            ]);
            exit;
        }
        
        if (strlen($_POST['usuario_contra']) < 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe tener al menos 8 caracteres'
            ]);
            exit;
        }
        
        if ($_POST['usuario_contra'] !== $_POST['confirmar_contra']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Las contraseñas no coinciden'
            ]);
            exit;
        }
        
        $_POST['usuario_token'] = uniqid();
        $_POST['usuario_fecha_creacion'] = '';
        $_POST['usuario_fecha_contra'] = '';
        
        $file = $_FILES['usuario_fotografia'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowed = ['jpg', 'jpeg', 'png'];
        
        if (!in_array($fileExtension, $allowed)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 2,
                'mensaje' => 'Solo puede cargar archivos JPG, PNG o JPEG',
            ]);
            exit;
        }
        
        if ($fileSize >= 2000000) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 2,
                'mensaje' => 'La imagen debe pesar menos de 2MB',
            ]);
            exit;
        }
        
        if ($fileError === 0) {
            $dpiCompleto = $_POST['usuario_dpi'];
            $ruta = "storage/fotosUsuarios/$dpiCompleto.$fileExtension";
            $subido = move_uploaded_file($file['tmp_name'], __DIR__ . "/../../" . $ruta);
            
            if ($subido) {
                try {
                    $_POST['usuario_contra'] = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
                    $usuario = new Usuario($_POST);
                    $usuario->usuario_fotografia = $ruta;
                    $resultado = $usuario->crear();

                    if($resultado['resultado'] == 1){
                        HistorialActividadesController::registrarActividad('/usuario/guardar', 'Usuario del sistema guardado exitosamente con ID: ' . $resultado['id'] . ' - DPI: ' . $_POST['usuario_dpi'], 1, ['id_generado' => $resultado['id'], 'dpi' => $_POST['usuario_dpi']]);
                        
                        http_response_code(200);
                        echo json_encode([
                            'codigo' => 1,
                            'mensaje' => 'Usuario registrado correctamente',
                        ]);
                    } else {
                        HistorialActividadesController::registrarError('/usuario/guardar', 'Error al registrar usuario del sistema', 1, ['resultado' => $resultado, 'dpi' => $_POST['usuario_dpi']]);
                        
                        http_response_code(500);
                        echo json_encode([
                            'codigo' => 0,
                            'mensaje' => 'Error en registrar al usuario',
                            'datos' => $_POST,
                            'usuario' => $usuario,
                        ]);
                    }
                } catch (Exception $e) {
                    HistorialActividadesController::registrarError('/usuario/guardar', 'Excepción al guardar usuario: ' . $e->getMessage(), 1, ['dpi' => $_POST['usuario_dpi']]);
                    
                    http_response_code(500);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Error: ' . $e->getMessage()
                    ]);
                }
            } else {
                HistorialActividadesController::registrarError('/usuario/guardar', 'Error al subir fotografía del usuario', 1, ['dpi' => $_POST['usuario_dpi'], 'ruta' => $ruta]);
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al subir la fotografía',
                ]);
            }
        } else {
            HistorialActividadesController::registrarError('/usuario/guardar', 'Error en la carga de fotografía - Código: ' . $fileError, 1, ['dpi' => $_POST['usuario_dpi'], 'error_code' => $fileError]);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en la carga de fotografia',
            ]);
        }
        exit;
    }
    
    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        
        try {
            HistorialActividadesController::registrarActividad('/usuario/buscar', 'Búsqueda de usuarios del sistema', 1);
            
            $sql = "SELECT usuario_id, usuario_nom1, usuario_nom2, usuario_ape1, usuario_ape2, 
                           usuario_tel, usuario_direc, usuario_dpi, usuario_correo, usuario_token,
                           usuario_fecha_creacion, usuario_fecha_contra, usuario_fotografia, usuario_situacion 
                    FROM jjjc_usuario 
                    WHERE usuario_situacion = 1";
            
            $usuarios = Usuario::fetchArray($sql);
            
            if (!empty($usuarios)) {
                foreach ($usuarios as &$u) {
                    if (!empty($u['usuario_fecha_creacion'])) {
                        $u['usuario_fecha_creacion'] = date('d/m/Y', strtotime($u['usuario_fecha_creacion']));
                    }
                    if (!empty($u['usuario_fecha_contra'])) {
                        $u['usuario_fecha_contra'] = date('d/m/Y', strtotime($u['usuario_fecha_contra']));
                    }
                }
            }
            
            if (!empty($usuarios)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuarios encontrados: ' . count($usuarios),
                    'data' => $usuarios
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron usuarios',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            HistorialActividadesController::registrarError('/usuario/buscar', 'Error al buscar usuarios: ' . $e->getMessage(), 1);
            
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar usuarios: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    public static function mostrarImagen()
    {
        hasPermissionApi(['ADMIN']);
        $dpi = $_GET['dpi'] ?? null;
        
        HistorialActividadesController::registrarActividad('/usuario/imagen', 'Consulta de imagen de usuario por DPI: ' . $dpi, 1, ['dpi_consultado' => $dpi]);
        
        if (!$dpi) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'DPI no proporcionado'
            ]);
            exit;
        }
        
        $directorio = __DIR__ . "/../../storage/fotosUsuarios/";
        $extensiones = ['jpg', 'jpeg', 'png'];
        
        foreach ($extensiones as $ext) {
            $rutaArchivo = $directorio . $dpi . '.' . $ext;
            
            if (file_exists($rutaArchivo)) {
                HistorialActividadesController::registrarActividad('/usuario/imagen', 'Imagen de usuario encontrada y servida - DPI: ' . $dpi, 1, ['archivo_encontrado' => $dpi . '.' . $ext]);
                
                switch($ext) {
                    case 'jpg':
                    case 'jpeg':
                        header('Content-Type: image/jpeg');
                        break;
                    case 'png':
                        header('Content-Type: image/png');
                        break;
                }
                
                header('Cache-Control: public, max-age=3600');
                header('Content-Length: ' . filesize($rutaArchivo));
                
                readfile($rutaArchivo);
                exit;
            }
        }
        
        HistorialActividadesController::registrarError('/usuario/imagen', 'Imagen de usuario no encontrada - DPI: ' . $dpi, 1, ['dpi_buscado' => $dpi]);
        
        http_response_code(404);
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'Imagen no encontrada'
        ]);
        exit;
    }
}