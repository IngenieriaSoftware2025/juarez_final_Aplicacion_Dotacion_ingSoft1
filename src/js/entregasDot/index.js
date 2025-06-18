import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const form = document.getElementById('FormEntregasDot');
const btnGuardar = document.getElementById('BtnGuardar');
const btnModificar = document.getElementById('BtnModificar');
const btnLimpiar = document.getElementById('BtnLimpiar');
const btnBuscar = document.getElementById('BtnBuscarEntregas');
const tabla = document.getElementById('TablaEntregas');

const selPersonal = document.getElementById('ent_per_id');
const selPedido = document.getElementById('ent_ped_id');
const selInventario = document.getElementById('ent_inv_id');
const selUsuario = document.getElementById('ent_usuario_ent');
const inputCantidad = document.getElementById('ent_cant_ent');
const inputObserv = document.getElementById('ent_observ');

let datos = { personal: [], pedidos: [], inventario: [], usuarios: [] };

// Funciones de utilidad
const mostrarAlerta = (icono, titulo, texto, timer = null) => 
    Swal.fire({ icon: icono, title: titulo, text: texto, showConfirmButton: !timer, timer });

const crearOpcion = (valor, texto) => {
    const opt = document.createElement('option');
    opt.value = valor;
    opt.textContent = texto;
    return opt;
};

