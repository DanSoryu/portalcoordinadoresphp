$(document).ready(function() {
    $('#tablaOrdenes').DataTable({
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
});