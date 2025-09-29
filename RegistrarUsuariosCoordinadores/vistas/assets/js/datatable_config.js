$(document).ready(function() {
    $('#tablaUsuarios').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.0/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [
            {
                targets: 1, // Columna de acciones
                orderable: false // Deshabilitar ordenamiento
            }
        ]
    });

    // Validación en tiempo real para nombre de usuario
    $('#usuario, #edit_usuario').on('input', function() {
        const valor = $(this).val().trim();
        if (valor.length === 0) {
            $(this).addClass('is-invalid');
        } else if (valor.length < 2 || valor.length > 25) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Validación en tiempo real para contraseña
    $('#password').on('input', function() {
        const valor = $(this).val().trim();
        if (valor.length === 0) {
            $(this).addClass('is-invalid');
        } else if (valor.length < 8) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Validación de contraseñas coincidentes
    $('#confirmPassword').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        
        if (password !== confirmPassword) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});