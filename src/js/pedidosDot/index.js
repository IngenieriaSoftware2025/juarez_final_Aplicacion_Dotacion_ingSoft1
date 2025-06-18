import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const formularioPedidos = document.getElementById('FormPedidosDot');
const botonGuardar = document.getElementById('BtnGuardar');
const botonModificar = document.getElementById('BtnModificar');
const botonLimpiar = document.getElementById('BtnLimpiar');
const botonBuscarPedidos = document.getElementById('BtnBuscarPedidos');
const tablaPedidos = document.getElementById('TablaPedidos');

const selectPersonal = document.getElementById('ped_per_id');
const selectPrenda = document.getElementById('ped_prenda_id');
const selectTalla = document.getElementById('ped_talla_id');

// Cargar personal desde la base de datos
const cargarPersonal = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot/personal';
    const config = {
        method: 'GET'
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectPersonal.innerHTML = '<option value="">Seleccione personal</option>';
            
            data.forEach(persona => {
                const option = document.createElement('option');
                option.value = persona.per_id;
                option.textContent = `${persona.nombre_completo} - ${persona.per_puesto}`;
                selectPersonal.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar personal:', error);
    }
};

// Cargar prendas desde la base de datos
const cargarPrendas = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot/prendas';
    const config = {
        method: 'GET'
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectPrenda.innerHTML = '<option value="">Seleccione prenda</option>';
            
            data.forEach(prenda => {
                const option = document.createElement('option');
                option.value = prenda.prenda_id;
                option.textContent = prenda.prenda_nombre;
                selectPrenda.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar prendas:', error);
    }
};

// Cargar tallas según la prenda seleccionada
const cargarTallas = async (prendaId) => {
    const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot/tallas?prenda_id=${prendaId}`;
    const config = {
        method: 'GET'
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectTalla.innerHTML = '<option value="">Seleccione talla</option>';
            selectTalla.disabled = false;
            
            data.forEach(talla => {
                const option = document.createElement('option');
                option.value = talla.talla_id;
                option.textContent = talla.talla_nombre;
                selectTalla.appendChild(option);
            });
        } else {
            selectTalla.innerHTML = '<option value="">No hay tallas disponibles</option>';
            selectTalla.disabled = true;
        }
    } catch (error) {
        console.error('Error al cargar tallas:', error);
        selectTalla.innerHTML = '<option value="">Error al cargar tallas</option>';
        selectTalla.disabled = true;
    }
};

selectPrenda.addEventListener('change', function() {
    const prendaId = this.value;
    
    selectTalla.innerHTML = '<option value="">Seleccione talla</option>';
    selectTalla.disabled = true;
    
    if (prendaId) {
        cargarTallas(prendaId);
    }
});

const guardarPedido = async (evento) => {
    evento.preventDefault();
    botonGuardar.disabled = true;

    const datosFormulario = new FormData(formularioPedidos);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot/guardar';
    const config = {
        method: 'POST',
        body: datosFormulario
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            formularioPedidos.reset();
            selectTalla.innerHTML = '<option value="">Primero seleccione prenda</option>';
            selectTalla.disabled = true;
            BuscarPedidos();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
    
    botonGuardar.disabled = false;
};

const BuscarPedidos = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot/buscar';
    const config = {
        method: 'POST'
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            tablaPedidos.innerHTML = '';
            
            data.forEach((pedido, index) => {
                const fila = document.createElement('tr');
                
                // Determinar clase de estado
                let claseEstado = 'estado-pendiente';
                switch (pedido.ped_estado) {
                    case 'APROBADO':
                        claseEstado = 'estado-aprobado';
                        break;
                    case 'RECHAZADO':
                        claseEstado = 'estado-rechazado';
                        break;
                    case 'ENTREGADO':
                        claseEstado = 'estado-entregado';
                        break;
                    default:
                        claseEstado = 'estado-pendiente';
                }
                
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        <span class="badge bg-info">
                            ${pedido.nombre_completo || 'N/A'}
                        </span>
                    </td>
                    <td>${pedido.per_puesto || 'N/A'}</td>
                    <td>
                        <span class="badge bg-primary">
                            ${pedido.prenda_nombre || 'N/A'}
                        </span>
                    </td>
                    <td><strong>${pedido.talla_nombre || 'N/A'}</strong></td>
                    <td class="text-center">${pedido.ped_cant_sol}</td>
                    <td class="text-center">
                        <span class="badge ${claseEstado}">
                            ${pedido.ped_estado}
                        </span>
                    </td>
                    <td>${pedido.ped_fecha_sol || 'N/A'}</td>
                    <td>${pedido.ped_observ || 'N/A'}</td>
                    <td>
                        <button class='btn btn-warning btn-sm modificar' 
                            data-id="${pedido.ped_id}" 
                            data-per-id="${pedido.ped_per_id}"
                            data-prenda-id="${pedido.ped_prenda_id}"
                            data-talla-id="${pedido.ped_talla_id}"
                            data-cant-sol="${pedido.ped_cant_sol}"
                            data-estado="${pedido.ped_estado}"
                            data-observ="${pedido.ped_observ || ''}">
                            Modificar
                        </button>
                        <button class='btn btn-danger btn-sm eliminar' 
                            data-id="${pedido.ped_id}">
                            Eliminar
                        </button>
                    </td>
                `;
                
                tablaPedidos.appendChild(fila);
            });

            document.querySelectorAll('.modificar').forEach(boton => {
                boton.addEventListener('click', TraerDatos);
            });

            document.querySelectorAll('.eliminar').forEach(boton => {
                boton.addEventListener('click', EliminarPedido);
            });

        } else {
            tablaPedidos.innerHTML = `<tr><td colspan="10" class="text-center">${mensaje}</td></tr>`;
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "No se pudo cargar la lista de pedidos",
            showConfirmButton: true,
        });
    }
};

