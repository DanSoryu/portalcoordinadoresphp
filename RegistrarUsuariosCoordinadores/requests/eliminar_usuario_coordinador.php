<?php
// Deshabilitar la salida de errores a la página
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Asegurarse de que no haya salida antes de los headers
ob_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/Operaciones/RegistrarUsuariosCoordinadores/db/Usuarios_coordinadores.php";

// Establecer el tipo de contenido como JSON desde el principio
header('Content-Type: application/json');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    die(json_encode(['success' => false, 'message' => 'Método no permitido']));
}

// Obtener el ID del usuario a eliminar
$idUsuario = $_POST['idUsuario'] ?? null;

// Validar el ID
if (empty($idUsuario)) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
    exit;
}

// Crear instancia del controlador
try {
    $usuarios = new Usuarios_coordinadores();
    
    // Intentar eliminar el usuario
    $resultado = $usuarios->eliminarUsuarioCoordinador($idUsuario);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    } else {
        throw new Exception('No se pudo eliminar el usuario o no existe');
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
        'message' => $e->getMessage() ?: 'Error interno del servidor'
    ]);
    // Registrar el error real en los logs del servidor
    error_log("Error en eliminar_usuario_coordinador.php: " . $e->getMessage());
}
