import Swal from "sweetalert2";
import { Chart } from "chart.js/auto";

const grafico1 = document.getElementById('grafico1').getContext('2d');
const grafico2 = document.getElementById('grafico2').getContext('2d');
const graficoEstados = document.getElementById('graficoEstados').getContext('2d');
const graficoUsuarios = document.getElementById('graficoUsuarios').getContext('2d');
const graficoApps = document.getElementById('graficoApps').getContext('2d');

const botonActualizar = document.getElementById('BtnActualizarEstadisticas');

const colores = [
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
];

let graficoDotaciones = new Chart(grafico1, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'Dotaciones Entregadas',
            data: [],
            backgroundColor: colores
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

let graficoTallas = new Chart(grafico2, {
    type: 'pie',
    data: {
        labels: [],
        datasets: [{
            label: 'Stock Disponible',
            data: [],
            backgroundColor: colores
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

let graficoEstadosActividades = new Chart(graficoEstados, {
    type: 'doughnut',
    data: {
        labels: [],
        datasets: [{
            data: [],
            backgroundColor: ['#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

let graficoUsuariosActivos = new Chart(graficoUsuarios, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'Total Actividades',
            data: [],
            backgroundColor: colores
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

let graficoAppsUsadas = new Chart(graficoApps, {
    type: 'pie',
    data: {
        labels: [],
        datasets: [{
            data: [],
            backgroundColor: colores
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

const cargarEstadisticas = async () => {
    try {
        const respuesta = await fetch('/juarez_final_Aplicacion_Dotacion_ingSoft1/estadisticas/buscar');
        const datos = await respuesta.json();

        if (datos.exito) {
            actualizarGraficos(datos);
            actualizarTotales(datos.totales);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.mensaje
            });
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Sin conexión',
            text: 'No se pudo conectar al servidor'
        });
    }
};

const cargarEstadisticasActividades = async () => {
    try {
        const respuesta = await fetch('/juarez_final_Aplicacion_Dotacion_ingSoft1/estadisticas/buscarActividades');
        const datos = await respuesta.json();

        if (datos.exito) {
            actualizarGraficosActividades(datos);
            actualizarTotalesActividades(datos.totales_actividades);
        }

    } catch (error) {
        console.error('Error actividades:', error);
    }
};

const actualizarGraficos = (datos) => {
    if (datos.dotaciones.length > 0) {
        graficoDotaciones.data.labels = datos.dotaciones.map(item => item.nombre);
        graficoDotaciones.data.datasets[0].data = datos.dotaciones.map(item => item.cantidad);
        graficoDotaciones.update();
    }

    if (datos.tallas.length > 0) {
        graficoTallas.data.labels = datos.tallas.map(item => item.nombre);
        graficoTallas.data.datasets[0].data = datos.tallas.map(item => item.cantidad);
        graficoTallas.update();
    }
};

const actualizarTotales = (totales) => {
    document.getElementById('total-dotaciones-entregadas').textContent = totales.total_dotaciones;
    document.getElementById('total-stock-disponible').textContent = totales.total_stock;
    document.getElementById('total-tipos-prendas').textContent = totales.tipos_prendas;
};

const actualizarGraficosActividades = (datos) => {
    if (datos.estados && datos.estados.length > 0) {
        graficoEstadosActividades.data.labels = datos.estados.map(item => item.estado);
        graficoEstadosActividades.data.datasets[0].data = datos.estados.map(item => item.cantidad);
        graficoEstadosActividades.update();
    }

    if (datos.usuarios && datos.usuarios.length > 0) {
        graficoUsuariosActivos.data.labels = datos.usuarios.map(item => item.nombre_usuario);
        graficoUsuariosActivos.data.datasets[0].data = datos.usuarios.map(item => item.total_actividades);
        graficoUsuariosActivos.update();
    }

    if (datos.aplicaciones && datos.aplicaciones.length > 0) {
        graficoAppsUsadas.data.labels = datos.aplicaciones.map(item => item.aplicacion);
        graficoAppsUsadas.data.datasets[0].data = datos.aplicaciones.map(item => item.total_actividades);
        graficoAppsUsadas.update();
    }
};

const actualizarTotalesActividades = (totales) => {
    document.getElementById('total-actividades-sistema').textContent = totales.total_actividades;
    document.getElementById('total-usuarios-activos').textContent = totales.total_usuarios_activos;
    document.getElementById('total-apps-usadas').textContent = totales.total_apps_usadas;
};

const cargarTodasLasEstadisticas = async () => {
    botonActualizar.disabled = true;
    botonActualizar.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Cargando...';

    try {
        await cargarEstadisticas();
        await cargarEstadisticasActividades();
        
        Swal.fire({
            icon: 'success',
            title: '¡Listo!',
            text: 'Estadísticas actualizadas',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar estadísticas'
        });
    }

    botonActualizar.disabled = false;
    botonActualizar.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Actualizar Estadísticas';
};

botonActualizar.addEventListener('click', cargarTodasLasEstadisticas);

document.getElementById('pills-actividades-tab').addEventListener('click', () => {
    setTimeout(cargarEstadisticasActividades, 100);
});

cargarTodasLasEstadisticas();