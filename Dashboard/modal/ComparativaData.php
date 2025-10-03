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
    
    public function obtenerDatosComparativos($periodo, $valorPeriodo, $fechaInicio, $fechaFin) {
        try {
            if (!$fechaInicio || !$fechaFin) {
                throw new Exception("Se requieren fechas de inicio y fin");
            }

            // Preparar las variables para la consulta según el periodo
            switch($periodo) {
                case 'semana':
                    $groupBy = "YEARWEEK(FECHA_LIQ, 1)";
                    $groupBy2 = "YEARWEEK(Fecha_Coordiapp, 1)";
                    $dateFormat = "STR_TO_DATE(CONCAT(YEARWEEK(FECHA_LIQ, 1), ' Monday'), '%X%V %W')";
                    $dateFormat2 = "STR_TO_DATE(CONCAT(YEARWEEK(Fecha_Coordiapp, 1), ' Monday'), '%X%V %W')";
                    break;
                case 'mes':
                    $groupBy = "DATE_FORMAT(FECHA_LIQ, '%Y-%m')";
                    $groupBy2= "DATE_FORMAT(Fecha_Coordiapp, '%Y-%m')";
                    $dateFormat = "DATE_FORMAT(FECHA_LIQ, '%Y-%m-01')";
                    $dateFormat2 = "DATE_FORMAT(Fecha_Coordiapp, '%Y-%m-01')";
                    break;
                case 'año':
                    $groupBy = "YEAR(FECHA_LIQ)";
                    $groupBy2 = "YEAR(Fecha_Coordiapp)";
                    $dateFormat = "DATE_FORMAT(FECHA_LIQ, '%Y-01-01')";
                    $dateFormat2 = "DATE_FORMAT(Fecha_Coordiapp, '%Y-01-01')";
                    break;
                default:
                    throw new Exception("Periodo no válido");
            }
            
            // Agregar condición de fechas
            $whereFechas = "WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";
            $whereFechas2 = "WHERE Fecha_Coordiapp BETWEEN :fechaInicio AND :fechaFin";
            
            // Construir consulta base para TAC
            $sqlTac = "SELECT 
                $dateFormat as FECHA_LIQ,
                COUNT(*) as total_registros,
                SUM(CASE WHEN Calificador_Edo = 'COMPLETADA' THEN 1 ELSE 0 END) as liquidadas,
                $groupBy as periodo_grupo
            FROM qm_tac_prod_bolsa
            $whereFechas
            GROUP BY periodo_grupo, FECHA_LIQ
            ORDER BY FECHA_LIQ";
            
            // Construir consulta base para Coordiapp
            $sqlCoordiapp = "SELECT 
                $dateFormat2 as Fecha_Coordiapp,
                COUNT(*) as total_registros,
                SUM(CASE WHEN Estatus_Orden = 'COMPLETADA' THEN 1 ELSE 0 END) as liquidadas,
                $groupBy2 as periodo_grupo  
            FROM tecnico_instalaciones_coordiapp
            $whereFechas2
            GROUP BY periodo_grupo, Fecha_Coordiapp
            ORDER BY Fecha_Coordiapp";

            // Ejecutar consultas con parámetros
            $stmtTac = $this->conn_tac->prepare($sqlTac);
            $stmtTac->bindParam(':fechaInicio', $fechaInicio);
            $stmtTac->bindParam(':fechaFin', $fechaFin);
            $stmtTac->execute();

            $stmtCoordiapp = $this->conn_coordiapp->prepare($sqlCoordiapp);
            $stmtCoordiapp->bindParam(':fechaInicio', $fechaInicio);
            $stmtCoordiapp->bindParam(':fechaFin', $fechaFin);
            $stmtCoordiapp->execute();
            
            // Procesar resultados
            $datosTac = array();
            while($row = $stmtTac->fetch(PDO::FETCH_ASSOC)) {
                $fecha = $row['FECHA_LIQ'];
                $datosTac[$fecha] = intval($row['total_registros']);
            }
            
            $datosCoordiapp = array();
            while($row = $stmtCoordiapp->fetch(PDO::FETCH_ASSOC)) {
                $fecha = $row['Fecha_Coordiapp'];
                $datosCoordiapp[$fecha] = intval($row['total_registros']);
            }
            
            // Combinar y formatear resultados
            $resultado = array();
            $todasLasFechas = array_unique(array_merge(array_keys($datosTac), array_keys($datosCoordiapp)));
            sort($todasLasFechas);
            
            foreach($todasLasFechas as $fecha) {
                $resultado[] = array(
                    "fecha" => $fecha,
                    "registros_tac" => isset($datosTac[$fecha]) ? $datosTac[$fecha] : 0,
                    "registros_coordiapp" => isset($datosCoordiapp[$fecha]) ? $datosCoordiapp[$fecha] : 0,
                    "diferencia" => (isset($datosCoordiapp[$fecha]) ? $datosCoordiapp[$fecha] : 0) - 
                                  (isset($datosTac[$fecha]) ? $datosTac[$fecha] : 0),
                    "cumplimiento" => isset($datosTac[$fecha]) && $datosTac[$fecha] > 0 ? 
                        round(((isset($datosCoordiapp[$fecha]) ? $datosCoordiapp[$fecha] : 0) / $datosTac[$fecha]) * 100, 2) : 0
                );
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            // Registrar y devolver el mensaje de error para depuración en frontend
            $msg = $e->getMessage();
            error_log("Error obteniendo datos comparativos: " . $msg);
            return array("error" => "Error al obtener los datos comparativos: " . $msg);
        }
    }

    public function generarComparativa($fecha, $division = null) {
        try {
            // Preparar condición de división
            $divisionCondition = $division ? "AND t.NOM_DIVISION = :division" : "";
            $divisionCondition2 = $division ? "AND c.Division = :division" : "";
            
            // Consulta para TAC
            $queryTac = "
                SELECT 
                    t.NOM_CT as ct,
                    t.NOM_DIVISION as division,
                    t.Expediente as expediente,
                    t.Tecnico as nombre,
                    COUNT(t.id) as tac_count,
                    SUM(CASE WHEN t.Calificador_Edo = 'COMPLETADA' THEN 1 ELSE 0 END) as tac_liquidaciones
                FROM qm_tac_prod_bolsa t
                WHERE DATE(t.FECHA_LIQ) = :fecha
                $divisionCondition
                GROUP BY t.NOM_CT, t.NOM_DIVISION, t.Expediente, t.Tecnico
            ";
            
            // Consulta para COORDIAPP
            $queryCoordiapp = "
                SELECT 
                    c.COPE as ct,
                    c.Division as division,
                    c.NExpediente as expediente,
                    c.Nombre_T as nombre,
                    c.Estatus_Orden as coordiapp_status
                FROM View_Detalle_Coordiapp_Completadas c
                WHERE DATE(c.Fecha_Coordiapp) = :fecha
                $divisionCondition2
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
    $comparativa = new ComparativaTacCoordiapp();

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'datosComparativos') {
        $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'mes';
        $valor = isset($_GET['valor']) ? $_GET['valor'] : date('Y-m');
        $fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : date('Y-m-d', strtotime('-30 days'));
        $fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : date('Y-m-d');
        
        $resultados = $comparativa->obtenerDatosComparativos($periodo, $valor, $fechaInicio, $fechaFin);
        echo json_encode($resultados);
    } 
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
        $division = isset($_POST['division']) && !empty($_POST['division']) ? $_POST['division'] : null;
        
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
