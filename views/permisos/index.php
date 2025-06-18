<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos</title>
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
        <!-- FORMULARIO PARA PERMISOS -->
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Gestión de Permisos</h2>
                        
                        <form id="FormPermisos" method="POST">
                            <input type="hidden" id="permiso_id" name="permiso_id">
                            
                            <div class="form-outline mb-3">
                                <select id="permiso_app_id" name="permiso_app_id" class="form-select" required>
                                    <option value="">Seleccione una aplicación</option>
                                </select>
                                <label class="form-label" for="permiso_app_id">Aplicación</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="text" id="permiso_nombre" name="permiso_nombre" class="form-control" maxlength="150" required />
                                <label class="form-label" for="permiso_nombre">Nombre del Permiso</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="text" id="permiso_clave" name="permiso_clave" class="form-control" maxlength="250" required />
                                <label class="form-label" for="permiso_clave">Clave del Permiso</label>
                                <div class="form-text">Se convertirá automáticamente a mayúsculas</div>
                            </div>

                            <div class="form-outline mb-4">
                                <textarea id="permiso_desc" name="permiso_desc" class="form-control" maxlength="250" rows="3" required></textarea>
                                <label class="form-label" for="permiso_desc">Descripción</label>
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

        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Permisos Registrados</h3>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <button type="button" id="BtnBuscarPermisos" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Buscar Permisos
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Aplicación</th>
                                        <th>Nombre</th>
                                        <th>Clave</th>
                                        <th>Descripción</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="TablaPermisos">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/permisos/index.js') ?>"></script>
</body>
</html>