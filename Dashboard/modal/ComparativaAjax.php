<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("ComparativaData.php");

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener la acción solicitada
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Crear instancia de la clase de comparativa
$comparativa = new ComparativaTacCoordiapp();

try {
    switch ($action) {
        case 'getDivisiones':
            $divisiones = $comparativa->obtenerDivisiones();
            echo json_encode([
                'success' => true,
                'divisiones' => $divisiones
            ]);
            break;
            
        case 'getComparativa':
            // Validar fecha
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
            if (!$fecha) {
                throw new Exception('Fecha no proporcionada');
            }
            
            // Obtener división (opcional)
            $division = isset($_POST['division']) ? $_POST['division'] : null;
            
            // Obtener los datos de la comparativa
            $resultados = $comparativa->obtenerComparativa($fecha, $division);
            
            echo json_encode([
                'success' => true,
                'resultados' => $resultados
            ]);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
