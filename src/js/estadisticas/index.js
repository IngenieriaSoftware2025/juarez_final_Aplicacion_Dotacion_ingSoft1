import Swal from "sweetalert2";
import { Chart, registerables } from "chart.js";

// Activar Chart.js
Chart.register(...registerables);

const grafico1 = document.getElementById('grafico1').getContext('2d');
const grafico2 = document.getElementById('grafico2').getContext('2d');

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

const cargarEstadisticas = async () => {
    const boton = document.getElementById('BtnActualizarEstadisticas');
    
    boton.disabled = true;
    boton.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Cargando...';

    try {
        const respuesta = await fetch('/juarez_final_Aplicacion_Dotacion_ingSoft1/estadisticas/buscar');
        const datos = await respuesta.json();

        if (datos.exito) {
            actualizarGraficos(datos);
            actualizarTotales(datos.totales);
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Listo!',
                text: 'Estadísticas actualizadas',
                timer: 2000,
                showConfirmButton: false
            });
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

    boton.disabled = false;
    boton.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Actualizar Estadísticas';
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

document.getElementById('BtnActualizarEstadisticas').addEventListener('click', cargarEstadisticas);

cargarEstadisticas();