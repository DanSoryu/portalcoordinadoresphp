<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/PortalCoordinadores/cnx/cnx.php";

class Auth extends Conexion
{
    // Método para autenticar a un usuario
    public function autenticar($usuario, $password)
    {
        try {
            $conexion = $this->get_conexion();
            $query = "SELECT * FROM usuarios_coordinadores WHERE usuario = :usuario";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario && password_verify($password, $usuario['password'])) {
                return $usuario;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en autenticar: " . $e->getMessage());
            return false;
        }
    }
}
