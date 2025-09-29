<?php
// Deshabilitar la salida de errores a la página
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display errors

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Ensure clean output
ob_start();

// Set error handler to convert PHP errors to exceptions
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

require_once $_SERVER['DOCUMENT_ROOT'] . "/Operaciones/RegistrarUsuariosCoordinadores/db/Usuarios_coordinadores.php";

// Establecer el tipo de contenido como JSON desde el principio
header('Content-Type: application/json');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    die(json_encode(['success' => false, 'message' => 'Método no permitido']));
}

// Obtener los datos enviados
$id = $_POST['id'] ?? null;
$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
// Asegurarnos que el estado sea explícitamente 1 o 0
// Validar campos requeridos
$camposRequeridos = ['id', 'usuario'];
foreach ($camposRequeridos as $campo) {
    if (empty($$campo)) {
        echo json_encode(['success' => false, 'message' => 'El campo ' . ucfirst($campo) . ' es requerido']);
        exit;
    }
}

// Crear instancia del controlador
try {
    // Debug temporal para verificar los valores
    error_log("Debug actualización - ID: $id, Usuario: $usuario");
    
    $usuarios = new Usuarios_coordinadores();
    
    // Verificar si el usuario ya existe (excluyendo el usuario actual)
    if ($usuarios->verificarUsuarioExistente($usuario, $id)) {
        echo json_encode(['success' => false, 'message' => 'Este nombre de usuario ya está en uso, por favor elija otro']);
        exit;
    }

    // Actualizar el usuario


    // Recoger los COPEs seleccionados (asegurar array aunque venga vacío)
    if (isset($_POST['copes'])) {
        if (is_array($_POST['copes'])) {
            $copes = $_POST['copes'];
        } else if ($_POST['copes'] !== '') {
            // Si solo viene un valor, PHP lo puede recibir como string
            $copes = [$_POST['copes']];
        } else {
            $copes = [];
        }
    } else {
        $copes = [];
    }


    $resultado = $usuarios->actualizarUsuarioCoordinador($id, $usuario, $password, $copes);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario actualizado correctamente'
        ]);
    } else {
        throw new Exception('Error al actualizar el usuario');
    }

} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
    // Registrar el error real en los logs del servidor
    error_log("Error en actualizar_usuario_coordinador.php: " . $e->getMessage());
}
?>