<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Dotación</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .gradient-custom-3 {
            background: #84fab0;
            background: -webkit-linear-gradient(to right, rgba(132, 250, 176, 0.5), rgba(143, 211, 244, 0.5));
            background: linear-gradient(to right, rgba(132, 250, 176, 0.5), rgba(143, 211, 244, 0.5));
        }
        
        .gradient-custom-4 {
            background: #84fab0;
            background: -webkit-linear-gradient(to right, rgba(132, 250, 176, 1), rgba(143, 211, 244, 1));
            background: linear-gradient(to right, rgba(132, 250, 176, 1), rgba(143, 211, 244, 1));
        }
        
        .canvas-contenedor {
            position: relative;
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body class="gradient-custom-3">
    <div class="container py-5">
        
        <!-- HEADER -->
        <div class="text-center mb-5">
            <h1 class="text-primary">
                <i class="bi bi-graph-up me-3"></i>Estadísticas de Dotación
            </h1>
            <h2 class="text-secondary">Subteniente de Infantería</h2>
            <h3 class="text-secondary">José de Jesús Juárez Castellanos</h3>
            <p class="lead text-muted">Dashboard de control de dotaciones militares</p>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4 text-primary">
                            <i class="bi bi-bar-chart me-2"></i>Dotaciones Entregadas por Prenda
                        </h4>
                        <div class="canvas-contenedor">
                            <canvas id="grafico1"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4 text-success">
                            <i class="bi bi-pie-chart me-2"></i>Tallas Disponibles en Inventario
                        </h4>
                        <div class="canvas-contenedor">
                            <canvas id="grafico2"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <button type="button" id="BtnActualizarEstadisticas" class="btn btn-primary gradient-custom-4">
                                <i class="bi bi-arrow-clockwise me-2"></i>Actualizar Estadísticas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display: none;">
        <div id="total-dotaciones-entregadas">0</div>
        <div id="total-personal-beneficiado">0</div>
        <div id="total-stock-disponible">0</div>
        <div id="total-tipos-prendas">0</div>
        <div id="anio-actual">2025</div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/estadisticas/index.js') ?>"></script>
</body>
</html>