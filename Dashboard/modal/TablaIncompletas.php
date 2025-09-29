<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassOps.php");
session_start();

$Usuario = $_SESSION['Usuario'] ?? '';
$idUsuarios = $_SESSION['idUsuarios'] ?? '';
$Fecha1 = $_POST['Fecha1'] ?? '';
$Fecha2 = $_POST['Fecha2'] ?? '';
$mes_anterior = date('Y-m-d', strtotime('-1 month'));

if (empty($Fecha1)) {
    $Fecha1 = $mes_anterior . " 00:00:00";
    $Fecha2 = date('Y-m-d') . " 23:59:59";
}
if (!empty($Fecha1) && !empty($Fecha2)) {
    $Fecha1 = date('Y-m-d', strtotime($Fecha1)) . " 00:00:00";
    $Fecha2 = date('Y-m-d', strtotime($Fecha2)) . " 23:59:59";
}

$modal = new Ops();
$filas = $modal->OrdenesCoordiAppIncompletas($Fecha1, $Fecha2);

function distanciaEuclidiana($lat1, $lon1, $lat2, $lon2)
{
    $radioTierra = 6371;
    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);
    $dLat = $lat2Rad - $lat1Rad;
    $dLon = $lon2Rad - $lon1Rad;
    $a = sin($dLat / 2) ** 2 + cos($lat1Rad) * cos($lat2Rad) * sin($dLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $radioTierra * $c;
}
?>
<table class="table table-bordered display nowrap" style="width:100%" id="ventas">
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>ONT</th>
            <th>Contratista</th>
            <th>Tecnico</th>
            <th>Expediente</th>
            <th>Cope</th>
            <th>Area</th>
            <th>Division</th>
            <th>Distrito</th>
            <th>Tecnologia</th>
            <th>Tipo Tarea</th>
            <th>Tipo Instalacion</th>
            <th>Metraje</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th>Terminal</th>
            <th>Puerto</th>
            <th>Nombre Cliente</th>
            <th>Direccion Cliente</th>
            <th>Latitud</th>
            <th>Longitud</th>
            <th>Ultimo Paso</th>
            <th>Accion</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>ONT</th>
            <th>Contratista</th>
            <th>Tecnico</th>
            <th>Expediente</th>
            <th>Cope</th>
            <th>Area</th>
            <th>Division</th>
            <th>Distrito</th>
            <th>Tecnologia</th>
            <th>Tipo Tarea</th>
            <th>Tipo Instalacion</th>
            <th>Metraje</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th>Terminal</th>
            <th>Puerto</th>
            <th>Nombre Cliente</th>
            <th>Direccion Cliente</th>
            <th>Latitud</th>
            <th>Longitud</th>
            <th>Ultimo Paso</th>
            <th>Accion</th>
        </tr>
    </tfoot>
    <tbody>
    <?php
    foreach ($filas as $row) {
        // Solo el usuario 90 ve solo FK_Contratista_Tecnico 111, los demás ven todo
        if ($idUsuarios == 90 && !in_array($row['FK_Contratista_Tecnico'], [111, 120, 34])) {
            continue;
        }

        // Contratista nombre completo
        $contratista = trim($row['Contratista'] . " " . strtoupper($row['apellido_paterno'] ?? '') . " " . strtoupper($row['apellido_materno'] ?? ''));

        // Cliente nombre completo
        $cliente = trim(strtoupper($row['Cliente_Titular'] ?? '') . " " . strtoupper($row['Apellido_Paterno_Titular'] ?? '') . " " . strtoupper($row['Apellido_Materno_Titular'] ?? ''));

        // Acciones
        $acciones = "
            <a href='https://www.google.com/maps/search/?api=1&query={$row['Latitud']},{$row['Longitud']}&zoom=20' target='_blank'>
                <button type='button' class='btn btn-danger' data-toggle='modal' data-target='#modalForm'>
                    <i class='fas fa-map-marker'></i>
                </button>
            </a>
            <a href='modal/Galeria.php?Folio_Pisa={$row['Folio_Pisa']}' target='_blank'>
                <button type='button' class='btn btn-success' data-toggle='modal' data-target='#modalForm'>
                    <i class='fas fa-images'></i>
                </button>
            </a>
        ";
        if (
            in_array($_SESSION['Usuario'], ['RYMVIEW', 'JMWU', 'RCM', 'JULIO', 'FIGR', 'SERRATOSADMIN','GFMT']) ||
            ($_SESSION['TipoUsuario'] ?? '') === 'Admin'
        ) {
            $acciones .= "
                <a href='modal/UpdateOrden.php?idtecnico_instalaciones_coordiapp={$row['idtecnico_instalaciones_coordiapp']}' target='_blank'>
                    <button type='button' class='btn btn-success' data-toggle='modal' data-target='#modalForm'>
                        <i class='fa-solid fa-pen-to-square'></i>
                    </button>
                </a>
            ";
        }

        echo "<tr>
            <td>{$row['Folio_Pisa']}</td>
            <td>{$row['Telefono']}</td>
            <td>{$row['Ont']}</td>
            <td>{$contratista}</td>
            <td>{$row['Nombre_T']} {$row['Apellidos_T']}</td>
            <td>{$row['NExpediente']}</td>
            <td>{$row['COPE']}</td>
            <td>{$row['area']}</td>
            <td>{$row['Division']}</td>
            <td>{$row['Distrito']}</td>
            <td>{$row['Tecnologia']}</td>
            <td>{$row['Tipo_Tarea']}</td>
            <td>{$row['Tipo_Instalacion']}</td>
            <td>{$row['Metraje']}</td>
            <td>{$row['Fecha_Coordiapp']}</td>
            <td>{$row['Estatus_Orden']}</td>
            <td>{$row['Terminal']}</td>
            <td>{$row['Puerto']}</td>
            <td>{$cliente}</td>
            <td>" . strtoupper($row['Direccion_Cliente'] ?? '') . "</td>
            <td>{$row['Latitud']}</td>
            <td>{$row['Longitud']}</td>
            <td>{$row['Step_Registro']}</td>
            <td>{$acciones}</td>
        </tr>";
    }
    ?>
    </tbody>
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