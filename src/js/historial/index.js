import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const BotonBuscarActividades = document.getElementById('BtnBuscarActividades');
const SelectorUsuario = document.getElementById('filtro_usuario');
const SelectorAplicacion = document.getElementById('filtro_aplicacion');
const SelectorRuta = document.getElementById('filtro_ruta');
const CampoFechaInicio = document.getElementById('fecha_inicio');
const CampoFechaFin = document.getElementById('fecha_fin');
const BotonLimpiarFiltros = document.getElementById('BtnLimpiarFiltros');
const seccionTabla = document.getElementById('seccionTabla');

const cargarUsuarios = async () => {
    const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/historial/buscarUsuariosAPI`;
    const configuracion = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, configuracion);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            SelectorUsuario.innerHTML = '<option value="">Todos los usuarios</option>';
            
            data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.historial_usuario_id;
                opcion.textContent = usuario.usuario_nombre;
                SelectorUsuario.appendChild(opcion);
            });
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
        console.error('Error al cargar usuarios:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudieron cargar los usuarios",
            showConfirmButton: true,
        });
    }
}

const cargarAplicaciones = async () => {
    const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/historial/buscarAplicacionesAPI`;
    const configuracion = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, configuracion);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            SelectorAplicacion.innerHTML = '<option value="">Todas las aplicaciones</option>';
            
            data.forEach(aplicacion => {
                const opcion = document.createElement('option');
                opcion.value = aplicacion.app_id;
                opcion.textContent = aplicacion.app_nombre;
                SelectorAplicacion.appendChild(opcion);
            });
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
        console.error('Error al cargar aplicaciones:', error);
    }
}

