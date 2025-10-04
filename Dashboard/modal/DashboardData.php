<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Configuración de conexiones a bases de datos
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
    
    // Clase principal del dashboard
    class DashboardCoordiapp {
        
        private $conn_coordiapp;
        private $conn_tac;
        private $idUsuario;
        
        public function __construct($idUsuario = null) {
            $this->conn_coordiapp = DatabaseConnections::conectarCoordiapp();
            $this->conn_tac = DatabaseConnections::conectarTac();
            $this->idUsuario = $idUsuario;
        }
        
        private function obtenerIdsCopesCoordinadorSafePlaceholders(array $ids): array {
            if (empty($ids)) { return ['placeholders' => '', 'params' => []]; }
            $ph = implode(',', array_fill(0, count($ids), '?'));
            return ['placeholders' => $ph, 'params' => $ids];
        }

        private function obtenerRankingTecnicos($fecha_inicio, $fecha_fin) {
            // Devuelve para cada técnico: asignadas_tac, liquidadas_tac, liquidadas_coordiapp, sin_registro_coordiapp, y total (liquidadas_coordiapp)
            $idsCopes = $this->obtenerIdsCopesCoordinador();
            if (empty($idsCopes)) { return []; }

            // Preparar placeholders
            $inBuild = $this->obtenerIdsCopesCoordinadorSafePlaceholders($idsCopes);
            $placeholders = $inBuild['placeholders'];
            $params = $inBuild['params'];

            // 1) Obtener liquidadas en Coordiapp por técnico (vista completadas)
            $sql_coordiapp = "
                SELECT 
                    TRIM(CONCAT(IFNULL(Nombre_T,''), ' ', IFNULL(Apellidos_T,''))) AS tecnico,
                    IFNULL(NExpediente,'') AS expediente,
                    TRIM(CONCAT(IFNULL(c.COPE,''))) AS cope,
                    TRIM(CONCAT(IFNULL(Contratista,''),' ',IFNULL(apellido_paterno,''),' ',IFNULL(apellido_materno,''))) AS contratista,
                    COUNT(*) AS liquidadas_coordiapp
                FROM View_Detalle_Coordiapp_Completadas v
                LEFT JOIN copes c ON c.id = v.FK_Cope
                WHERE DATE(v.Fecha_Coordiapp) BETWEEN ? AND ?
                  AND v.FK_Cope IN ($placeholders)
                GROUP BY tecnico, expediente, cope, contratista
            ";

            $stmt = $this->conn_coordiapp->prepare($sql_coordiapp);
            $bindParams = array_merge([$fecha_inicio, $fecha_fin], $params);
            $stmt->execute($bindParams);
            $rows_coordiapp = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2) Obtener asignadas y liquidadas en TAC por expediente/folio y técnico desde la BD TAC
            // Usamos NExpediente y Folio_Pisa mappeados; asumimos que NExpediente coincide con Expediente en TAC o Folio_Pisa
            // Vamos a contar por técnico (nombre técnico en TAC) las asignadas y liquidadas

            // Obtener los expedientes/folios únicos desde la vista completadas (para acotar consulta TAC)
            $folios = array();
            foreach ($rows_coordiapp as $r) {
                if (!empty($r['expediente'])) $folios[] = $r['expediente'];
            }
            $folios = array_values(array_unique($folios));

            $map_tac_counts = [];
            if (!empty($folios)) {
                // Preparar placeholders para los folios
                $placeholders_folios = implode(',', array_fill(0, count($folios), '?'));
                $sql_tac = "
                    SELECT 
                        TRIM(IFNULL(tecnico,'')) AS tecnico_tac,
                        IFNULL(Expediente,'') AS expediente,
                        SUM(CASE WHEN Calificador_Edo = 'ASIGNADA' THEN 1 ELSE 0 END) AS asignadas_tac,
                        SUM(CASE WHEN Calificador_Edo = 'COMPLETADA' THEN 1 ELSE 0 END) AS liquidadas_tac
                    FROM qm_tac_prod_bolsa
                    WHERE DATE(FECHA_LIQ) BETWEEN ? AND ?
                      AND (Expediente IN ($placeholders_folios) OR Folio_Pisa IN ($placeholders_folios))
                    GROUP BY tecnico_tac, expediente
                ";

                $stmt2 = $this->conn_tac->prepare($sql_tac);
                // bind parameters: fecha_inicio, fecha_fin, luego los folios (duplicados para Expediente y Folio_Pisa)
                $executeParams = array_merge([$fecha_inicio, $fecha_fin], $folios, $folios);
                $stmt2->execute($executeParams);
                $tac_rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                foreach ($tac_rows as $tr) {
                    $tname = trim($tr['tecnico_tac']);
                    if ($tname === '') $tname = 'Sin técnico';
                    if (!isset($map_tac_counts[$tname])) {
                        $map_tac_counts[$tname] = ['asignadas_tac' => 0, 'liquidadas_tac' => 0, 'expedientes' => []];
                    }
                    $map_tac_counts[$tname]['asignadas_tac'] += intval($tr['asignadas_tac']);
                    $map_tac_counts[$tname]['liquidadas_tac'] += intval($tr['liquidadas_tac']);
                    $map_tac_counts[$tname]['expedientes'][] = $tr['expediente'];
                }
            }

            // 3) Combinar datos y construir resultado por técnico
            $result_map = [];
            foreach ($rows_coordiapp as $r) {
                $tname = trim($r['tecnico']) ?: 'Sin técnico';
                if (!isset($result_map[$tname])) {
                    $result_map[$tname] = [
                        'tecnico' => $tname,
                        'expediente' => $r['expediente'],
                        'cope' => $r['cope'],
                        'contratista' => $r['contratista'],
                        'liquidadas_coordiapp' => 0,
                        'asignadas_tac' => 0,
                        'liquidadas_tac' => 0,
                        'sin_registro_coordiapp' => 0,
                        'total' => 0
                    ];
                }
                $result_map[$tname]['liquidadas_coordiapp'] += intval($r['liquidadas_coordiapp']);
            }

            // Añadir datos TAC al mapa
            foreach ($map_tac_counts as $tname => $counts) {
                if (!isset($result_map[$tname])) {
                    $result_map[$tname] = [
                        'tecnico' => $tname,
                        'expediente' => '',
                        'cope' => '',
                        'contratista' => '',
                        'liquidadas_coordiapp' => 0,
                        'asignadas_tac' => 0,
                        'liquidadas_tac' => 0,
                        'sin_registro_coordiapp' => 0,
                        'total' => 0
                    ];
                }
                $result_map[$tname]['asignadas_tac'] += intval($counts['asignadas_tac']);
                $result_map[$tname]['liquidadas_tac'] += intval($counts['liquidadas_tac']);
            }

            // Calcular sin_registro_coordiapp: para cada técnico, estimamos como asignadas_tac - liquidadas_coordiapp (no perfecto pero útil)
            foreach ($result_map as $tname => &$entry) {
                $entry['sin_registro_coordiapp'] = max(0, intval($entry['asignadas_tac']) - intval($entry['liquidadas_coordiapp']));
                $entry['total'] = intval($entry['liquidadas_coordiapp']);
            }

            // Convertir a array ordenado por total (liquidadas_coordiapp)
            $results = array_values($result_map);
            usort($results, function($a, $b) { return $b['total'] <=> $a['total']; });

            return $results;
        }

        private function obtenerRankingContratistas($fecha_inicio, $fecha_fin) {
            // Devuelve por contratista: asignadas_tac, liquidadas_tac, liquidadas_coordiapp, sin_registro_coordiapp, total
            $idsCopes = $this->obtenerIdsCopesCoordinador();
            if (empty($idsCopes)) { return []; }

            $inBuild = $this->obtenerIdsCopesCoordinadorSafePlaceholders($idsCopes);
            $placeholders = $inBuild['placeholders'];
            $params = $inBuild['params'];

            // 1) Coordiapp: liquidadas por contratista
            $sql_coordiapp = "
                SELECT 
                    TRIM(CONCAT(IFNULL(Contratista,''),' ',IFNULL(apellido_paterno,''),' ',IFNULL(apellido_materno,''))) AS contratista,
                    COUNT(*) AS liquidadas_coordiapp
                FROM View_Detalle_Coordiapp_Completadas v
                WHERE DATE(v.Fecha_Coordiapp) BETWEEN ? AND ?
                  AND v.FK_Cope IN ($placeholders)
                GROUP BY Contratista, apellido_paterno, apellido_materno
            ";

            $stmt = $this->conn_coordiapp->prepare($sql_coordiapp);
            $bindParams = array_merge([$fecha_inicio, $fecha_fin], $params);
            $stmt->execute($bindParams);
            $rows_coordiapp = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2) TAC: obtener asignadas/liquidadas por Expediente y mapear Expediente => Contratista
            // Primero agregamos en TAC por Expediente para el rango y COPEs del coordinador
            $map_tac = [];
            // Usamos los mismos COPEs del coordinador para acotar la consulta en TAC
            $sql_tac_expedientes = "
                SELECT 
                    IFNULL(Expediente,'') AS expediente,
                    SUM(CASE WHEN Calificador_Edo = 'ASIGNADA' THEN 1 ELSE 0 END) AS asignadas_tac,
                    SUM(CASE WHEN Calificador_Edo = 'COMPLETADA' THEN 1 ELSE 0 END) AS liquidadas_tac
                FROM qm_tac_prod_bolsa
                WHERE DATE(FECHA_LIQ) BETWEEN ? AND ?
                  AND NOM_CT IN ($placeholders)
                GROUP BY expediente
            ";

            $stmt2 = $this->conn_tac->prepare($sql_tac_expedientes);
            $bindParamsTac = array_merge([$fecha_inicio, $fecha_fin], $params);
            $stmt2->execute($bindParamsTac);
            $tac_rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            // Extraer expedientes para mapearlos en la base COORDIAPP (tabla tecnicos -> contratistas)
            $expedientes = [];
            foreach ($tac_rows as $tr) {
                if (!empty($tr['expediente'])) $expedientes[] = $tr['expediente'];
            }
            $expedientes = array_values(array_unique($expedientes));

            $expediente_to_contratista = [];
            if (!empty($expedientes)) {
                // Consultar en conn_coordiapp: tabla tecnicos join contratistas para obtener el nombre del contratista
                $placeholders_exp = implode(',', array_fill(0, count($expedientes), '?'));
                $sql_map = "
                    SELECT t.Expediente, t.FK_Contratista_Tecnico, IFNULL(c.Contratista,'') AS contratista
                    FROM tecnicos t
                    LEFT JOIN contratistas c ON t.FK_Contratista_Tecnico = c.idContratistas
                    WHERE t.Expediente IN ($placeholders_exp)
                ";

                $stmtMap = $this->conn_coordiapp->prepare($sql_map);
                $stmtMap->execute($expedientes);
                $map_rows = $stmtMap->fetchAll(PDO::FETCH_ASSOC);

                foreach ($map_rows as $mr) {
                    $exp = $mr['Expediente'];
                    $expediente_to_contratista[$exp] = $mr['contratista'];
                }
            }

            // Ahora agregar los totales TAC por contratista usando el mapeo por expediente
            foreach ($tac_rows as $tr) {
                $exp = $tr['expediente'];
                $asig = intval($tr['asignadas_tac']);
                $liq = intval($tr['liquidadas_tac']);
                $contr_name = isset($expediente_to_contratista[$exp]) && $expediente_to_contratista[$exp] !== '' ? $expediente_to_contratista[$exp] : 'Sin contratista';
                if (!isset($map_tac[$contr_name])) {
                    $map_tac[$contr_name] = ['asignadas_tac' => 0, 'liquidadas_tac' => 0];
                }
                $map_tac[$contr_name]['asignadas_tac'] += $asig;
                $map_tac[$contr_name]['liquidadas_tac'] += $liq;
            }

            // 3) Combinar y construir resultado
            $result = [];
            foreach ($rows_coordiapp as $r) {
                $name = $r['contratista'];
                if (!isset($result[$name])) {
                    $result[$name] = [
                        'contratista' => $name,
                        'liquidadas_coordiapp' => 0,
                        'asignadas_tac' => 0,
                        'liquidadas_tac' => 0,
                        'sin_registro_coordiapp' => 0,
                        'total' => 0
                    ];
                }
                $result[$name]['liquidadas_coordiapp'] += intval($r['liquidadas_coordiapp']);
            }

            foreach ($map_tac as $name => $counts) {
                if (!isset($result[$name])) {
                    $result[$name] = [
                        'contratista' => $name,
                        'liquidadas_coordiapp' => 0,
                        'asignadas_tac' => 0,
                        'liquidadas_tac' => 0,
                        'sin_registro_coordiapp' => 0,
                        'total' => 0
                    ];
                }
                $result[$name]['asignadas_tac'] += $counts['asignadas_tac'];
                $result[$name]['liquidadas_tac'] += $counts['liquidadas_tac'];
            }

            foreach ($result as $name => &$entry) {
                $entry['sin_registro_coordiapp'] = max(0, intval($entry['asignadas_tac']) - intval($entry['liquidadas_coordiapp']));
                $entry['total'] = intval($entry['liquidadas_coordiapp']);
            }

            $out = array_values($result);
            usort($out, function($a, $b){ return $b['total'] <=> $a['total']; });
            return $out;
        }

        private function obtenerRankingCopes($fecha_inicio, $fecha_fin) {
            // Devuelve por COPE: asignadas_tac, liquidadas_tac, liquidadas_coordiapp, sin_registro_coordiapp, total
            $idsCopes = $this->obtenerIdsCopesCoordinador();
            if (empty($idsCopes)) { return []; }

            $inBuild = $this->obtenerIdsCopesCoordinadorSafePlaceholders($idsCopes);
            $placeholders = $inBuild['placeholders'];
            $params = $inBuild['params'];

            // 1) Coordiapp: liquidadas por COPE
            $sql_coordiapp = "
                SELECT 
                    TRIM(IFNULL(c.COPE,'')) AS cope,
                    COUNT(*) AS liquidadas_coordiapp
                FROM View_Detalle_Coordiapp_Completadas v
                LEFT JOIN copes c ON c.id = v.FK_Cope
                WHERE DATE(v.Fecha_Coordiapp) BETWEEN ? AND ?
                  AND v.FK_Cope IN ($placeholders)
                GROUP BY c.COPE
            ";

            $stmt = $this->conn_coordiapp->prepare($sql_coordiapp);
            $bindParams = array_merge([$fecha_inicio, $fecha_fin], $params);
            $stmt->execute($bindParams);
            $rows_coordiapp = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2) TAC: asignadas/liquidadas por COPE (usando NOM_CT)
            $copes_names = array_map(function($r){ return $r['cope']; }, $rows_coordiapp);
            $copes_names = array_values(array_unique($copes_names));

            $map_tac = [];
            if (!empty($copes_names)) {
                $placeholders_ct = implode(',', array_fill(0, count($copes_names), '?'));
                $sql_tac = "
                    SELECT 
                        TRIM(IFNULL(NOM_CT,'')) AS cope_tac,
                        SUM(CASE WHEN Calificador_Edo = 'ASIGNADA' THEN 1 ELSE 0 END) AS asignadas_tac,
                        SUM(CASE WHEN Calificador_Edo = 'COMPLETADA' THEN 1 ELSE 0 END) AS liquidadas_tac
                    FROM qm_tac_prod_bolsa
                    WHERE DATE(FECHA_LIQ) BETWEEN ? AND ?
                      AND NOM_CT IN ($placeholders_ct)
                    GROUP BY cope_tac
                ";

                $stmt2 = $this->conn_tac->prepare($sql_tac);
                $executeParams = array_merge([$fecha_inicio, $fecha_fin], $copes_names);
                $stmt2->execute($executeParams);
                $tac_rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                foreach ($tac_rows as $tr) {
                    $map_tac[trim($tr['cope_tac'])] = ['asignadas_tac' => intval($tr['asignadas_tac']), 'liquidadas_tac' => intval($tr['liquidadas_tac'])];
                }
            }

            // 3) Combinar
            $result = [];
            foreach ($rows_coordiapp as $r) {
                $name = $r['cope'];
                if (!isset($result[$name])) {
                    $result[$name] = [
                        'cope' => $name,
                        'liquidadas_coordiapp' => 0,
                        'asignadas_tac' => 0,
                        'liquidadas_tac' => 0,
                        'sin_registro_coordiapp' => 0,
                        'total' => 0
                    ];
                }
                $result[$name]['liquidadas_coordiapp'] += intval($r['liquidadas_coordiapp']);
            }

            foreach ($map_tac as $name => $counts) {
                if (!isset($result[$name])) {
                    $result[$name] = [
                        'cope' => $name,
                        'liquidadas_coordiapp' => 0,
                        'asignadas_tac' => 0,
                        'liquidadas_tac' => 0,
                        'sin_registro_coordiapp' => 0,
                        'total' => 0
                    ];
                }
                $result[$name]['asignadas_tac'] += $counts['asignadas_tac'];
                $result[$name]['liquidadas_tac'] += $counts['liquidadas_tac'];
            }

            foreach ($result as $name => &$entry) {
                $entry['sin_registro_coordiapp'] = max(0, intval($entry['asignadas_tac']) - intval($entry['liquidadas_coordiapp']));
                $entry['total'] = intval($entry['liquidadas_coordiapp']);
            }

            $out = array_values($result);
            usort($out, function($a, $b){ return $b['total'] <=> $a['total']; });
            return $out;
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
        
        // Función para obtener los IDs de COPEs del coordinador
        private function obtenerIdsCopesCoordinador() {
            $copes = $this->obtenerCopesCoordinador();
            return array_column($copes, 'id');
        }
        
        // Función para obtener los nombres de COPEs del coordinador
        private function obtenerNombresCopesCoordinador() {
            $copes = $this->obtenerCopesCoordinador();
            return array_column($copes, 'COPE');
        }

        private function obtenerMapaTecnicos($folios) {
            if (empty($folios)) {
                return array();
            }

            // Crear placeholders para la consulta IN
            $placeholders = str_repeat('?,', count($folios) - 1) . '?';
            
            // Consultar en TAC devolviendo técnico y expediente por separado
            $query = "
                SELECT 
                    Folio_Pisa,
                    TRIM(IFNULL(tecnico, '')) AS tecnico,
                    TRIM(IFNULL(Expediente, '')) AS expediente
                FROM qm_tac_prod_bolsa
                WHERE Folio_Pisa IN ($placeholders)
                ORDER BY FECHA_LIQ DESC
            ";
            
            try {
                $stmt = $this->conn_tac->prepare($query);
                $stmt->execute($folios);
                
                // Crear un mapa de folio -> nombre del técnico
                $mapa_tecnicos = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $folio_key = strval(trim($row['Folio_Pisa']));
                    // Mantener solo el primer técnico encontrado por folio
                    if (!isset($mapa_tecnicos[$folio_key])) {
                        $mapa_tecnicos[$folio_key] = array(
                            'tecnico' => isset($row['tecnico']) ? trim($row['tecnico']) : '',
                            'expediente' => isset($row['expediente']) ? strval(trim($row['expediente'])) : ''
                        );
                    }
                }
                return $mapa_tecnicos;
            } catch (Exception $e) {
                error_log("Error obteniendo nombres de técnicos desde TAC: " . $e->getMessage());
                return array();
            }
        }
        
        public function obtenerOrdenesTac($fecha_inicio, $fecha_fin = null) {
            // Si no se proporciona fecha_fin, usar la misma fecha_inicio
            if ($fecha_fin === null) {
                $fecha_fin = $fecha_inicio;
            }
            
            // Obtener los nombres de COPEs del coordinador
            $copesCoordinador = $this->obtenerNombresCopesCoordinador();
            
            // Si no hay COPEs asignados, retornar array vacío
            if (empty($copesCoordinador)) {
                error_log("No hay COPEs asignados al coordinador ID: " . $this->idUsuario);
                return array();
            }
            
            // Crear placeholders para los COPEs
            $placeholders = str_repeat('?,', count($copesCoordinador) - 1) . '?';
            
            $query = "
                SELECT 
                    Folio_Pisa,
                    TELEFONO,
                    NOM_AREA,
                    IFNULL(NOM_DIVISION, 'Sin División') as NOM_DIVISION,
                    NOM_CT,
                    DATE(FECHA_LIQ) as FECHA_LIQ
                FROM qm_tac_prod_bolsa 
                WHERE DATE(FECHA_LIQ) BETWEEN ? AND ?
                AND Calificador_Edo = 'COMPLETADA'
                AND NOM_CT IN ($placeholders)
                ORDER BY FECHA_LIQ DESC, NOM_DIVISION, NOM_CT
            ";
            
            $stmt = $this->conn_tac->prepare($query);
            $stmt->bindParam(1, $fecha_inicio);
            $stmt->bindParam(2, $fecha_fin);
            
            // Bind de los COPEs
            for ($i = 0; $i < count($copesCoordinador); $i++) {
                $stmt->bindParam(3 + $i, $copesCoordinador[$i]);
            }
            
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log de consulta TAC
            error_log("TAC Query - Fechas: $fecha_inicio a $fecha_fin, COPEs: " . implode(',', $copesCoordinador) . ", Registros encontrados: " . count($result));
            
            return $result;
        }

        public function obtenerAsignadasTac($fecha_inicio, $fecha_fin = null) {
            // Si no se proporciona fecha_fin, usar la misma fecha_inicio
            if ($fecha_fin === null) {
                $fecha_fin = $fecha_inicio;
            }
            
            // Obtener los nombres de COPEs del coordinador
            $copesCoordinador = $this->obtenerNombresCopesCoordinador();
            
            // Si no hay COPEs asignados, retornar array vacío
            if (empty($copesCoordinador)) {
                error_log("No hay COPEs asignados al coordinador ID: " . $this->idUsuario);
                return array();
            }
            
            // Crear placeholders para los COPEs
            $placeholders = str_repeat('?,', count($copesCoordinador) - 1) . '?';
            
            $query = "
                SELECT 
                    Folio_Pisa,
                    TELEFONO,
                    NOM_AREA,
                    IFNULL(NOM_DIVISION, 'Sin División') as NOM_DIVISION,
                    NOM_CT,
                    DATE(FECHA_LIQ) as FECHA_LIQ
                FROM qm_tac_prod_bolsa 
                WHERE DATE(FECHA_LIQ) BETWEEN ? AND ?
                AND Calificador_Edo = 'ASIGNADA'
                AND NOM_CT IN ($placeholders)
                ORDER BY FECHA_LIQ DESC, NOM_DIVISION, NOM_CT
            ";
            
            $stmt = $this->conn_tac->prepare($query);
            $stmt->bindParam(1, $fecha_inicio);
            $stmt->bindParam(2, $fecha_fin);
            
            // Bind de los COPEs
            for ($i = 0; $i < count($copesCoordinador); $i++) {
                $stmt->bindParam(3 + $i, $copesCoordinador[$i]);
            }
            
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log de consulta TAC
            error_log("TAC Query Asignadas - Fechas: $fecha_inicio a $fecha_fin, COPEs: " . implode(',', $copesCoordinador) . ", Registros encontrados: " . count($result));
            
            return $result;
        }
        
        public function obtenerOrdenesCoordiapp() {
            // Obtener los IDs de COPEs del coordinador
            $idsCopesCoordinador = $this->obtenerIdsCopesCoordinador();
            
            // Si no hay COPEs asignados, retornar array vacío
            if (empty($idsCopesCoordinador)) {
                error_log("No hay COPEs asignados al coordinador ID: " . $this->idUsuario);
                return array();
            }
            
            // Crear placeholders para los IDs de COPEs
            $placeholders = str_repeat('?,', count($idsCopesCoordinador) - 1) . '?';
            
            $query = "
                SELECT 
                    Folio_Pisa,
                    Fecha_Coordiapp
                FROM tecnico_instalaciones_coordiapp
                WHERE FK_Cope IN ($placeholders)
                ORDER BY Fecha_Coordiapp DESC
            ";
            
            $stmt = $this->conn_coordiapp->prepare($query);
            
            // Bind de los IDs de COPEs
            for ($i = 0; $i < count($idsCopesCoordinador); $i++) {
                $stmt->bindParam($i + 1, $idsCopesCoordinador[$i], PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function identificarFaltantes($ordenes_tac, $ordenes_coordiapp) {
            // Crear set de folios en COORDIAPP para búsqueda rápida
            $coordiapp_folios = array();
            foreach ($ordenes_coordiapp as $orden) {
                $folio = $orden['Folio_Pisa'];
                if (!empty($folio)) {
                    $coordiapp_folios[strval($folio)] = true;
                }
            }
            
            // Identificar faltantes
            $faltantes = array();
            foreach ($ordenes_tac as $orden) {
                $folio = $orden['Folio_Pisa'];
                if (!empty($folio)) {
                    $folio_str = strval($folio);
                    if (!isset($coordiapp_folios[$folio_str])) {
                        $faltantes[] = $orden;
                    }
                }
            }
            
            return $faltantes;
        }
        
        public function calcularEstadisticasPorArea($ordenes_tac, $ordenes_asignadas, $faltantes) {
            $copes_stats = array(); // Cambiar de areas_stats a copes_stats
            $division_stats = array();
            
            // Inicializar estadísticas por COPE y división
            foreach ($ordenes_tac as $orden) {
                $area = !empty($orden['NOM_AREA']) ? trim($orden['NOM_AREA']) : 'Sin Área';
                $division = !empty($orden['NOM_DIVISION']) ? trim($orden['NOM_DIVISION']) : 'Sin División';
                $cope = !empty($orden['NOM_CT']) ? trim($orden['NOM_CT']) : 'SIN_COPE';
                
                // Debug para ver qué valores estamos recibiendo
                error_log("División recibida: " . print_r($orden['NOM_DIVISION'], true));
                
                // Estadísticas por COPE para las gráficas (en lugar de área)
                if (!isset($copes_stats[$cope])) {
                    $copes_stats[$cope] = array(
                        'cope' => $cope, // Cambiar de 'area' a 'cope'
                        'total_tac' => 0,
                        'registradas' => 0,
                        'faltantes' => 0
                    );
                }
                $copes_stats[$cope]['total_tac']++;
                
                // Estadísticas por división para el detalle
                if (!isset($division_stats[$division])) {
                    $division_stats[$division] = array(
                        'division' => $division,
                        'total_tac' => 0,
                        'total_asignadas' => 0,
                        'registradas' => 0,
                        'faltantes' => 0,
                        'folios_faltantes' => array(),
                        'copes' => array()
                    );
                }
                
                if (!isset($division_stats[$division]['copes'][$cope])) {
                    $division_stats[$division]['copes'][$cope] = array(
                        'cope' => $cope,
                        'total_tac' => 0,
                        'total_asignadas' => 0,
                        'registradas' => 0,
                        'faltantes' => 0,
                        'folios_faltantes' => array()
                    );
                }
                
                $division_stats[$division]['total_tac']++;
                $division_stats[$division]['copes'][$cope]['total_tac']++;
            }
            
            // Procesar órdenes asignadas
            foreach ($ordenes_asignadas as $orden) {
                $area = !empty($orden['NOM_AREA']) ? trim($orden['NOM_AREA']) : 'Sin Área';
                $division = !empty($orden['NOM_DIVISION']) ? trim($orden['NOM_DIVISION']) : 'Sin División';
                $cope = !empty($orden['NOM_CT']) ? trim($orden['NOM_CT']) : 'SIN_COPE';
                
                // Inicializar la división si no existe
                if (!isset($division_stats[$division])) {
                    $division_stats[$division] = array(
                        'division' => $division,
                        'total_tac' => 0,
                        'total_asignadas' => 0,
                        'registradas' => 0,
                        'faltantes' => 0,
                        'folios_faltantes' => array(),
                        'copes' => array()
                    );
                }

                // Inicializar el COPE si no existe
                if (!isset($division_stats[$division]['copes'][$cope])) {
                    $division_stats[$division]['copes'][$cope] = array(
                        'cope' => $cope,
                        'total_tac' => 0,
                        'total_asignadas' => 0,
                        'registradas' => 0,
                        'faltantes' => 0,
                        'folios_faltantes' => array()
                    );
                }
                
                // Actualizar estadísticas
                $division_stats[$division]['total_asignadas']++;
                $division_stats[$division]['copes'][$cope]['total_asignadas']++;
            }

            // Obtener todos los folios únicos primero
            $folios_unicos = [];
            $ordenes_por_folio = [];
            
            foreach ($faltantes as $orden) {
                $folio = strval($orden['Folio_Pisa']);
                if (!in_array($folio, $folios_unicos)) {
                    $folios_unicos[] = $folio;
                }
                $ordenes_por_folio[$folio] = $orden;
            }
            
            // Obtener todos los técnicos en una sola consulta
            $mapa_tecnicos = $this->obtenerMapaTecnicos($folios_unicos);
            
            // Procesar faltantes con los técnicos ya cargados
            foreach ($faltantes as $orden) {
                $area = !empty($orden['NOM_AREA']) ? $orden['NOM_AREA'] : 'Sin Área';
                $division = !empty($orden['NOM_DIVISION']) ? $orden['NOM_DIVISION'] : 'Sin División';
                $cope = !empty($orden['NOM_CT']) ? $orden['NOM_CT'] : 'SIN_COPE';
                $folio = strval($orden['Folio_Pisa']);
                
                // Obtener técnico y expediente del mapa
                $tecnico_map = isset($mapa_tecnicos[$folio]) ? $mapa_tecnicos[$folio] : array('tecnico' => 'Sin técnico', 'expediente' => '');
                $tecnico_nombre = isset($tecnico_map['tecnico']) ? $tecnico_map['tecnico'] : 'Sin técnico';
                $expediente_val = isset($tecnico_map['expediente']) ? $tecnico_map['expediente'] : '';
                
                // Actualizar faltantes por COPE (en lugar de área)
                if (isset($copes_stats[$cope])) {
                    $copes_stats[$cope]['faltantes']++;
                }
                
                // Actualizar faltantes por división
                if (isset($division_stats[$division])) {
                    $division_stats[$division]['faltantes']++;
                    $division_stats[$division]['folios_faltantes'][] = array(
                        'folio' => $folio,
                        'tecnico' => $tecnico_nombre,
                        'expediente' => $expediente_val
                    );
                    
                    if (isset($division_stats[$division]['copes'][$cope])) {
                        $division_stats[$division]['copes'][$cope]['faltantes']++;
                        $division_stats[$division]['copes'][$cope]['folios_faltantes'][] = array(
                            'folio' => $folio,
                            'tecnico' => $tecnico_nombre,
                            'expediente' => $expediente_val
                        );
                    }
                }
            }
            
            // Calcular registradas y porcentajes para COPEs
            foreach ($copes_stats as $cope => &$stats) {
                $stats['registradas'] = $stats['total_tac'] - $stats['faltantes'];
                $stats['porcentaje'] = $stats['total_tac'] > 0 ? 
                    ($stats['registradas'] / $stats['total_tac'] * 100) : 0;
            }
            
            // Calcular registradas y porcentajes para divisiones y copes
            foreach ($division_stats as $division => &$stats) {
                $stats['registradas'] = $stats['total_tac'] - $stats['faltantes'];
                $stats['porcentaje'] = $stats['total_tac'] > 0 ? 
                    ($stats['registradas'] / $stats['total_tac'] * 100) : 0;
                
                // Calcular para cada cope
                foreach ($stats['copes'] as $cope => &$cope_stats) {
                    $cope_stats['registradas'] = $cope_stats['total_tac'] - $cope_stats['faltantes'];
                    $cope_stats['porcentaje'] = $cope_stats['total_tac'] > 0 ? 
                        ($cope_stats['registradas'] / $cope_stats['total_tac'] * 100) : 0;
                }
                
                // Convertir copes array asociativo a array indexado y ordenar por porcentaje
                $stats['copes'] = array_values($stats['copes']);
                usort($stats['copes'], function($a, $b) {
                    return $b['porcentaje'] <=> $a['porcentaje'];
                });
            }
            
            // Ordenar COPEs por porcentaje de cumplimiento (mayor a menor)
            uasort($copes_stats, function($a, $b) {
                return $b['porcentaje'] <=> $a['porcentaje'];
            });
            
            // Ordenar divisiones por porcentaje de cumplimiento (mayor a menor)
            uasort($division_stats, function($a, $b) {
                return $b['porcentaje'] <=> $a['porcentaje'];
            });
            
            // Retornar ambos conjuntos de estadísticas (copes en lugar de areas)
            return [
                'copes' => array_values($copes_stats), // Cambiar de 'areas' a 'copes'
                'divisiones' => array_values($division_stats)
            ];
        }
        
        public function generarReporte($fecha_inicio, $fecha_fin = null, $tipo_analisis = 'diario') {
            // Si no se proporciona fecha_fin, usar la misma fecha_inicio
            if ($fecha_fin === null) {
                $fecha_fin = $fecha_inicio;
            }
            
            // Obtener datos de ambas bases
            $ordenes_tac = $this->obtenerOrdenesTac($fecha_inicio, $fecha_fin);
            $ordenes_asignadas_tac = $this->obtenerAsignadasTac($fecha_inicio, $fecha_fin);
            $ordenes_coordiapp = $this->obtenerOrdenesCoordiapp();
            
            // Identificar faltantes
            $faltantes = $this->identificarFaltantes($ordenes_tac, $ordenes_coordiapp);
            
            // Calcular estadísticas generales
            $total_tac = count($ordenes_tac);
            $total_faltantes = count($faltantes);
            $total_registradas = $total_tac - $total_faltantes;
            $porcentaje_cumplimiento = $total_tac > 0 ? ($total_registradas / $total_tac * 100) : 0;
            
            // Calcular estadísticas por área y división
            $estadisticas_detalladas = $this->calcularEstadisticasPorArea($ordenes_tac, $ordenes_asignadas_tac, $faltantes);
            
            // Calcular días analizados
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);
            $diferencia = $inicio->diff($fin);
            $dias_analizados = $diferencia->days + 1;
            
            // Estadísticas adicionales por rango
            $estadisticas_por_fecha = array();
            if ($tipo_analisis === 'rango' && $fecha_inicio !== $fecha_fin) {
                $estadisticas_por_fecha = $this->calcularEstadisticasPorFecha($ordenes_tac, $ordenes_coordiapp, $fecha_inicio, $fecha_fin);
            }
            
            $ranking_tecnicos = $this->obtenerRankingTecnicos($fecha_inicio, $fecha_fin);
            $ranking_contratistas = $this->obtenerRankingContratistas($fecha_inicio, $fecha_fin);
            $ranking_copes = $this->obtenerRankingCopes($fecha_inicio, $fecha_fin);

            $resultado = array(
                'resumen' => array(
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'dias_analizados' => $dias_analizados,
                    'tipo_analisis' => $tipo_analisis,
                    'total_tac' => $total_tac,
                    'total_registradas' => $total_registradas,
                    'total_faltantes' => $total_faltantes,
                    'porcentaje_cumplimiento' => $porcentaje_cumplimiento,
                    'promedio_diario' => $dias_analizados > 0 ? round($total_tac / $dias_analizados, 1) : 0
                ),
                'copes' => $estadisticas_detalladas['copes'], // Cambiar de 'areas' a 'copes'
                'divisiones' => $estadisticas_detalladas['divisiones'],
                'estadisticas_por_fecha' => $estadisticas_por_fecha,
                'ranking_tecnicos' => $ranking_tecnicos,
                'ranking_contratistas' => $ranking_contratistas,
                'ranking_copes' => $ranking_copes,
                'ultima_actualizacion' => date('Y-m-d H:i:s')
            );
            
            // Debug: Verificar la estructura de los datos antes de enviarlos
            error_log("Estructura de folios_faltantes: " . print_r($estadisticas_detalladas['divisiones'][0]['folios_faltantes'], true));
            
            // Asegurarse de que los datos se envíen como JSON válido
            return json_decode(json_encode($resultado), true);
        }
        
        public function calcularEstadisticasPorFecha($ordenes_tac, $ordenes_coordiapp, $fecha_inicio, $fecha_fin) {
            $estadisticas = array();
            
            // Crear set de folios en COORDIAPP
            $coordiapp_folios = array();
            foreach ($ordenes_coordiapp as $orden) {
                $folio = $orden['Folio_Pisa'];
                if (!empty($folio)) {
                    $coordiapp_folios[strval($folio)] = true;
                }
            }
            
            // Agrupar órdenes TAC por fecha
            $ordenes_por_fecha = array();
            foreach ($ordenes_tac as $orden) {
                $fecha = $orden['FECHA_LIQ'];
                if (!isset($ordenes_por_fecha[$fecha])) {
                    $ordenes_por_fecha[$fecha] = array();
                }
                $ordenes_por_fecha[$fecha][] = $orden;
            }
            
            // Calcular estadísticas para cada fecha
            foreach ($ordenes_por_fecha as $fecha => $ordenes_del_dia) {
                $total_dia = count($ordenes_del_dia);
                $faltantes_dia = 0;
                
                foreach ($ordenes_del_dia as $orden) {
                    $folio = $orden['Folio_Pisa'];
                    if (!empty($folio)) {
                        $folio_str = strval($folio);
                        if (!isset($coordiapp_folios[$folio_str])) {
                            $faltantes_dia++;
                        }
                    }
                }
                
                $registradas_dia = $total_dia - $faltantes_dia;
                $porcentaje_dia = $total_dia > 0 ? ($registradas_dia / $total_dia * 100) : 0;
                
                $estadisticas[] = array(
                    'fecha' => $fecha,
                    'total_tac' => $total_dia,
                    'registradas' => $registradas_dia,
                    'faltantes' => $faltantes_dia,
                    'porcentaje_cumplimiento' => $porcentaje_dia
                );
            }
            
            // Ordenar por fecha
            usort($estadisticas, function($a, $b) {
                return strcmp($a['fecha'], $b['fecha']);
            });
            
            return $estadisticas;
        }
    }
    
    // Procesar la solicitud
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : date('Y-m-d');
        $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : $fecha_inicio;
        $tipo_analisis = isset($_POST['tipo_analisis']) ? $_POST['tipo_analisis'] : 'diario';
        
        // Debug: Log de fechas recibidas
        error_log("DashboardData.php - Fechas recibidas: inicio=$fecha_inicio, fin=$fecha_fin");
        error_log("DashboardData.php - POST data: " . print_r($_POST, true));
        
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
        
        $dashboard = new DashboardCoordiapp($idUsuario);
        $data = $dashboard->generarReporte($fecha_inicio, $fecha_fin, $tipo_analisis);
        
        echo json_encode(array(
            'success' => true,
            'data' => $data,
            'message' => 'Datos obtenidos correctamente'
        ));
    } else {
        throw new Exception("Método no permitido");
    }
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage(),
        'data' => null
    ));
}
?>
