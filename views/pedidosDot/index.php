<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos de Dotación</title>
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

        .estado-pendiente {
            background-color: #ffc107;
        }
        
        .estado-aprobado {
            background-color: #28a745;
        }
        
        .estado-rechazado {
            background-color: #dc3545;
        }
        
        .estado-entregado {
            background-color: #6c757d;
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
                            <i class="bi bi-clipboard-check me-2"></i>Gestión de Pedidos de Dotación
                        </h2>
                        
                        <form id="FormPedidosDot" method="POST">
                            <input type="hidden" id="ped_id" name="ped_id">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-outline mb-3">
                                        <select id="ped_per_id" name="ped_per_id" class="form-select" required>
                                            <option value="">Seleccione personal</option>
                                        </select>
                                        <label class="form-label" for="ped_per_id">Personal Solicitante</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-outline mb-3">
                                        <select id="ped_prenda_id" name="ped_prenda_id" class="form-select" required>
                                            <option value="">Seleccione prenda</option>
                                        </select>
                                        <label class="form-label" for="ped_prenda_id">Prenda Solicitada</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-outline mb-3">
                                        <select id="ped_talla_id" name="ped_talla_id" class="form-select" required>
                                            <option value="">Primero seleccione prenda</option>
                                        </select>
                                        <label class="form-label" for="ped_talla_id">Talla</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-outline mb-3">
                                        <input type="number" id="ped_cant_sol" name="ped_cant_sol" class="form-control" min="1" required />
                                        <label class="form-label" for="ped_cant_sol">Cantidad Solicitada</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-outline mb-3">
                                        <select id="ped_estado" name="ped_estado" class="form-control">
                                            <option value="PENDIENTE">PENDIENTE</option>
                                            <option value="APROBADO">APROBADO</option>
                                            <option value="RECHAZADO">RECHAZADO</option>
                                            <option value="ENTREGADO">ENTREGADO</option>
                                        </select>
                                        <label class="form-label" for="ped_estado">Estado del Pedido</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-outline mb-4">
                                <textarea id="ped_observ" name="ped_observ" class="form-control" maxlength="250" rows="2"></textarea>
                                <label class="form-label" for="ped_observ">Observaciones (Opcional)</label>
                                <div class="form-text">Motivo o detalles del pedido</div>
                            </div>

                            <div class="d-flex justify-content-center gap-2">
                                <button type="submit" id="BtnGuardar" class="btn btn-success gradient-custom-4">
                                    <i class="bi bi-save me-1"></i>Guardar Pedido
                                </button>
                                <button type="button" id="BtnModificar" class="btn btn-warning d-none">
                                    <i class="bi bi-pencil me-1"></i>Modificar Pedido
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
                            <i class="bi bi-list-ul me-2"></i>Pedidos Registrados
                        </h3>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <button type="button" id="BtnBuscarPedidos" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Buscar Pedidos
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Personal</th>
                                        <th>Puesto</th>
                                        <th>Prenda</th>
                                        <th>Talla</th>
                                        <th>Cantidad</th>
                                        <th>Estado</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="TablaPedidos">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/pedidosDot/index.js') ?>"></script>
</body>
</html>