$(document).ready(function() {
	const form = $('.login-form');
	let formSubmitting = false;

	form.on('submit', function(e) {
		e.preventDefault();
		if (formSubmitting) {
			return;
		}
		form.find('.is-invalid').removeClass('is-invalid');
		$('.login-error').hide();

		// Obtener valores
		const usuario = $('#usuario').val().trim();
		const password = $('#password').val().trim();

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
		if (!isValid) {
			Toast.error('Por favor completa los campos obligatorios.');
			return;
		}

		formSubmitting = true;
		const formData = {
			usuario: usuario,
			password: password
		};

		$.ajax({
			url: './requests/login_usuario.php',
			type: 'POST',
			data: formData,
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					Toast.success(response.message || 'Autenticación exitosa');
					setTimeout(function() {
						window.location.href = response.redirect || '../Dashboard/';
					}, 800);
				} else {
					Toast.error(response.message || 'Error de autenticación');
				}
			},
			error: function() {
				Toast.error('Error al procesar la solicitud');
			},
			complete: function() {
				formSubmitting = false;
			}
		});
	});

	// Validación en tiempo real (opcional, solo para UX)
	$('#usuario, #password').on('input', function() {
		if ($(this).val().trim()) {
			$(this).removeClass('is-invalid');
		}
	});
});
