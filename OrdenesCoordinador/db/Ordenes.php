<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/PortalCoordinadores/cnx/cnx.php";

class Ordenes extends Conexion
{
    public function obtenerOrdenesPorCopes($copes, $fechaInicio = null, $fechaFin = null, $estatusOrden = null, $perPage = 20, $page = 1)
    {
        try {
            $conexion = $this->get_conexion();
            $offset = ($page - 1) * $perPage;

            // Filtro de copes
            if (empty($copes) || !is_array($copes)) {
                throw new Exception("Debe proporcionar al menos un COPE");
            }
            $placeholders = implode(',', array_fill(0, count($copes), '?'));
            $condiciones = ["FK_Cope IN ($placeholders)"];
            $params = $copes;

            // Filtro de fechas
            if ($fechaInicio && $fechaFin) {
                $condiciones[] = "Fecha_Coordiapp BETWEEN ? AND ?";
                $params[] = $fechaInicio;
                $params[] = $fechaFin;
            }

            // Filtro de estatus
            if ($estatusOrden !== null) {
                if ($estatusOrden === 'VACIO') {
                    $condiciones[] = "(Estatus_Orden IS NULL OR Estatus_Orden = '')";
                } elseif ($estatusOrden === 'COMPLETADA') {
                    $condiciones[] = "(Estatus_Orden = 'COMPLETADA' AND Step_Registro = 5)";
                } elseif ($estatusOrden === 'INCOMPLETA') {
                    $condiciones[] = "((Estatus_Orden = 'INCOMPLETA') OR (Estatus_Orden = 'COMPLETADA' AND Step_Registro < 5))";
                } else {
                    $condiciones[] = "Estatus_Orden = ?";
                    $params[] = $estatusOrden;
                }
            }

            $where = 'WHERE ' . implode(' AND ', $condiciones);

            // Columnas a seleccionar (ajusta si necesitas menos/más)
            $select = "
                Folio_Pisa, Telefono, Ont,
                CASE 
                    WHEN Step_Registro < 5 THEN 'INCOMPLETO'
                    WHEN Step_Registro = 5 THEN 'COMPLETADA'
                    ELSE 'INCOMPLETO'
                END as Estatus_Real,
                FK_Contratista_Tecnico,
                CONCAT_WS(' ', Contratista, apellido_paterno, apellido_materno) as nombre_completo_contratista,
                CONCAT_WS(' ', Nombre_T, Apellidos_T) as nombre_completo_tecnico,
                NExpediente, COPE, FK_Cope, area, FK_Area, Division, FK_Division,
                Foto_Ont, Foto_Casa_Cliente, No_Serie_ONT, Foto_Puerto, Foto_INE,
                Distrito, Tecnologia, Tipo_Tarea, Tipo_Instalacion, Metraje,
                Fecha_Coordiapp, Estatus_Orden, Terminal, Puerto,
                CONCAT_WS(' ', Cliente_Titular, Apellido_Paterno_Titular, Apellido_Materno_Titular) as nombre_completo_cliente,
                Direccion_Cliente, Latitud, Longitud, Step_Registro, idtecnico_instalaciones_coordiapp,
                Latitud_Terminal, Longitud_Terminal, FK_Tecnico_apps, Tipo_Orden, Tipo_reparacion, Tipo_sub_reparaviob
            ";

            // Query para completadas
            $sqlCompletadas = "SELECT $select FROM erpintr1_erp.View_Detalle_Coordiapp_Completadas $where";
            // Query para incompletas (Latitud_Terminal y Longitud_Terminal como NULL)
            $selectIncompletas = str_replace(
                ['Latitud_Terminal', 'Longitud_Terminal'],
                ['NULL as Latitud_Terminal', 'NULL as Longitud_Terminal'],
                $select
            );
            $sqlIncompletas = "SELECT $selectIncompletas FROM erpintr1_erp.View_Detalle_Coordiapp_Incompletas $where";
            $sqlUnion = "($sqlCompletadas) UNION ALL ($sqlIncompletas)";

            // Parámetros para ambas consultas
            $allParams = array_merge($params, $params);

            // Conteo
            $sqlCount = "SELECT COUNT(*) as total FROM ($sqlUnion) as todas_ordenes";
            $stmtCount = $conexion->prepare($sqlCount);
            $stmtCount->execute($allParams);
            $total = $stmtCount->fetchColumn();

            // Paginación
            $sqlFinal = $sqlUnion . " ORDER BY Fecha_Coordiapp DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
            $finalParams = array_merge($params, $params);
            $stmt = $conexion->prepare($sqlFinal);
            $stmt->execute($finalParams);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'current_page' => (int)$page,
                'data' => $resultados,
                'first_page' => 1,
                'from' => $offset + 1,
                'last_page' => (int)ceil($total / $perPage),
                'next_page' => $page < ceil($total / $perPage) ? $page + 1 : null,
                'per_page' => (int)$perPage,
                'prev_page' => $page > 1 ? $page - 1 : null,
                'to' => min($offset + $perPage, $total),
                'total' => (int)$total
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerOrdenesPorCopes (PDO): " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Error al obtener las órdenes: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("Error en obtenerOrdenesPorCopes (Exception): " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Error al obtener las órdenes: ' . $e->getMessage()
            ];
        }
    }

    // Método para obtener los COPEs asignados a un coordinador
    public function obtenerCopesCoordinador($idCoordinador)
    {
        try {
            $conexion = $this->get_conexion();
            $query = "SELECT c.id, c.COPE 
                     FROM copes c 
                     INNER JOIN coordinador_cope cc ON c.id = cc.FK_Cope 
                     WHERE cc.FK_Coordinador = :idCoordinador 
                     ORDER BY c.COPE";
            
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':idCoordinador', $idCoordinador);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCopesCoordinador: " . $e->getMessage());
            return array();
        }
    }
}
?>