const cargarRutas = async (idAplicacion = '') => {
    const parametros = new URLSearchParams();
    if (idAplicacion) {
        parametros.append('aplicacion_id', idAplicacion);
    }
    
    const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/historial/buscarRutasAPI${parametros.toString() ? '?' + parametros.toString() : ''}`;
    const configuracion = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, configuracion);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            SelectorRuta.innerHTML = '<option value="">Todas las rutas</option>';
            
            data.forEach(ruta => {
                const opcion = document.createElement('option');
                opcion.value = ruta.ruta_id;
                opcion.textContent = ruta.ruta_nombre;
                SelectorRuta.appendChild(opcion);
            });
        } else {
            SelectorRuta.innerHTML = '<option value="">Todas las rutas</option>';
        }

    } catch (error) {
        console.error('Error al cargar rutas:', error);
        SelectorRuta.innerHTML = '<option value="">Todas las rutas</option>';
    }
}

const organizarDatosPorAplicacion = (data) => {
    const aplicaciones = {};
    const iconosAplicacion = {
        'SISTEMA DE DOTACIÓN': '👕',
        'ADMINISTRACION': '⚙️',
        'PERSONAL': '👥',
        'INVENTARIO': '📦',
        'PEDIDOS': '📋',
        'ENTREGAS': '📤',
        'REPORTES': '📊'
    };
    
    data.forEach(actividad => {
        const nombreApp = actividad.aplicacion_nombre;
        if (!aplicaciones[nombreApp]) {
            aplicaciones[nombreApp] = [];
        }
        aplicaciones[nombreApp].push(actividad);
    });
    
    let datosOrganizados = [];
    let contador = 1;
    
    Object.keys(aplicaciones).forEach(nombreApp => {
        const actividadesApp = aplicaciones[nombreApp];
        
        if (actividadesApp.length > 0) {
            datosOrganizados.push({
                esSeparador: true,
                aplicacion: nombreApp,
                icono: iconosAplicacion[nombreApp] || '📱',
                cantidad: actividadesApp.length
            });
            
            actividadesApp.forEach(actividad => {
                datosOrganizados.push({
                    ...actividad,
                    numeroConsecutivo: contador++,
                    esSeparador: false
                });
            });
        }
    });
    
    return datosOrganizados;
}

const buscarActividades = async () => {
    BotonBuscarActividades.disabled = true;
    BotonBuscarActividades.innerHTML = '<i class="bi bi-arrow-clockwise spinner-border spinner-border-sm me-2"></i>Buscando...';

    const parametros = new URLSearchParams();
    
    if (CampoFechaInicio.value) {
        parametros.append('fecha_inicio', CampoFechaInicio.value);
    }
    
    if (CampoFechaFin.value) {
        parametros.append('fecha_fin', CampoFechaFin.value);
    }
    
    if (SelectorUsuario.value) {
        parametros.append('usuario_id', SelectorUsuario.value);
    }
    
    if (SelectorAplicacion.value) {
        parametros.append('aplicacion_id', SelectorAplicacion.value);
    }
    
    if (SelectorRuta.value) {
        parametros.append('ruta_id', SelectorRuta.value);
    }

    const url = `/juarez_final_Aplicacion_Dotacion_ingSoft1/historial/buscarAPI${parametros.toString() ? '?' + parametros.toString() : ''}`;
    const configuracion = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, configuracion);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            console.log('Historial encontrado:', data);
            
            const datosOrganizados = organizarDatosPorAplicacion(data);

            if (tablaHistorial) {
                tablaHistorial.clear().draw();
                tablaHistorial.rows.add(datosOrganizados).draw();
            }

            await Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Búsqueda completada!",
                text: `Se encontraron ${data.length} registros`,
                timer: 2000,
                showConfirmButton: false
            });

        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Sin resultados",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.error('Error en búsqueda:', error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de búsqueda",
            text: "Ocurrió un error al buscar el historial",
            showConfirmButton: true,
        });
    } finally {
        BotonBuscarActividades.disabled = false;
        BotonBuscarActividades.innerHTML = '<i class="bi bi-search me-1"></i>Buscar Historial';
    }
}

const mostrarTabla = () => {
    if (seccionTabla.style.display === 'none') {
        seccionTabla.style.display = 'block';
        buscarActividades();
    } else {
        seccionTabla.style.display = 'none';
    }
}

const limpiarFiltros = () => {
    SelectorUsuario.value = '';
    SelectorAplicacion.value = '';
    SelectorRuta.value = '';
    CampoFechaInicio.value = '';
    CampoFechaFin.value = '';
    
    cargarRutas();
    
    if (seccionTabla.style.display !== 'none') {
        buscarActividades();
    }

    Swal.fire({
        position: "center",
        icon: "success",
        title: "Filtros limpiados",
        timer: 1500,
        showConfirmButton: false
    });
}

const tablaHistorial = new DataTable('#TableHistorialActividades', {
    dom: `
        <"row mt-3 justify-content-between" 
            <"col" l> 
            <"col" B> 
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between" 
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    ordering: false,
    pageLength: 25,
    responsive: true,
    columns: [
        {
            title: 'No.',
            data: null,
            width: '5%',
            render: (data, type, row, meta) => {
                if (row.esSeparador) {
                    return '';
                }
                return row.numeroConsecutivo;
            }
        },
        { 
            title: 'Usuario', 
            data: 'nombre_usuario',
            width: '12%',
            render: (data, type, row, meta) => {
                if (row.esSeparador) {
                    return `<strong class="text-primary fs-5 text-center w-100 d-block">${row.icono} ${row.aplicacion} (${row.cantidad})</strong>`;
                }
                return data || 'Sistema';
            }
        },
        { 
            title: 'Aplicación', 
            data: 'aplicacion_nombre',
            width: '12%',
            render: (data, type, row, meta) => {
                if (row.esSeparador) return '';
                return `<span class="badge bg-secondary">${data}</span>`;
            }
        },
        { 
            title: 'Ruta', 
            data: 'ruta_nombre',
            width: '15%',
            render: (data, type, row, meta) => {
                if (row.esSeparador) return '';
                return `<span class="badge bg-info text-dark">${data}</span>`;
            }
        },
        { 
            title: 'Descripción', 
            data: 'ruta_descripcion',
            width: '20%',
            render: (data, type, row, meta) => {
                if (row.esSeparador) return '';
                return data.length > 50 ? data.substring(0, 50) + '...' : data;
            }
        },
        { 
            title: 'Ejecución', 
            data: 'historial_ejecucion',
            width: '18%',
            render: (data, type, row, meta) => {
                if (row.esSeparador) return '';
                return data.length > 60 ? data.substring(0, 60) + '...' : data;
            }
        },
        { 
            title: 'Estado', 
            data: 'estado_ejecucion',
            width: '8%',
            render: (data, type, row, meta) => {
                if (row.esSeparador) return '';
                if (data === 'EXITOSO') {
                    return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>EXITOSO</span>';
                } else {
                    return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>ERROR</span>';
                }
            }
        },
        { 
            title: 'Fecha', 
            data: 'historial_fecha',
            width: '10%',
            render: (data, type, row, meta) => {
                if (row.esSeparador) return '';
                // Formatear fecha 
                const fecha = new Date(data);
                return fecha.toLocaleString('es-GT', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
    ],
    rowCallback: function(fila, datos) {
        if (datos.esSeparador) {
            fila.classList.add('table-secondary');
            fila.style.backgroundColor = '#e9ecef';
            fila.style.fontWeight = 'bold';
            fila.cells[1].colSpan = 7;
            for (let i = 2; i < fila.cells.length; i++) {
                fila.cells[i].style.display = 'none';
            }
        } else {
            if (datos.estado_ejecucion === 'ERROR') {
                fila.classList.add('table-danger-subtle');
            } else {
                fila.classList.add('table-success-subtle');
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    cargarUsuarios();
    cargarAplicaciones();
    cargarRutas();
});

BotonBuscarActividades.addEventListener('click', mostrarTabla);
BotonLimpiarFiltros.addEventListener('click', limpiarFiltros);

SelectorUsuario.addEventListener('change', () => {
    if (seccionTabla.style.display !== 'none') {
        buscarActividades();
    }
});

SelectorAplicacion.addEventListener('change', () => {
    cargarRutas(SelectorAplicacion.value);
    if (seccionTabla.style.display !== 'none') {
        buscarActividades();
    }
});

SelectorRuta.addEventListener('change', () => {
    if (seccionTabla.style.display !== 'none') {
        buscarActividades();
    }
});

CampoFechaInicio.addEventListener('change', () => {
    if (seccionTabla.style.display !== 'none') {
        buscarActividades();
    }
});

CampoFechaFin.addEventListener('change', () => {
    if (seccionTabla.style.display !== 'none') {
        buscarActividades();
    }
});