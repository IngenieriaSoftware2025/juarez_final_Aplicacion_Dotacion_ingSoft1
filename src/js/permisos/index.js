import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const FormPermisos = document.getElementById('FormPermisos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscarPermisos = document.getElementById('BtnBuscarPermisos');
const TablaPermisos = document.getElementById('TablaPermisos');
const selectAplicacion = document.getElementById('permiso_app_id');

const GuardarPermiso = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    const body = new FormData(FormPermisos);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/permisos/guardar';
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
            BuscarPermisos();

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

const BuscarPermisos = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/permisos/buscar';
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
            TablaPermisos.innerHTML = '';
            
            // Agregar cada permiso a la tabla
            data.forEach((permiso, index) => {
                const fila = document.createElement('tr');
                
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${permiso.app_nombre_corto}</td>
                    <td>${permiso.permiso_nombre}</td>
                    <td><span class="badge bg-primary">${permiso.permiso_clave}</span></td>
                    <td>${permiso.permiso_desc}</td>
                    <td>${permiso.permiso_fecha}</td>
                    <td>
                        <div class='d-flex justify-content-center gap-1'>
                            <button class='btn btn-warning btn-sm modificar' 
                                data-id="${permiso.permiso_id}" 
                                data-app="${permiso.permiso_app_id}"  
                                data-nombre="${permiso.permiso_nombre}"  
                                data-clave="${permiso.permiso_clave}"  
                                data-desc="${permiso.permiso_desc}">
                                <i class='bi bi-pencil-square'></i>
                            </button>
                            <button class='btn btn-danger btn-sm eliminar' 
                                data-id="${permiso.permiso_id}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                `;
                
                TablaPermisos.appendChild(fila);
            });

            // Agregar event listeners a los botones
            document.querySelectorAll('.modificar').forEach(btn => {
                btn.addEventListener('click', llenarFormulario);
            });
            
            document.querySelectorAll('.eliminar').forEach(btn => {
                btn.addEventListener('click', EliminarPermiso);
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
        // Error handling sin console.log
    }
}

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('permiso_id').value = datos.id
    document.getElementById('permiso_app_id').value = datos.app
    document.getElementById('permiso_nombre').value = datos.nombre
    document.getElementById('permiso_clave').value = datos.clave
    document.getElementById('permiso_desc').value = datos.desc

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    });
}

const limpiarTodo = () => {
    FormPermisos.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}

const ModificarPermiso = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    const body = new FormData(FormPermisos);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/permisos/modificar';
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
            BuscarPermisos();

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
    BtnModificar.disabled = false;
}

const EliminarPermiso = async (e) => {
    const idPermiso = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: 'Esta completamente seguro que desea eliminar este permiso',
        showConfirmButton: true,
        confirmButtonText: 'Si, Eliminar',
        confirmButtonColor: 'red',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/permisos/eliminar?id=${idPermiso}`;
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

                BuscarPermisos();
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
            text: 'No se pudo eliminar el permiso'
        });
        }
    }
}

const CargarAplicaciones = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/permisos/aplicaciones';
    const config = {
        method: 'POST'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos

        if (codigo == 1) {
            selectAplicacion.innerHTML = '<option value="">Seleccione una aplicación</option>';
            
            data.forEach(app => {
                const option = document.createElement('option');
                option.value = app.app_id;
                option.textContent = app.app_nombre_corto;
                selectAplicacion.appendChild(option);
            });
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las aplicaciones'
        });
    }
}

// Event Listeners
FormPermisos.addEventListener('submit', GuardarPermiso);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarPermiso);
BtnBuscarPermisos.addEventListener('click', BuscarPermisos);

// Cargar aplicaciones y permisos al inicio
CargarAplicaciones();
BuscarPermisos();