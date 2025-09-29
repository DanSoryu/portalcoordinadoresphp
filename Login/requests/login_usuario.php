<?php
// Deshabilitar la salida de errores a la página
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

require_once $_SERVER['DOCUMENT_ROOT'] . "/PortalCoordinadores/Login/db/Auth.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	ob_clean();
	echo json_encode(['success' => false, 'message' => 'Método no permitido']);
	exit;
}

$usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if (empty($usuario) || empty($password)) {
	ob_clean();
	echo json_encode(['success' => false, 'message' => 'El usuario y la contraseña son requeridos']);
	exit;
}

try {
	$auth = new Auth();
	$user = $auth->autenticar($usuario, $password);
	if ($user) {
		// Aquí puedes iniciar sesión PHP si lo deseas
		// session_start();
		// $_SESSION['usuario'] = $user['usuario'];
		ob_clean();
		echo json_encode([
			'success' => true,
			'message' => 'Autenticación exitosa',
			// Puedes personalizar la redirección si lo deseas
			'redirect' => '../Dashboard/Dashboard.php'
		]);
	} else {
		ob_clean();
		echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
	}
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
	error_log("Error en login_usuario.php: " . $e->getMessage());
}
?>