<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Aplicaciones</title>
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
        
        .form-outline {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="gradient-custom-3">
    <div class="container py-5">
        <!-- FORMULARIO PARA APLICACIONES -->
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Gestión de Aplicaciones</h2>
                        
                        <form id="FormAplicaciones" method="POST">
                            <input type="hidden" id="app_id" name="app_id">
                            
                            <div class="form-outline mb-3">
                                <input type="text" id="app_nombre_largo" name="app_nombre_largo" class="form-control" maxlength="250" required />
                                <label class="form-label" for="app_nombre_largo">Nombre Largo</label>
                                <div class="form-text">Nombre descriptivo completo de la aplicación</div>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="text" id="app_nombre_mediano" name="app_nombre_mediano" class="form-control" maxlength="150" required />
                                <label class="form-label" for="app_nombre_mediano">Nombre Mediano</label>
                                <div class="form-text">Nombre abreviado de la aplicación</div>
                            </div>

                            <div class="form-outline mb-4">
                                <input type="text" id="app_nombre_corto" name="app_nombre_corto" class="form-control" maxlength="50" required />
                                <label class="form-label" for="app_nombre_corto">Nombre Corto</label>
                                <div class="form-text">Código o siglas (se convertirá a mayúsculas)</div>
                            </div>

                            <div class="d-flex justify-content-center gap-2">
                                <button type="submit" id="BtnGuardar" class="btn btn-success gradient-custom-4">
                                    <i class="bi bi-save me-1"></i>Guardar
                                </button>
                                <button type="button" id="BtnModificar" class="btn btn-warning d-none">
                                    <i class="bi bi-pencil me-1"></i>Modificar
                                </button>
                                <button type="button" id="BtnLimpiar" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN PARA BUSCAR APLICACIONES -->
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Aplicaciones Registradas</h3>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <button type="button" id="BtnBuscarAplicaciones" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Buscar Aplicaciones
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nombre Largo</th>
                                        <th>Nombre mediano</th>
                                        <th>Nombre Corto</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="TablaAplicaciones">
                                    <!-- Las aplicaciones se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/aplicaciones/index.js') ?>"></script>
</body>
</html>