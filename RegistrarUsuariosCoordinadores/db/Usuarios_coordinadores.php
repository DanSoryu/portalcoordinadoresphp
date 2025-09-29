<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/PortalCoordinadores/cnx/cnx.php";

class Usuarios_coordinadores extends Conexion
{
    // Método para obtener todos los usuarios coordinadores
    public function obtenerUsuariosCoordinadores()
    {
        try {
            $conexion = $this->get_conexion();
            $query = "SELECT 
                        uc.idusuarios_coordinadores, 
                        uc.usuario,
                        GROUP_CONCAT(c.COPE ORDER BY c.COPE SEPARATOR ', ') as copes_asignados
                     FROM usuarios_coordinadores uc
                     LEFT JOIN coordinador_cope cc ON uc.idusuarios_coordinadores = cc.FK_Coordinador
                     LEFT JOIN copes c ON cc.FK_Cope = c.id
                     GROUP BY uc.idusuarios_coordinadores, uc.usuario
                     ORDER BY uc.usuario";
            
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuariosCoordinadores: " . $e->getMessage());
            return array();
        }
    }

    // Método para verificar si un COPE ya está asignado a otro usuario
    private function verificarCopeAsignado($idCope, $idCoordinador)
    {
        try {
            $conexion = $this->get_conexion();
            $query = "SELECT COUNT(*) FROM coordinador_cope WHERE FK_Cope = :idCope AND FK_Coordinador != :idCoordinador";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':idCope', $idCope);
            $stmt->bindParam(':idCoordinador', $idCoordinador);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en verificarCopeAsignado: " . $e->getMessage());
            throw $e;
        }
    }

    // Método para eliminar un COPE específico de un usuario
    private function eliminarCope($idCoordinador, $idCope)
    {
        try {
            $conexion = $this->get_conexion();
            $query = "DELETE FROM coordinador_cope WHERE FK_Coordinador = :idCoordinador AND FK_Cope = :idCope";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':idCoordinador', $idCoordinador);
            $stmt->bindParam(':idCope', $idCope);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en eliminarCope: " . $e->getMessage());
            throw $e;
        }
    }

    // Método para actualizar un usuario coordinador
    public function actualizarUsuarioCoordinador($id, $usuario, $password, $copes = [])
    {
        try {
            // Verificar si el usuario ya existe (excluyendo el usuario actual)
            if ($this->verificarUsuarioExistente($usuario, $id)) {
                throw new PDOException('El nombre de usuario ya existe en el sistema');
            }

            $conexion = $this->get_conexion();
            $conexion->beginTransaction();

            // Actualizar datos del usuario coordinador
            if ($password) {
                $query = "UPDATE usuarios_coordinadores SET usuario = :usuario, password = :password WHERE idusuarios_coordinadores = :id";
                $stmt = $conexion->prepare($query);
                $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt->bindParam(':password', $hashed_password);
            } else {
                $query = "UPDATE usuarios_coordinadores SET usuario = :usuario WHERE idusuarios_coordinadores = :id";
                $stmt = $conexion->prepare($query);
            }
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();

            // Obtener COPEs actuales del usuario
            $queryCopesActuales = "SELECT FK_Cope FROM coordinador_cope WHERE FK_Coordinador = :idCoordinador";
            $stmtCopesActuales = $conexion->prepare($queryCopesActuales);
            $stmtCopesActuales->bindParam(':idCoordinador', $id);
            $stmtCopesActuales->execute();
            $copesActuales = $stmtCopesActuales->fetchAll(PDO::FETCH_COLUMN);

            // Convertir todos los COPEs a enteros para comparación correcta
            $copesActuales = array_map('intval', $copesActuales);
            $copes = array_map('intval', $copes);

            // Identificar COPEs a eliminar (los que tenía y ya no están seleccionados)
            $copesAEliminar = array_diff($copesActuales, $copes);
            foreach ($copesAEliminar as $idCope) {
                $this->eliminarCope($id, $idCope);
            }

            // Identificar COPEs a agregar (los que están seleccionados y no tenía)
            $copesAAgregar = array_diff($copes, $copesActuales);
            foreach ($copesAAgregar as $idCope) {
                if ($this->verificarCopeAsignado($idCope, $id)) {
                    throw new PDOException("El COPE con ID $idCope ya está asignado a otro usuario");
                }
                $queryRelacion = "INSERT INTO coordinador_cope (FK_Coordinador, FK_Cope) VALUES (:idCoordinador, :idCope)";
                $stmtRelacion = $conexion->prepare($queryRelacion);
                $stmtRelacion->bindParam(':idCoordinador', $id);
                $stmtRelacion->bindParam(':idCope', $idCope);
                $stmtRelacion->execute();
            }

            $conexion->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conexion) && $conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en actualizarUsuarioCoordinador: " . $e->getMessage());
            throw $e;
        }
    }


    // Método modificado para verificar usuario existente (soporta edición)
    public function verificarUsuarioExistente($usuario, $id_excluir = null)
    {
        try {
            $conexion = $this->get_conexion();
            if ($id_excluir) {
                $query = "SELECT COUNT(*) FROM usuarios_coordinadores 
                         WHERE usuario = :usuario AND idusuarios_coordinadores != :id";
                $stmt = $conexion->prepare($query);
                $stmt->bindParam(':id', $id_excluir);
            } else {
                $query = "SELECT COUNT(*) FROM usuarios_coordinadores WHERE usuario = :usuario";
                $stmt = $conexion->prepare($query);
            }
            
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en verificarUsuarioExistente: " . $e->getMessage());
            throw $e;
        }
    }

    // Método para registrar un nuevo usuario coordinador
    public function registrarUsuarioCoordinador($usuario, $password, $copes = [])
    {
        try {
            $conexion = $this->get_conexion();
            $conexion->beginTransaction();

            // Insertar el usuario
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $query = "INSERT INTO usuarios_coordinadores (usuario, password) 
                     VALUES (:usuario, :password)";
            
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->execute();
            
            $idCoordinador = $conexion->lastInsertId();

            // Insertar las relaciones con los COPEs
            if (!empty($copes)) {
                $queryRelacion = "INSERT INTO coordinador_cope (FK_Coordinador, FK_Cope) VALUES (:idCoordinador, :idCope)";
                $stmtRelacion = $conexion->prepare($queryRelacion);

                foreach ($copes as $idCope) {
                    $stmtRelacion->bindParam(':idCoordinador', $idCoordinador);
                    $stmtRelacion->bindParam(':idCope', $idCope);
                    $stmtRelacion->execute();
                }
            }

            $conexion->commit();
            return true;
        } catch (PDOException $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en registrarUsuarioCoordinador: " . $e->getMessage());
            throw $e;
        }
    }

    // Método para obtener COPEs no asignados
    public function obtenerCopes()
    {
        try {
            $conexion = $this->get_conexion();
            $query = "SELECT c.id, c.COPE 
                     FROM copes c 
                     WHERE NOT EXISTS (
                         SELECT 1 
                         FROM coordinador_cope cc 
                         WHERE cc.FK_Cope = c.id
                     )
                     ORDER BY c.COPE";
            
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCopes: " . $e->getMessage());
            return array();
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

    // Método para obtener todos los COPEs (asignados y no asignados)
    public function obtenerTodosLosCopes()
    {
        try {
            $conexion = $this->get_conexion();
            $query = "SELECT c.id, c.COPE 
                     FROM copes c 
                     ORDER BY c.COPE";
            
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerTodosLosCopes: " . $e->getMessage());
            return array();
        }
    }

     // Método para obtener COPEs disponibles para edición (no asignados o asignados al usuario actual)
    public function obtenerCopesDisponiblesParaEdicion($idCoordinador)
    {
        try {
            $conexion = $this->get_conexion();
            $query = "SELECT c.id, c.COPE
                      FROM copes c
                      LEFT JOIN coordinador_cope cc ON c.id = cc.FK_Cope
                      WHERE cc.FK_Coordinador IS NULL OR cc.FK_Coordinador = :idCoordinador
                      ORDER BY c.COPE";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':idCoordinador', $idCoordinador);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerCopesDisponiblesParaEdicion: " . $e->getMessage());
            return array();
        }
    }

    public function eliminarUsuarioCoordinador($id)
    {
        try {
            $conexion = $this->get_conexion();

            // Eliminar registros de la tabla coordinador_cope relacionados con el usuario coordinador
            $queryEliminarCopes = "DELETE FROM coordinador_cope WHERE FK_Coordinador = :id";
            $stmtEliminarCopes = $conexion->prepare($queryEliminarCopes);
            $stmtEliminarCopes->bindParam(':id', $id);
            $stmtEliminarCopes->execute();

            // Eliminar el usuario coordinador
            $query = "DELETE FROM usuarios_coordinadores WHERE idusuarios_coordinadores = :id";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':id', $id);

            if (!$stmt->execute() || $stmt->rowCount() === 0) {
                throw new PDOException("No se pudo eliminar el usuario o no existe");
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error en eliminarUsuarioCoordinador: " . $e->getMessage());
            throw $e;
        }
    }
}
?>