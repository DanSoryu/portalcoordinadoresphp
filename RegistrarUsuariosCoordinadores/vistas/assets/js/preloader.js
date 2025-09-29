// Función para mostrar el preloader
function showPreloader() {
    document.getElementById('preloader').classList.remove('fade-out');
}

// Función para ocultar el preloader
function hidePreloader() {
    const preloader = document.getElementById('preloader');
    preloader.classList.add('fade-out');
    setTimeout(() => {
        preloader.style.display = 'none';
    }, 500);
}

// Evento que se dispara cuando el DOM está completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(hidePreloader, 800);
});

// Interceptamos cualquier recarga de página para mostrar el preloader
window.addEventListener('beforeunload', function() {
    showPreloader();
});

// Mostrar preloader al enviar formularios
document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function() {
        showPreloader();
    });
});

// Configuración para llamadas jQuery AJAX
if (typeof jQuery !== 'undefined') {
    $(document).ajaxSend(function() {
        showPreloader();
    });
    
    $(document).ajaxComplete(function() {
        hidePreloader();
    });
}