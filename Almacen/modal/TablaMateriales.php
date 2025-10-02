<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../Login/Login.php');
    exit();
}
 
$Usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Dashboard</title>
    <!-- Custom fonts for this template -->
    <link href="/Operaciones/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/Operaciones/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>
    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Preloader styles -->
    <link rel="stylesheet" href="../RegistrarUsuariosCoordinadores/vistas/assets/css/preloader.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Nunito', sans-serif; }
        .main-header { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); position: fixed; top: 0; left: 0; right: 0; z-index: 1000; }
        .header-container { max-width: 100%; margin: 0 auto; padding: 0 1.5rem; }
        .header-content { display: flex; justify-content: space-between; align-items: center; height: 64px; }
        .header-left { display: flex; align-items: center; gap: 2rem; }
        .header-title { font-size: 1.25rem; font-weight: 700; margin: 0; white-space: nowrap; }
        .main-nav { display: flex; align-items: center; gap: 0.5rem; }
        .nav-item { position: relative; }
        .nav-link { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; color: white; text-decoration: none; border-radius: 6px; transition: all 0.2s; font-size: 0.875rem; white-space: nowrap; }
        .nav-link:hover { background-color: rgba(255, 255, 255, 0.1); color: white; }
        .nav-link i { font-size: 1rem; }
        .dropdown-toggle { background: none; border: none; cursor: pointer; }
        .dropdown-menu-custom { position: absolute; top: 100%; left: 0; margin-top: 0.5rem; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); min-width: 250px; padding: 0.5rem 0; display: none; z-index: 1001; }
        .dropdown-menu-custom.show { display: block; }
        .dropdown-header-custom { padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .dropdown-item-custom { display: block; padding: 0.5rem 1rem; color: #374151; text-decoration: none; transition: all 0.2s; font-size: 0.875rem; }
        .dropdown-item-custom:hover { background-color: #f3f4f6; color: #2563eb; }
        .user-menu { position: relative; }
        .user-button { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; background: none; border: none; color: white; cursor: pointer; border-radius: 6px; transition: all 0.2s; }
        .user-button:hover { background-color: rgba(255, 255, 255, 0.1); }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; }
        .user-avatar svg { width: 20px; height: 20px; color: #2563eb; }
        .user-name { font-weight: 500; font-size: 0.875rem; }
        .user-dropdown { position: absolute; right: 0; top: 100%; margin-top: 0.5rem; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); min-width: 200px; padding: 0.5rem 0; display: none; z-index: 1001; }
        .user-dropdown.show { display: block; }
        .user-dropdown-item { display: block; width: 100%; padding: 0.5rem 1rem; color: #374151; text-decoration: none; background: none; border: none; text-align: left; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; }
        .user-dropdown-item:hover { background-color: #f3f4f6; color: #dc2626; }
        .user-dropdown-item i { margin-right: 0.5rem; width: 16px; }
        .main-content { margin-top: 64px; padding: 2rem; }
        @media (max-width: 1024px) { .header-left { gap: 1rem; } .nav-link { padding: 0.5rem; font-size: 0.8rem; } .nav-link span { display: none; } .nav-link i { margin: 0; } }
        @media (max-width: 768px) { .header-title { font-size: 1rem; } .main-nav { display: none; } .user-name { display: none; } }
        .chevron-icon { width: 16px; height: 16px; transition: transform 0.2s; }
        .chevron-icon.rotate { transform: rotate(180deg); }
        .scroll-to-top { bottom: 20px !important; }
        /* Estilos para los filtros */
        .card { border: none; border-radius: 10px; }
        .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
        .card-body { padding: 1.25rem; }
        .font-weight-bold { font-weight: 700 !important; }
        .text-primary { color: #4e73df !important; }
        .form-label { font-weight: 600; color: #5a5c69; }
        .form-control { border-radius: 6px; border: 1px solid #d1d3e2; padding: 0.375rem 0.75rem; }
        .form-control:focus { border-color: #bac8f3; box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25); }
        .btn-primary { background-color: #4e73df; border-color: #4e73df; }
        .btn-primary:hover { background-color: #2e59d9; border-color: #2653d4; }
        .container-fluid { padding: 1.5rem; }
        #filtroForm { margin-bottom: 0; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <div class="header-content">
                <div class="header-left">
                    <h1 class="header-title">Portal Coordinadores</h1>
                    <!-- Navegación principal -->
                    <nav class="main-nav">
                        <div class="nav-item">
                            <button class="nav-link" id="modulosDropdown">
                                <i class="fas fa-folder"></i>
                                <span>Módulos</span>
                                <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="dropdown-menu-custom" id="modulosMenu">
                                <div class="dropdown-header-custom">Módulos</div>
                                <a class="dropdown-item-custom" href="../../Dashboard/Dashboard.php">
                                    <i class="fas fa-cogs"></i> Operaciones
                                </a>
                                <a class="dropdown-item-custom" href="../index.php">
                                    <i class="fas fa-warehouse"></i> Almacén
                                </a>
                            </div>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="../index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="TablaMateriales.php">
                                <i class="fas fa-table"></i>
                                <span>Material Instalado</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="TablaMaterialesNoInstalados.php">
                                <i class="fas fa-file-invoice"></i>
                                <span>Material sin Instalar</span>
                            </a>
                        </div>
                    </nav>
                </div>
                <!-- Menú de usuario -->
                <div class="user-menu">
                    <button class="user-button" id="userMenuButton">
                        <div class="user-avatar">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="user-name"><?php echo $Usuario; ?></span>
                        <svg class="chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdownMenu">
                        <a class="user-dropdown-item" href="#" data-toggle="modal" data-target="#modalLogout">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Filtros de fecha -->
    <div class="container-fluid mt-5 pt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
            </div>
            <div class="card-body">
                <form id="filtroForm" method="POST" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="Fecha1" class="form-label">Fecha Inicial</label>
                        <input type="date" class="form-control" id="Fecha1" name="Fecha1" value="<?php echo empty($Fecha1) ? $mes_anterior : date('Y-m-d', strtotime($Fecha1)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="Fecha2" class="form-label">Fecha Final</label>
                        <input type="date" class="form-control" id="Fecha2" name="Fecha2" value="<?php echo empty($Fecha2) ? date('Y-m-d') : date('Y-m-d', strtotime($Fecha2)); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/PortalCoordinadores/Login/vistas/components/Logout.php'); ?>
    <!-- jQuery primero -->
    <script src="/PortalCoordinadores/vendor2/jquery/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap core JavaScript (Bootstrap 4 for sb-admin-2)-->
    <script src="/PortalCoordinadores/vendor2/bootstrap/js/bootstrap.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="/PortalCoordinadores/vendor2/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="/PortalCoordinadores/js/sb-admin-2.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js"></script>
    <!-- Toastify -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Custom JS -->
    <script src="../vistas/assets/js/preloader.js"></script>
    <script src="../vistas/assets/js/toasts.js"></script>
    <script src="../vistas/assets/js/notifications.js"></script>
</body>
</html>
<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassMateriales.php");

    $modal = new Materiales();

    $Usuario = $_SESSION['usuario'];

    // Establecer valores por defecto para las fechas
    $mes_anterior = date('Y-m-d', strtotime('-1 month'));
    $hoy = date('Y-m-d');

    // Obtener fechas del POST o usar valores por defecto
    $Fecha1 = isset($_POST['Fecha1']) ? $_POST['Fecha1'] : $mes_anterior;
    $Fecha2 = isset($_POST['Fecha2']) ? $_POST['Fecha2'] : $hoy;

    // Inicializar $filas
    $filas = [];

    // Agregar hora a las fechas y obtener materiales
    $Fecha1 = $Fecha1." 00:00:00";
    $Fecha2 = $Fecha2." 23:59:59";
    $filas = $modal->Materiales($Fecha1,$Fecha2);

?>
<table class="table table-bordered display nowrap" style="width:100%" id="ventas">			
    <thead>
        <tr>
            <th>Folio Salida</th> 
            <th>Contratista</th>
            <th>Tecnico</th>
            <th>Orden Servicio</th>
            <th>Material</th>
            <th>Numero Serie</th>
            <th>Fecha Asignada</th>
            <th>Fecha Instalada</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Folio Salida</th> 
            <th>Contratista</th>
            <th>Tecnico</th>
            <th>Orden Servicio</th>
            <th>Material</th>
            <th>Numero Serie</th>
            <th>Fecha Asignada</th>
            <th>Fecha Instalada</th>
        </tr>
    </tfoot>
    <?php
    foreach($filas as $row){

        echo "
            <tr>
                <td>".$row['Folio_Salida_Det']."</td>
                <td>".strtoupper($row['Contratista'] ?? '')."</td>
                <td>".strtoupper($row['Tecnico'] ?? '')."</td>
                <td>".$row['Folio_Pisa']."</td>
                <td>".$row['Producto']."</td>
                <td>".$row['Num_Serie_Salida_Det']."</td>
                <td>".$row['Fecha_Salida_Det']."</td>
                <td>".$row['Fecha_Coordiapp']."</td>
            </tr>";
    }
    ?>
</table>
<script>
    // Dropdown de módulos
        document.getElementById('modulosDropdown').addEventListener('click', function(e) {
        e.stopPropagation();
        const menu = document.getElementById('modulosMenu');
        const chevron = this.querySelector('.chevron-icon');
        menu.classList.toggle('show');
        chevron.classList.toggle('rotate');
        });
        // Menú de usuario
        document.getElementById('userMenuButton').addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = document.getElementById('userDropdownMenu');
            const chevron = this.querySelector('.chevron-icon');
            menu.classList.toggle('show');
            chevron.classList.toggle('rotate');
        });
        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-menu-custom, .user-dropdown').forEach(function(menu) {
                menu.classList.remove('show');
            });
            document.querySelectorAll('.chevron-icon').forEach(function(chevron) {
                chevron.classList.remove('rotate');
            });
        });

	$(document).ready(function () {
        let table = new DataTable('#ventas', {
            fixedColumns: {
                start: 1,
                end: 1
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