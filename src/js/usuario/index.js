import DataTable from "datatables.net-bs5";
import { removeBootstrapValidation, soloNumeros, Toast } from "../funciones";
import { Modal, Dropdown } from "bootstrap";
import Swal from "sweetalert2";

const formUsuario = document.getElementById('formUsuario')
const BtnBuscarUsuarios = document.getElementById('BtnBuscarUsuarios')
const TablaUsuarios = document.getElementById('TablaUsuarios')

const guardarUsuario = async e => {
  e.preventDefault();
  
  try {

    const body = new FormData(formUsuario)
    const url = "/juarez_final_Aplicacion_Dotacion_ingSoft1/usuario/guardar"
    const config = {
      method: 'POST',
      body
    }

    const respuesta = await fetch(url, config);
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;

    let icon = 'info'
    if (codigo == 1) {
      icon = 'success'
      formUsuario.reset()
      
      Swal.fire({
        title: '¡Registro exitoso!',
        text: 'Usuario registrado correctamente. ¿Desea registrar otro usuario?',
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Registrar otro',
        cancelButtonText: 'Ver usuarios',
        confirmButtonColor: '#84fab0'
      }).then((result) => {
        if (result.isDismissed) {
          BuscarUsuarios();
        }
      });

    } else if (codigo == 2) {
      icon = 'warning'

    } else if (codigo == 0) {
      icon = 'error'

    }

    if(codigo !== 1) {
      Toast.fire({
        icon,
        title: mensaje
      });
    }

  } catch (error) {
    console.log(error);
  }

}

const BuscarUsuarios = async () => {
    const url = '/juarez_final_Aplicacion_Dotacion_ingSoft1/usuario/buscar';
    const config = {
        method: 'POST'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            const seccionTabla = document.getElementById('SeccionTablaUsuarios');
            if(seccionTabla) {
                seccionTabla.classList.remove('d-none');
            }

            TablaUsuarios.innerHTML = '';
            
            data.forEach((usuario, index) => {
                const fila = document.createElement('tr');
                
                // CREAR CELDA DE IMAGEN 
                const celdaImagen = document.createElement('td');
                celdaImagen.className = 'text-center';
                
                if (usuario.usuario_fotografia && usuario.usuario_fotografia !== '' && usuario.usuario_fotografia !== null) {
                    // Usar la ruta del controlador para servir las imágenes
                    const rutaImagen = `/juarez_final_Aplicacion_Dotacion_ingSoft1/usuario/imagen?dpi=${usuario.usuario_dpi}`;
                    
                    // Crear elemento img
                    const imgElement = document.createElement('img');
                    imgElement.src = rutaImagen;
                    imgElement.className = 'foto-usuario';
                    imgElement.alt = 'Foto usuario';
                    imgElement.style.cssText = 'width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;';
                    
                    // Crear contenedor para la imagen
                    const contenedorImg = document.createElement('div');
                    contenedorImg.style.cssText = 'position: relative; display: inline-block;';
                    
                    // Agregar spinner de carga
                    const spinner = document.createElement('div');
                    spinner.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Cargando...</span></div>';
                    spinner.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);';
                    
                    contenedorImg.appendChild(spinner);
                    contenedorImg.appendChild(imgElement);
                    
                    // Eventos de la imagen
                    imgElement.addEventListener('load', function() {
                        spinner.style.display = 'none';
                        this.style.display = 'block';
                    });
                    
                    imgElement.addEventListener('error', function() {
                        spinner.style.display = 'none';
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-secondary';
                        badge.textContent = 'Sin foto';
                        badge.style.cssText = 'width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 8px;';
                        contenedorImg.appendChild(badge);
                        this.style.display = 'none';
                    });
                    
                    // Inicialmente ocultar imagen hasta que cargue
                    imgElement.style.display = 'none';
                    
                    celdaImagen.appendChild(contenedorImg);
                } else {
                    // usuarios sin foto
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-secondary';
                    badge.textContent = 'Sin foto';
                    badge.style.cssText = 'width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 8px;';
                    celdaImagen.appendChild(badge);
                }
                
                // Construir el resto de la fila
                fila.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${usuario.usuario_nom1 || ''} ${usuario.usuario_nom2 || ''}</td>
                    <td>${usuario.usuario_ape1 || ''} ${usuario.usuario_ape2 || ''}</td>
                    <td>${usuario.usuario_tel || ''}</td>
                    <td>${usuario.usuario_dpi || ''}</td>
                    <td>${usuario.usuario_correo || ''}</td>
                    <td>${usuario.usuario_fecha_creacion || ''}</td>
                `;
                
                // Insertar la celda de imagen en la posición correcta (segunda columna)
                fila.insertBefore(celdaImagen, fila.children[1]);
                
                TablaUsuarios.appendChild(fila);
            });

        } else {
            Swal.fire({
                position: "center",
                icon: "info",
                title: "Info",
                text: mensaje,
                showConfirmButton: true,
            });

            const seccionTabla = document.getElementById('SeccionTablaUsuarios');
            if(seccionTabla) {
                seccionTabla.classList.add('d-none');
            }
        }

    } catch (error) {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Error al buscar usuarios: " + error.message,
            showConfirmButton: true,
        });
    }
}

if (formUsuario) {
    formUsuario.addEventListener('submit', guardarUsuario);
}

if (BtnBuscarUsuarios) {
    BtnBuscarUsuarios.addEventListener('click', BuscarUsuarios);
}