<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../cnx/cnx.php';
require_once('./Ordenes.php');

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
            if (isset($copeRow['id'])) {
                $copes[] = $copeRow['id'];
            }
        }
    }

    // Verificar si es una petición de exportación
    $isExport = isset($_POST['export']) && $_POST['export'] === 'true';

    // Si la petición proviene de DataTables (server-side)
    $isDataTables = isset($_POST['draw']) && isset($_POST['start']) && isset($_POST['length']);

    if ($isDataTables || $isExport) {
        // Sin COPEs asignados => regresar estructura vacía
        if (empty($copes)) {
            echo json_encode([
                'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'debug' => [
                    'motivo' => 'sin_copes_para_usuario',
                    'idUsuario' => $idUsuario
                ]
            ]);
            exit;
        }

        $length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
        $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        $page = $length > 0 ? intval(floor($start / $length)) + 1 : 1;

        // Filtros opcionales
        $fechaInicio = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
        $fechaFin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
        $estatus = isset($_POST['estatus']) && $_POST['estatus'] !== '' ? $_POST['estatus'] : null;
        $copeFiltro = isset($_POST['cope']) && $_POST['cope'] !== '' ? $_POST['cope'] : null;

        $copesFiltrados = $copes;
        if ($copeFiltro !== null) {
            $copeFiltroInt = (int)$copeFiltro;
            if (in_array($copeFiltroInt, array_map('intval', $copes), true)) {
                $copesFiltrados = [$copeFiltroInt];
            } else {
                echo json_encode([
                    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ]);
                exit;
            }
        }

        // Para exportación, no aplicamos paginación
        if ($isExport) {
            $resultado = $ordenesObj->obtenerOrdenesPorCopes($copesFiltrados, $fechaInicio, $fechaFin, $estatus);
        } else {
            $resultado = $ordenesObj->obtenerOrdenesPorCopes($copesFiltrados, $fechaInicio, $fechaFin, $estatus, $length, $page);
        }

        if (isset($resultado['error']) && $resultado['error'] === true) {
            echo json_encode([
                'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $resultado['message']
            ]);
            exit;
        }

        echo json_encode([
            'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            'recordsTotal' => isset($resultado['total']) ? (int)$resultado['total'] : 0,
            'recordsFiltered' => isset($resultado['total']) ? (int)$resultado['total'] : 0,
            'data' => isset($resultado['data']) ? $resultado['data'] : [],
            'debug' => [
                'params_recibidos' => [
                    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                    'start' => $start,
                    'length' => $length,
                    'page' => $page,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'estatus' => $estatus,
                    'cope' => $copeFiltro,
                    'copes_filtrados' => $copesFiltrados,
                    'is_export' => $isExport
                ],
                'paginacion' => [
                    'current_page' => $resultado['current_page'] ?? null,
                    'per_page' => $resultado['per_page'] ?? null,
                    'total' => $resultado['total'] ?? null,
                    'last_page' => $resultado['last_page'] ?? null
                ]
            ]
        ]);
        exit;
    }

    // Caso legacy: respuesta simple (no DataTables)
    if (empty($copes)) {
        echo json_encode([
            'success' => true,
            'copes' => [],
            'ordenes' => []
        ]);
        exit;
    }

    $ordenes = $ordenesObj->obtenerOrdenesPorCopes($copes);

    echo json_encode([
        'success' => true,
        'copes' => $copes,
        'ordenes' => $ordenes
    ]);
} catch (Exception $e) {
    error_log("Error en obtener_ordenes_coordinador.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
