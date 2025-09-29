<?php
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'] . '/cnx/cnx.php');
require_once('../db/Usuarios_coordinadores.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Verificar que se recibió el ID del usuario
if (!isset($_POST['idUsuario'])) {
    echo json_encode(['error' => 'No se proporcionó ID de usuario']);
    exit;
}

$idUsuario = $_POST['idUsuario'];
$usuarios = new Usuarios_coordinadores();

try {
    // Obtener los COPEs asignados al usuario
    $copesAsignados = $usuarios->obtenerCopesCoordinador($idUsuario);

    // Obtener solo los COPEs disponibles para edición (no asignados o asignados a este usuario)
    $copesDisponibles = $usuarios->obtenerCopesDisponiblesParaEdicion($idUsuario);

    // Si no hay COPEs, devolver arrays vacíos
    if ($copesAsignados === null) {
        $copesAsignados = [];
    }
    if ($copesDisponibles === null) {
        $copesDisponibles = [];
    }

    echo json_encode([
        'success' => true,
        'copesAsignados' => $copesAsignados,
        'copesDisponibles' => $copesDisponibles
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>