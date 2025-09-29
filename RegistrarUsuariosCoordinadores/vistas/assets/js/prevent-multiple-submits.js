/**
 * prevent-multiple-submits.js
 * 
 * Script para prevenir múltiples envíos de formularios bloqueando los botones
 * de tipo submit durante 5 segundos después de hacer clic.
 * 
 * Fecha: 17-09-2025
 */

document.addEventListener('DOMContentLoaded', function() {
    // Función para inicializar los controladores de eventos en los formularios
    function inicializarFormularios() {
        // Obtener todos los formularios del documento
        const formularios = document.querySelectorAll('form');
        
        formularios.forEach(function(formulario) {
            // Añadir manejador de eventos al envío de cada formulario
            formulario.addEventListener('submit', function(evento) {
                // Encontrar todos los botones de tipo submit dentro del formulario
                const botonesSubmit = formulario.querySelectorAll('button[type="submit"]');
                
                // Bloquear cada botón de tipo submit
                botonesSubmit.forEach(function(boton) {
                    // Guardar el texto original del botón
                    const textoOriginal = boton.innerHTML;
                    
                    // Cambiar el contenido y deshabilitar el botón
                    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                    boton.disabled = true;
                    
                    // Añadir clase para cambiar el estilo visual (opcional)
                    boton.classList.add('btn-disabled');
                    
                    // Habilitar el botón después de 5 segundos
                    setTimeout(function() {
                        boton.innerHTML = textoOriginal;
                        boton.disabled = false;
                        boton.classList.remove('btn-disabled');
                    }, 3000); // 3000 ms = 3 segundos
                });
            });
        });
    }

    // También aplicar a los formularios que utilizan jQuery para el envío AJAX
    $(document).ready(function() {
        // Capturar todos los formularios con manejadores de jQuery submit
        $('form').each(function() {
            const form = $(this);
            const submitOriginal = form.submit;
            
            // Sobrescribir el método submit
            form.submit = function(event) {
                // Encontrar todos los botones de tipo submit dentro del formulario
                const submitButtons = form.find('button[type="submit"]');
                
                // Bloquear cada botón
                submitButtons.each(function() {
                    const button = $(this);
                    const originalText = button.html();
                    
                    // Cambiar el contenido y deshabilitar el botón
                    button.html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
                    button.prop('disabled', true);
                    button.addClass('btn-disabled');
                    
                    // Habilitar el botón después de 5 segundos
                    setTimeout(function() {
                        button.html(originalText);
                        button.prop('disabled', false);
                        button.removeClass('btn-disabled');
                    }, 5000);
                });
                
                // Llamar al método submit original
                return submitOriginal.apply(this, arguments);
            };
        });
    });

    // Inicializar los manejadores de eventos
    inicializarFormularios();

    // Estilo CSS para los botones deshabilitados
    const style = document.createElement('style');
    style.textContent = `
        .btn-disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }
    `;
    document.head.appendChild(style);
});