import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const formularioTallas = document.getElementById('FormTallasDot');
const botonGuardar = document.getElementById('BtnGuardar');
const botonModificar = document.getElementById('BtnModificar');
const botonLimpiar = document.getElementById('BtnLimpiar');
const botonBuscarTallas = document.getElementById('BtnBuscarTallas');
const tablaTallas = document.getElementById('TablaTallas');

const selectPrenda = document.getElementById('talla_prenda_id');

// Cargar prendas desde la base de datos
const cargarPrendas = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/tallasDot/prendas';
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

const guardarTalla = async (evento) => {
    evento.preventDefault();
    botonGuardar.disabled = true;

    const datosFormulario = new FormData(formularioTallas);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/tallasDot/guardar';
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

            formularioTallas.reset();
            BuscarTallas();
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

const BuscarTallas = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/tallasDot/buscar';
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

            tablaTallas.innerHTML = '';
            
            data.forEach((talla, index) => {
                const fila = document.createElement('tr');
                
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        <span class="badge bg-primary">
                            ${talla.prenda_nombre || 'N/A'}
                        </span>
                    </td>
                    <td><strong>${talla.talla_nombre}</strong></td>
                    <td>${talla.talla_desc || 'N/A'}</td>
                    <td>
                        <button class='btn btn-warning btn-sm modificar' 
                            data-id="${talla.talla_id}" 
                            data-nombre="${talla.talla_nombre}"  
                            data-desc="${talla.talla_desc || ''}"
                            data-prenda-id="${talla.talla_prenda_id || ''}">
                            Modificar
                        </button>
                        <button class='btn btn-danger btn-sm eliminar' 
                            data-id="${talla.talla_id}">
                            Eliminar
                        </button>
                    </td>
                `;
                
                tablaTallas.appendChild(fila);
            });

            document.querySelectorAll('.modificar').forEach(boton => {
                boton.addEventListener('click', TraerDatos);
            });

            document.querySelectorAll('.eliminar').forEach(boton => {
                boton.addEventListener('click', EliminarTalla);
            });

        } else {
            tablaTallas.innerHTML = `<tr><td colspan="5" class="text-center">${mensaje}</td></tr>`;
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "No se pudo cargar la lista de tallas",
            showConfirmButton: true,
        });
    }
};

const TraerDatos = (evento) => {
    const boton = evento.currentTarget;
    const datos = boton.dataset;

    selectPrenda.value = datos.prendaId;
    document.getElementById('talla_nombre').value = datos.nombre;
    document.getElementById('talla_desc').value = datos.desc;
    
    botonGuardar.classList.add('d-none');
    botonModificar.classList.remove('d-none');
    botonModificar.dataset.id = datos.id;
};

const ModificarTalla = async () => {
    botonModificar.disabled = true;

    const datosFormulario = new FormData(formularioTallas);
    datosFormulario.append('talla_id', botonModificar.dataset.id);

    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/tallasDot/modificar';
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
            BuscarTallas();
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

const EliminarTalla = async (evento) => {
    const boton = evento.currentTarget;
    const id = boton.dataset.id;

    const resultado = await Swal.fire({
        title: '¿Eliminar Talla?',
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
            const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/tallasDot/eliminar?id=${id}`;
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

                BuscarTallas();
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
    formularioTallas.reset();
    botonGuardar.classList.remove('d-none');
    botonModificar.classList.add('d-none');
};

formularioTallas.addEventListener('submit', guardarTalla);
botonLimpiar.addEventListener('click', LimpiarFormulario);
botonModificar.addEventListener('click', ModificarTalla);
botonBuscarTallas.addEventListener('click', BuscarTallas);

// Cargar datos al iniciar
cargarPrendas();
BuscarTallas();