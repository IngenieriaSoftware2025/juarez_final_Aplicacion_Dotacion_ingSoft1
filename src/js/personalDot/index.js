import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const formularioPersonal = document.getElementById('FormPersonalDot');
const botonGuardar = document.getElementById('BtnGuardar');
const botonModificar = document.getElementById('BtnModificar');
const botonLimpiar = document.getElementById('BtnLimpiar');
const botonBuscarPersonal = document.getElementById('BtnBuscarPersonal');
const tablaPersonal = document.getElementById('TablaPersonal');

// Validar teléfono 
document.getElementById('per_tel').addEventListener('blur', function() {
    const telefono = this.value.trim();
    
    if (telefono.length > 0 && telefono.length !== 8) {
        Swal.fire({
            icon: 'error',
            title: 'Teléfono Inválido',
            text: 'El teléfono debe tener exactamente 8 dígitos',
            showConfirmButton: false,
            timer: 3000
        });
    }
});

// Validar 
document.getElementById('per_dpi').addEventListener('blur', function() {
    const dpi = this.value.trim();
    
    if (dpi.length > 0 && dpi.length !== 13) {
        Swal.fire({
            icon: 'error',
            title: 'DPI Inválido',
            text: 'El DPI debe tener exactamente 13 dígitos',
            showConfirmButton: false,
            timer: 3000
        });
    }
});

const guardarPersonal = async (evento) => {
    evento.preventDefault();
    botonGuardar.disabled = true;

    const datosFormulario = new FormData(formularioPersonal);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/personalDot/guardar';
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

            formularioPersonal.reset();
            BuscarPersonal();
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

const BuscarPersonal = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/personalDot/buscar';
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

            tablaPersonal.innerHTML = '';
            
            data.forEach((personal, index) => {
                const fila = document.createElement('tr');
                
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${personal.per_nom1} ${personal.per_nom2}</td>
                    <td>${personal.per_ape1} ${personal.per_ape2}</td>
                    <td>${personal.per_dpi}</td>
                    <td>${personal.per_tel}</td>
                    <td>${personal.per_puesto}</td>
                    <td>${personal.per_area}</td>
                    <td>${personal.per_fecha_ing || 'N/A'}</td>
                    <td>
                        <button class='btn btn-warning btn-sm modificar' 
                            data-id="${personal.per_id}" 
                            data-nom1="${personal.per_nom1}"  
                            data-nom2="${personal.per_nom2}"  
                            data-ape1="${personal.per_ape1}"
                            data-ape2="${personal.per_ape2}"
                            data-dpi="${personal.per_dpi}"
                            data-tel="${personal.per_tel}"
                            data-direc="${personal.per_direc}"
                            data-correo="${personal.per_correo}"
                            data-puesto="${personal.per_puesto}"
                            data-area="${personal.per_area}">
                            Modificar
                        </button>
                        <button class='btn btn-danger btn-sm eliminar' 
                            data-id="${personal.per_id}">
                            Eliminar
                        </button>
                    </td>
                `;
                
                tablaPersonal.appendChild(fila);
            });

            document.querySelectorAll('.modificar').forEach(boton => {
                boton.addEventListener('click', TraerDatos);
            });

            document.querySelectorAll('.eliminar').forEach(boton => {
                boton.addEventListener('click', EliminarPersonal);
            });

        } else {
            tablaPersonal.innerHTML = `<tr><td colspan="9" class="text-center">${mensaje}</td></tr>`;
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "No se pudo cargar la lista de personal",
            showConfirmButton: true,
        });
    }
};

const TraerDatos = (evento) => {
    const boton = evento.currentTarget;
    const datos = boton.dataset;

    document.getElementById('per_nom1').value = datos.nom1;
    document.getElementById('per_nom2').value = datos.nom2;
    document.getElementById('per_ape1').value = datos.ape1;
    document.getElementById('per_ape2').value = datos.ape2;
    document.getElementById('per_dpi').value = datos.dpi;
    document.getElementById('per_tel').value = datos.tel;
    document.getElementById('per_direc').value = datos.direc;
    document.getElementById('per_correo').value = datos.correo;
    document.getElementById('per_puesto').value = datos.puesto;
    document.getElementById('per_area').value = datos.area;

    botonGuardar.classList.add('d-none');
    botonModificar.classList.remove('d-none');
    botonModificar.dataset.id = datos.id;
};

const ModificarPersonal = async () => {
    botonModificar.disabled = true;

    const datosFormulario = new FormData(formularioPersonal);
    datosFormulario.append('per_id', botonModificar.dataset.id);

    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/personalDot/modificar';
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
            BuscarPersonal();
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

const EliminarPersonal = async (evento) => {
    const boton = evento.currentTarget;
    const id = boton.dataset.id;

    const resultado = await Swal.fire({
        title: '¿Eliminar Personal?',
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
            const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/personalDot/eliminar?id=${id}`;
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

                BuscarPersonal();
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
    formularioPersonal.reset();
    botonGuardar.classList.remove('d-none');
    botonModificar.classList.add('d-none');
};

formularioPersonal.addEventListener('submit', guardarPersonal);
botonLimpiar.addEventListener('click', LimpiarFormulario);
botonModificar.addEventListener('click', ModificarPersonal);
botonBuscarPersonal.addEventListener('click', BuscarPersonal);

BuscarPersonal();