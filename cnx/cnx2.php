<?php

    class Conexiones
    {
        public function get_conexiones()
        {
            try {
                $user = "root";     
                $pass = "";   
                $host = "localhost";
                $db = "erpintr1_erp";
                $conexion = new PDO("mysql:host=$host;dbname=$db",$user,$pass);               
                $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                if(!$conexion) throw new Exception('Error de conexion MSSQL: ');

                return $conexion;

            } catch(PDOException $e) {
                return $e->getMessage();
            }
        }
    }

?>