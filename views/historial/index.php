<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body bg-gradient" style="background: linear-gradient(90deg, #f8fafc 60%, #e3f2fd 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">¡Panel de Administración!</h5>
                        <h3 class="fw-bold text-primary mb-0">HISTORIAL DE ACTIVIDADES</h3>
                    </div>
                    <div class="p-4 bg-white rounded-3 shadow-sm border">
                        <div class="row g-4 mb-3">
                            <div class="col-md-3">
                                <label for="filtro_usuario" class="form-label">Usuario</label>
                                <select class="form-select form-select-lg" id="filtro_usuario">
                                    <option value="">Todos los usuarios</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro_aplicacion" class="form-label">Aplicación</label>
                                <select class="form-select form-select-lg" id="filtro_aplicacion">
                                    <option value="">Todas las aplicaciones</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro_ruta" class="form-label">Ruta</label>
                                <select class="form-select form-select-lg" id="filtro_ruta">
                                    <option value="">Todas las rutas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-secondary btn-lg w-100" id="BtnLimpiarFiltros">
                                    <i class="bi bi-eraser me-1"></i>Limpiar Filtros
                                </button>
                            </div>
                        </div>

                        <div class="row g-4 mb-3">
                            <div class="col-md-4">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control form-control-lg" id="fecha_inicio">
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control form-control-lg" id="fecha_fin">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-lg w-100" id="BtnBuscarActividades">
                                    <i class="bi bi-search me-1"></i>Buscar Historial
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mt-5" id="seccionTabla" style="display: none;">
        <div class="col-12 d-flex justify-content-center">
            <div class="card shadow-lg border-primary rounded-4" style="width: 98%;">
                <div class="card-body">
                    <h3 class="text-center text-primary mb-4">Historial de Actividades del Sistema</h3>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden w-100" id="TableHistorialActividades" style="width: 100% !important;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Usuario</th>
                                    <th>Aplicación</th>
                                    <th>Ruta</th>
                                    <th>Descripción</th>
                                    <th>Ejecución</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/historial/index.js') ?>"></script>