// Función global para editar usuario
function editarUsuario(usuario) {
    // Llenar el formulario con los datos del usuario
    $('#edit_id').val(usuario.idusuarios_coordinadores);
    $('#edit_usuario').val(usuario.usuario);
    $('#edit_password').val(''); // Limpiar el campo de contraseña

    // Asegurar que el campo esté en modo password
    const passwordInput = document.getElementById('edit_password');
    passwordInput.type = 'password';
    editPasswordVisible = false;

    // Limpiar checkboxes anteriores
    $('#edit-cope-checkboxes input[name="copes[]"]').prop('checked', false);

    // Cargar COPEs actuales del usuario
    showPreloader(); // Mostrar preloader antes de la petición
    $.ajax({
        url: '../requests/obtener_copes_usuario.php',
        method: 'POST',
        data: { idUsuario: usuario.idusuarios_coordinadores },
        dataType: 'json',
        success: function(response) {
            hidePreloader(); // Ocultar preloader cuando se recibe respuesta
            if (response.success) {
                // Limpiar los checkboxes existentes
                $('#edit-cope-checkboxes').empty();
                
                // Crear un mapa de COPEs asignados para verificación rápida
                const copesAsignadosMap = {};
                if (response.copesAsignados) {
                    response.copesAsignados.forEach(cope => {
                        copesAsignadosMap[cope.id] = true;
                    });
                }
                
                // Crear un mapa de COPEs disponibles para evitar duplicados
                const copesDisponiblesMap = {};
                if (response.copesDisponibles) {
                    response.copesDisponibles.forEach(cope => {
                        copesDisponiblesMap[cope.id] = cope;
                    });
                }
                
                // Combinar COPEs asignados y disponibles sin duplicados
                const todosLosCopes = [];
                
                // Agregar COPEs asignados
                if (response.copesAsignados) {
                    response.copesAsignados.forEach(cope => {
                        todosLosCopes.push(cope);
                    });
                }
                
                // Agregar COPEs disponibles que no estén ya en asignados
                if (response.copesDisponibles) {
                    response.copesDisponibles.forEach(cope => {
                        if (!copesAsignadosMap[cope.id]) {
                            todosLosCopes.push(cope);
                        }
                    });
                }
                
                // Crear checkboxes para todos los COPEs combinados
                todosLosCopes.forEach(cope => {
                    const isChecked = copesAsignadosMap[cope.id] ? 'checked' : '';
                    const checkbox = `<div class="form-check">
                        <input class="form-check-input" type="checkbox" name="copes[]" value="${cope.id}" id="edit_cope${cope.id}" ${isChecked}>
                        <label class="form-check-label" for="edit_cope${cope.id}">${cope.COPE}</label>
                    </div>`;
                    $('#edit-cope-checkboxes').append(checkbox);
                });
            } else {
                console.error('Error al cargar COPEs:', response.error || 'No se pudieron cargar los COPEs');
                Toast.error('Error al cargar los COPEs. Por favor, intente nuevamente.');
            }
        },
        error: function(xhr, status, error) {
            hidePreloader(); // Ocultar preloader en caso de error
            console.error('Error en la petición AJAX:', error);
            console.log('Respuesta del servidor:', xhr.responseText);
            // Mostrar un mensaje al usuario
            Toast.error('Error al cargar los COPEs del usuario. Por favor, intente nuevamente.');
        }
    });

    // Abrir el modal
    $('#modalEditarUsuario').modal('show');
}

// Variables para el manejo de la contraseña en el modal de edición
let editPasswordVisible = false;

// Función para mostrar/ocultar contraseña
function toggleEditPassword() {
    const passwordInput = document.getElementById('edit_password');
    const icon = document.querySelector('#modalEditarUsuario .btn-outline-secondary i');

    editPasswordVisible = !editPasswordVisible;

    if (editPasswordVisible) {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Función para generar contraseña segura
function generarEditPassword() {
    const passwordInput = document.getElementById('edit_password');

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
    passwordInput.value = password;
    passwordInput.dispatchEvent(new Event('input'));

    if (editPasswordVisible) {
        passwordInput.type = 'text';
    }
}

// Función para manejar el envío del formulario de edición
$(document).ready(function() {
    $('#formEditarUsuario').on('submit', function(e) {
        e.preventDefault();

        // Crear objeto con los datos del formulario
        const formData = {
            id: $('#edit_id').val(),
            usuario: $('#edit_usuario').val().trim(),
            estado: $('#edit_estado').val() === "1" ? 1 : 0 // Explicitly convert to number
        };

        // Agregar contraseña solo si tiene valor
        const password = $('#edit_password').val();
        if (password) {
            formData.password = password;
        }

        // Recoger los COPEs seleccionados
        const copesSeleccionados = [];
        $('#edit-cope-checkboxes input[name="copes[]"]:checked').each(function() {
            copesSeleccionados.push($(this).val());
        });
        formData.copes = copesSeleccionados;

        console.log('COPEs seleccionados:', copesSeleccionados);
        console.log('Datos de edición a enviar:', formData);
        // Realizar la llamada AJAX
        showPreloader(); // Mostrar preloader antes de la petición
        $.ajax({
            url: '../requests/actualizar_usuario_coordinador.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log('Respuesta del servidor (edición):', response);
                try {
                    if (typeof response === 'string') {
                        console.log('Convirtiendo respuesta string a JSON (edición)');
                        response = JSON.parse(response);
                    }

                    if (response.success) {
                        console.log('Edición exitosa:', response.message);
                        Toast.success(response.message);
                        $('#modalEditarUsuario').modal('hide');
                        localStorage.setItem('toastMessage', response.message);
                        localStorage.setItem('toastType', 'success');
                        // Recargar la página inmediatamente
                        location.reload();
                    } else {
                        console.error('Error en la edición:', response);
                        hidePreloader();
                        Toast.error(response.message || 'Error al actualizar el usuario');
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta de edición:', e);
                    console.log('Respuesta original de edición:', response);
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
            },
            // El botón será reactivado por prevent-multiple-submits.js
            complete: function() {
                // No ocultamos el preloader aquí si hubo éxito, ya que habrá una recarga de página
            }
        });
    });
});