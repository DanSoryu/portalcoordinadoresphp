<?php
require_once("ClassMateriales.php");

// Verificar si se recibe una acción por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $materiales = new Materiales();
    $response = array('success' => false, 'message' => '');
    
    switch ($_POST['action']) {
        case 'eliminar_carso':
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $id = intval($_POST['id']);
                if ($materiales->eliminarRegistroCarso($id)) {
                    $response['success'] = true;
                    $response['message'] = 'Registro eliminado correctamente';
                } else {
                    $response['message'] = 'Error al eliminar el registro';
                }
            } else {
                $response['message'] = 'ID inválido';
            }
            break;
            
        default:
            $response['message'] = 'Acción no válida';
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>