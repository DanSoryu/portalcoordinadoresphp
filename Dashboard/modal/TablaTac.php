<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");

    $modal = new Ops();
    session_start();

    $Usuario = $_SESSION['Usuario'];

    $Fecha1 = $_POST['Fecha1'];
    $Fecha2 = $_POST['Fecha2'];

    $mes_anterior = date('Y-m-d', strtotime('-1 week'));

    if(empty($Fecha1)){
        $Fecha1 = $mes_anterior." 00:00:00";
        $Fecha2 = date('Y-m-d')." 23:59:59";
        $filas = $modal->OrdenesTac($Fecha1,$Fecha2);
    }elseif(!empty($Fecha1) && !empty($Fecha2)){
        $Fecha1 = $_POST['Fecha1']." 00:00:00"; 
        $Fecha2 = $_POST['Fecha2']." 23:59:59";
        $filas = $modal->OrdenesTac($Fecha1,$Fecha2);
    }
?>
<table class="table table-bordered display nowrap" style="width:100%" id="ventas">			
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Expediente</th>
            <th>Tecnico</th>
            <th>Cope</th>
            <th>Area</th>
            <th>Division</th>
            <th>Distrito</th>
            <th>Tecnologia</th>
            <th>Tipo Tarea</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th>Origen</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Expediente</th>
            <th>Tecnico</th>
            <th>Cope</th>
            <th>Area</th>
            <th>Division</th>
            <th>Distrito</th>
            <th>Tecnologia</th>
            <th>Tipo Tarea</th>
            <th>Fecha</th>
            <th>Estatus</th>
        </tr>
    </tfoot>
    <?php
    foreach($filas as $row){
        echo "
        <tr>
            <td>".$row['Folio_Pisa']."</td>
            <td>".$row['TELEFONO']."</td>
            <td>".$row['Expediente']."</td>
            <td>".$row['Tecnico']."</td>
            <td>".$row['NOM_CT']."</td>
            <td>".$row['NOM_AREA']."</td>
            <td>".$row['NOM_DIVISION']."</td>
            <td>".$row['DISTRITO']."</td>
            <td>".$row['ORIGEN']."</td>
            <td>".$row['Tipo_tarea']."</td>
            <td>".$row['FECHA_LIQ']."</td>
            <td>".$row['Queja_status']."</td>
        </tr>";
    }
    ?>
</table>
<script>
	$(document).ready(function () {
        let table = new DataTable('#ventas', {
            fixedColumns: {
                start: 1
            },
            responsive:true,
            'language':{
                'URL':'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
            },
            "ordering": false,
            "searching": true,
				"scrollY": true,
				"scrollX": true
        });
    
        new DataTable.Buttons(table, {
                buttons: [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fas fa-file-excel"></i> ',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend:    'pdfHtml5',
                        text:      '<i class="fas fa-file-pdf"></i> ',
                        titleAttr: 'Exportar a PDF',
                        className: 'btn btn-danger'
                    }
                ],
        });
        
        table
            .buttons(0, null)
            .container()
            .prependTo(table.table().container());
	});
</script>