const TraerDatos = (evento) => {
    const boton = evento.currentTarget;
    const datos = boton.dataset;

    selectPersonal.value = datos.perId;
    selectPrenda.value = datos.prendaId;
    
    // Cargar tallas para la prenda seleccionada
    if (datos.prendaId) {
        cargarTallas(datos.prendaId).then(() => {
            selectTalla.value = datos.tallaId;
        });
    }
    
    document.getElementById('ped_cant_sol').value = datos.cantSol;
    document.getElementById('ped_estado').value = datos.estado;
    document.getElementById('ped_observ').value = datos.observ;
    
    botonGuardar.classList.add('d-none');
    botonModificar.classList.remove('d-none');
    botonModificar.dataset.id = datos.id;
};

const ModificarPedido = async () => {
    botonModificar.disabled = true;

    const datosFormulario = new FormData(formularioPedidos);
    datosFormulario.append('ped_id', botonModificar.dataset.id);

    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot/modificar';
    const config = {
        method: 'POST',
        body: datosFormulario
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            LimpiarFormulario();
            BuscarPedidos();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
    
    botonModificar.disabled = false;
};

const EliminarPedido = async (evento) => {
    const boton = evento.currentTarget;
    const id = boton.dataset.id;

    const resultado = await Swal.fire({
        title: '¿Eliminar Pedido?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (resultado.isConfirmed) {
        try {
            const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/pedidosDot/eliminar?id=${id}`;
            const respuesta = await fetch(url);
            const datos = await respuesta.json();
            const { codigo, mensaje } = datos;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarPedidos();
            } else {
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }
        } catch (error) {
            console.error('Error:', error);
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: "No se pudo conectar con el servidor",
                showConfirmButton: true,
            });
        }
    }
};

const LimpiarFormulario = () => {
    formularioPedidos.reset();
    selectTalla.innerHTML = '<option value="">Primero seleccione prenda</option>';
    selectTalla.disabled = true;
    
    botonGuardar.classList.remove('d-none');
    botonModificar.classList.add('d-none');
};

formularioPedidos.addEventListener('submit', guardarPedido);
botonLimpiar.addEventListener('click', LimpiarFormulario);
botonModificar.addEventListener('click', ModificarPedido);
botonBuscarPedidos.addEventListener('click', BuscarPedidos);

// Cargar datos al inicializar
cargarPersonal();
cargarPrendas();
BuscarPedidos();