// Sistema global de notificaciones usando Toastify
const Toast = {
    success: function(message) {
        Toastify({
            text: message,
            duration: 7000,
            gravity: "bottom",
            position: "right",
            style: {
                background: "#28a745"
            },
            stopOnFocus: true,
            close: true
        }).showToast();
    },

    error: function(message) {
        Toastify({
            text: message,
            duration: 7000,
            gravity: "bottom",
            position: "right",
            style: {
                background: "#dc3545"
            },
            stopOnFocus: true,
            close: true
        }).showToast();
    },

    warning: function(message) {
        Toastify({
            text: message,
            duration: 7000,
            gravity: "bottom",
            position: "right",
            style: {
                background: "#ffc107"
            },
            stopOnFocus: true,
            close: true
        }).showToast();
    },

    info: function(message) {
        Toastify({
            text: message,
            duration: 7000,
            gravity: "bottom",
            position: "right",
            style: {
                background: "#17a2b8"
            },
            stopOnFocus: true,
            close: true
        }).showToast();
    }
};