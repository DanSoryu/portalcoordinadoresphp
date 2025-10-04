<?php
session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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
    
    /**
     * Obtener los COPEs asignados a un coordinador
     * @param int $idCoordinador ID del coordinador
     * @return array Array con 'ids' y 'nombres' de COPEs
     */
    public function obtenerCopesCoordinador($idCoordinador) {
        try {
            $query = "SELECT c.id, c.COPE 
                     FROM copes c 
                     INNER JOIN coordinador_cope cc ON c.id = cc.FK_Cope 
                     WHERE cc.FK_Coordinador = :idCoordinador 
                     ORDER BY c.COPE";
            
            $stmt = $this->conn_coordiapp->prepare($query);
            $stmt->bindParam(':idCoordinador', $idCoordinador, PDO::PARAM_INT);
            $stmt->execute();
            
            $copesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Extraer IDs y nombres
            $copesIds = [];
            $copesNombres = [];
            
            if (!empty($copesData)) {
                foreach ($copesData as $copeRow) {
                    if (isset($copeRow['id'])) {
                        $copesIds[] = $copeRow['id'];
                    }
                    if (isset($copeRow['COPE'])) {
                        $copesNombres[] = $copeRow['COPE'];
                    }
                }
            }
            
            $resultado = [
                'ids' => $copesIds,
                'nombres' => $copesNombres
            ];
            
            error_log("COPEs obtenidos para coordinador $idCoordinador - IDs: " . json_encode($copesIds) . ", Nombres: " . json_encode($copesNombres));
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en obtenerCopesCoordinador: " . $e->getMessage());
            return ['ids' => [], 'nombres' => []];
        }
    }
    
    public function obtenerDatosComparativos($periodo, $valorPeriodo, $fechaInicio, $fechaFin, $copesData = []) {
        try {
            error_log("Iniciando obtenerDatosComparativos - Periodo: $periodo, Valor: $valorPeriodo, FechaInicio: $fechaInicio, FechaFin: $fechaFin");
            
            // Extraer IDs y nombres de COPEs
            $copesIds = isset($copesData['ids']) ? $copesData['ids'] : [];
            $copesNombres = isset($copesData['nombres']) ? $copesData['nombres'] : [];
            
            error_log("COPEs recibidos - IDs: " . json_encode($copesIds) . ", Nombres: " . json_encode($copesNombres));
            
            // Ajustar fechas según el periodo seleccionado
            $fechasAjustadas = $this->ajustarFechasPorPeriodo($periodo, $valorPeriodo, $fechaInicio, $fechaFin);
            $fechaInicio = $fechasAjustadas['fechaInicio'];
            $fechaFin = $fechasAjustadas['fechaFin'];
            
            error_log("Fechas ajustadas según periodo - FechaInicio: $fechaInicio, FechaFin: $fechaFin");
            
            if (!$fechaInicio || !$fechaFin) {
                error_log("Error: Fechas de inicio o fin no proporcionadas");
                throw new Exception("Se requieren fechas de inicio y fin");
            }

            // Preparar las variables para la consulta según el periodo
            switch($periodo) {
                case 'semana':
                    // Para semana: agrupar por día específico
                    $groupBy = "DATE(FECHA_LIQ)";
                    $groupBy2 = "DATE(Fecha_Coordiapp)";
                    $dateFormat = "DATE(FECHA_LIQ)";
                    $dateFormat2 = "DATE(Fecha_Coordiapp)";
                    break;
                case 'mes':
                    // Para mes: agrupar por semana del mes
                    $groupBy = "YEARWEEK(FECHA_LIQ, 1)";
                    $groupBy2 = "YEARWEEK(Fecha_Coordiapp, 1)";
                    $dateFormat = "DATE(DATE_SUB(FECHA_LIQ, INTERVAL WEEKDAY(FECHA_LIQ) DAY))";
                    $dateFormat2 = "DATE(DATE_SUB(Fecha_Coordiapp, INTERVAL WEEKDAY(Fecha_Coordiapp) DAY))";
                    break;
                case 'año':
                    // Para año: agrupar por mes
                    $groupBy = "DATE_FORMAT(FECHA_LIQ, '%Y-%m')";
                    $groupBy2 = "DATE_FORMAT(Fecha_Coordiapp, '%Y-%m')";
                    $dateFormat = "DATE_FORMAT(FECHA_LIQ, '%Y-%m-01')";
                    $dateFormat2 = "DATE_FORMAT(Fecha_Coordiapp, '%Y-%m-01')";
                    break;
                default:
                    throw new Exception("Periodo no válido");
            }
            
            // Agregar condición de fechas
            $whereFechas = "WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";
            $whereFechas2 = "WHERE Fecha_Coordiapp BETWEEN :fechaInicio AND :fechaFin";
            
            // Agregar filtro de COPEs para TAC usando nombres
            $copesNombresCondition = "";
            $copesNombresPlaceholders = [];
            if (!empty($copesNombres) && is_array($copesNombres)) {
                $placeholders = [];
                foreach ($copesNombres as $index => $copeNombre) {
                    $placeholders[] = ":copeNombre" . $index;
                }
                $copesNombresCondition = " AND NOM_CT IN (" . implode(',', $placeholders) . ")";
                $copesNombresPlaceholders = $placeholders;
                error_log("Filtro de COPEs (nombres) aplicado para TAC: " . json_encode($copesNombres));
            } else {
                error_log("No se aplicó filtro de COPEs para TAC (array vacío)");
            }
            
            // Agregar filtro de COPEs para COORDIAPP usando IDs
            $copesIdsCondition = "";
            $copesIdsPlaceholders = [];
            if (!empty($copesIds) && is_array($copesIds)) {
                $placeholders = [];
                foreach ($copesIds as $index => $copeId) {
                    $placeholders[] = ":copeId" . $index;
                }
                $copesIdsCondition = " AND FK_Cope IN (" . implode(',', $placeholders) . ")";
                $copesIdsPlaceholders = $placeholders;
                error_log("Filtro de COPEs (IDs) aplicado para COORDIAPP: " . json_encode($copesIds));
            } else {
                error_log("No se aplicó filtro de COPEs para COORDIAPP (array vacío)");
            }
            
            $whereFechas .= $copesNombresCondition;
            $whereFechas2 .= $copesIdsCondition;
            
            // Construir consulta base para TAC
            $sqlTac = "SELECT 
                $dateFormat as FECHA_LIQ,
                COUNT(*) as total_registros,
                SUM(CASE WHEN Calificador_Edo = 'COMPLETADA' THEN 1 ELSE 0 END) as liquidadas
            FROM qm_tac_prod_bolsa
            $whereFechas
            GROUP BY $groupBy
            ORDER BY FECHA_LIQ";
            
            error_log("SQL TAC: " . $sqlTac);
            
            // Construir consulta base para Coordiapp
            $sqlCoordiapp = "SELECT 
                $dateFormat2 as Fecha_Coordiapp,
                COUNT(*) as total_registros,
                SUM(CASE WHEN Estatus_Orden = 'COMPLETADA' THEN 1 ELSE 0 END) as liquidadas
            FROM tecnico_instalaciones_coordiapp
            $whereFechas2
            GROUP BY $groupBy2
            ORDER BY Fecha_Coordiapp";
            
            error_log("SQL COORDIAPP: " . $sqlCoordiapp);

            // Ejecutar consultas con parámetros
            try {
                $stmtTac = $this->conn_tac->prepare($sqlTac);
                $stmtTac->bindParam(':fechaInicio', $fechaInicio);
                $stmtTac->bindParam(':fechaFin', $fechaFin);
                
                // Bind de los nombres de COPEs para TAC
                if (!empty($copesNombresPlaceholders)) {
                    foreach ($copesNombres as $index => $copeNombre) {
                        $stmtTac->bindValue(':copeNombre' . $index, $copeNombre, PDO::PARAM_STR);
                    }
                }
                
                $stmtTac->execute();
                error_log("Consulta TAC ejecutada correctamente");
            } catch (PDOException $e) {
                error_log("Error al ejecutar consulta TAC: " . $e->getMessage());
                error_log("Parámetros: fechaInicio=$fechaInicio, fechaFin=$fechaFin, copesNombres=" . json_encode($copesNombres));
                throw new Exception("Error en consulta TAC: " . $e->getMessage());
            }

            try {
                $stmtCoordiapp = $this->conn_coordiapp->prepare($sqlCoordiapp);
                $stmtCoordiapp->bindParam(':fechaInicio', $fechaInicio);
                $stmtCoordiapp->bindParam(':fechaFin', $fechaFin);
                
                // Bind de los IDs de COPEs para COORDIAPP
                if (!empty($copesIdsPlaceholders)) {
                    foreach ($copesIds as $index => $copeId) {
                        $stmtCoordiapp->bindValue(':copeId' . $index, $copeId, PDO::PARAM_INT);
                    }
                }
                
                $stmtCoordiapp->execute();
                error_log("Consulta COORDIAPP ejecutada correctamente");
            } catch (PDOException $e) {
                error_log("Error al ejecutar consulta COORDIAPP: " . $e->getMessage());
                error_log("Parámetros: fechaInicio=$fechaInicio, fechaFin=$fechaFin, copesIds=" . json_encode($copesIds));
                throw new Exception("Error en consulta COORDIAPP: " . $e->getMessage());
            }
            
            // Procesar resultados
            $datosTac = array();
            $countTac = 0;
            while($row = $stmtTac->fetch(PDO::FETCH_ASSOC)) {
                $fecha = $row['FECHA_LIQ'];
                $datosTac[$fecha] = intval($row['total_registros']);
                $countTac++;
            }
            error_log("Registros TAC recuperados: $countTac");
            error_log("Datos TAC: " . json_encode($datosTac));
            
            $datosCoordiapp = array();
            $countCoordiapp = 0;
            while($row = $stmtCoordiapp->fetch(PDO::FETCH_ASSOC)) {
                $fecha = $row['Fecha_Coordiapp'];
                $datosCoordiapp[$fecha] = intval($row['total_registros']);
                $countCoordiapp++;
            }
            error_log("Registros COORDIAPP recuperados: $countCoordiapp");
            error_log("Datos COORDIAPP: " . json_encode($datosCoordiapp));
            
            // Combinar y formatear resultados
            $resultado = array();
            $todasLasFechas = array_unique(array_merge(array_keys($datosTac), array_keys($datosCoordiapp)));
            sort($todasLasFechas);
            
            error_log("Total fechas combinadas: " . count($todasLasFechas));
            
            foreach($todasLasFechas as $fecha) {
                $registrosTac = isset($datosTac[$fecha]) ? $datosTac[$fecha] : 0;
                $registrosCoord = isset($datosCoordiapp[$fecha]) ? $datosCoordiapp[$fecha] : 0;
                $diferencia = $registrosCoord - $registrosTac;
                $cumplimiento = $registrosTac > 0 ? round(($registrosCoord / $registrosTac) * 100, 2) : 0;
                
                $resultado[] = array(
                    "fecha" => $fecha,
                    "registros_tac" => $registrosTac,
                    "registros_coordiapp" => $registrosCoord,
                    "diferencia" => $diferencia,
                    "cumplimiento" => $cumplimiento
                );
            }
            
            error_log("Resultado final de obtenerDatosComparativos: " . json_encode($resultado));
            return $resultado;
            
        } catch (Exception $e) {
            // Registrar y devolver el mensaje de error para depuración en frontend
            $msg = $e->getMessage();
            error_log("Error obteniendo datos comparativos: " . $msg);
            return array("error" => "Error al obtener los datos comparativos: " . $msg);
        }
    }

    /**
     * Ajusta las fechas de inicio y fin según el periodo seleccionado
     * @param string $periodo Tipo de periodo (año, mes, semana)
     * @param string $valorPeriodo El valor específico del periodo seleccionado
     * @param string $fechaInicio Fecha de inicio (YYYY-MM-DD)
     * @param string $fechaFin Fecha de fin (YYYY-MM-DD)
     * @return array Fechas ajustadas con hora
     */
    private function ajustarFechasPorPeriodo($periodo, $valorPeriodo, $fechaInicio, $fechaFin) {
        error_log("Ajustando fechas para periodo: $periodo, valor: $valorPeriodo");
        
        $fechaActual = date('Y-m-d');
        $anioActual = date('Y');
        
        switch($periodo) {
            case 'año':
                $anio = !empty($valorPeriodo) ? $valorPeriodo : $anioActual;
                $fechaInicioAjustada = "$anio-01-01 00:00:00";
                
                // Si es el año actual, usar la fecha actual como fin, sino el 31 de diciembre
                $fechaFinAjustada = ($anio == $anioActual) ? 
                    date('Y-m-d') . ' 23:59:59' : 
                    "$anio-12-31 23:59:59";
                
                error_log("Ajuste por AÑO - Inicio: $fechaInicioAjustada, Fin: $fechaFinAjustada");
                break;
                
            case 'mes':
                // Si valorPeriodo está en formato YYYY-MM
                if (!empty($valorPeriodo) && preg_match('/^\d{4}-\d{2}$/', $valorPeriodo)) {
                    $anioMes = $valorPeriodo;
                    list($anio, $mes) = explode('-', $anioMes);
                } else {
                    // Usar el mes actual del año actual
                    $anio = date('Y');
                    $mes = date('m');
                    $anioMes = "$anio-$mes";
                }
                
                // Primer día del mes
                $fechaInicioAjustada = "$anioMes-01 00:00:00";
                
                // Último día del mes
                $ultimoDiaMes = date('t', strtotime($anioMes . '-01'));
                $fechaFinAjustada = "$anioMes-$ultimoDiaMes 23:59:59";
                
                error_log("Ajuste por MES - Inicio: $fechaInicioAjustada, Fin: $fechaFinAjustada");
                break;
                
            case 'semana':
                // Si valorPeriodo está en formato YYYY-Wnn (año-semana)
                if (!empty($valorPeriodo) && preg_match('/^\d{4}-W\d{2}$/', $valorPeriodo)) {
                    list($anio, $semana) = explode('-W', $valorPeriodo);
                } else {
                    // Usar la semana actual
                    $anio = date('Y');
                    $semana = date('W');
                }
                
                // Calcular el primer día de la semana (lunes)
                $primerDiaSemana = new DateTime();
                $primerDiaSemana->setISODate($anio, $semana, 1); // 1 = lunes
                $fechaInicioAjustada = $primerDiaSemana->format('Y-m-d') . ' 00:00:00';
                
                // Calcular el último día de la semana (domingo)
                $ultimoDiaSemana = clone $primerDiaSemana;
                $ultimoDiaSemana->modify('+6 days'); // +6 días = domingo
                $fechaFinAjustada = $ultimoDiaSemana->format('Y-m-d') . ' 23:59:59';
                
                error_log("Ajuste por SEMANA - Inicio: $fechaInicioAjustada, Fin: $fechaFinAjustada");
                break;
                
            default:
                // Si no se reconoce el periodo o no hay valor, usar las fechas proporcionadas
                $fechaInicioAjustada = $fechaInicio . ' 00:00:00';
                $fechaFinAjustada = $fechaFin . ' 23:59:59';
                error_log("Usando fechas proporcionadas - Inicio: $fechaInicioAjustada, Fin: $fechaFinAjustada");
        }
        
        return [
            'fechaInicio' => $fechaInicioAjustada,
            'fechaFin' => $fechaFinAjustada
        ];
    }
    
    public function generarComparativa($fecha, $copesData = []) {
        try {
            error_log("Iniciando generarComparativa - Fecha: $fecha");
            
            // Extraer IDs y nombres de COPEs
            $copesIds = isset($copesData['ids']) ? $copesData['ids'] : [];
            $copesNombres = isset($copesData['nombres']) ? $copesData['nombres'] : [];
            
            // Asegurarse de que la fecha tenga el formato correcto para la comparación exacta
            $fechaCompleta = $fecha . ' 00:00:00';
            $fechaFinDia = $fecha . ' 23:59:59';
            
            error_log("Convertida fecha para consulta - Inicio día: $fechaCompleta, Fin día: $fechaFinDia");
            
            // Preparar filtro de COPEs para TAC usando nombres
            $copesNombresCondition = "";
            $copesNombresPlaceholders = [];
            if (!empty($copesNombres) && is_array($copesNombres)) {
                $placeholders = [];
                foreach ($copesNombres as $index => $copeNombre) {
                    $placeholders[] = ":copeNombre" . $index;
                }
                $copesNombresCondition = " AND t.NOM_CT IN (" . implode(',', $placeholders) . ")";
                $copesNombresPlaceholders = $placeholders;
                error_log("Filtro de COPEs (nombres) aplicado para TAC: " . json_encode($copesNombres));
            } else {
                error_log("No se aplicó filtro de COPEs para TAC");
            }
            
            // Preparar filtro de COPEs para COORDIAPP usando nombres del campo COPE
            $copesCoordCondition = "";
            $copesCoordPlaceholders = [];
            if (!empty($copesNombres) && is_array($copesNombres)) {
                $placeholders = [];
                foreach ($copesNombres as $index => $copeNombre) {
                    $placeholders[] = ":copeCoord" . $index;
                }
                $copesCoordCondition = " AND c.COPE IN (" . implode(',', $placeholders) . ")";
                $copesCoordPlaceholders = $placeholders;
                error_log("Filtro de COPEs (nombres) aplicado para COORDIAPP: " . json_encode($copesNombres));
            } else {
                error_log("No se aplicó filtro de COPEs para COORDIAPP");
            }
            
            // Consulta para TAC
            $queryTac = "
                SELECT 
                    t.NOM_CT as ct,
                    t.NOM_DIVISION as division,
                    t.Expediente as expediente,
                    t.Tecnico as nombre,
                    COUNT(*) as tac_count,
                    SUM(CASE WHEN t.Calificador_Edo = 'COMPLETADA' THEN 1 ELSE 0 END) as tac_liquidaciones
                FROM qm_tac_prod_bolsa t
                WHERE t.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin
                $copesNombresCondition
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
                WHERE c.Fecha_Coordiapp BETWEEN :fechaInicio AND :fechaFin
                $copesCoordCondition
            ";
            
            // Ejecutar consulta TAC
            error_log("SQL TAC: " . $queryTac);
            try {
                $stmtTac = $this->conn_tac->prepare($queryTac);
                $stmtTac->bindParam(':fechaInicio', $fechaCompleta);
                $stmtTac->bindParam(':fechaFin', $fechaFinDia);
                
                // Bind de los nombres de COPEs para TAC
                if (!empty($copesNombresPlaceholders)) {
                    foreach ($copesNombres as $index => $copeNombre) {
                        $stmtTac->bindValue(':copeNombre' . $index, $copeNombre, PDO::PARAM_STR);
                    }
                }
                
                $stmtTac->execute();
                $resultadosTac = $stmtTac->fetchAll(PDO::FETCH_ASSOC);
                error_log("Registros TAC recuperados: " . count($resultadosTac));
                error_log("Parámetros: fechaInicio=$fechaCompleta, fechaFin=$fechaFinDia, copesNombres=" . json_encode($copesNombres));
                error_log("Muestra de datos TAC: " . json_encode(array_slice($resultadosTac, 0, 3)));
            } catch (PDOException $e) {
                error_log("Error al ejecutar consulta TAC: " . $e->getMessage());
                error_log("Parámetros: fechaInicio=$fechaCompleta, fechaFin=$fechaFinDia");
                throw $e;
            }
            
            // Ejecutar consulta COORDIAPP
            error_log("SQL COORDIAPP: " . $queryCoordiapp);
            try {
                $stmtCoordiapp = $this->conn_coordiapp->prepare($queryCoordiapp);
                $stmtCoordiapp->bindParam(':fechaInicio', $fechaCompleta);
                $stmtCoordiapp->bindParam(':fechaFin', $fechaFinDia);
                
                // Bind de los nombres de COPEs para COORDIAPP
                if (!empty($copesCoordPlaceholders)) {
                    foreach ($copesNombres as $index => $copeNombre) {
                        $stmtCoordiapp->bindValue(':copeCoord' . $index, $copeNombre, PDO::PARAM_STR);
                    }
                }
                
                $stmtCoordiapp->execute();
                $resultadosCoordiapp = $stmtCoordiapp->fetchAll(PDO::FETCH_ASSOC);
                error_log("Registros COORDIAPP recuperados: " . count($resultadosCoordiapp));
                error_log("Parámetros: fechaInicio=$fechaCompleta, fechaFin=$fechaFinDia, copesNombres=" . json_encode($copesNombres));
                error_log("Muestra de datos COORDIAPP: " . json_encode(array_slice($resultadosCoordiapp, 0, 3)));
            } catch (PDOException $e) {
                error_log("Error al ejecutar consulta COORDIAPP: " . $e->getMessage());
                error_log("Parámetros: fechaInicio=$fechaCompleta, fechaFin=$fechaFinDia");
                throw $e;
            }
            
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
            
            $resultado = [
                'totales' => $totales,
                'registros' => array_values($registros)
            ];
            
            error_log("Totales calculados: " . json_encode($totales));
            error_log("Registros procesados: " . count($registros));
            error_log("Finalizando generarComparativa");
            
            return $resultado;
            
        } catch (PDOException $e) {
            $errorMsg = "Error en comparativa (PDOException): " . $e->getMessage();
            error_log($errorMsg);
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'error' => $errorMsg,
                'totales' => [
                    'tac' => 0,
                    'coordiapp' => 0,
                    'diferencias' => 0,
                    'coincidencias' => 0,
                    'porcentaje' => 0
                ],
                'registros' => []
            ];
        } catch (Exception $e) {
            $errorMsg = "Error general en comparativa: " . $e->getMessage();
            error_log($errorMsg);
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'error' => $errorMsg,
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
    error_log("=== INICIO PETICIÓN ComparativaData.php ===");
    error_log("Método HTTP: " . $_SERVER['REQUEST_METHOD']);
    error_log("Parámetros recibidos: " . json_encode($_REQUEST));
    
    // Verificar sesión
    if (!isset($_SESSION['idusuarios_coordinadores'])) {
        error_log("Error: Usuario no autenticado");
        throw new Exception("Usuario no autenticado");
    }
    
    $idUsuario = $_SESSION['idusuarios_coordinadores'];
    error_log("Usuario autenticado: $idUsuario");
    
    $comparativa = new ComparativaTacCoordiapp();
    
    // Obtener COPEs del coordinador (IDs y nombres)
    $copesCoordinador = $comparativa->obtenerCopesCoordinador($idUsuario);
    error_log("COPEs del coordinador $idUsuario - IDs: " . json_encode($copesCoordinador['ids']) . ", Nombres: " . json_encode($copesCoordinador['nombres']));

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'datosComparativos') {
        error_log("Procesando acción: datosComparativos");
        
        // Obtener los parámetros
        $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'mes';
        $valor = isset($_GET['valor']) ? $_GET['valor'] : '';
        
        // Determinar fechas según el período
        switch($periodo) {
            case 'año':
                $anio = !empty($valor) ? $valor : date('Y');
                $fechaInicio = "$anio-01-01";
                $fechaFin = "$anio-12-31";
                break;
                
            case 'mes':
                if (!empty($valor) && preg_match('/^\d{4}-\d{2}$/', $valor)) {
                    $anioMes = $valor;
                } else {
                    $anioMes = date('Y-m');
                }
                
                $fechaInicio = "$anioMes-01";
                $ultimoDiaMes = date('t', strtotime($anioMes . '-01'));
                $fechaFin = "$anioMes-$ultimoDiaMes";
                break;
                
            case 'semana':
                if (!empty($valor) && preg_match('/^\d{4}-W\d{2}$/', $valor)) {
                    list($anio, $semana) = explode('-W', $valor);
                } else {
                    $anio = date('Y');
                    $semana = date('W');
                }
                
                $primerDiaSemana = new DateTime();
                $primerDiaSemana->setISODate($anio, $semana, 1);
                $fechaInicio = $primerDiaSemana->format('Y-m-d');
                
                $ultimoDiaSemana = clone $primerDiaSemana;
                $ultimoDiaSemana->modify('+6 days');
                $fechaFin = $ultimoDiaSemana->format('Y-m-d');
                break;
                
            default:
                // Usar las fechas proporcionadas o fechas por defecto
                $fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : date('Y-m-d', strtotime('-30 days'));
                $fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : date('Y-m-d');
        }
        
        // Si se proporcionaron fechas específicas, usarlas
        if (isset($_GET['fechaInicio']) && !empty($_GET['fechaInicio'])) {
            $fechaInicio = $_GET['fechaInicio'];
        }
        
        if (isset($_GET['fechaFin']) && !empty($_GET['fechaFin'])) {
            $fechaFin = $_GET['fechaFin'];
        }
        
        error_log("Parámetros para obtenerDatosComparativos - Periodo: $periodo, Valor: $valor, FechaInicio: $fechaInicio, FechaFin: $fechaFin, COPEs IDs: " . count($copesCoordinador['ids']) . ", COPEs Nombres: " . count($copesCoordinador['nombres']));
        $resultados = $comparativa->obtenerDatosComparativos($periodo, $valor, $fechaInicio, $fechaFin, $copesCoordinador);
        error_log("Resultado obtenido: " . (isset($resultados['error']) ? "ERROR: " . $resultados['error'] : "OK - " . count($resultados) . " registros"));
        echo json_encode($resultados);
    } 
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log("Procesando petición POST para generarComparativa");
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
        
        error_log("Parámetros para generarComparativa - Fecha: $fecha, COPEs: " . json_encode($copesCoordinador['nombres']));
        $resultados = $comparativa->generarComparativa($fecha, $copesCoordinador);
        
        error_log("Resultado generarComparativa: " . (isset($resultados['error']) ? 
            "ERROR: " . $resultados['error'] : 
            "OK - Registros: " . count($resultados['registros']) . ", Totales: " . json_encode($resultados['totales'])));
        
        echo json_encode($resultados);
    } else {
        error_log("Error: Método HTTP no permitido: " . $_SERVER['REQUEST_METHOD']);
        throw new Exception("Método no permitido");
    }
} catch (Exception $e) {
    $errorMsg = "Error en ComparativaData.php: " . $e->getMessage();
    error_log($errorMsg);
    error_log("Stack trace: " . $e->getTraceAsString());
    error_log("=== FIN PETICIÓN ComparativaData.php (CON ERROR) ===");
    
    echo json_encode([
        'error' => $errorMsg,
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

error_log("=== FIN PETICIÓN ComparativaData.php ===");
?>
