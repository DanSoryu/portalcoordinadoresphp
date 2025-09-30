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
			beforeSend: function(jqXHR, settings) {
				try {
					console.groupCollapsed('[LOGIN] Enviando petición');
					console.log('URL:', settings.url);
					console.log('Método:', settings.type);
					console.log('Payload:', { usuario: usuario, password: '[OCULTO]' });
					console.time('[LOGIN] Duración');
					console.groupEnd();
				} catch (e) {}
			},
			success: function(response) {
				try {
					console.groupCollapsed('[LOGIN] Respuesta success');
					console.log('HTTP 200 OK');
					console.log('JSON:', response);
					console.timeEnd('[LOGIN] Duración');
					console.groupEnd();
				} catch (e) {}
				if (response.success) {
					Toast.success(response.message || 'Autenticación exitosa');
					setTimeout(function() {
						window.location.href = response.redirect || '../Dashboard/';
					}, 800);
				} else {
					Toast.error(response.message || 'Error de autenticación');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				try {
					console.group('[LOGIN] Error AJAX');
					console.error('Status:', jqXHR.status, textStatus, errorThrown);
					console.error('Response headers:', jqXHR.getAllResponseHeaders && jqXHR.getAllResponseHeaders());
					console.error('Response text:', jqXHR.responseText);
					console.timeEnd('[LOGIN] Duración');
					console.groupEnd();
				} catch (e) {}
				Toast.error('Error al procesar la solicitud');
			},
			complete: function() {
				try {
					console.log('[LOGIN] Petición completada');
				} catch (e) {}
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
