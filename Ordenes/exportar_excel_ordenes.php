<?php
header('Content-Type: application/json');
require_once($_SERVER['DOCUMENT_ROOT'] . '/PortalCoordinadores/cnx/cnx.php');
require_once('./Ordenes.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$idUsuario = isset($_POST['idUsuario']) ? $_POST['idUsuario'] : null;
$fechaInicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
$fechaFin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
// Normalizar estatus vacío a null para no filtrar por cadena vacía
$estatus = (isset($_POST['estatus']) && $_POST['estatus'] !== '') ? $_POST['estatus'] : null;
// Normalizar cope vacío a null
$cope = (isset($_POST['cope']) && $_POST['cope'] !== '') ? $_POST['cope'] : null;

if (!$idUsuario) {
    echo json_encode(['error' => 'No se proporcionó ID de usuario']);
    exit;
}

$ordenesObj = new Ordenes();
$copesData = $ordenesObj->obtenerCopesCoordinador($idUsuario);
$copes = [];
if (!empty($copesData)) {
    foreach ($copesData as $copeRow) {
        if (isset($copeRow['id'])) {
            $copes[] = $copeRow['id'];
        }
    }
}
// Si se seleccionó un COPE específico, filtrar solo ese
if ($cope !== null) {
    $copes = [$cope];
}

if (empty($copes)) {
    echo json_encode([
        'success' => true,
        'data' => []
    ]);
    exit;
}

// Obtener todas las órdenes sin paginación
$ordenes = $ordenesObj->obtenerOrdenesPorCopes($copes, $fechaInicio, $fechaFin, $estatus, 1000000, 1); // 1 millón por si acaso
$data = isset($ordenes['data']) ? $ordenes['data'] : [];

echo json_encode([
    'success' => true,
    'data' => $data
]);
