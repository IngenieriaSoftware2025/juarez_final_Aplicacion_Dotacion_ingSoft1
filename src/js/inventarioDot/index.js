import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const formularioInventario = document.getElementById('FormInventarioDot');
const botonGuardar = document.getElementById('BtnGuardar');
const botonModificar = document.getElementById('BtnModificar');
const botonLimpiar = document.getElementById('BtnLimpiar');
const botonBuscarInventario = document.getElementById('BtnBuscarInventario');
const tablaInventario = document.getElementById('TablaInventario');

const selectPrenda = document.getElementById('inv_prenda_id');
const selectTalla = document.getElementById('inv_talla_id');
const campoCantTotal = document.getElementById('inv_cant_total');
const campoCantDisp = document.getElementById('inv_cant_disp');

// Cargar prendas desde la base de datos
const cargarPrendas = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/inventarioDot/prendas';
    const config = {
        method: 'GET'
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectPrenda.innerHTML = '<option value="">Seleccione una prenda</option>';
            
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
    const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/inventarioDot/tallas?prenda_id=${prendaId}`;
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

campoCantTotal.addEventListener('input', function() {
    if (!campoCantDisp.value || campoCantDisp.value == 0) {
        campoCantDisp.value = this.value;
    }
});

campoCantDisp.addEventListener('input', function() {
    validarCantidades();
});

// Función para validar cantidades
const validarCantidades = () => {
    const cantTotal = parseInt(campoCantTotal.value) || 0;
    const cantDisp = parseInt(campoCantDisp.value) || 0;
    
    if (cantDisp > cantTotal) {
        Swal.fire({
            icon: 'warning',
            title: 'Cantidad Incorrecta',
            text: 'La cantidad disponible no puede ser mayor a la cantidad total',
            showConfirmButton: true,
        });
        campoCantDisp.value = cantTotal;
    }
};

const guardarInventario = async (evento) => {
    evento.preventDefault();
    botonGuardar.disabled = true;

    const cantTotal = parseInt(campoCantTotal.value) || 0;
    const cantDisp = parseInt(campoCantDisp.value) || 0;
    
    if (cantDisp > cantTotal) {
        await Swal.fire({
            icon: 'error',
            title: 'Error de Validación',
            text: 'La cantidad disponible no puede ser mayor a la cantidad total',
            showConfirmButton: true,
        });
        botonGuardar.disabled = false;
        return;
    }

    const datosFormulario = new FormData(formularioInventario);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/inventarioDot/guardar';
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

            formularioInventario.reset();
            selectTalla.innerHTML = '<option value="">Primero seleccione prenda</option>';
            selectTalla.disabled = true;
            BuscarInventario();
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

const BuscarInventario = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/inventarioDot/buscar';
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

            tablaInventario.innerHTML = '';
            
            data.forEach((inventario, index) => {
                const fila = document.createElement('tr');
                
                // Determinar clase de stock
                const porcentajeStock = (inventario.inv_cant_disp / inventario.inv_cant_total) * 100;
                let claseStock = 'stock-disponible';
                if (porcentajeStock <= 0) {
                    claseStock = 'stock-agotado';
                } else if (porcentajeStock <= 20) {
                    claseStock = 'stock-bajo';
                }
                
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        <span class="badge bg-primary">
                            ${inventario.prenda_nombre || 'N/A'}
                        </span>
                    </td>
                    <td><strong>${inventario.talla_nombre || 'N/A'}</strong></td>
                    <td class="text-center">${inventario.inv_cant_total}</td>
                    <td class="text-center">
                        <span class="badge ${claseStock} stock-badge">
                            ${inventario.inv_cant_disp}
                        </span>
                    </td>
                    <td>${inventario.inv_lote || 'N/A'}</td>
                    <td>${inventario.inv_fecha_ing || 'N/A'}</td>
                    <td>${inventario.inv_observ || 'N/A'}</td>
                    <td>
                        <button class='btn btn-warning btn-sm modificar' 
                            data-id="${inventario.inv_id}" 
                            data-prenda-id="${inventario.inv_prenda_id}"
                            data-talla-id="${inventario.inv_talla_id}"
                            data-cant-total="${inventario.inv_cant_total}"
                            data-cant-disp="${inventario.inv_cant_disp}"
                            data-lote="${inventario.inv_lote || ''}"
                            data-observ="${inventario.inv_observ || ''}">
                            Modificar
                        </button>
                        <button class='btn btn-danger btn-sm eliminar' 
                            data-id="${inventario.inv_id}">
                            Eliminar
                        </button>
                    </td>
                `;
                
                tablaInventario.appendChild(fila);
            });

            document.querySelectorAll('.modificar').forEach(boton => {
                boton.addEventListener('click', TraerDatos);
            });

            document.querySelectorAll('.eliminar').forEach(boton => {
                boton.addEventListener('click', EliminarInventario);
            });

        } else {
            tablaInventario.innerHTML = `<tr><td colspan="9" class="text-center">${mensaje}</td></tr>`;
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "No se pudo cargar la lista de inventarios",
            showConfirmButton: true,
        });
    }
};

const TraerDatos = (evento) => {
    const boton = evento.currentTarget;
    const datos = boton.dataset;

    selectPrenda.value = datos.prendaId;
    
    // Cargar tallas para esa prenda
    if (datos.prendaId) {
        cargarTallas(datos.prendaId).then(() => {
            selectTalla.value = datos.tallaId;
        });
    }
    
    campoCantTotal.value = datos.cantTotal;
    campoCantDisp.value = datos.cantDisp;
    document.getElementById('inv_lote').value = datos.lote;
    document.getElementById('inv_observ').value = datos.observ;
    
    botonGuardar.classList.add('d-none');
    botonModificar.classList.remove('d-none');
    botonModificar.dataset.id = datos.id;
};

const ModificarInventario = async () => {
    botonModificar.disabled = true;

    // Validar cantidades antes de modificar
    const cantTotal = parseInt(campoCantTotal.value) || 0;
    const cantDisp = parseInt(campoCantDisp.value) || 0;
    
    if (cantDisp > cantTotal) {
        await Swal.fire({
            icon: 'error',
            title: 'Error de Validación',
            text: 'La cantidad disponible no puede ser mayor a la cantidad total',
            showConfirmButton: true,
        });
        botonModificar.disabled = false;
        return;
    }

    const datosFormulario = new FormData(formularioInventario);
    datosFormulario.append('inv_id', botonModificar.dataset.id);

    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/inventarioDot/modificar';
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
            BuscarInventario();
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

const EliminarInventario = async (evento) => {
    const boton = evento.currentTarget;
    const id = boton.dataset.id;

    const resultado = await Swal.fire({
        title: '¿Eliminar Inventario?',
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
            const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/inventarioDot/eliminar?id=${id}`;
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

                BuscarInventario();
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
    formularioInventario.reset();
    selectTalla.innerHTML = '<option value="">Primero seleccione prenda</option>';
    selectTalla.disabled = true;
    
    botonGuardar.classList.remove('d-none');
    botonModificar.classList.add('d-none');
};

formularioInventario.addEventListener('submit', guardarInventario);
botonLimpiar.addEventListener('click', LimpiarFormulario);
botonModificar.addEventListener('click', ModificarInventario);
botonBuscarInventario.addEventListener('click', BuscarInventario);

// Cargar datos al inicializar
cargarPrendas();
BuscarInventario();