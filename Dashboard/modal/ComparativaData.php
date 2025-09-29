<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class DatabaseConnections {
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
}

class ComparativaTacCoordiapp {
    private $conn_coordiapp;
    private $conn_tac;
    
    public function __construct() {
        $this->conn_coordiapp = DatabaseConnections::conectarCoordiapp();
        $this->conn_tac = DatabaseConnections::conectarTac();
    }
    
    public function obtenerDivisiones() {
        try {
            $query = "SELECT DISTINCT division FROM coordinador_tac ORDER BY division";
            $stmt = $this->conn_tac->prepare($query);
            $stmt->execute();
            $divisiones = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $divisiones ?: [];
        } catch (PDOException $e) {
            error_log("Error obteniendo divisiones: " . $e->getMessage());
            return [];
        }
    }

    public function generarComparativa($fecha, $division = null) {
        try {
            // Preparar condición de división
            $divisionCondition = $division ? "AND t.division = :division" : "";
            
            // Consulta para TAC
            $queryTac = "
                SELECT 
                    t.cope as ct,
                    t.division,
                    t.expediente,
                    t.nombre_tecnico as nombre,
                    COUNT(t.id) as tac_count,
                    SUM(CASE WHEN t.status = 'LIQUIDADA' THEN 1 ELSE 0 END) as tac_liquidaciones
                FROM coordinador_tac t
                WHERE DATE(t.fecha) = :fecha
                $divisionCondition
                GROUP BY t.cope, t.division, t.expediente, t.nombre_tecnico
            ";
            
            // Consulta para COORDIAPP
            $queryCoordiapp = "
                SELECT 
                    c.cope as ct,
                    c.division,
                    c.expediente,
                    c.nombre_tecnico as nombre,
                    c.status as coordiapp_status
                FROM coordiapp c
                WHERE DATE(c.fecha) = :fecha
                $divisionCondition
            ";
            
            // Ejecutar consulta TAC
            $stmtTac = $this->conn_tac->prepare($queryTac);
            $stmtTac->bindParam(':fecha', $fecha);
            if ($division) {
                $stmtTac->bindParam(':division', $division);
            }
            $stmtTac->execute();
            $resultadosTac = $stmtTac->fetchAll(PDO::FETCH_ASSOC);
            
            // Ejecutar consulta COORDIAPP
            $stmtCoordiapp = $this->conn_coordiapp->prepare($queryCoordiapp);
            $stmtCoordiapp->bindParam(':fecha', $fecha);
            if ($division) {
                $stmtCoordiapp->bindParam(':division', $division);
            }
            $stmtCoordiapp->execute();
            $resultadosCoordiapp = $stmtCoordiapp->fetchAll(PDO::FETCH_ASSOC);
            
            // Procesar resultados
            $registros = [];
            $totales = [
                'tac' => 0,
                'coordiapp' => 0,
                'diferencias' => 0,
                'coincidencias' => 0,
                'porcentaje' => 0
            ];
            
            // Procesar registros de TAC
            foreach ($resultadosTac as $tac) {
                $key = $tac['expediente'];
                $registros[$key] = [
                    'ct' => $tac['ct'],
                    'division' => $tac['division'],
                    'expediente' => $tac['expediente'],
                    'nombre' => $tac['nombre'],
                    'tac_count' => $tac['tac_count'],
                    'tac_liquidaciones' => $tac['tac_liquidaciones'],
                    'coordiapp_status' => '-',
                    'estado' => 'solo_tac'
                ];
                $totales['tac']++;
            }
            
            // Procesar registros de COORDIAPP y actualizar estados
            foreach ($resultadosCoordiapp as $coordiapp) {
                $key = $coordiapp['expediente'];
                $totales['coordiapp']++;
                
                if (isset($registros[$key])) {
                    // Existe en ambos sistemas
                    $registros[$key]['coordiapp_status'] = $coordiapp['coordiapp_status'];
                    $registros[$key]['estado'] = 'coincide';
                    $totales['coincidencias']++;
                } else {
                    // Solo existe en COORDIAPP
                    $registros[$key] = [
                        'ct' => $coordiapp['ct'],
                        'division' => $coordiapp['division'],
                        'expediente' => $coordiapp['expediente'],
                        'nombre' => $coordiapp['nombre'],
                        'tac_count' => 0,
                        'tac_liquidaciones' => 0,
                        'coordiapp_status' => $coordiapp['coordiapp_status'],
                        'estado' => 'solo_coordiapp'
                    ];
                }
            }
            
            // Calcular diferencias y porcentaje
            $totales['diferencias'] = count($registros) - $totales['coincidencias'];
            $total = max($totales['tac'], $totales['coordiapp']);
            $totales['porcentaje'] = $total > 0 ? round(($totales['coincidencias'] / $total) * 100, 2) : 0;
            
            return [
                'totales' => $totales,
                'registros' => array_values($registros)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en comparativa: " . $e->getMessage());
            return [
                'totales' => [
                    'tac' => 0,
                    'coordiapp' => 0,
                    'diferencias' => 0,
                    'coincidencias' => 0,
                    'porcentaje' => 0
                ],
                'registros' => []
            ];
        }
    }
}

// Procesar la solicitud
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
        $division = isset($_POST['division']) && !empty($_POST['division']) ? $_POST['division'] : null;
        
        $comparativa = new ComparativaTacCoordiapp();
        $resultados = $comparativa->generarComparativa($fecha, $division);
        
        echo json_encode($resultados);
    } else {
        throw new Exception("Método no permitido");
    }
} catch (Exception $e) {
    error_log("Error en ComparativaData.php: " . $e->getMessage());
    echo json_encode([
        'totales' => [
            'tac' => 0,
            'coordiapp' => 0,
            'diferencias' => 0,
            'coincidencias' => 0,
            'porcentaje' => 0
        ],
        'registros' => []
    ]);
}
?>
