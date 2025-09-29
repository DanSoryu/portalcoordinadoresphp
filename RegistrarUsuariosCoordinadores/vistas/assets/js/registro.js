
$(document).ready(function() {
    const form = $('#formRegistroCoordinador');
    let formSubmitting = false;

    form.on('submit', function(e) {
        e.preventDefault();
        if (formSubmitting) {
            return;
        }
        form.find('.is-invalid').removeClass('is-invalid');

        // Obtener valores
        const usuario = $('#usuario').val().trim();
        const password = $('#password').val().trim();
        const confirmPassword = $('#confirmPassword').val().trim();

        // Recoger los COPEs seleccionados
        const copesSeleccionados = [];
        $('input[name="copes[]"]:checked').each(function() {
            copesSeleccionados.push($(this).val());
        });

        // Validaciones
        let isValid = true;
        if (!usuario) {
            $('#usuario').addClass('is-invalid');
            isValid = false;
        }
        if (!password) {
            $('#password').addClass('is-invalid');
            isValid = false;
        }
        if (!confirmPassword || password !== confirmPassword) {
            $('#confirmPassword').addClass('is-invalid');
            isValid = false;
        }
        // Validar que se haya seleccionado al menos un COPE
        if (copesSeleccionados.length === 0) {
            $('.cope-checkboxes').addClass('is-invalid');
            isValid = false;
        } else {
            $('.cope-checkboxes').removeClass('is-invalid');
        }
        if (!isValid) {
            return;
        }

        formSubmitting = true;
        // Construir el objeto de datos explícitamente
        const formData = {
            usuario: usuario,
            password: password,
            copes: copesSeleccionados
        };

        $.ajax({
            url: '../requests/guardar_usuario_coordinador.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    form[0].reset();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            },
            complete: function() {
                formSubmitting = false;
            }
        });
    });

    // Validación en tiempo real de confirmación de contraseña
    $('#confirmPassword, #password').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();
        if (confirmPassword && password !== confirmPassword) {
            $('#confirmPassword').addClass('is-invalid');
        } else {
            $('#confirmPassword').removeClass('is-invalid');
        }
    });
});