$(document).ready(function() {
    $('#formLogout').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../../requests/logout.php',
            type: 'POST',
            success: function() {
                // Puedes mostrar un toast si tienes sistema de notificaciones
                // Toast.success('Sesión cerrada correctamente');
                window.location.href = '../../Login.php';
            },
            error: function() {
                // Toast.error('Error al cerrar sesión');
                window.location.href = '../../Login.php';
            }
        });
    });
});
