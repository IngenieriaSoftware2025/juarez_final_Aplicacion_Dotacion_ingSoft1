import Swal from 'sweetalert2';
import { validarFormulario } from '../funciones';

const FormLogin = document.getElementById('FormLogin');
const BtnIniciar = document.getElementById('BtnIniciar');

const login = async (e) => {
    e.preventDefault();
    BtnIniciar.disabled = true;

    if (!validarFormulario(FormLogin, [''])) {
        Swal.fire({
            title: "Campos vacíos",
            text: "Debe llenar todos los campos",
            icon: "info"
        });
        BtnIniciar.disabled = false;
        return;
    }

    try {
        const body = new FormData(FormLogin);
        const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/login/iniciar';
        const config = {
            method: 'POST',
            body
        };

        console.log('Enviando petición a:', url); 
        
        const respuesta = await fetch(url, config);
        
        const contentType = respuesta.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("La respuesta del servidor no es JSON válido");
        }
        
        const data = await respuesta.json();
        const { codigo, mensaje, detalle } = data;

        if (codigo == 1) {
            await Swal.fire({
                title: 'Éxito',
                text: mensaje,
                icon: 'success',
                showConfirmButton: true,
                timer: 1500,
                timerProgressBar: false,
                background: '#e0f7fa',
                customClass: {
                    title: 'custom-title-class',
                    text: 'custom-text-class'
                }
            });

            FormLogin.reset();
            location.href = '/juarez_final_Aplicacion_Dotacion_ingSoft1/inicio';
        } else {
            Swal.fire({
                title: '¡Error!',
                text: mensaje,
                icon: 'warning',
                showConfirmButton: true,
                timer: 1500,
                timerProgressBar: false,
                background: '#e0f7fa',
                customClass: {
                    title: 'custom-title-class',
                    text: 'custom-text-class'
                }
            });
        }

    } catch (error) {
        console.log(error);
        
        Swal.fire({
            title: '¡Error de conexión!',
            text: 'No se pudo conectar con el servidor',
            icon: 'error',
            showConfirmButton: true,
            background: '#e0f7fa',
            customClass: {
                title: 'custom-title-class',
                text: 'custom-text-class'
            }
        });
    }

    BtnIniciar.disabled = false;
}

FormLogin.addEventListener('submit', login);