// Script para manejar la eliminación de usuarios
$(document).ready(function() {
    // Función para confirmar eliminación
    window.confirmarEliminar = function(id, usuario) {
        $('#idUsuarioEliminar').val(id);
        $('#nombreUsuarioEliminar').text(usuario);
        $('#modalEliminarUsuario').modal('show');
    };
    
    // Manejar el envío del formulario de eliminación
    $('#formEliminarUsuario').on('submit', function(e) {
        e.preventDefault();
        
        const idUsuario = $('#idUsuarioEliminar').val();
        
        showPreloader();
        
        console.log('Intentando eliminar usuario:', idUsuario);
        $.ajax({
            url: '../requests/eliminar_usuario_coordinador.php',
            type: 'POST',
            data: {
                idUsuario: idUsuario
            },
            success: function(response) {
                console.log('Respuesta del servidor (eliminación):', response);
                try {
                    if (typeof response === 'string') {
                        console.log('Convirtiendo respuesta string a JSON (eliminación)');
                        response = JSON.parse(response);
                    }
                    
                    if(response.success) {
                        console.log('Eliminación exitosa:', response.message);
                        Toast.success(response.message);
                        $('#modalEliminarUsuario').modal('hide');
                        localStorage.setItem('toastMessage', response.message);
                        localStorage.setItem('toastType', 'success');
                        location.reload();
                    } else {
                        console.error('Error en la eliminación:', response);
                        hidePreloader();
                        Toast.error(response.message || 'Error al eliminar el usuario');
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta de eliminación:', e);
                    console.log('Respuesta original de eliminación:', response);
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