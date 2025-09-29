<?php

    class Conexion
    {
        public function get_conexion()
        {
            try {
                $user = "erpintr1";     
                $pass = "#k1u3T3f5";   
                $host = "74.208.237.139";
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