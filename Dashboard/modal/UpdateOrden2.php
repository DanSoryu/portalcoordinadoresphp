<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once("../controller/ClassOps.php");

    $modal = new Ops();
    $Folio_Pisa = $_POST['Folio_Pisa'];
    $Telefono = $_POST['Telefono'];
    $FK_Cope = $_POST['FK_Cope'];
    $Ont = $_POST['Ont'];
    $idtecnico_instalaciones_coordiapp = $_POST['idtecnico_instalaciones_coordiapp'];

    $update = $modal->SetOntTecnico($Ont);
    $insert = $modal->SetOrdenUpdate($Folio_Pisa, $Telefono, $FK_Cope, $Ont, $idtecnico_instalaciones_coordiapp);
    $errors = "";
    $messages = ""; 
    if ($insert){
        $messages = "Datos Registrados Correctamente!.";
    } else{
        $errors = "Lo siento algo ha salido mal intenta nuevamente.";
    }

    ?>
        <script>
             function load() {
                alert("Actualizacion Exitosa!");
            }
            window.onload = load;
        </script>
    <?php
    header("refresh:1;url=../OrdenesCoordiapp.php");
    
?>