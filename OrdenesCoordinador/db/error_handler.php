<?php
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    $error_message = date('Y-m-d H:i:s') . " - Error [$errno] $errstr - File: $errfile:$errline\n";
    error_log($error_message, 3, __DIR__ . '/../logs/error.log');
    
    if (ini_get('display_errors')) {
        echo "Ha ocurrido un error. Por favor, contacte al administrador.";
    }
    return true;
}

set_error_handler("custom_error_handler");

function checkDatabaseConnection($conexion) {
    if (!$conexion) {
        error_log("Error de conexión a la base de datos", 3, __DIR__ . '/../logs/error.log');
        throw new Exception("Error de conexión a la base de datos");
    }
}
?>