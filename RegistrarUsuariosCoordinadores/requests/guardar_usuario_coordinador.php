<?php
// Deshabilitar la salida de errores a la página

// Log errors to a file, no mostrar en pantalla
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');


require_once $_SERVER['DOCUMENT_ROOT'] . "/Operaciones/RegistrarUsuariosCoordinadores/db/Usuarios_coordinadores.php";

// Establecer el tipo de contenido como JSON desde el principio

header('Content-Type: application/json');

// Verificar que sea una petición POST

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener y validar los datos enviados
$usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$copes = isset($_POST['copes']) && is_array($_POST['copes']) ? $_POST['copes'] : [];

// Debug: Log los datos recibidos
error_log("Datos recibidos - Usuario: " . $usuario . ", Password: " . (empty($password) ? "vacío" : "presente") . ", COPEs: " . json_encode($copes));

// Validar campos requeridos

if (empty($usuario) || empty($password)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'El usuario y la contraseña son requeridos']);
    exit;
}

if (empty($copes)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Debe seleccionar al menos un COPE']);
    exit;
}

// Crear instancia del controlador
try {
    $usuarios = new Usuarios_coordinadores();
    
    // Verificar si el usuario ya existe

    if ($usuarios->verificarUsuarioExistente($usuario)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Este nombre de usuario ya está en uso, por favor elija otro']);
        exit;
    }

    // Validar que los COPEs seleccionados estén disponibles
    $copesDisponibles = $usuarios->obtenerCopes();
    $copesDisponiblesIds = array_column($copesDisponibles, 'id');
    

    foreach ($copes as $cope) {
        if (!in_array($cope, $copesDisponiblesIds)) {
            ob_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Uno o más COPEs seleccionados ya no están disponibles'
            ]);
            exit;
        }
    }


    // Registrar el nuevo usuario coordinador con los COPEs
    $resultado = $usuarios->registrarUsuarioCoordinador($usuario, $password, $copes);
    if (!$resultado) {
        throw new Exception('Error al registrar el usuario');
    }

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado correctamente con los COPEs asignados'
    ]);


} catch (PDOException $e) {
    http_response_code(400);
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
    error_log("Error en guardar_usuario_coordinador.php: " . $e->getMessage());
}
?>