<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Configuración de conexiones a bases de datos
    class DatabaseConnections {
        
        public static function conectarTac() {
            try {
                $host = '67.217.246.65';
                $user = 'erpintr1';
                $password = '#k1u3T3f5';
                $database = 'analisis_bd';
                
                $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (Exception $e) {
                throw new Exception("Error conectando a TAC: " . $e->getMessage());
            }
        }
        
        public static function conectarCoordiapp() {
            try {
                $host = '74.208.237.139';
                $user = 'erpintr1';
                $password = '#k1u3T3f5';
                $database = 'erpintr1_erp';
                
                $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (Exception $e) {
                throw new Exception("Error conectando a COORDIAPP: " . $e->getMessage());
            }
        }
    }
    
    // Clase para manejar órdenes TAC
    class OrdenesTACManager {
        
        private $conn_tac;
        private $conn_coordiapp;
        private $idUsuario;
        
        public function __construct($idUsuario = null) {
            $this->conn_tac = DatabaseConnections::conectarTac();
            $this->conn_coordiapp = DatabaseConnections::conectarCoordiapp();
            $this->idUsuario = $idUsuario;
        }
        
        // Función para obtener los COPEs del coordinador
        private function obtenerCopesCoordinador() {
            if (!$this->idUsuario) {
                return array(); // Si no hay usuario, retornar array vacío
            }
            
            try {
                $query = "SELECT c.id, c.COPE 
                         FROM copes c 
                         INNER JOIN coordinador_cope cc ON c.id = cc.FK_Cope 
                         WHERE cc.FK_Coordinador = :idCoordinador 
                         ORDER BY c.COPE";
                
                $stmt = $this->conn_coordiapp->prepare($query);
                $stmt->bindParam(':idCoordinador', $this->idUsuario);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Error obteniendo COPEs del coordinador: " . $e->getMessage());
                return array();
            }
        }
        
        // Función para obtener los nombres de COPEs del coordinador
        private function obtenerNombresCopesCoordinador() {
            $copes = $this->obtenerCopesCoordinador();
            return array_column($copes, 'COPE');
        }
        
        // Función principal para obtener órdenes TAC filtradas por COPEs
        public function obtenerOrdenesTAC($fecha_inicio, $fecha_fin, $estatus = null) {
            // Obtener los nombres de COPEs del coordinador
            $copesCoordinador = $this->obtenerNombresCopesCoordinador();
            
            // Si no hay COPEs asignados, retornar array vacío
            if (empty($copesCoordinador)) {
                error_log("No hay COPEs asignados al coordinador ID: " . $this->idUsuario);
                return array();
            }
            
            // Crear placeholders para los COPEs
            $placeholders = str_repeat('?,', count($copesCoordinador) - 1) . '?';
            
            // Construir la consulta base
            $query = "
                SELECT 
                    Folio_Pisa,
                    TELEFONO,
                    Expediente,
                    Tecnico,
                    NOM_CT,
                    NOM_AREA,
                    NOM_DIVISION,
                    Distrito,
                    TECNOLOGIA,
                    Tipo_tarea,
                    DATE(FECHA_LIQ) as FECHA_LIQ
                FROM qm_tac_prod_bolsa 
                WHERE DATE(FECHA_LIQ) BETWEEN ? AND ?
                AND NOM_CT IN ($placeholders)
            ";
            
            // Agregar filtro de estatus si se proporciona
            if ($estatus && !empty($estatus)) {
                $query .= " AND Calificador_Edo = ?";
            }
            
            $query .= " ORDER BY FECHA_LIQ DESC, NOM_DIVISION, NOM_CT";
            
            $stmt = $this->conn_tac->prepare($query);
            
            // Bind de parámetros
            $stmt->bindParam(1, $fecha_inicio);
            $stmt->bindParam(2, $fecha_fin);
            
            // Bind de los COPEs
            for ($i = 0; $i < count($copesCoordinador); $i++) {
                $stmt->bindParam(3 + $i, $copesCoordinador[$i]);
            }
            
            // Bind del estatus si se proporciona
            if ($estatus && !empty($estatus)) {
                $stmt->bindParam(3 + count($copesCoordinador), $estatus);
            }
            
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log de consulta TAC
            error_log("TAC Query - Fechas: $fecha_inicio a $fecha_fin, COPEs: " . implode(',', $copesCoordinador) . ", Estatus: $estatus, Registros encontrados: " . count($result));
            
            return $result;
        }
    }
    
    // Procesar la solicitud
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : date('Y-m-d');
        $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : $fecha_inicio;
        $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : null;
        
        // Debug: Log de fechas recibidas
        error_log("obtener_ordenes_tac.php - Fechas recibidas: inicio=$fecha_inicio, fin=$fecha_fin, estatus=$estatus");
        
        // Validar formato de fechas
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
            throw new Exception("Formato de fecha inválido");
        }
        
        // Validar que fecha_inicio no sea mayor que fecha_fin
        if ($fecha_inicio > $fecha_fin) {
            throw new Exception("La fecha de inicio no puede ser mayor que la fecha fin");
        }
        
        // Validar que el rango no sea muy amplio (máximo 31 días)
        $inicio = new DateTime($fecha_inicio);
        $fin = new DateTime($fecha_fin);
        $diferencia = $inicio->diff($fin);
        if ($diferencia->days > 31) {
            throw new Exception("El rango de fechas no puede ser mayor a 31 días");
        }
        
        // Obtener el ID del usuario de la sesión
        $idUsuario = isset($_SESSION['idusuarios_coordinadores']) ? $_SESSION['idusuarios_coordinadores'] : null;
        
        $ordenesManager = new OrdenesTACManager($idUsuario);
        $ordenes = $ordenesManager->obtenerOrdenesTAC($fecha_inicio, $fecha_fin, $estatus);
        
        echo json_encode(array(
            'success' => true,
            'data' => $ordenes,
            'message' => 'Órdenes TAC obtenidas correctamente',
            'total' => count($ordenes)
        ));
    } else {
        throw new Exception("Método no permitido");
    }
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage(),
        'data' => array(),
        'total' => 0
    ));
}
?>

