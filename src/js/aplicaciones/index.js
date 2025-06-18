import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const FormAplicaciones = document.getElementById('FormAplicaciones');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscarAplicaciones = document.getElementById('BtnBuscarAplicaciones');
const TablaAplicaciones = document.getElementById('TablaAplicaciones');

const GuardarAplicacion = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    const body = new FormData(FormAplicaciones);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/aplicaciones/guardar';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarAplicaciones();

        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        // Error handling sin console.log
    }
    BtnGuardar.disabled = false;
}

const BuscarAplicaciones = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/aplicaciones/buscar';
    const config = {
        method: 'POST'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            // Limpiar tabla
            TablaAplicaciones.innerHTML = '';
            
            // Agregar cada aplicación a la tabla
            data.forEach((aplicacion, index) => {
                const fila = document.createElement('tr');
                
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${aplicacion.app_nombre_largo}</td>
                    <td>${aplicacion.app_nombre_mediano}</td>
                    <td><span class="badge bg-info">${aplicacion.app_nombre_corto}</span></td>
                    <td>${aplicacion.app_fecha_creacion}</td>
                    <td>
                        <div class='d-flex justify-content-center gap-1'>
                            <button class='btn btn-warning btn-sm modificar' 
                                data-id="${aplicacion.app_id}" 
                                data-largo="${aplicacion.app_nombre_largo}"  
                                data-medium="${aplicacion.app_nombre_mediano}"  
                                data-corto="${aplicacion.app_nombre_corto}">
                                <i class='bi bi-pencil-square'></i>
                            </button>
                            <button class='btn btn-danger btn-sm eliminar' 
                                data-id="${aplicacion.app_id}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                `;
                
                TablaAplicaciones.appendChild(fila);
            });

            // Agregar event listeners a los botones
            document.querySelectorAll('.modificar').forEach(btn => {
                btn.addEventListener('click', llenarFormulario);
            });
            
            document.querySelectorAll('.eliminar').forEach(btn => {
                btn.addEventListener('click', EliminarAplicacion);
            });

        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Info",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontraro aplicaciones'
                });
    }
}

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('app_id').value = datos.id
    document.getElementById('app_nombre_largo').value = datos.largo
    document.getElementById('app_nombre_mediano').value = datos.medium
    document.getElementById('app_nombre_corto').value = datos.corto

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    });
}

const limpiarTodo = () => {
    FormAplicaciones.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}

const ModificarAplicacion = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    const body = new FormData(FormAplicaciones);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/aplicaciones/modificar';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarAplicaciones();

        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo llenar el formulario'
                });
    }
    BtnModificar.disabled = false;
}

const EliminarAplicacion = async (e) => {
    const idAplicacion = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: 'Esta completamente seguro que desea eliminar esta aplicación',
        showConfirmButton: true,
        confirmButtonText: 'Si, Eliminar',
        confirmButtonColor: 'red',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/aplicaciones/eliminar?id=${idAplicacion}`;
        const config = {
            method: 'GET'
        }

        try {
            const consulta = await fetch(url, config);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarAplicaciones();
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
            Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar'
                    });
        }
    }
}

// Event Listeners
FormAplicaciones.addEventListener('submit', GuardarAplicacion);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarAplicacion);
BtnBuscarAplicaciones.addEventListener('click', BuscarAplicaciones);

// Buscar aplicaciones al inicio
BuscarAplicaciones();