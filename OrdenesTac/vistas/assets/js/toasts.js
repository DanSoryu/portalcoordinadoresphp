// Script para mostrar toasts después de recargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Comprobar si hay mensajes guardados en localStorage
    const toastMessage = localStorage.getItem('toastMessage');
    const toastType = localStorage.getItem('toastType');
    
    if (toastMessage) {
        // Mostrar el toast guardado
        Toastify({
            text: toastMessage,
            duration: 7000,
            gravity: 'bottom',
            position: 'right',
            style: {
                background: toastType === 'success' ? '#28a745' : '#dc3545'
            },
            stopOnFocus: true,
            close: true
        }).showToast();
        
        // Limpiar localStorage después de mostrar el toast
        localStorage.removeItem('toastMessage');
        localStorage.removeItem('toastType');
    }
});