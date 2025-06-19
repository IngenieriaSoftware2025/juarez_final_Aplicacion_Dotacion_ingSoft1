<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema de Dotación Militar</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .tarjeta-bienvenida {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 30px 0;
        }
        
        .tarjeta-modulo {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: block;
        }
        
        .tarjeta-modulo:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-decoration: none;
            color: inherit;
        }
        
        .icono-modulo {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .icono-personal { color: #28a745; }
        .icono-inventario { color: #007bff; }
        .icono-entregas { color: #fd7e14; }
        .icono-estadisticas { color: #6f42c1; }
        .icono-pedidos { color: #dc3545; }
        .icono-configuracion { color: #6c757d; }
        
        .titulo-modulo {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .descripcion-modulo {
            color: #666;
            font-size: 0.9rem;
        }
        
        .btn-logout {
            background: #dc3545;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
        }
        
        .btn-logout:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shield-check me-2"></i>
                Sistema de Dotación Militar
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="tarjeta-bienvenida">
            <div class="row">
                <div class="col-md-8">
                    <h2>¡Bienvenido al Sistema del Comando de Apoyo Logistico</h2>
                    <p class="lead">Con dignidad, respeto y transparencia, defendemos a la Nación</p>
                    <p class="text-muted">
                        Sistema de gestión y control de dotaciones militares, utiliza los módulos disponibles para administrar el inventario, personal y entregas.
                    </p>
                </div>
                
            </div>
        </div>

        <h3 class="mb-4">Módulos Disponibles</h3>
        
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/personalDot" class="tarjeta-modulo">
                    <div class="text-center">
                        <i class="bi bi-people icono-modulo icono-personal"></i>
                        <div class="titulo-modulo">Gestión de Personal</div>
                        <div class="descripcion-modulo">
                            Registrar y administrar el personal militar
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6">
                <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/inventarioDot" class="tarjeta-modulo">
                    <div class="text-center">
                        <i class="bi bi-boxes icono-modulo icono-inventario"></i>
                        <div class="titulo-modulo">Inventario</div>
                        <div class="descripcion-modulo">
                            Control de prendas, tallas y stock disponible
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6">
                <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot" class="tarjeta-modulo">
                    <div class="text-center">
                        <i class="bi bi-clipboard-check icono-modulo icono-pedidos"></i>
                        <div class="titulo-modulo">Pedidos</div>
                        <div class="descripcion-modulo">
                            Gestionar solicitudes de dotación del personal
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6">
                <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot" class="tarjeta-modulo">
                    <div class="text-center">
                        <i class="bi bi-truck icono-modulo icono-entregas"></i>
                        <div class="titulo-modulo">Entregas</div>
                        <div class="descripcion-modulo">
                            Registrar y controlar entregas de dotación
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6">
                <a href="/juarez_final_Aplicacion_Dotacion_ingSoft1/estadisticas" class="tarjeta-modulo">
                    <div class="text-center">
                        <i class="bi bi-graph-up icono-modulo icono-estadisticas"></i>
                        <div class="titulo-modulo">Estadísticas</div>
                        <div class="descripcion-modulo">
                            Reportes y gráficos del sistema de dotación
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
<script src="<?= asset('build/js/inicio.js') ?>"></script>
</body>
</html>