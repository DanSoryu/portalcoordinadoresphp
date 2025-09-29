// Variable de estado para la visibilidad de la contraseña
let passwordVisible = false;

// Función para mostrar/ocultar contraseña
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.querySelector('.btn-outline-secondary i');
    
    passwordVisible = !passwordVisible;
    
    if (passwordVisible) {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Función para generar una contraseña segura
function generarPassword() {
    const minusculas = 'abcdefghijklmnopqrstuvwxyz';
    const mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const numeros = '0123456789';
    const especiales = '!@#$%^&*_-+=';
    const todosCaracteres = minusculas + mayusculas + numeros + especiales;

    let password = [
        minusculas[Math.floor(Math.random() * minusculas.length)],
        mayusculas[Math.floor(Math.random() * mayusculas.length)],
        numeros[Math.floor(Math.random() * numeros.length)],
        especiales[Math.floor(Math.random() * especiales.length)]
    ];

    for (let i = 0; i < 4; i++) {
        password.push(todosCaracteres[Math.floor(Math.random() * todosCaracteres.length)]);
    }

    password = password.sort(() => Math.random() - 0.5).join('');

    const passwordInput = document.getElementById('password');
    passwordInput.value = password;
    passwordInput.dispatchEvent(new Event('input'));

    if (passwordVisible) {
        passwordInput.type = 'text';
    }
}

$(document).ready(function() {
    // Función para manejar el envío del formulario de creación
    $('#formAgregarUsuario').on('submit', function(e) {
        e.preventDefault();
        
        // Obtener valores del formulario
        const usuario = $('#usuario').val().trim();
        const password = $('#password').val().trim();
        
        // Recoger los COPEs seleccionados
        const copesSeleccionados = [];
        $('input[name="copes[]"]:checked').each(function() {
            copesSeleccionados.push($(this).val());
        });
        
        console.log('COPEs seleccionados:', copesSeleccionados);
        
        // Validaciones del lado del cliente
        if (!usuario || !password) {
            Toast.error('Todos los campos son obligatorios');
            return false;
        }
        
        // Validar que se haya seleccionado al menos un COPE
        if (copesSeleccionados.length === 0) {
            Toast.error('Debe seleccionar al menos un COPE');
            return false;
        }
        
        // Construir el objeto de datos explícitamente
        const formData = {
            usuario: usuario,
            password: password,
            copes: copesSeleccionados
        };

        showPreloader();
        
        console.log('Enviando datos:', formData);
        $.ajax({
            url: '../requests/guardar_usuario_coordinador.php',
            type: 'POST',
                data: formData,
                dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                try {
                        if(response.success) {
                            console.log('Operación exitosa:', response.message);
                            Toast.success(response.message);
                            $('#modalAgregarUsuario').modal('hide');
                            localStorage.setItem('toastMessage', response.message);
                            localStorage.setItem('toastType', 'success');
                            location.reload();
                        } else {
                            console.error('Error en la respuesta:', response);
                            hidePreloader();
                            Toast.error(response.message || 'Error al crear el usuario');
                        }
                } catch (e) {
                    console.error('Error al procesar la respuesta:', e);
                    console.log('Respuesta original:', response);
                    hidePreloader();
                    Toast.error('Error al procesar la respuesta del servidor');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hidePreloader();
                let errorMessage = 'Error al procesar la solicitud';
                try {
                    const response = JSON.parse(jqXHR.responseText);
                    errorMessage = response.message || errorThrown || textStatus;
                } catch (e) {
                    errorMessage += ': ' + (errorThrown || textStatus);
                }
                Toast.error(errorMessage);
            }
        });
    });
});