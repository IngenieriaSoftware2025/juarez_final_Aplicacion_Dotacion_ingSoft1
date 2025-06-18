import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';

const FormAsigPermisos = document.getElementById('FormAsigPermisos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscarAsignaciones = document.getElementById('BtnBuscarAsignaciones');
const TablaAsignaciones = document.getElementById('TablaAsignaciones');
const selectUsuario = document.getElementById('asignacion_usuario_id');
const selectAplicacion = document.getElementById('asignacion_app_id');
const selectPermiso = document.getElementById('asignacion_permiso_id');
const selectUsuarioAsigno = document.getElementById('asignacion_usuario_asigno');

const GuardarAsignacion = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    const body = new FormData(FormAsigPermisos);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/asigPermisos/guardar';
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
            BuscarAsignaciones();

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
            text: 'No se pudo guardar'
        });

    }
    BtnGuardar.disabled = false;
}

const BuscarAsignaciones = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/asigPermisos/buscar';
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
            TablaAsignaciones.innerHTML = '';
            
            // Agregar cada asignación a la tabla
            data.forEach((asignacion, index) => {
                const fila = document.createElement('tr');
                
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${asignacion.usuario_completo}</td>
                    <td><span class="badge bg-info">${asignacion.app_nombre_corto}</span></td>
                    <td>${asignacion.permiso_nombre}</td>
                    <td><span class="badge bg-primary">${asignacion.permiso_clave}</span></td>
                    <td>${asignacion.usuario_asigno_completo}</td>
                    <td>${asignacion.asignacion_motivo}</td>
                    <td>${asignacion.asignacion_fecha}</td>
                    <td>
                        <div class='d-flex justify-content-center gap-1'>
                            <button class='btn btn-warning btn-sm modificar' 
                                data-id="${asignacion.asignacion_id}" 
                                data-usuario="${asignacion.asignacion_usuario_id}"  
                                data-app="${asignacion.asignacion_app_id}"  
                                data-permiso="${asignacion.asignacion_permiso_id}"  
                                data-usuario-asigno="${asignacion.asignacion_usuario_asigno}"  
                                data-motivo="${asignacion.asignacion_motivo}">
                                <i class='bi bi-pencil-square'></i>
                            </button>
                            <button class='btn btn-danger btn-sm eliminar' 
                                data-id="${asignacion.asignacion_id}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                `;
                
                TablaAsignaciones.appendChild(fila);
            });

            // Agregar event listeners a los botones
            document.querySelectorAll('.modificar').forEach(btn => {
                btn.addEventListener('click', llenarFormulario);
            });
            
            document.querySelectorAll('.eliminar').forEach(btn => {
                btn.addEventListener('click', EliminarAsignacion);
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

const llenarFormulario = async (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('asignacion_id').value = datos.id
    document.getElementById('asignacion_usuario_id').value = datos.usuario
    document.getElementById('asignacion_app_id').value = datos.app
    document.getElementById('asignacion_usuario_asigno').value = datos.usuarioAsigno
    document.getElementById('asignacion_motivo').value = datos.motivo

    // Cargar permisos de la aplicación seleccionada
    await CargarPermisosPorApp(datos.app);
    document.getElementById('asignacion_permiso_id').value = datos.permiso

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    });
}

const limpiarTodo = () => {
    FormAsigPermisos.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    // Limpiar y deshabilitar select de permisos
    selectPermiso.innerHTML = '<option value="">Primero seleccione una aplicación</option>';
    selectPermiso.disabled = true;
}

const ModificarAsignacion = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    const body = new FormData(FormAsigPermisos);
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/asigPermisos/modificar';
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
            BuscarAsignaciones();

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
            text: 'No se pudo cargar la imagen'
        });    }
    BtnModificar.disabled = false;
}

const EliminarAsignacion = async (e) => {
    const idAsignacion = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: 'Esta completamente seguro que desea eliminar esta asignación de permiso',
        showConfirmButton: true,
        confirmButtonText: 'Si, Eliminar',
        confirmButtonColor: 'red',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/asigPermisos/eliminar?id=${idAsignacion}`;
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

                BuscarAsignaciones();
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

const CargarUsuarios = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/asigPermisos/usuarios';
    const config = {
        method: 'POST'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos

        if (codigo == 1) {
            selectUsuario.innerHTML = '<option value="">Seleccione un usuario</option>';
            selectUsuarioAsigno.innerHTML = '<option value="">Seleccione quien asigna</option>';
            
            data.forEach(usuario => {
                const nombreCompleto = `${usuario.usuario_nom1} ${usuario.usuario_nom2} ${usuario.usuario_ape1} ${usuario.usuario_ape2}`;
                
                // Para usuario a asignar
                const option1 = document.createElement('option');
                option1.value = usuario.usuario_id;
                option1.textContent = nombreCompleto;
                selectUsuario.appendChild(option1);
                
                // Para usuario que asigna
                const option2 = document.createElement('option');
                option2.value = usuario.usuario_id;
                option2.textContent = nombreCompleto;
                selectUsuarioAsigno.appendChild(option2);
            });
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los usuarios'
        });
    }
}

const CargarAplicaciones = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/asigPermisos/aplicaciones';
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

const CargarPermisosPorApp = async (appId) => {
    if (!appId) {
        selectPermiso.innerHTML = '<option value="">Primero seleccione una aplicación</option>';
        selectPermiso.disabled = true;
        return;
    }

    const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/asigPermisos/permisos?app_id=${appId}`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos

        if (codigo == 1) {
            selectPermiso.innerHTML = '<option value="">Seleccione un permiso</option>';
            selectPermiso.disabled = false;
            
            data.forEach(permiso => {
                const option = document.createElement('option');
                option.value = permiso.permiso_id;
                option.textContent = `${permiso.permiso_nombre} (${permiso.permiso_clave})`;
                selectPermiso.appendChild(option);
            });
        } else {
            selectPermiso.innerHTML = '<option value="">No hay permisos disponibles</option>';
            selectPermiso.disabled = true;
        }

    } catch (error) {
        selectPermiso.innerHTML = '<option value="">Error al cargar permisos</option>';
        selectPermiso.disabled = true;
    }
}

selectAplicacion.addEventListener('change', function() {
    const appId = this.value;
    CargarPermisosPorApp(appId);
});

FormAsigPermisos.addEventListener('submit', GuardarAsignacion);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarAsignacion);
BtnBuscarAsignaciones.addEventListener('click', BuscarAsignaciones);

// Cargar datos al inicio
CargarUsuarios();
CargarAplicaciones();
BuscarAsignaciones();