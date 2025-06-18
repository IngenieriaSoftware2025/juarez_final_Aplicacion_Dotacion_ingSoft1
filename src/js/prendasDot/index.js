import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const formularioPrendas = document.getElementById('FormPrendasDot');
const botonGuardar = document.getElementById('BtnGuardar');
const botonModificar = document.getElementById('BtnModificar');
const botonLimpiar = document.getElementById('BtnLimpiar');
const botonBuscarPrendas = document.getElementById('BtnBuscarPrendas');
const tablaPrendas = document.getElementById('TablaPrendas');

const guardarPrenda = async (evento) => {
    evento.preventDefault();
    botonGuardar.disabled = true;

    const datosFormulario = new FormData(formularioPrendas);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/prendasDot/guardar';
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

            formularioPrendas.reset();
            BuscarPrendas();
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

const BuscarPrendas = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/prendasDot/buscar';
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

            tablaPrendas.innerHTML = '';
            
            data.forEach((prenda, index) => {
                const fila = document.createElement('tr');
                
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${prenda.prenda_nombre}</td>
                    <td>${prenda.prenda_desc}</td>
                    <td>${prenda.prenda_fecha_crea || 'N/A'}</td>
                    <td>
                        <button class='btn btn-warning btn-sm modificar' 
                            data-id="${prenda.prenda_id}" 
                            data-nombre="${prenda.prenda_nombre}"  
                            data-desc="${prenda.prenda_desc}">
                            Modificar
                        </button>
                        <button class='btn btn-danger btn-sm eliminar' 
                            data-id="${prenda.prenda_id}">
                            Eliminar
                        </button>
                    </td>
                `;
                
                tablaPrendas.appendChild(fila);
            });

            document.querySelectorAll('.modificar').forEach(boton => {
                boton.addEventListener('click', TraerDatos);
            });

            document.querySelectorAll('.eliminar').forEach(boton => {
                boton.addEventListener('click', EliminarPrenda);
            });

        } else {
            tablaPrendas.innerHTML = `<tr><td colspan="5" class="text-center">${mensaje}</td></tr>`;
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "No se pudo cargar la lista de prendas",
            showConfirmButton: true,
        });
    }
};

const TraerDatos = (evento) => {
    const boton = evento.currentTarget;
    const datos = boton.dataset;

    document.getElementById('prenda_nombre').value = datos.nombre;
    document.getElementById('prenda_desc').value = datos.desc;

    botonGuardar.classList.add('d-none');
    botonModificar.classList.remove('d-none');
    botonModificar.dataset.id = datos.id;
};

const ModificarPrenda = async () => {
    botonModificar.disabled = true;

    const datosFormulario = new FormData(formularioPrendas);
    datosFormulario.append('prenda_id', botonModificar.dataset.id);

    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/prendasDot/modificar';
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
            BuscarPrendas();
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

const EliminarPrenda = async (evento) => {
    const boton = evento.currentTarget;
    const id = boton.dataset.id;

    const resultado = await Swal.fire({
        title: '¿Eliminar Prenda?',
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
            const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/prendasDot/eliminar?id=${id}`;
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

                BuscarPrendas();
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
    formularioPrendas.reset();
    botonGuardar.classList.remove('d-none');
    botonModificar.classList.add('d-none');
};

formularioPrendas.addEventListener('submit', guardarPrenda);
botonLimpiar.addEventListener('click', LimpiarFormulario);
botonModificar.addEventListener('click', ModificarPrenda);
botonBuscarPrendas.addEventListener('click', BuscarPrendas);

BuscarPrendas();