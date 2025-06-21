    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Inventario de Dotación</title>
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

            .stock-badge {
                font-size: 0.9em;
            }
            
            .stock-disponible {
                background-color: #28a745;
            }
            
            .stock-bajo {
                background-color: #ffc107;
            }
            
            .stock-agotado {
                background-color: #dc3545;
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
                                <i class="bi bi-boxes me-2"></i>Gestión de Inventario de Dotación
                            </h2>
                            
                            <form id="FormInventarioDot" method="POST">
                                <input type="hidden" id="inv_id" name="inv_id">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-outline mb-3">
                                            <select id="inv_prenda_id" name="inv_prenda_id" class="form-select" required>
                                                <option value="">Seleccione una prenda</option>
                                            </select>
                                            <label class="form-label" for="inv_prenda_id">Prenda de Dotación</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-outline mb-3">
                                            <select id="inv_talla_id" name="inv_talla_id" class="form-select" required>
                                                <option value="">Primero seleccione prenda</option>
                                            </select>
                                            <label class="form-label" for="inv_talla_id">Talla</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-outline mb-3">
                                            <input type="number" id="inv_cant_total" name="inv_cant_total" class="form-control" min="1" required />
                                            <label class="form-label" for="inv_cant_total">Cantidad Total</label>
                                            <div class="form-text">Cantidad total ingresada</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-outline mb-3">
                                            <input type="number" id="inv_cant_disp" name="inv_cant_disp" class="form-control" min="0" />
                                            <label class="form-label" for="inv_cant_disp">Cantidad Disponible</label>
                                            <div class="form-text">Cantidad disponible actual</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-outline mb-3">
                                            <input type="text" id="inv_lote" name="inv_lote" class="form-control" maxlength="50" />
                                            <label class="form-label" for="inv_lote">Lote</label>
                                            <div class="form-text">Número de lote (opcional)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-outline mb-4">
                                    <textarea id="inv_observ" name="inv_observ" class="form-control" maxlength="250" rows="2"></textarea>
                                    <label class="form-label" for="inv_observ">Observaciones (Opcional)</label>
                                    <div class="form-text">Notas adicionales sobre el inventario</div>
                                </div>

                                <div class="d-flex justify-content-center gap-2">
                                    <button type="submit" id="BtnGuardar" class="btn btn-success gradient-custom-4">
                                        <i class="bi bi-save me-1"></i>Guardar Inventario
                                    </button>
                                    <button type="button" id="BtnModificar" class="btn btn-warning d-none">
                                        <i class="bi bi-pencil me-1"></i>Modificar Inventario
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
                                <i class="bi bi-list-ul me-2"></i>Inventario Registrado
                            </h3>
                            
                            <div class="d-flex justify-content-center mb-3">
                                <button type="button" id="BtnBuscarInventario" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Buscar Inventario
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No.</th>
                                            <th>Prenda</th>
                                            <th>Talla</th>
                                            <th>Cantidad Total</th>
                                            <th>Disponible</th>
                                            <th>Lote</th>
                                            <th>Fecha Ingreso</th>
                                            <th>Observaciones</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="TablaInventario">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="<?= asset('build/js/inventarioDot/index.js') ?>"></script>
    </body>
    </html>