<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Dotación y Actividades</title>
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

        .tarjeta-estadistica {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .tarjeta-estadistica:hover {
            transform: translateY(-5px);
        }

        .numero-estadistica {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .nav-pills-custom .nav-link {
            border-radius: 25px;
            margin: 0 0.5rem;
            font-weight: 500;
        }

        .nav-pills-custom .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="gradient-custom-3">
    <div class="container py-5">
        
        <div class="text-center mb-5">
            <h1 class="text-primary">
                <i class="bi bi-graph-up me-3"></i>Panel de Estadísticas
            </h1>
            <h2 class="text-secondary">Estadísticas de Dotación y Actividades del Sistema</h2>
        </div>

        <ul class="nav nav-pills nav-pills-custom justify-content-center mb-4" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-dotaciones-tab" data-bs-toggle="pill" data-bs-target="#pills-dotaciones" type="button" role="tab">
                    <i class="bi bi-box-seam me-2"></i>Dotaciones
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-actividades-tab" data-bs-toggle="pill" data-bs-target="#pills-actividades" type="button" role="tab">
                    <i class="bi bi-activity me-2"></i>Actividades de Usuarios
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            
            <div class="tab-pane fade show active" id="pills-dotaciones" role="tabpanel">
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

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="numero-estadistica" id="total-dotaciones-entregadas">0</div>
                            <h6><i class="bi bi-box-arrow-right me-2"></i>Total Dotaciones</h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="numero-estadistica" id="total-stock-disponible">0</div>
                            <h6><i class="bi bi-boxes me-2"></i>Stock Disponible</h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="numero-estadistica" id="total-tipos-prendas">0</div>
                            <h6><i class="bi bi-tags me-2"></i>Tipos de Prendas</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="pills-actividades" role="tabpanel">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="numero-estadistica" id="total-actividades-sistema">0</div>
                            <h6><i class="bi bi-activity me-2"></i>Total Actividades</h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="numero-estadistica" id="total-usuarios-activos">0</div>
                            <h6><i class="bi bi-person-check me-2"></i>Usuarios Activos</h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="numero-estadistica" id="total-apps-usadas">0</div>
                            <h6><i class="bi bi-app me-2"></i>Aplicaciones Usadas</h6>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-md-12 mb-4">
                        <div class="card" style="border-radius: 15px;">
                            <div class="card-body p-4">
                                <h5 class="text-center mb-4 text-info">
                                    <i class="bi bi-pie-chart-fill me-2"></i>Estados de Actividades
                                </h5>
                                <div class="canvas-contenedor">
                                    <canvas id="graficoEstados"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12 mb-4">
                        <div class="card" style="border-radius: 15px;">
                            <div class="card-body p-4">
                                <h5 class="text-center mb-4 text-warning">
                                    <i class="bi bi-bar-chart-fill me-2"></i>Top 5 Usuarios
                                </h5>
                                <div class="canvas-contenedor">
                                    <canvas id="graficoUsuarios"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12 mb-4">
                        <div class="card" style="border-radius: 15px;">
                            <div class="card-body p-4">
                                <h5 class="text-center mb-4 text-success">
                                    <i class="bi bi-diagram-3-fill me-2"></i>Por Aplicación
                                </h5>
                                <div class="canvas-contenedor">
                                    <canvas id="graficoApps"></canvas>
                                </div>
                            </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/estadisticas/index.js') ?>"></script>
</body>
</html>