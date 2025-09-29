<?php
    header("Access-Control-Allow-Origin: *");
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Conexión a la base de datos
    $mysqli = new mysqli("74.208.237.139", "erpintr1", "#k1u3T3f5", "erpintr1_erp");
    if ($mysqli->connect_errno) {
        die("Fallo la conexión: " . $mysqli->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fotografia'])) {

        $folio_pisa = isset($_POST['folio_pisa']) ? $_POST['folio_pisa'] : '';
        $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

        $carpeta_destino = "../imagesCordiapp/" . $tipo . "/";

        $nombre_archivo = $carpeta_destino . $folio_pisa . '.jpg';

        if (move_uploaded_file($_FILES['fotografia']['tmp_name'], $nombre_archivo)) {

            // Determinar la columna a actualizar según el tipo
            if ($tipo === 'fotoSerie') {
                $columnmysql = 'No_Serie_ONT';
            } elseif ($tipo === 'fotoONT') {
                $columnmysql = 'Foto_Ont';
            } elseif ($tipo === 'foto_INE') {
                $columnmysql = 'Foto_INE';
            } elseif ($tipo === 'foto_casa_cliente') {
                $columnmysql = 'Foto_Casa_Cliente';
            } elseif ($tipo === 'foto_puerto') {
                $columnmysql = 'Foto_Puerto';
            } else {
                echo "Tipo de fotografía no válido.";
                exit;
            }

            // Guardar la ruta relativa para la base de datos
            $ruta_bd = $nombre_archivo;

            $sql = "UPDATE tecnico_instalaciones_coordiapp SET $columnmysql = '$ruta_bd' WHERE folio_pisa = '$folio_pisa'";
            if ($mysqli->query($sql)) {
                $mysqli->close();
                echo "ok";
            } else {
                echo "Error al guardar en la base de datos: " . $mysqli->error;
            }

        } else {
            echo "Error al guardar la fotografía.";
        }
    } else {
        echo "No se ha enviado el formulario correctamente.";
    }
?>