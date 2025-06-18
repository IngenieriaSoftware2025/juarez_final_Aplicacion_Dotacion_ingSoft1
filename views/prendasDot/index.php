<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Prendas de Dotación</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .gradiente-personalizado-3 {
            background: #84fab0;
            background: -webkit-linear-gradient(to right, rgba(132, 250, 176, 0.5), rgba(143, 211, 244, 0.5));
            background: linear-gradient(to right, rgba(132, 250, 176, 0.5), rgba(143, 211, 244, 0.5));
        }
        
        .gradiente-personalizado-4 {
            background: #84fab0;
            background: -webkit-linear-gradient(to right, rgba(132, 250, 176, 1), rgba(143, 211, 244, 1));
            background: linear-gradient(to right, rgba(132, 250, 176, 1), rgba(143, 211, 244, 1));
        }
        
        .contorno-formulario {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="gradiente-personalizado-3">
    <div class="container py-5">
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">
                            <i class="bi bi-bag me-2"></i>Gestión de Prendas de Dotación
                        </h2>
                        
                        <form id="FormPrendasDot" method="POST">
                            <input type="hidden" id="prenda_id" name="prenda_id">
                            
                            <div class="contorno-formulario mb-3">
                                <input type="text" id="prenda_nombre" name="prenda_nombre" class="form-control" maxlength="100" required />
                                <label class="form-label" for="prenda_nombre">Nombre de la Prenda</label>
                                <div class="form-text">Ejemplo: Botas Militares, Pantalón de Campaña, etc.</div>
                            </div>

                            <div class="contorno-formulario mb-4">
                                <textarea id="prenda_desc" name="prenda_desc" class="form-control" maxlength="250" rows="3" required></textarea>
                                <label class="form-label" for="prenda_desc">Descripción</label>
                                <div class="form-text">Descripción detallada de la prenda</div>
                            </div>

                            <div class="d-flex justify-content-center gap-2">
                                <button type="submit" id="BtnGuardar" class="btn btn-success gradiente-personalizado-4">
                                    <i class="bi bi-save me-1"></i>Guardar Prenda
                                </button>
                                <button type="button" id="BtnModificar" class="btn btn-warning d-none">
                                    <i class="bi bi-pencil me-1"></i>Modificar Prenda
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
                        <h3 class="text-center mb-4">
                            <i class="bi bi-list-ul me-2"></i>Prendas Registradas
                        </h3>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <button type="button" id="BtnBuscarPrendas" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Buscar Prendas
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nombre de Prenda</th>
                                        <th>Descripción</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="TablaPrendas">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/prendasDot/index.js') ?>"></script>
</body>
</html>