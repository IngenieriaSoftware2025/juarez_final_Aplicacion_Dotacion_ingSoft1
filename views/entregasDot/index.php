<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Entregas de Dotación</title>
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
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-10 col-lg-10">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">
                            <i class="bi bi-box-arrow-down me-2"></i>Gestión de Entregas de Dotación
                        </h2>
                        
                        <form id="FormEntregasDot" method="POST">
                            <input type="hidden" id="ent_id" name="ent_id">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-outline mb-3">
                                        <select id="ent_per_id" name="ent_per_id" class="form-select" required>
                                            <option value="">Seleccione personal</option>
                                        </select>
                                        <label class="form-label" for="ent_per_id">Personal Receptor</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-outline mb-3">
                                        <select id="ent_usuario_ent" name="ent_usuario_ent" class="form-select" required>
                                            <option value="">Seleccione personal entregador</option>
                                        </select>
                                        <label class="form-label" for="ent_usuario_ent">Personal que Entrega</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-outline mb-3">
                                        <select id="ent_ped_id" name="ent_ped_id" class="form-select" disabled required>
                                            <option value="">Primero seleccione personal</option>
                                        </select>
                                        <label class="form-label" for="ent_ped_id">Pedido Aprobado</label>
                                        <div class="form-text">Solo se muestran pedidos aprobados</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-outline mb-3">
                                        <select id="ent_inv_id" name="ent_inv_id" class="form-select" disabled required>
                                            <option value="">Primero seleccione pedido</option>
                                        </select>
                                        <label class="form-label" for="ent_inv_id">Lote de Inventario</label>
                                        <div class="form-text">Muestra stock disponible por lote</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-outline mb-3">
                                        <input type="number" id="ent_cant_ent" name="ent_cant_ent" class="form-control" min="1" required />
                                        <label class="form-label" for="ent_cant_ent">Cantidad a Entregar</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="form-outline mb-3">
                                        <textarea id="ent_observ" name="ent_observ" class="form-control" maxlength="250" rows="2"></textarea>
                                        <label class="form-label" for="ent_observ">Observaciones (Opcional)</label>
                                        <div class="form-text">Notas adicionales sobre la entrega</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center gap-2">
                                <button type="submit" id="BtnGuardar" class="btn btn-success gradient-custom-4">
                                    <i class="bi bi-check-circle me-1"></i>Registrar Entrega
                                </button>
                                <button type="button" id="BtnModificar" class="btn btn-warning d-none">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar Entrega
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
                            <i class="bi bi-table me-2"></i>Entregas Registradas
                        </h3>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <button type="button" id="BtnBuscarEntregas" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Buscar Entregas
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Personal</th>
                                        <th>Artículo</th>
                                        <th>Cantidad</th>
                                        <th>Fecha Entrega</th>
                                        <th>Entregado por</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="TablaEntregas">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/entregasDot/index.js') ?>"></script>
</body>
</html>