const realizarFetch = async (url, config = { method: 'GET' }) => {
    try {
        const resp = await fetch(url, config);
        return await resp.json();
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

// Cargar datos base
const cargarPersonal = async () => {
    const { codigo, data } = await realizarFetch('/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/personal');
    if (codigo == 1) {
        datos.personal = data;
        selPersonal.innerHTML = '<option value="">Seleccione personal</option>';
        data.forEach(p => selPersonal.appendChild(crearOpcion(p.per_id, `${p.nombre_completo} - ${p.per_puesto}`)));
    }
};

const cargarUsuarios = async () => {
    const { codigo, data } = await realizarFetch('/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/usuarios');
    if (codigo == 1) {
        datos.usuarios = data;
        selUsuario.innerHTML = '<option value="">Seleccione usuario</option>';
        data.forEach(u => selUsuario.appendChild(crearOpcion(u.usuario_id, u.nombre_completo)));
    }
};

// Verificar control anual
const verificarControlAnual = async (personalId) => {
    const { codigo, mensaje, data } = await realizarFetch(`/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/controlAnual?personal_id=${personalId}`);
    
    if (codigo == 1) {
        const icono = data.estado === 'limite_alcanzado' ? 'error' : 
                     data.estado === 'ultima_disponible' ? 'warning' : 
                     data.estado === 'nuevo' ? 'success' : 'info';
        
        const color = data.estado === 'limite_alcanzado' ? '#dc3545' : 
                      data.estado === 'ultima_disponible' ? '#ffc107' : 
                      data.estado === 'nuevo' ? '#28a745' : '#17a2b8';

        await Swal.fire({
            icon: icono,
            title: 'Control Anual de Dotaciones',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Personal:</strong> ${data.nombre_personal || 'N/A'}</p>
                    <p><strong>Año:</strong> ${data.anio}</p>
                    <div style="background: ${color}; color: white; padding: 10px; border-radius: 5px; margin: 10px 0;">
                        <p style="margin: 0;"><strong>Dotaciones:</strong> ${data.dotaciones_usadas}/3</p>
                        <p style="margin: 0;"><strong>Restantes:</strong> ${data.dotaciones_restantes}</p>
                    </div>
                    <p><strong>Última Entrega:</strong> ${data.fecha_ultima_entrega}</p>
                </div>
            `,
            allowOutsideClick: data.puede_recibir
        });
        return data.puede_recibir;
    }
    mostrarAlerta('error', 'Error', mensaje);
    return false;
};

// Cargar pedidos
const cargarPedidos = async (personalId) => {
    const { codigo, data } = await realizarFetch(`/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/pedidos?personal_id=${personalId}`);
    
    if (codigo == 1) {
        datos.pedidos = data;
        selPedido.innerHTML = '<option value="">Seleccione pedido</option>';
        selPedido.disabled = false;
        
        data.forEach(p => {
            const opt = crearOpcion(p.ped_id, `${p.prenda_nombre} - Talla ${p.talla_nombre} (Sol: ${p.ped_cant_sol})`);
            opt.dataset.prendaId = p.ped_prenda_id;
            opt.dataset.tallaId = p.ped_talla_id;
            opt.dataset.cantidadSolicitada = p.ped_cant_sol;
            selPedido.appendChild(opt);
        });
        
        mostrarAlerta('info', 'Pedidos Disponibles', `Se encontraron ${data.length} pedidos aprobados`, 2000);
    } else {
        selPedido.innerHTML = '<option value="">No hay pedidos aprobados</option>';
        selPedido.disabled = true;
        mostrarAlerta('warning', 'Sin Pedidos', 'Este personal no tiene pedidos aprobados');
    }
};

// Cargar inventario
const cargarInventario = async (prendaId, tallaId) => {
    const { codigo, data, mensaje } = await realizarFetch(`/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/inventario?prenda_id=${prendaId}&talla_id=${tallaId}`);
    
    if (codigo == 1) {
        datos.inventario = data;
        selInventario.innerHTML = '<option value="">Seleccione lote</option>';
        selInventario.disabled = false;
        
        let stockTotal = 0;
        data.forEach(inv => {
            stockTotal += parseInt(inv.inv_cant_disp);
            const opt = crearOpcion(inv.inv_id, `Lote: ${inv.inv_lote || 'Sin lote'} - Disp: ${inv.inv_cant_disp}`);
            opt.dataset.disponible = inv.inv_cant_disp;
            selInventario.appendChild(opt);
        });
        
        if (stockTotal > 0) {
            mostrarAlerta('success', 'Stock Disponible', `${stockTotal} unidades de ${data[0].prenda_nombre} talla ${data[0].talla_nombre}`, 3000);
        }
    } else {
        selInventario.innerHTML = '<option value="">Sin stock disponible</option>';
        selInventario.disabled = true;
        mostrarAlerta('error', 'Sin Stock', mensaje);
    }
};

// Eventos principales
selPersonal.addEventListener('change', async function() {
    const personalId = this.value;
    selPedido.innerHTML = '<option value="">Seleccione pedido</option>';
    selPedido.disabled = true;
    selInventario.innerHTML = '<option value="">Seleccione inventario</option>';
    selInventario.disabled = true;
    
    if (personalId) {
        const puedeRecibir = await verificarControlAnual(personalId);
        if (puedeRecibir) {
            cargarPedidos(personalId);
        } else {
            selPedido.innerHTML = '<option value="">Límite anual alcanzado</option>';
            selPedido.disabled = true;
        }
    }
});

selPedido.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    selInventario.innerHTML = '<option value="">Seleccione inventario</option>';
    selInventario.disabled = true;
    
    if (opt && opt.value) {
        const prendaId = opt.dataset.prendaId;
        const tallaId = opt.dataset.tallaId;
        if (prendaId && tallaId) cargarInventario(prendaId, tallaId);
    }
});

inputCantidad.addEventListener('input', function() {
    const opt = selInventario.options[selInventario.selectedIndex];
    if (opt && opt.dataset.disponible) {
        const max = parseInt(opt.dataset.disponible);
        const val = parseInt(this.value) || 0;
        if (val > max) {
            mostrarAlerta('warning', 'Cantidad Excesiva', `Máximo disponible: ${max}`);
            this.value = max;
        }
    }
});

// Guardar entrega
const guardar = async (e) => {
    e.preventDefault();
    btnGuardar.disabled = true;

    try {
        const { codigo, mensaje } = await realizarFetch('/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/guardar', {
            method: 'POST',
            body: new FormData(form)
        });

        if (codigo == 1) {
            await mostrarAlerta('success', '¡Entrega Exitosa!', mensaje);
            limpiar();
            buscar();
        } else {
            mostrarAlerta('error', 'Error', mensaje);
        }
    } catch {
        mostrarAlerta('error', 'Error de Conexión', 'No se pudo conectar con el servidor');
    }
    
    btnGuardar.disabled = false;
};

// Buscar entregas
const buscar = async () => {
    try {
        const { codigo, mensaje, data } = await realizarFetch('/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/buscar', { method: 'POST' });

        if (codigo == 1) {
            mostrarAlerta('success', 'Búsqueda Completada', mensaje);
            tabla.innerHTML = '';
            
            data.forEach((ent, i) => {
                const colorBadge = (ent.dotaciones_usadas_anio >= 3) ? 'bg-danger' : 
                                 (ent.dotaciones_usadas_anio === 2) ? 'bg-warning' : 'bg-success';
                
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${i + 1}</td>
                    <td>
                        <span class="badge bg-info">${ent.nombre_personal || 'N/A'}</span><br>
                        <small class="text-muted">${ent.per_puesto || ''}</small><br>
                        <span class="badge ${colorBadge} small">Dotaciones ${ent.anio_entrega}: ${ent.dotaciones_usadas_anio}/3</span>
                    </td>
                    <td>
                        <span class="badge bg-primary">${ent.prenda_nombre || 'N/A'}</span><br>
                        <small><strong>Talla: ${ent.talla_nombre || 'N/A'}</strong></small>
                    </td>
                    <td class="text-center"><span class="badge bg-success fs-6">${ent.ent_cant_ent}</span></td>
                    <td>${ent.ent_fecha_ent || 'N/A'}</td>
                    <td><small class="text-muted">${ent.nombre_usuario || 'N/A'}</small></td>
                    <td><small>${ent.ent_observ || 'Sin observaciones'}</small></td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class='btn btn-warning btn-sm modificar' 
                                data-id="${ent.ent_id}" data-per-id="${ent.ent_per_id}" data-ped-id="${ent.ent_ped_id}"
                                data-inv-id="${ent.ent_inv_id}" data-cant-ent="${ent.ent_cant_ent}" 
                                data-usuario-ent="${ent.ent_usuario_ent}" data-observ="${ent.ent_observ || ''}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class='btn btn-danger btn-sm eliminar' data-id="${ent.ent_id}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                `;
                tabla.appendChild(fila);
            });

            // Eventos de botones
            document.querySelectorAll('.modificar').forEach(btn => 
                btn.addEventListener('click', llenarForm));
            document.querySelectorAll('.eliminar').forEach(btn => 
                btn.addEventListener('click', eliminar));
        } else {
            tabla.innerHTML = `<tr><td colspan="8" class="text-center">${mensaje}</td></tr>`;
        }
    } catch {
        mostrarAlerta('error', 'Error de Búsqueda', 'No se pudo cargar la lista');
    }
};

// Llenar formulario para modificar
const llenarForm = (e) => {
    const d = e.currentTarget.dataset;
    selPersonal.value = d.perId;
    selPedido.value = d.pedId;
    selInventario.value = d.invId;
    inputCantidad.value = d.cantEnt;
    selUsuario.value = d.usuarioEnt;
    inputObserv.value = d.observ;
    
    btnGuardar.classList.add('d-none');
    btnModificar.classList.remove('d-none');
    btnModificar.dataset.id = d.id;
};

// Modificar entrega
const modificar = async () => {
    btnModificar.disabled = true;
    const formData = new FormData(form);
    formData.append('ent_id', btnModificar.dataset.id);

    try {
        const { codigo, mensaje } = await realizarFetch('/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/modificar', {
            method: 'POST',
            body: formData
        });

        if (codigo == 1) {
            await mostrarAlerta('success', 'Modificación Exitosa', mensaje);
            limpiar();
            buscar();
        } else {
            mostrarAlerta('error', 'Error', mensaje);
        }
    } catch {
        mostrarAlerta('error', 'Error de Conexión', 'No se pudo conectar');
    }
    
    btnModificar.disabled = false;
};

// Eliminar entrega
const eliminar = async (e) => {
    const id = e.currentTarget.dataset.id;
    const confirm = await Swal.fire({
        title: '¿Eliminar Entrega?',
        html: `
            <div style="text-align: left;">
                <p>Esta acción:</p>
                <ul>
                    <li>Restaurará el stock al inventario</li>
                    <li><strong>Reducirá el contador anual de dotaciones</strong></li>
                </ul>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (confirm.isConfirmed) {
        try {
            const { codigo, mensaje } = await realizarFetch(`/juarez_final_Aplicacion_Dotacion_ingSoft1/entregasDot/eliminar?id=${id}`);
            
            if (codigo == 1) {
                await mostrarAlerta('success', 'Eliminación Exitosa', mensaje);
                buscar();
            } else {
                mostrarAlerta('error', 'Error', mensaje);
            }
        } catch {
            mostrarAlerta('error', 'Error de Conexión', 'No se pudo conectar');
        }
    }
};

// Limpiar formulario
const limpiar = () => {
    form.reset();
    selPedido.innerHTML = '<option value="">Primero seleccione personal</option>';
    selPedido.disabled = true;
    selInventario.innerHTML = '<option value="">Primero seleccione pedido</option>';
    selInventario.disabled = true;
    btnGuardar.classList.remove('d-none');
    btnModificar.classList.add('d-none');
};

form.addEventListener('submit', guardar);
btnLimpiar.addEventListener('click', limpiar);
btnModificar.addEventListener('click', modificar);
btnBuscar.addEventListener('click', buscar);

cargarPersonal();
cargarUsuarios();
buscar();