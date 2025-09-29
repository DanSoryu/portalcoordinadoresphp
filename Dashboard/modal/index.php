<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    date_default_timezone_set('America/Monterrey');

    require_once("../Login/controller/Class_Login.php");
    require_once("controller/ClassOps.php");
    require_once("modal/cargar.php");

    session_start();

    $Usuario = $_SESSION['Usuario'];
    $Nombres = $_SESSION['Nombres'];
    $idUsuario = $_SESSION['idUsuarios'];

    if (empty($Usuario)) {
        header("location: ../Login/modal/poweroff.php");
    }

    $login = new Login();
    $modulos = $login->cargar_modulos($idUsuario);
    
?>
<!DOCTYPE html>
<html lang="en">    
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ERP - DASHBOARD</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    
    <style>
        .metric-card {
            border-left: 4px solid #4e73df;
            background: linear-gradient(45deg, #f8f9fc, #ffffff);
            transition: all 0.3s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .metric-card.success { border-left-color: #1cc88a; }
        .metric-card.warning { border-left-color: #f6c23e; }
        .metric-card.danger { border-left-color: #e74a3b; }
        .metric-card.info { border-left-color: #36b9cc; }
        
        .metric-number {
            font-size: 2rem;
            font-weight: 700;
            color: #5a5c69;
        }
        
        .area-progress {
            height: 0.5rem;
            border-radius: 0.25rem;
            margin-top: 0.5rem;
        }
        
        .area-card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .area-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .refresh-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, #4e73df, #224abe);
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(78, 115, 223, 0.6);
        }
        
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4e73df;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .folio-item {
            display: inline-block;
            background-color: #e74a3b;
            color: white;
            padding: 0.2rem 0.4rem;
            margin: 0.1rem;
            border-radius: 0.2rem;
            font-size: 0.7rem;
        }
        
        .btn-ver-mas {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-ver-mas:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .cope-item {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
        }
        
        .cope-item:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .cope-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .cope-title {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .cope-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
        }
        
        .cope-stat {
            text-align: center;
        }
        
        .cope-progress {
            height: 0.25rem;
            border-radius: 0.125rem;
            margin-top: 0.25rem;
        }
        
        .btn-xs {
            padding: 0.125rem 0.25rem;
            font-size: 0.7rem;
            line-height: 1.2;
            border-radius: 0.2rem;
        }
        
        .btn-ver-mas-cope {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-text mx-3">DASHBOARD</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard Principal</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                ACCION
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>MODULOS</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">MODULOS</h6>
                        <?php 
                            if(!empty($modulos)){
                                foreach($modulos as $mod){
                                    echo "<a class='collapse-item' href='".$mod['ruta_modulo']."'>".$mod['icon_fav']." ".$mod['nombre_modulo']."</a>";
                                }
                            }else{  
                                echo "<a class='collapse-item'>NO TIENE MODULO ASIGNADOS</a>";
                            }
                        ?>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="OrdenesCoordiapp.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION COORDIAPP COMPLETAS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="OrdenesIncompletas.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION COORDIAPP INCOMPLETAS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="OrdenesTac.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION TAC</span>
                </a>
            </li>
            
            <?php
                if (in_array($_SESSION['Usuario'], ['RYMVIEW', 'JMWU', 'RCM', 'JULIO', 'FIGR', 'SERRATOSADMIN']) || $_SESSION['TipoUsuario'] === 'Admin') {
                    echo '
                        <li class="nav-item">
                            <a class="nav-link" href="CargarFotografias.php">
                                <i class="fas fa-fw fa-table"></i>
                                <span>CARGAR FOTOGRAFIAS</span>
                            </a>
                        </li>';
                }
            ?>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="col-xl-10 col-md-10 mb-0" id="FormFiltros">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                            
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control bg-light border-0 small" 
                                   value="<?php echo date('Y-m-d'); ?>"
                                   aria-label="Fecha Inicio" placeholder="Fecha Inicio">
                            
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control bg-light border-0 small" 
                                   value="<?php echo date('Y-m-d'); ?>"
                                   aria-label="Fecha Fin" placeholder="Fecha Fin">
                            
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" onclick="cargarDashboard();">
                                    <i class="fas fa-search fa-sm"></i> Analizar
                                </button>
                                <button class="btn btn-outline-secondary" type="button" onclick="limpiarFiltros();" title="Limpiar filtros">
                                    <i class="fas fa-eraser fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $Nombres;?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    PERFIL
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    CERRAR SESION
                                </a>
                            </div>
                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h1 class="h3 mb-0 text-gray-800">
                                <i class="fas fa-chart-line text-primary"></i> Dashboard COORDIAPP
                            </h1>
                            <p class="text-muted mb-0" id="rango_fechas">Selecciona un rango de fechas para analizar</p>
                        </div>
                        <div class="text-right">
                            <small class="text-muted">Última actualización: <span id="ultima_actualizacion">Cargando...</span></small>
                            <br>
                            <span class="badge badge-info" id="total_dias">0 días analizados</span>
                        </div>
                    </div>
                    
                    <!-- Alert para mensajes -->
                    <div id="alertContainer"></div>
                    
                    <!-- Dashboard Content -->
                    <div id="dashboardContent">
                        <div class="loading-spinner"></div>
                        <div class="text-center mt-3">
                            <p class="text-muted">Cargando datos del dashboard...</p>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; ENLACE DIGITAL 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- Refresh Button -->
    <button class="refresh-btn" onclick="cargarDashboard();" title="Actualizar datos">
        <i class="fas fa-sync-alt" id="refreshIcon"></i>
    </button>
    
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CERRAR SESION</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">PRESIONA SALIR, PARA CERRAR LA SESSION.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">CANCELAR</button>
                    <a class="btn btn-primary" href="../Login/modal/poweroff.php">SALIR</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Chart.js -->
    <script src="vendor/chart.js/Chart.min.js"></script>
    
    <script>
        // Variables globales
        let chartCumplimiento = null;
        let chartAreas = null;
        let chartTemporal = null;
        
        $(document).ready(function(){
            cargarDashboard();
            
            // Auto-refresh cada 10 minutos para rangos
            setInterval(cargarDashboard, 600000);
        });
        
        function cargarDashboard() {
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();
            
            console.log('Cargando dashboard con fechas:', fechaInicio, fechaFin); // Debug
            
            // Validar fechas
            if (!fechaInicio || !fechaFin) {
                mostrarAlerta('warning', 'Por favor selecciona ambas fechas');
                return;
            }
            
            if (fechaInicio > fechaFin) {
                mostrarAlerta('warning', 'La fecha de inicio no puede ser mayor que la fecha fin');
                return;
            }
            
            // Calcular días
            const inicio = new Date(fechaInicio);
            const fin = new Date(fechaFin);
            const diferenciaDias = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
            
            // Actualizar información del rango
            actualizarInfoRango(fechaInicio, fechaFin, diferenciaDias);
            
            console.log('Fechas para actualizar rango:', fechaInicio, fechaFin); // Debug adicional
            
            // Mostrar loading
            $('#dashboardContent').html(`
                <div class="loading-spinner"></div>
                <div class="text-center mt-3">
                    <p class="text-muted">Analizando datos de COORDIAPP...</p>
                    <small class="text-info">Procesando ${diferenciaDias} día(s) de datos</small>
                </div>
            `);
            
            // Animar icono de refresh
            $('#refreshIcon').addClass('fa-spin');
            
            $.ajax({
                url: 'modal/DashboardData.php',
                type: 'POST',
                data: {
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin
                },
                dataType: 'json',
                cache: false,
                success: function(response) {
                    console.log('Datos recibidos:', response); // Debug
                    console.log('Fechas enviadas:', fechaInicio, fechaFin); // Debug
                    if (response.success) {
                        mostrarDashboard(response.data);
                        $('#ultima_actualizacion').text(new Date().toLocaleString());
                        mostrarAlerta('success', `Datos actualizados correctamente (${diferenciaDias} días analizados)`);
                    } else {
                        mostrarAlerta('danger', 'Error: ' + response.message);
                        console.error('Error del servidor:', response.message); // Debug
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    console.error('Respuesta del servidor:', xhr.responseText); // Debug
                    mostrarAlerta('danger', 'Error al cargar los datos del dashboard');
                },
                complete: function() {
                    $('#refreshIcon').removeClass('fa-spin');
                }
            });
        }

        function actualizarInfoRango(fechaInicio, fechaFin, dias) {
            // Formatear fechas de manera más confiable para evitar problemas de timezone
            const inicioFormatted = fechaInicio.split('-').reverse().join('/');
            const finFormatted = fechaFin.split('-').reverse().join('/');
            
            if (fechaInicio === fechaFin) {
                $('#rango_fechas').text(`Análisis del día: ${fechaInicio}`);
            } else {
                $('#rango_fechas').text(`Análisis del ${fechaInicio} al ${fechaFin}`);
            }
            
            $('#total_dias').text(`${dias} día${dias > 1 ? 's' : ''} analizados`);
        }
        
        function limpiarFiltros() {
            $('#fecha_inicio').val('<?php echo date('Y-m-d'); ?>');
            $('#fecha_fin').val('<?php echo date('Y-m-d'); ?>');
            cargarDashboard(); // Cargar inmediatamente después de limpiar
            mostrarAlerta('info', 'Filtros restablecidos');
        }
        
        function mostrarDashboard(data) {
            // Calcular si necesitamos mostrar gráfico temporal
            const mostrarGraficoTemporal = data.estadisticas_por_fecha && data.estadisticas_por_fecha.length > 1;
            
            const html = `
                <!-- Métricas Principales -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card metric-card info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total TAC</div>
                                        <div class="metric-number">${data.resumen.total_tac}</div>
                                        <small class="text-muted">Promedio: ${data.resumen.promedio_diario}/día</small>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card metric-card success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Registradas</div>
                                        <div class="metric-number">${data.resumen.total_registradas}</div>
                                        <small class="text-muted">Promedio: ${Math.round(data.resumen.total_registradas / data.resumen.dias_analizados * 10) / 10}/día</small>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card metric-card danger h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Faltantes</div>
                                        <div class="metric-number">${data.resumen.total_faltantes}</div>
                                        <small class="text-muted">Promedio: ${Math.round(data.resumen.total_faltantes / data.resumen.dias_analizados * 10) / 10}/día</small>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card metric-card ${data.resumen.porcentaje_cumplimiento >= 90 ? 'success' : data.resumen.porcentaje_cumplimiento >= 70 ? 'warning' : 'danger'} h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-${data.resumen.porcentaje_cumplimiento >= 90 ? 'success' : data.resumen.porcentaje_cumplimiento >= 70 ? 'warning' : 'danger'} text-uppercase mb-1">Cumplimiento</div>
                                        <div class="metric-number">${data.resumen.porcentaje_cumplimiento.toFixed(1)}%</div>
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-${data.resumen.porcentaje_cumplimiento >= 90 ? 'success' : data.resumen.porcentaje_cumplimiento >= 70 ? 'warning' : 'danger'}" 
                                                 role="progressbar" style="width: ${data.resumen.porcentaje_cumplimiento}%"></div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráficos -->
                <div class="row mb-4">
                    ${mostrarGraficoTemporal ? `
                        <div class="col-xl-12 col-lg-12 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-line"></i> Tendencia Temporal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartTemporal"></canvas>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-pie"></i> Distribución General
                                </h6>
                            </div>
                            <div class="card-body">
                                <canvas id="chartCumplimiento"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-bar"></i> Top 10 Áreas por Cumplimiento
                                </h6>
                            </div>
                            <div class="card-body">
                                <canvas id="chartAreas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detalle por Divisiones -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-list"></i> Detalle por Divisiones
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row" id="divisionesContainer">
                                    ${generarTarjetasAreas(data.areas)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#dashboardContent').html(html);
            
            // Generar gráficos
            generarGraficoCumplimiento(data.resumen);
            generarGraficoAreas(data.areas);
            
            // Generar gráfico temporal si es necesario
            if (mostrarGraficoTemporal) {
                generarGraficoTemporal(data.estadisticas_por_fecha);
            }
        }
        
        function generarTarjetasAreas(areas) {
            let html = '';
            
            areas.forEach((area, index) => {
                const porcentaje = area.total_tac > 0 ? (area.registradas / area.total_tac * 100) : 0;
                const colorClass = porcentaje >= 90 ? 'success' : porcentaje >= 70 ? 'warning' : 'danger';
                
                html += `
                    <div class="col-xl-4 col-lg-6 col-md-12 mb-3">
                        <div class="division-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="card-title font-weight-bold text-primary mb-0">${area.division}</h6>
                                    <span class="badge badge-${colorClass} badge-pill">${porcentaje.toFixed(1)}%</span>
                                </div>
                                
                                <div class="row text-center mb-2">
                                    <div class="col-4">
                                        <small class="text-muted">Total</small>
                                        <div class="font-weight-bold">${area.total_tac}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-success">Registradas</small>
                                        <div class="font-weight-bold text-success">${area.registradas}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-danger">Faltantes</small>
                                        <div class="font-weight-bold text-danger">${area.faltantes}</div>
                                    </div>
                                </div>
                                
                                <div class="progress division-progress mb-2">
                                    <div class="progress-bar bg-${colorClass}" role="progressbar" 
                                         style="width: ${porcentaje}%"></div>
                                </div>
                                
                                ${area.copes && area.copes.length > 0 ? `
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted font-weight-bold">COPEs:</small>
                                            <button class="btn btn-sm btn-outline-info" onclick="toggleCopes(${index})" id="btn-copes-${index}">
                                                <i class="fas fa-chevron-down" id="icon-copes-${index}"></i> Ver COPEs
                                            </button>
                                        </div>
                                        <div class="copes-container" id="copes-${index}" style="display: none;">
                                            ${area.copes.map((cope, copeIndex) => {
                                                const copeColorClass = cope.porcentaje >= 90 ? 'success' : cope.porcentaje >= 70 ? 'warning' : 'danger';
                                                return `
                                                    <div class="cope-item">
                                                        <div class="cope-header">
                                                            <span class="cope-title">${cope.cope}</span>
                                                            <span class="badge badge-${copeColorClass} badge-pill">${cope.porcentaje.toFixed(1)}%</span>
                                                        </div>
                                                        <div class="cope-stats">
                                                            <div class="cope-stat">
                                                                <small class="text-muted">Total</small>
                                                                <div class="font-weight-bold">${cope.total_tac}</div>
                                                            </div>
                                                            <div class="cope-stat">
                                                                <small class="text-success">Registradas</small>
                                                                <div class="font-weight-bold text-success">${cope.registradas}</div>
                                                            </div>
                                                            <div class="cope-stat">
                                                                <small class="text-danger">Faltantes</small>
                                                                <div class="font-weight-bold text-danger">${cope.faltantes}</div>
                                                            </div>
                                                        </div>
                                                        <div class="progress cope-progress">
                                                            <div class="progress-bar bg-${copeColorClass}" role="progressbar" 
                                                                 style="width: ${cope.porcentaje}%"></div>
                                                        </div>
                                                        ${cope.folios_faltantes && cope.folios_faltantes.length > 0 ? `
                                                            <div class="mt-2">
                                                                <small class="text-muted">Folios faltantes:</small><br>
                                                                <div class="folios-container-cope" id="folios-cope-${index}-${copeIndex}">
                                                                    ${cope.folios_faltantes.slice(0, 5).map(folio => 
                                                                        `<span class="folio-item">${folio}</span>`
                                                                    ).join('')}
                                                                    ${cope.folios_faltantes.length > 5 ? `
                                                                        <div class="folios-hidden-cope" style="display: none;">
                                                                            ${cope.folios_faltantes.slice(5).map(folio => 
                                                                                `<span class="folio-item">${folio}</span>`
                                                                            ).join('')}
                                                                        </div>
                                                                        <div class="mt-1">
                                                                            <button class="btn btn-xs btn-outline-secondary btn-ver-mas-cope" onclick="toggleFoliosCope(${index}, ${copeIndex})" id="btn-folios-cope-${index}-${copeIndex}">
                                                                                Ver +${cope.folios_faltantes.length - 5} más
                                                                            </button>
                                                                        </div>
                                                                    ` : ''}
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            return html;
        }
        
        function generarGraficoCumplimiento(resumen) {
            const ctx = document.getElementById('chartCumplimiento').getContext('2d');
            
            if (chartCumplimiento) {
                chartCumplimiento.destroy();
            }
            
            chartCumplimiento = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Registradas', 'Faltantes'],
                    datasets: [{
                        data: [resumen.total_registradas, resumen.total_faltantes],
                        backgroundColor: ['#1cc88a', '#e74a3b'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    legend: {
                        position: 'bottom'
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const value = data.datasets[0].data[tooltipItem.index];
                                const percentage = ((value / total) * 100).toFixed(1);
                                return data.labels[tooltipItem.index] + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            });
        }
        
        function generarGraficoAreas(areas) {
            const ctx = document.getElementById('chartAreas').getContext('2d');
            
            if (chartAreas) {
                chartAreas.destroy();
            }
            
            // Verificar si areas está definido y tiene datos
            if (!areas || !areas.length) {
                console.warn('No hay datos de áreas disponibles para el gráfico');
                return;
            }
            
            // Tomar solo las primeras 10 áreas ordenadas por cumplimiento
            const top10Areas = areas.slice(0, 10);
            
            chartAreas = new Chart(ctx, {
                type: 'horizontalBar',
                data: {
                    labels: top10Areas.map(area => area.area ? (area.area.length > 15 ? area.area.substring(0, 15) + '...' : area.area) : 'Sin Área'),
                    datasets: [{
                        label: 'Cumplimiento (%)',
                        data: top10Areas.map(area => area.total_tac > 0 ? (area.registradas / area.total_tac * 100) : 0),
                        backgroundColor: top10Areas.map(area => {
                            const porcentaje = area.total_tac > 0 ? (area.registradas / area.total_tac * 100) : 0;
                            return porcentaje >= 90 ? '#1cc88a' : porcentaje >= 70 ? '#f6c23e' : '#e74a3b';
                        }),
                        borderWidth: 1,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true,
                                max: 100,
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Cumplimiento: ' + tooltipItem.xLabel.toFixed(1) + '%';
                            }
                        }
                    }
                }
            });
        }
        
        function generarGraficoTemporal(estadisticas) {
            const ctx = document.getElementById('chartTemporal').getContext('2d');
            
            if (window.chartTemporal) {
                window.chartTemporal.destroy();
            }
            
            const fechas = estadisticas.map(stat => {
                const fecha = new Date(stat.fecha);
                return fecha.toLocaleDateString('es-MX', { month: 'short', day: 'numeric' });
            });
            
            window.chartTemporal = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: fechas,
                    datasets: [
                        {
                            label: 'Total TAC',
                            data: estadisticas.map(stat => stat.total_tac),
                            borderColor: '#36b9cc',
                            backgroundColor: 'rgba(54, 185, 204, 0.1)',
                            fill: false,
                            tension: 0.4
                        },
                        {
                            label: 'Registradas',
                            data: estadisticas.map(stat => stat.registradas),
                            borderColor: '#1cc88a',
                            backgroundColor: 'rgba(28, 200, 138, 0.1)',
                            fill: false,
                            tension: 0.4
                        },
                        {
                            label: 'Faltantes',
                            data: estadisticas.map(stat => stat.faltantes),
                            borderColor: '#e74a3b',
                            backgroundColor: 'rgba(231, 74, 59, 0.1)',
                            fill: false,
                            tension: 0.4
                        },
                        {
                            label: 'Cumplimiento %',
                            data: estadisticas.map(stat => stat.porcentaje_cumplimiento),
                            borderColor: '#f6c23e',
                            backgroundColor: 'rgba(246, 194, 62, 0.1)',
                            fill: false,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Cantidad de Órdenes'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Porcentaje (%)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                            min: 0,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    if (context.datasetIndex === 3) {
                                        return context.parsed.y.toFixed(1) + '%';
                                    }
                                    return context.parsed.y + ' órdenes';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function mostrarAlerta(tipo, mensaje) {
            const alertHtml = `
                <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
                    ${mensaje}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            
            $('#alertContainer').html(alertHtml);
            
            // Auto-hide después de 5 segundos
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        }
        
        // Función para mostrar/ocultar COPEs
        function toggleCopes(index) {
            const contenedor = $(`#copes-${index}`);
            const boton = $(`#btn-copes-${index}`);
            const icono = $(`#icon-copes-${index}`);
            
            if (contenedor.is(':visible')) {
                // Ocultar COPEs
                contenedor.slideUp(300);
                boton.html('<i class="fas fa-chevron-down"></i> Ver COPEs');
                icono.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                // Mostrar COPEs
                contenedor.slideDown(300);
                boton.html('<i class="fas fa-chevron-up"></i> Ocultar COPEs');
                icono.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
        }
        
        // Función para mostrar/ocultar folios de un COPE específico
        function toggleFoliosCope(areaIndex, copeIndex) {
            const contenedor = $(`#folios-cope-${areaIndex}-${copeIndex}`);
            const foliosOcultos = contenedor.find('.folios-hidden-cope');
            const boton = $(`#btn-folios-cope-${areaIndex}-${copeIndex}`);
            
            if (foliosOcultos.is(':visible')) {
                // Ocultar folios adicionales
                foliosOcultos.slideUp(300);
                boton.html(boton.html().replace('Ver menos', 'Ver +' + foliosOcultos.find('.folio-item').length + ' más'));
            } else {
                // Mostrar todos los folios
                foliosOcultos.slideDown(300);
                boton.html('Ver menos');
            }
        }
    </script>
</body>
</html>
