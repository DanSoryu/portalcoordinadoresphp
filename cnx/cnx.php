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

        public function get_conexion_analytics()
        {
            $user = "analisis15";
            $pass = "8#1gnbB30";
            $host = "67.217.246.65";
            $db   = "analisis_bd";
            $dsn  = "mysql:host=$host;dbname=$db;charset=utf8";

            try {
                $conexion = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                return $conexion;
            } catch (PDOException $e) {
                // Loguear el error en producción en vez de mostrarlo
                die("Error de conexión a la base de datos remota: " . $e->getMessage());
            }
        }
    }

?>