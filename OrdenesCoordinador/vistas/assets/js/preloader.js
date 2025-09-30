// API simple y estandarizada de preloader para toda la app
window.AppPreloader = (function(){
    const PRELOADER_ID = 'preloader';
    function getEl(){ return document.getElementById(PRELOADER_ID); }
    function show(){ const el = getEl(); if (el){ el.style.display = 'block'; el.classList.remove('fade-out'); } }
    function hide(){ const el = getEl(); if (el){ el.classList.add('fade-out'); setTimeout(()=>{ el.style.display='none'; }, 300); } }
    return { show, hide };
})();

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function(){ window.AppPreloader.hide(); }, 600);
});

window.addEventListener('beforeunload', function() {
    window.AppPreloader.show();
});

document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function() {
        window.AppPreloader.show();
    });
});

if (typeof jQuery !== 'undefined') {
    $(document).ajaxSend(function() { window.AppPreloader.show(); });
    $(document).ajaxComplete(function() { window.AppPreloader.hide(); });
}