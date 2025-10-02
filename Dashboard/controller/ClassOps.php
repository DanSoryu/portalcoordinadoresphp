<?php 

	/**
	 * DESARROLLADO ING. ANTONIO CARDONA
	 */
    require_once __DIR__ . '/../cnx/cnx.php';

    class Ops extends Conexion
    {
        public function CountOrdenesCoordiAppIncompletos($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL FROM View_Detalle_Coordiapp_Incompletas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }
        
        public function CountOrdenesCoordiApp($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }
        
        public function OrdenesCoordiApp($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function OrdenesCoordiAppIncompletas($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM View_Detalle_Coordiapp_Incompletas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function OrdenesTac($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion_analytics();
            $sql="SELECT * FROM qm_tac_prod_bolsa	
            WHERE FECHA_LIQ BETWEEN :Fecha1 and :Fecha2";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }
        
        public function CountOrdenesTac($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion_analytics();
            $sql="SELECT COUNT(*) AS TOTAL FROM qm_tac_prod_bolsa
            WHERE FECHA_LIQ BETWEEN :Fecha1 and :Fecha2";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function TecnologiaKpi($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, Tecnologia FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2
            GROUP BY Tecnologia
            ORDER BY TOTAL DESC
            LIMIT 10";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function CountTopTecnico($Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico, NExpediente FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2
            GROUP BY NExpediente
            ORDER BY TOTAL DESC
            LIMIT 10";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function ProduccionDivisiones($Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, Division FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2
            GROUP BY Division
            ORDER BY TOTAL DESC
            LIMIT 10";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function ListDivisiones()
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM divisiones";
            $statement = $conexion->prepare($sql);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }  

        public function GetFotos($Folio_Pisa)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM tecnico_instalaciones_coordiapp 
            WHERE Folio_Pisa = :Folio_Pisa";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Pisa', $Folio_Pisa, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        } 
        
        public function GetOrdenUpdate($idtecnico_instalaciones_coordiapp)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM tecnico_instalaciones_coordiapp
            LEFT JOIN copes ON tecnico_instalaciones_coordiapp.FK_Cope = copes.id
            WHERE idtecnico_instalaciones_coordiapp = :idtecnico_instalaciones_coordiapp";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':idtecnico_instalaciones_coordiapp', $idtecnico_instalaciones_coordiapp, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function SetOrdenUpdate($idtecnico_instalaciones_coordiapp, $Folio_Pisa, $Telefono, $Ont, $Terminal, $Puerto, $Metraje, $Observaciones, $No_Serie_ONT)
        {
            $query = "UPDATE tecnico_instalaciones_coordiapp SET 
                    Folio_Pisa = ?, 
                    Telefono = ?, 
                    Ont = ?, 
                    Terminal = ?, 
                    Puerto = ?, 
                    Metraje = ?, 
                    Observaciones = ?, 
                    No_Serie_ONT = ? 
                    WHERE idtecnico_instalaciones_coordiapp = ?";
            
            $statement = $this->get_conexion()->prepare($query);
            
            $statement->bindParam(1, $Folio_Pisa, PDO::PARAM_STR);
            $statement->bindParam(2, $Telefono, PDO::PARAM_STR);
            $statement->bindParam(3, $Ont, PDO::PARAM_STR);
            $statement->bindParam(4, $Terminal, PDO::PARAM_STR);
            $statement->bindParam(5, $Puerto, PDO::PARAM_STR);
            $statement->bindParam(6, $Metraje, PDO::PARAM_STR);
            $statement->bindParam(7, $Observaciones, $Observaciones === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $statement->bindParam(8, $No_Serie_ONT, PDO::PARAM_STR);
            $statement->bindParam(9, $idtecnico_instalaciones_coordiapp, PDO::PARAM_INT);
            
            $statement->execute();
            
            return $statement->rowCount();
        }

        public function UpdateTecnicoInstalacionFlexible(int $id, array $data): int
        {
            // Whitelist de columnas permitidas
            $whitelist = [
                'Folio_Pisa', 'FK_Cope', 'Latitud', 'Longitud', 'Fecha_Coordiapp','Distrito', 
                'Puerto', 'Terminal', 'Tipo_Tarea', 'Estatus_Orden', 'Metraje', 
                'Tecnologia', 'Telefono', 'Tipo_Instalacion', 'Cliente_Titular', 'Cliente_Recibe', 
                'Step_Registro', 'Direccion_Cliente', 'Telefono_Cliente', 'Ont', 'Latitud_Terminal', 
                'Longitud_Terminal', 'Tipo_Orden', 'Tipo_reparacion', 'Tipo_sub_reparaviob', 
                'Codigo_Liquidacion', 'Descripcion_Queja', 'NOM_CT', 'Numero_serie_Cobre'
            ];
            
            // Exclusiones absolutas
            $exclusions = [
                'No_Serie_ONT', 'Foto_Casa_Cliente', 'Foto_INE', 'fk_distrito', 'FK_Tecnico_apps', 
                'Foto_Puerto', 'FK_Auditor', 'Fecha_Asignacion_Auditor', 'Foto_Ont_Hash', 
                'No_Serie_ONT_Hash', 'Foto_Casa_Cliente_Hash', 'Foto_INE_Hash', 'Foto_Puerto_Hash', 
                'fk_validacion_orden', 'Foto_Frontal_OntRetirada', 'Foto_Trasera_OntRetirada', 
                'Foto_frontal_Cobre', 'Foto_trasera_Cobre'
            ];
            
            // Columnas de tipo entero
            $intColumns = ['Folio_Pisa', 'FK_Cope', 'Metraje', 'Step_Registro'];
            
            // Filtrar datos válidos
            $validData = [];
            foreach ($data as $column => $value) {
                if (in_array($column, $whitelist) && !in_array($column, $exclusions)) {
                    // Normalizar strings
                    if (is_string($value)) {
                        $value = trim($value);
                        if ($value === '') {
                            $value = null;
                        }
                    }
                    $validData[$column] = $value;
                }
            }
            
            // Si no hay columnas válidas, retornar 0
            if (empty($validData)) {
                return 0;
            }
            
            // Construir SQL dinámico
            $setParts = [];
            foreach (array_keys($validData) as $column) {
                $setParts[] = "$column = :$column";
            }
            
            $sql = "UPDATE tecnico_instalaciones_coordiapp SET " . 
                implode(', ', $setParts) . 
                " WHERE idtecnico_instalaciones_coordiapp = :id LIMIT 1";
            
            $conexion = $this->get_conexion();
            $statement = $conexion->prepare($sql);
            
            // Bind de parámetros
            foreach ($validData as $column => $value) {
                if ($value === null) {
                    $statement->bindParam(":$column", $validData[$column], PDO::PARAM_NULL);
                } elseif (in_array($column, $intColumns)) {
                    $statement->bindParam(":$column", $validData[$column], PDO::PARAM_INT);
                } else {
                    $statement->bindParam(":$column", $validData[$column], PDO::PARAM_STR);
                }
            }
            
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            
            return $statement->rowCount();
        }

        public function EjecutarConsultaIA($sql)
        {
            $rows = [];
            $conexion = $this->get_conexion();

            try {
                $statement = $conexion->prepare($sql);
                $statement->execute();
                while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $rows[] = $result;
                }
            } catch (PDOException $e) {
                // Opcional: podrías registrar el error en logs o mostrar un mensaje controlado
                error_log("Error en EjecutarConsultaIA: " . $e->getMessage());
            }

            return $rows;
        }

        public function GetNumSerieSalidaDetTecnico()
        {
            $rows = [];
            $conexion = $this->get_conexion();
            $sql = "SELECT Num_Serie_Salida_Det FROM salidas_contratistas 
            WHERE Ont_Ubicacion = 'TECNICO' 
            ORDER BY Num_Serie_Salida_Det ASC";
            $statement = $conexion->prepare($sql);
            $statement->execute();
            while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = $result;
            }
            return $rows;
        }

        public function SetOntTecnico($Num_Serie_Salida_Det)
        {
            $conexion = $this->get_conexion();
            $sql = "UPDATE salidas_contratistas 
                    SET Ont_Ubicacion = 'TERMINADO'
                    WHERE Num_Serie_Salida_Det = :Num_Serie_Salida_Det 
                    AND Ont_Ubicacion = 'TECNICO'";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Num_Serie_Salida_Det', $Num_Serie_Salida_Det, PDO::PARAM_STR);

            try {
                $statement->execute();
                return $statement->rowCount();
            } catch (PDOException $e) {
                // Puedes registrar el error si lo deseas: error_log($e->getMessage());
                return false;
            }
        }

        public function ListOrdenesFotografias()
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT Folio_Pisa, Foto_Casa_Cliente, Foto_INE, Foto_Puerto, Foto_Ont, No_Serie_ONT FROM tecnico_instalaciones_coordiapp 
            ORDER BY Folio_Pisa ASC";
            $statement = $conexion->prepare($sql);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function ListCopes()
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM copes";
            $statement = $conexion->prepare($sql);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function getOrden($Folio_Pisa){
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM View_Detalle_Coordiapp_Completadas where Folio_Pisa = :Folio_Pisa";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Pisa', $Folio_Pisa, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }
    }

?>