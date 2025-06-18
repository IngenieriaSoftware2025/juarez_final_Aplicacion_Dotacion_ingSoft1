<?php

namespace Controllers;

use MVC\Router;

class AppController 
{
    public static function index(Router $router)
    {
        if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
            header('Location: /juarez_final_Aplicacion_Dotacion_ingSoft1/inicio');
        } else {
            header('Location: /juarez_final_Aplicacion_Dotacion_ingSoft1/login');
        }
        exit;
    }

   public static function inicio(Router $router)
    {
        // Verificar autenticación
        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /juarez_final_Aplicacion_Dotacion_ingSoft1/login');
            exit;
        }

        // Mostrar la página de inicio
        $router->render('pages/index', []);
    }
}
