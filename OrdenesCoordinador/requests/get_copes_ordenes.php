<?php
header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'] . '/PortalCoordinadores/cnx/cnx.php');
require_once('../db/Ordenes.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

if (!isset($_POST['idUsuario'])) {
    echo json_encode(['error' => 'No se proporcionó ID de usuario']);
    exit;
}

$idUsuario = $_POST['idUsuario'];
$ordenesObj = new Ordenes();

try {
    // Obtener los COPEs asignados al usuario
    $copesData = $ordenesObj->obtenerCopesCoordinador($idUsuario);
    $copes = [];
    if (!empty($copesData)) {
        foreach ($copesData as $copeRow) {
            if (isset($copeRow['COPE'])) {
                $copes[] = $copeRow['COPE'];
            }
        }
    }

    // Si no hay copes, devolver vacío
    if (empty($copes)) {
        echo json_encode([
            'success' => true,
            'copes' => [],
            'ordenes' => []
        ]);
        exit;
    }

    // Obtener las órdenes por COPE
    $ordenes = $ordenesObj->obtenerOrdenesPorCopes($copes);

    echo json_encode([
        'success' => true,
        'copes' => $copes,
        'ordenes' => $ordenes
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
