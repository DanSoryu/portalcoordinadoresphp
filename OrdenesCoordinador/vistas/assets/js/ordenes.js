// Obtener copes y órdenes del coordinador al cargar la página
$(document).ready(function() {
	// Suponiendo que tienes el idUsuario disponible en una variable global o en un input hidden
	var idUsuario = window.idUsuario || $("#idUsuario").val();
	if (!idUsuario) {
		console.error('No se encontró el idUsuario.');
		return;
	}
	idUsuario = idUsuario.toString(); // Asegura que sea string

	$.ajax({
		url: '../OrdenesCoordinador/requests/get_copes_ordenes.php',
		type: 'POST',
		data: { idUsuario: idUsuario },
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				console.log('Copes:', response.copes);
				console.log('Órdenes:', response.ordenes);
				// Aquí puedes llamar a una función para renderizar la tabla con response.ordenes
			} else {
				console.error(response.error || 'Error al obtener los datos');
			}
		},
		error: function(xhr, status, error) {
			console.error('Error en la petición AJAX:', error);
		}
	});
});