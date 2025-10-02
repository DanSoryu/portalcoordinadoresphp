<?php
	/**
	 * DESARROLLADO ING. ANTONIO CARDONA
	 */
    require_once($_SERVER['DOCUMENT_ROOT']."/PortalCoordinadores/cnx/cnx.php");

    class Materiales extends Conexion
    {
        public function __construct() {
            // Constructor vacío para poder instanciar sin parámetros
        }
        
        public function Materiales($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT Folio_Salida_Det, Folio_Pisa, Fecha_Coordiapp, Producto, Modelo, Num_Serie_Salida_Det, CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS Contratista, Fecha_Salida_Det,  CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico FROM `salidas_contratistas` 
            INNER JOIN producto ON idProducto = FK_Material_Salidas_Contratistas 
            INNER JOIN contratistas ON FK_Contratista_Salida_Det = idContratistas 
            INNER JOIN tecnicos ON FK_Tecnico_Salida_Det = idTecnico
            INNER JOIN tecnico_instalaciones_coordiapp ON Ont = Num_Serie_Salida_Det 
            WHERE Ont_Ubicacion = 'TERMINADO' AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            ORDER BY Fecha_Salida_Det DESC";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function MaterialesNoInstalados($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT Folio_Salida_Det, Producto, Modelo, Num_Serie_Salida_Det, CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS Contratista, Fecha_Salida_Det,  CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico FROM `salidas_contratistas` 
            INNER JOIN producto ON idProducto = FK_Material_Salidas_Contratistas 
            INNER JOIN contratistas ON FK_Contratista_Salida_Det = idContratistas 
            INNER JOIN tecnicos ON FK_Tecnico_Salida_Det = idTecnico
            WHERE Ont_Ubicacion = 'TECNICO' AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            ORDER BY Fecha_Salida_Det DESC";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function Almacenes($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT Folio_Entrada_Material_Det, Almacen, Producto, Modelo, Num_Serie, Fecha_Entrada FROM materiales_det
            INNER JOIN producto ON idProducto = FK_Material 
            INNER JOIN entradas_almacen_t ON Folio_Entrada_Material_Det = Folio_Entrada_T
            INNER JOIN almacenes ON FK_Almacen_T = idAlmacenes
            WHERE Estatus_NS IS NULL AND Almacen <> 'TESTING' AND Fecha_Entrada BETWEEN :Fecha1 and :Fecha2
            ORDER BY Fecha_Entrada DESC";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function DetalleOnt($Fecha1, $Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM ont_detalle 
            WHERE (
                Fecha_Entrada_Almacen BETWEEN :Fecha1 AND :Fecha2
                OR Carso_FechaSalida BETWEEN :Fecha1 AND :Fecha2
                OR Fecha_Liq_Tac BETWEEN :Fecha1 AND :Fecha2
            )
            ORDER BY COALESCE(Fecha_Entrada_Almacen, Carso_FechaSalida, Fecha_Liq_Tac) DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }
            return $rows;
        }

        public function getDatosPorSerie($numeroSerie) {
            $conexion = $this->get_conexion(); // Método que retorna la conexión PDO
            $sql = "SELECT Numero_Serie, Producto, Modelo, Proveedor, Almacen_Entrada, Folio_Entrada_Almacen, Fecha_Entrada_Almacen, Folio_Salida_Almacen, Fecha_Salida_Almacen, Nombre_Contratista, Folio_Salida_Contratista, Fecha_Salida_Contratista, Nombre_Tecnico, Folio_Pisa_Coordiapp, Fecha_Coordiapp FROM ont_detalle WHERE Numero_Serie = :numeroSerie LIMIT 1";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':numeroSerie', $numeroSerie, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: false;
        }

        public function getEstadisticasPorEstatus($Fecha1, $Fecha2) {
            $conexion = $this->get_conexion();
            $sql = "SELECT 
                        Estatus_Ont_detalle,
                        COUNT(*) as cantidad
                    FROM ont_detalle
                    WHERE (
                        Fecha_Entrada_Almacen BETWEEN :Fecha1 AND :Fecha2
                        OR Carso_FechaSalida BETWEEN :Fecha1 AND :Fecha2
                        OR Fecha_Liq_Tac BETWEEN :Fecha1 AND :Fecha2
                    )
                    GROUP BY Estatus_Ont_detalle
                    ORDER BY cantidad DESC;";
                    
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
         
         public function ONTCobre($Fecha1, $Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM ont_cobre 
            WHERE Fecha BETWEEN :Fecha1 AND :Fecha2
            ORDER BY Fecha DESC";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }
            return $rows;
        }

        /**
         * Obtener datos de CARSO desde la tabla ont_detalle
         */
        public function obtenerDatosCarso()
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT idontDetalle, Numero_Serie, Estatus_Ont_detalle, Carso_Vale, Carso_FechaSalida 
                    FROM ont_detalle 
                    WHERE Estatus_Ont_detalle = 'CARSO' 
                    ORDER BY idontDetalle DESC";
            $statement = $conexion->prepare($sql);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }
            return $rows;
        }
        
        /**
         * Insertar datos de CARSO en la tabla ont_detalle
         */
        public function insertarDatosCarso($numero_serie, $carso_vale, $carso_fecha_salida, $division_carso = null)
        {
            try {
                $conexion = $this->get_conexion();

                // Normalización básica
                $numero_serie       = trim((string)$numero_serie);
                $carso_vale         = trim((string)$carso_vale);
                $carso_vale         = ($carso_vale === '') ? null : $carso_vale;
                $division_carso     = trim((string)$division_carso);
                $division_carso     = ($division_carso === '') ? null : $division_carso;

                // Fecha en formato YYYY-MM-DD o NULL
                $carso_fecha_salida = trim((string)$carso_fecha_salida);
                $carso_fecha_salida = ($carso_fecha_salida === '' || $carso_fecha_salida === '0000-00-00') ? null : $carso_fecha_salida;

                // INSERT ... ON DUPLICATE KEY UPDATE (solo actualiza columnas de CARSO)
                // - Si no existe la serie: inserta con Estatus 'NO LOCALIZADAS'
                // - Si existe: actualiza SOLO Carso_Vale, Carso_FechaSalida y division_carso
                // - COALESCE evita sobreescribir con NULL cuando el Excel venga vacío
                $sql = "
                    INSERT INTO ont_detalle (Numero_Serie, Estatus_Ont_detalle, Carso_Vale, Carso_FechaSalida, division_carso)
                    VALUES (:numero_serie, 'NO LOCALIZADAS', :carso_vale, :carso_fecha_salida, :division_carso)
                    ON DUPLICATE KEY UPDATE
                        Carso_Vale        = COALESCE(VALUES(Carso_Vale), Carso_Vale),
                        Carso_FechaSalida = COALESCE(VALUES(Carso_FechaSalida), Carso_FechaSalida),
                        division_carso    = COALESCE(VALUES(division_carso), division_carso)
                ";

                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':numero_serie', $numero_serie, PDO::PARAM_STR);
                // Para NULLs en PDO: bindValue con PARAM_NULL si corresponde
                if ($carso_vale === null) {
                    $stmt->bindValue(':carso_vale', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':carso_vale', $carso_vale, PDO::PARAM_STR);
                }
                if ($carso_fecha_salida === null) {
                    $stmt->bindValue(':carso_fecha_salida', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':carso_fecha_salida', $carso_fecha_salida, PDO::PARAM_STR);
                }
                if ($division_carso === null) {
                    $stmt->bindValue(':division_carso', null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':division_carso', $division_carso, PDO::PARAM_STR);
                }

                return $stmt->execute();
            } catch (Exception $e) {
                error_log("Error upsert CARSO: " . $e->getMessage());
                return false;
            }
        }

        /**
         * Procesar archivo Excel de CARSO
         */
        public function procesarExcelCarso($archivo_excel)
        {
            try {
                // Verificar si el archivo existe
                if (!file_exists($archivo_excel)) {
                    return false;
                }
                
                // Incluir la librería SimpleXLSX si existe
                if (file_exists('../vendor/SimpleXLSX.php')) {
                    require_once '../vendor/SimpleXLSX.php';
                    
                    if ($xlsx = SimpleXLSX::parse($archivo_excel)) {
                        $registros_procesados = 0;
                        $registros_fallidos = 0;
                        
                        foreach ($xlsx->rows() as $k => $r) {
                            // Omitir la primera fila (encabezados)
                            if ($k == 0) continue;
                            
                            // Validar que tenga al menos 3 columnas
                            if (count($r) >= 3) {
                                $numero_serie = trim($r[0]);
                                $carso_vale = trim($r[1]);
                                $carso_fecha_salida = trim($r[2]);
                                
                                // Validar datos no vacíos
                                if (!empty($numero_serie) && !empty($carso_vale) && !empty($carso_fecha_salida)) {
                                    if ($this->insertarDatosCarso($numero_serie, $carso_vale, $carso_fecha_salida)) {
                                        $registros_procesados++;
                                    } else {
                                        $registros_fallidos++;
                                    }
                                }
                            }
                        }
                        
                        return $registros_procesados > 0;
                    }
                } else {
                    // Método alternativo básico para archivos CSV (si el Excel se guarda como CSV)
                    return $this->procesarCSVCarso($archivo_excel);
                }
                
                return false;
            } catch (Exception $e) {
                error_log("Error al procesar Excel CARSO: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Procesar archivo CSV alternativo
         */
        private function procesarCSVCarso($archivo)
        {
            try {
                $handle = fopen($archivo, "r");
                if ($handle !== FALSE) {
                    $primera_fila = true;
                    $registros_procesados = 0;
                    
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        // Omitir encabezados
                        if ($primera_fila) {
                            $primera_fila = false;
                            continue;
                        }
                        
                        if (count($data) >= 3) {
                            $numero_serie = trim($data[0]);
                            $carso_vale = trim($data[1]);
                            $carso_fecha_salida = trim($data[2]);
                            
                            if (!empty($numero_serie) && !empty($carso_vale) && !empty($carso_fecha_salida)) {
                                if ($this->insertarDatosCarso($numero_serie, $carso_vale, $carso_fecha_salida)) {
                                    $registros_procesados++;
                                }
                            }
                        }
                    }
                    fclose($handle);
                    return $registros_procesados > 0;
                }
                return false;
            } catch (Exception $e) {
                error_log("Error al procesar CSV CARSO: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Eliminar registro de CARSO
         */
        public function eliminarRegistroCarso($id)
        {
            try {
                $conexion = $this->get_conexion();
                $sql = "DELETE FROM ont_detalle WHERE idontDetalle = :id AND Estatus_Ont_detalle = 'CARSO'";
                $statement = $conexion->prepare($sql);
                $statement->bindParam(':id', $id, PDO::PARAM_INT);
                return $statement->execute();
            } catch (Exception $e) {
                error_log("Error al eliminar registro CARSO: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Procesar datos CARSO recibidos desde el frontend
         */
        public function procesarDatosCarso($datos_json)
        {
            try {
                $datos = json_decode($datos_json, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
                }
                
                if (empty($datos) || !is_array($datos)) {
                    throw new Exception('No hay datos válidos para procesar');
                }
                
                $registros_procesados = 0;
                $registros_insertados = 0;
                $registros_duplicados = 0;
                $duplicados_detalle = [];
                
                foreach ($datos as $registro) {
                    if (!isset($registro['numero_serie']) || !isset($registro['numero_vale']) || !isset($registro['fecha_salida'])) {
                        continue; // Saltar registros incompletos
                    }
                    
                    $numero_serie = trim($registro['numero_serie']);
                    $numero_vale = trim($registro['numero_vale']);
                    $fecha_salida = trim($registro['fecha_salida']);
                    $division = isset($registro['division']) ? trim($registro['division']) : null;
                    
                    if (empty($numero_serie) || empty($numero_vale)) {
                        continue; // Saltar registros vacíos
                    }
                    
                    $registros_procesados++;
                    
                    // Intentar insertar
                    $resultado = $this->insertarDatosCarso($numero_serie, $numero_vale, $fecha_salida, $division);
                    
                    if ($resultado) {
                        $registros_insertados++;
                    } else {
                        $registros_duplicados++;
                        $duplicados_detalle[] = [
                            'numero_serie' => $numero_serie,
                            'numero_vale' => $numero_vale
                        ];
                    }
                }
                
                return [
                    'success' => true,
                    'message' => 'Procesamiento completado',
                    'registros_procesados' => $registros_procesados,
                    'registros_insertados' => $registros_insertados,
                    'registros_duplicados' => $registros_duplicados,
                    'duplicados_detalle' => $duplicados_detalle
                ];
                
            } catch (Exception $e) {
                error_log("Error al procesar datos CARSO: " . $e->getMessage());
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        /**
         * Análisis de Vales CARSO vs ERP
         * Compara la cantidad de ONTs por vale en CARSO vs las registradas en ERP
         */
        public function AnalisisVale($Fecha1, $Fecha2)
        {
            $conexion = $this->get_conexion();
            
            $sql = "SELECT 
                        COALESCE(Carso_Vale, 'SIN_VALE_CARSO') AS Carso_Vale,
                        COUNT(*) AS Total_ONTs,
                        SUM(CASE WHEN IN_Diario_T IS NOT NULL THEN 1 ELSE 0 END) AS ONT_ERP,
                        SUM(CASE WHEN Carso_Vale IS NULL THEN 0 ELSE 1 END) AS ONT_CARSO,
                        (SUM(CASE WHEN Carso_Vale IS NULL THEN 0 ELSE 1 END) - 
                         SUM(CASE WHEN IN_Diario_T IS NOT NULL THEN 1 ELSE 0 END)) AS Diferencia
                    FROM ont_detalle
                    WHERE (
                        Fecha_Entrada_Almacen BETWEEN :Fecha1 AND :Fecha2
                        OR Carso_FechaSalida BETWEEN :Fecha1 AND :Fecha2
                        OR Fecha_Liq_Tac BETWEEN :Fecha1 AND :Fecha2
                    )
                    GROUP BY COALESCE(Carso_Vale, 'SIN_VALE_CARSO')
                    ORDER BY Total_ONTs DESC";
                    
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }

    }

?>