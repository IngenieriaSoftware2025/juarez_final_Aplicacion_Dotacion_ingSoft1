<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tallas de Dotación</title>
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

        .talla-badge {
            font-size: 0.85em;
            margin-right: 5px;
        }
    </style>
</head>
<body class="gradient-custom-3">
    <div class="container py-5">
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">
                            <i class="bi bi-rulers me-2"></i>Gestión de Tallas de Dotación
                        </h2>
                        
                        <form id="FormTallasDot" method="POST">
                            <input type="hidden" id="talla_id" name="talla_id">
                            
                            <div class="form-outline mb-3">
                                <select id="talla_prenda_id" name="talla_prenda_id" class="form-select" required>
                                    <option value="">Seleccione una prenda</option>
                                </select>
                                <label class="form-label" for="talla_prenda_id">Prenda de Dotación</label>
                                <div class="form-text">Selecciona la prenda para agregarle tallas</div>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="text" id="talla_nombre" name="talla_nombre" class="form-control" maxlength="20" required />
                                <label class="form-label" for="talla_nombre">Talla</label>
                                <div class="form-text">Ej: XS, S, M, L, XL para ropa | 6, 7, 8, 9, 10 para calzado</div>
                            </div>

                            <div class="form-outline mb-4">
                                <textarea id="talla_desc" name="talla_desc" class="form-control" maxlength="100" rows="2"></textarea>
                                <label class="form-label" for="talla_desc">Descripción (Opcional)</label>
                                <div class="form-text">Información adicional sobre la talla</div>
                            </div>

                            <div class="d-flex justify-content-center gap-2">
                                <button type="submit" id="BtnGuardar" class="btn btn-success gradient-custom-4">
                                    <i class="bi bi-save me-1"></i>Guardar Talla
                                </button>
                                <button type="button" id="BtnModificar" class="btn btn-warning d-none">
                                    <i class="bi bi-pencil me-1"></i>Modificar Talla
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
                            <i class="bi bi-list-ul me-2"></i>Tallas Registradas
                        </h3>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <button type="button" id="BtnBuscarTallas" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Buscar Tallas
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Prenda</th>
                                        <th>Talla</th>
                                        <th>Descripción</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="TablaTallas">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/tallasDot/index.js') ?>"></script>
</body>
</html>