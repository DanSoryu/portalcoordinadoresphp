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
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
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
                                <a class="dropdown-item-custom" href="./Dashboard.php">
                                    <i class="fas fa-cogs"></i> Operaciones
                                </a>
                                <a class="dropdown-item-custom" href="../Almacen/index.php">
                                    <i class="fas fa-warehouse"></i> Almacén
                                </a>
                            </div>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="./Dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="../Ordenes/OrdenesCoordinador.php">
                                <i class="fas fa-table"></i>
                                <span>Ordenes Coordiapp</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="../OrdenesTac/OrdenesTAC.php">
                                <i class="fas fa-file-invoice"></i>
                                <span>Ordenes TAC</span>
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

    <?php include('../Login/vistas/components/Logout.php'); ?>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js"></script>
    <!-- Toastify -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Custom JS -->
    <script src="vistas/assets/js/preloader.js"></script>
    <script src="vistas/assets/js/toasts.js"></script>
    <script src="vistas/assets/js/notifications.js"></script>
</body>
</html>
<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    date_default_timezone_set('America/Monterrey');

    require_once("controller/ClassOps.php");
    require_once("modal/cargar.php");


    $Usuario = $_SESSION['usuario'];
    $idUsuario = $_SESSION['idusuarios_coordinadores'];

    if (empty($Usuario)) {
        header("location: ../Login/modal/poweroff.php");
    }

    
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

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <br><br><br>

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

                    <!-- JavaScript para inicializar selectores y eventos -->
                    <script>
                    $(document).ready(function() {
                        // Llenar el selector de períodos al iniciar
                        actualizarSelectorPeriodos();
                        
                        // Evento al cambiar el tipo de período
                        $('#tipoPeriodo').change(function() {
                            actualizarSelectorPeriodos();
                        });
                        
                        // Evento al cambiar el período específico
                        $('#periodoSelect').change(function() {
                            cargarComparativo($('#tipoPeriodo').val(), $(this).val());
                        });
                        
                        // No cargar datos iniciales aquí, se cargarán después de cargarDashboard()
                    });
                    
                    function actualizarSelectorPeriodos() {
                        const tipoPeriodo = $('#tipoPeriodo').val();
                        const periodoSelect = $('#periodoSelect');
                        periodoSelect.empty();
                        
                        const fechaActual = new Date();
                        let opciones = [];
                        
                        switch(tipoPeriodo) {
                            case 'semana':
                                // Últimas 12 semanas
                                for(let i = 0; i < 12; i++) {
                                    const fecha = new Date(fechaActual);
                                    fecha.setDate(fecha.getDate() - (i * 7));
                                    const numSemana = getWeekNumber(fecha)[1];
                                    const anio = getWeekNumber(fecha)[0];
                                    opciones.push({
                                        value: `${anio}-W${numSemana}`,
                                        text: `Semana ${numSemana} (${fecha.toLocaleDateString('es-MX', {month: 'short', year: 'numeric'})})`
                                    });
                                }
                                break;
                                
                            case 'mes':
                                // Últimos 12 meses
                                for(let i = 0; i < 12; i++) {
                                    const fecha = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
                                    opciones.push({
                                        value: fecha.toISOString().slice(0,7),
                                        text: fecha.toLocaleDateString('es-MX', {month: 'long', year: 'numeric'})
                                    });
                                }
                                break;
                                
                            case 'año':
                                // Últimos 5 años
                                const anioActual = fechaActual.getFullYear();
                                for(let i = 0; i < 5; i++) {
                                    const anio = anioActual - i;
                                    opciones.push({
                                        value: anio.toString(),
                                        text: anio.toString()
                                    });
                                }
                                break;
                        }
                        
                        // Agregar opciones al select
                        opciones.forEach(opcion => {
                            periodoSelect.append(new Option(opcion.text, opcion.value));
                        });
                        
                        // Cargar datos con el nuevo período seleccionado
                        cargarComparativo(tipoPeriodo, periodoSelect.val());
                    }
                    
                    function getWeekNumber(d) {
                        d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
                        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
                        var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
                        var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
                        return [d.getUTCFullYear(), weekNo];
                    }
                    </script>
                    
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

    <!-- Chart.js -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- ExcelJS + FileSaver para exporte con estilos -->
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
    
    <script>
        // Variables globales
        let chartCumplimiento = null;
        let chartCopes = null; // Cambiar de chartAreas a chartCopes
        let chartTemporal = null;
        let dashboardData = null; // almacena el último payload del dashboard
        
        $(document).ready(function(){
            cargarDashboard();
            
            // Auto-refresh cada 10 minutos para rangos
            setInterval(cargarDashboard, 600000);
        });

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
                        dashboardData = response.data; // guardar datos para exportación
                        $('#ultima_actualizacion').text(new Date().toLocaleString());
                        // Cargar datos comparativos después de mostrar el dashboard
                        cargarComparativo($('#tipoPeriodo').val(), $('#periodoSelect').val());
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
                <!-- Gráfica Comparativa -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Comparativa de Registros en el Tiempo</h6>
                                <div class="d-flex gap-2">
                                    <select id="tipoPeriodo" class="form-control form-control-sm mr-2">
                                        <option value="semana">Por Semana</option>
                                        <option value="mes">Por Mes</option>
                                        <option value="año">Por Año</option>
                                    </select>
                                    <select id="periodoSelect" class="form-control form-control-sm mr-2">
                                        <!-- Se llena dinámicamente -->
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-area" style="height: 300px;">
                                    <canvas id="comparativaChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <i class="fas fa-chart-bar"></i> Top 10 COPEs por Cumplimiento
                                </h6>
                            </div>
                            <div class="card-body">
                                <canvas id="chartCopes"></canvas>
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
                                    
                                    <button type="button" class="btn btn-success btn-sm" onclick="descargarExcelDetallado()">
                                        <i class="fas fa-file-excel"></i> Descargar Excel
                                    </button>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row" id="divisionesContainer">
                                    ${generarTarjetasDivisiones(data.divisiones)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reporte: Rankings Técnicos y Contratistas -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-user-cog"></i> Ranking de Técnicos (Liq. Coordiapp)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tablaRankingTecnicos" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Técnico</th>
                                                <th>Expediente</th>
                                                <th>COPE</th>
                                                <th>Contratista</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${Array.isArray(data.ranking_tecnicos) ? data.ranking_tecnicos.map((t, idx) => `
                                                <tr>
                                                    <td>${idx + 1}</td>
                                                    <td>${(t.tecnico || '').toString().toUpperCase()}</td>
                                                    <td>${t.expediente || ''}</td>
                                                    <td>${t.cope || ''}</td>
                                                    <td>${(t.contratista || '').toString().toUpperCase()}</td>
                                                    <td>${t.total || 0}</td>
                                                </tr>
                                            `).join('') : ''}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-people-carry"></i> Ranking de Contratistas (Liq. Coordiapp)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tablaRankingContratistas" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Contratista</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${Array.isArray(data.ranking_contratistas) ? data.ranking_contratistas.map((c, idx) => `
                                                <tr>
                                                    <td>${idx + 1}</td>
                                                    <td>${(c.contratista || '').toString().toUpperCase()}</td>
                                                    <td>${c.total || 0}</td>
                                                </tr>
                                            `).join('') : ''}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-network-wired"></i> Ranking de COPEs (Liq. Coordiapp)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tablaRankingCopes" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>COPE</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${Array.isArray(data.ranking_copes) ? data.ranking_copes.map((r, idx) => `
                                                <tr>
                                                    <td>${idx + 1}</td>
                                                    <td>${(r.cope || '').toString().toUpperCase()}</td>
                                                    <td>${r.total || 0}</td>
                                                </tr>
                                            `).join('') : ''}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#dashboardContent').html(html);
            
            // Generar gráficos
            generarGraficoCumplimiento(data.resumen);
            console.log('DEBUG: datos recibidos para COPEs =>', data.copes);
            generarGraficoCopes(data.copes); // Cambiar de data.areas a data.copes
            
            // Generar gráfico temporal si es necesario
            if (mostrarGraficoTemporal) {
                generarGraficoTemporal(data.estadisticas_por_fecha);
            }

            // Inicializar DataTables para rankings con orden descendente por Total
            try {
                if (window.DataTable) {
                    new DataTable('#tablaRankingTecnicos', {
                        pageLength: 10,
                        order: [[5, 'desc']],
                        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' }
                    });
                    new DataTable('#tablaRankingContratistas', {
                        pageLength: 10,
                        order: [[2, 'desc']],
                        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' }
                    });
                    new DataTable('#tablaRankingCopes', {
                        pageLength: 10,
                        order: [[2, 'desc']],
                        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' }
                    });
                }
            } catch (e) { console.error('Error inicializando DataTables rankings', e); }
            
            // Inicializar selectores de períodos después de cargar el HTML
            console.log('Inicializando selectores de períodos para gráfico comparativo');
            actualizarSelectorPeriodos();
        }
        
        function generarTarjetasDivisiones(divisiones) {
            if (!divisiones) return '';
            let html = '';
            
            divisiones.forEach((division, index) => {
                const porcentaje = division.total_tac > 0 ? (division.registradas / division.total_tac * 100) : 0;
                const colorClass = porcentaje >= 90 ? 'success' : porcentaje >= 70 ? 'warning' : 'danger';
                
                html += `
                    <div class="col-xl-4 col-lg-6 col-md-12 mb-3">
                        <div class="division-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="card-title font-weight-bold text-primary mb-0">${division.division}</h6>
                                    <span class="badge badge-${colorClass} badge-pill">${porcentaje.toFixed(1)}%</span>
                                </div>
                                
                                <div class="row text-center mb-2">
                                    <div class="col-4">
                                        <small class="text-muted">Tac Asig</small>
                                        <div class="font-weight-bold">${division.total_asignadas}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Tac Liq</small>
                                        <div class="font-weight-bold">${division.total_tac}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-success">Coordiapp Liq</small>
                                        <div class="font-weight-bold text-success">${division.registradas}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-danger">Sin registro Coordiapp</small>
                                        <div class="font-weight-bold text-danger">${division.faltantes}</div>
                                    </div>
                                </div>
                                
                                <div class="progress division-progress mb-2">
                                    <div class="progress-bar bg-${colorClass}" role="progressbar" 
                                         style="width: ${porcentaje}%"></div>
                                </div>
                                
                                ${division.copes && division.copes.length > 0 ? `
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted font-weight-bold">COPEs:</small>
                                            <button class="btn btn-sm btn-outline-info" onclick="toggleCopes(${index})" id="btn-copes-${index}">
                                                <i class="fas fa-chevron-down" id="icon-copes-${index}"></i> Ver COPEs
                                            </button>
                                        </div>
                                        <div class="copes-container" id="copes-${index}" style="display: none;">
                                            ${division.copes.map((cope, copeIndex) => {
                                                const copeColorClass = cope.porcentaje >= 90 ? 'success' : cope.porcentaje >= 70 ? 'warning' : 'danger';
                                                return `
                                                    <div class="cope-item">
                                                        <div class="cope-header">
                                                            <span class="cope-title">${cope.cope}</span>
                                                            <span class="badge badge-${copeColorClass} badge-pill">${cope.porcentaje.toFixed(1)}%</span>
                                                        </div>
                                                        <div class="cope-stats">
                                                            <div class="cope-stat">
                                                                <small class="text-muted">Tac Asig</small>
                                                                <div class="font-weight-bold">${cope.total_asignadas}</div>
                                                            </div>
                                                            <div class="cope-stat">
                                                                <small class="text-muted">Tac Liq</small>
                                                                <div class="font-weight-bold">${cope.total_tac}</div>
                                                            </div>
                                                            <div class="cope-stat">
                                                                <small class="text-success">Coordiapp Liq</small>
                                                                <div class="font-weight-bold text-success">${cope.registradas}</div>
                                                            </div>
                                                            <div class="cope-stat">
                                                                <small class="text-danger">Sin registro Coordiapp</small>
                                                                <div class="font-weight-bold text-danger">${cope.faltantes}</div>
                                                            </div>
                                                        </div>
                                                        <div class="progress cope-progress">
                                                            <div class="progress-bar bg-${copeColorClass}" role="progressbar" 
                                                                 style="width: ${cope.porcentaje}%"></div>
                                                        </div>
                                                        ${cope.folios_faltantes && cope.folios_faltantes.length > 0 ? `
                                                            <div class="mt-2">
                                                                <small class="text-muted">Folios faltantes por registrar:</small><br>
                                                                <div class="folios-container-cope" id="folios-cope-${index}-${copeIndex}">
                                                                    ${cope.folios_faltantes && cope.folios_faltantes.slice(0, 5).map(item => 
                                                                        `<div class="folio-item-container">
                                                                            <span class="folio-item">${item.folio || 'N/A'}</span>
                                                                            <small class="text-muted ml-2">${item.tecnico + ' ' + item.expediente || 'Sin asignar'}</small>
                                                                         </div>`
                                                                    ).join('')}
                                                                    ${cope.folios_faltantes && cope.folios_faltantes.length > 5 ? `
                                                                        <div class="folios-hidden-cope" style="display: none;">
                                                                            ${cope.folios_faltantes.slice(5).map(item => 
                                                                                `<div class="folio-item-container">
                                                                                    <span class="folio-item">${item.folio || 'N/A'}</span>
                                                                                    <small class="text-muted ml-2">${item.tecnico || 'Sin asignar'}</small>
                                                                                </div>`
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

        function convertJsonToExcel() {
            // JSON data to be converted
            const jsonData = [
                { "Name": "Amit Kumar", "Age": 29, "City": "Mumbai" },
                { "Name": "Priya Sharma", "Age": 25, "City": "Delhi" },
                { "Name": "Ravi Patel", "Age": 35, "City": "Ahmedabad" },
                { "Name": "Anjali Verma", "Age": 28, "City": "Pune" }
            ];

            // Convert JSON to worksheet
            const worksheet = XLSX.utils.json_to_sheet(jsonData);

            // Create a new workbook and add the worksheet
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

            // Export the Excel file
            XLSX.writeFile(workbook, "data.xlsx");
        }

        // Exporta a Excel la información mostrada en las cards de Divisiones y COPEs
        function descargarExcelDivisiones() {
            try {
                if (!dashboardData || !dashboardData.divisiones || dashboardData.divisiones.length === 0) {
                    mostrarAlerta && mostrarAlerta('warning', 'No hay datos de divisiones para exportar');
                    return;
                }

                // 1) Preparar datos de Divisiones
                const divisionesRows = dashboardData.divisiones.map(div => {
                    const porcentaje = div.total_tac > 0 ? (div.registradas / div.total_tac * 100) : 0;
                    return {
                        Division: div.division || '',
                        'Tac Asig': Number(div.total_asignadas || 0),
                        'Tac Liq': Number(div.total_tac || 0),
                        'Coordiapp Liq': Number(div.registradas || 0),
                        'Sin registro Coordiapp': Number(div.faltantes || 0),
                        '% Cumplimiento': Number(porcentaje.toFixed(1)),
                    };
                });

                // 2) Preparar datos de COPEs (flatten por división)
                const copesRows = [];
                // 2.b) Preparar detalle de folios faltantes
                const foliosRows = [];
                dashboardData.divisiones.forEach(div => {
                    if (div.copes && Array.isArray(div.copes)) {
                        div.copes.forEach(cope => {
                            const p = cope.total_tac > 0 ? (cope.registradas / cope.total_tac * 100) : 0;
                            copesRows.push({
                                Division: div.division || '',
                                COPE: cope.cope || '',
                                'Tac Asig': Number(cope.total_asignadas || 0),
                                'Tac Liq': Number(cope.total_tac || 0),
                                'Coordiapp Liq': Number(cope.registradas || 0),
                                'Sin registro Coordiapp': Number(cope.faltantes || 0),
                                '% Cumplimiento': Number((cope.porcentaje != null ? cope.porcentaje : p).toFixed(1)),
                                'Folios faltantes (conteo)': Array.isArray(cope.folios_faltantes) ? cope.folios_faltantes.length : 0,
                            });

                            // Agregar detalle por folio faltante
                            if (Array.isArray(cope.folios_faltantes) && cope.folios_faltantes.length) {
                                cope.folios_faltantes.forEach(item => {
                                    foliosRows.push({
                                        Division: div.division || '',
                                        COPE: cope.cope || '',
                                        Folio: (item && (item.folio ?? item.FOLIO)) || 'N/A',
                                        Tecnico: (item && (item.tecnico ?? item.TECNICO ?? item.tecnico_asignado)) || 'Sin asignar',
                                    });
                                });
                            }
                        });
                    }
                });

                // 3) Crear workbook y hojas
                const wb = XLSX.utils.book_new();
                const wsDiv = XLSX.utils.json_to_sheet(divisionesRows);
                const wsCop = XLSX.utils.json_to_sheet(copesRows);
                const wsFol = XLSX.utils.json_to_sheet(foliosRows);
                XLSX.utils.book_append_sheet(wb, wsDiv, 'Divisiones');
                XLSX.utils.book_append_sheet(wb, wsCop, 'COPEs');
                XLSX.utils.book_append_sheet(wb, wsFol, 'Folios faltantes');

                // 4) Generar nombre con rango de fechas si existen inputs visibles
                const fi = (document.getElementById('fecha_inicio')?.value) || '';
                const ff = (document.getElementById('fecha_fin')?.value) || '';
                const nombre = fi && ff ? `DivisionesCOPEs_${fi}_a_${ff}.xlsx` : 'DivisionesCOPEs.xlsx';

                // 5) Descargar
                XLSX.writeFile(wb, nombre);
            } catch (err) {
                console.error('Error al exportar Excel:', err);
                try { mostrarAlerta && mostrarAlerta('danger', 'Error al exportar a Excel'); } catch(e) {}
            }
        }
        
        // Exporte DETALLADO con formato similar al ejemplo (encabezados combinados y colores)
        async function descargarExcelDetallado() {
            if (!dashboardData || !dashboardData.divisiones || dashboardData.divisiones.length === 0) {
                try { mostrarAlerta('warning', 'No hay datos para exportar'); } catch(e) {}
                return;
            }

            const wb = new ExcelJS.Workbook();
            const ws = wb.addWorksheet('Seguimiento');

            // Utilidades de estilo
            const darkHeader = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF264653' } }; // azul oscuro
            const tacFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFB9313A' } };    // rojo TAC
            const coordFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2A9D8F' } };  // verde Coordiapp
            const borderThin = { style: 'thin', color: { argb: 'FF000000' } };

            // Título global
            const fi = (document.getElementById('fecha_inicio')?.value) || '';
            const ff = (document.getElementById('fecha_fin')?.value) || '';
            const corte = ff || fi || new Date().toISOString().slice(0,10);
            ws.mergeCells('A1:G1');
            const titleCell = ws.getCell('A1');
            titleCell.value = `ESTATUS SEGUIMIENTO LIQUIDADO TAC vs COORDIAPP    Corte:${corte.split('-').reverse().join('-')}`;
            titleCell.fill = darkHeader; titleCell.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 12 };
            titleCell.alignment = { vertical: 'middle', horizontal: 'center' };
            ws.getRow(1).height = 22;

            // Definir anchos de columnas (A:G)
            // A: Asig (TAC), B: Liq (TAC), C: Liq Coordiapp, D: Sin registro Coordiapp, E: Folio PISA, F: Expediente, G: Técnico
            ws.columns = [
                { key: 'tac_asig', width: 10 },
                { key: 'tac_liq', width: 10 },
                { key: 'coord_liq', width: 16 },
                { key: 'coord_sin', width: 20 },
                { key: 'folio_pisa', width: 22 },
                { key: 'expediente', width: 14 },
                { key: 'tecnico', width: 40 }
            ];

            let rowIdx = 3; // arrancar después del título

            dashboardData.divisiones.forEach(div => {
                (div.copes || []).forEach(cope => {
                    // Encabezado del bloque: nombre del COPE (con posible División)
                    ws.mergeCells(`A${rowIdx}:G${rowIdx}`);
                    const sect = ws.getCell(`A${rowIdx}`);
                    sect.value = `${div.division || ''} ${div.division ? '- ' : ''}${cope.cope || ''}`.trim();
                    sect.fill = darkHeader;
                    sect.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                    sect.alignment = { vertical: 'middle', horizontal: 'left' };
                    ws.getRow(rowIdx).height = 18;
                    rowIdx++;

                    // Cabecera de tabla (dos filas con merges) segun diseño: [TAC]{Asig,Liq} [COORDIAPP]{Liq,Sin registro} [Folio PISA] [Expediente] [Técnico]
                    ws.mergeCells(`A${rowIdx}:B${rowIdx}`);
                    ws.mergeCells(`C${rowIdx}:D${rowIdx}`);
                    ws.getCell(`A${rowIdx}`).value = 'TAC';
                    ws.getCell(`C${rowIdx}`).value = 'COORDIAPP';
                    ws.getCell(`E${rowIdx}`).value = 'Folio PISA sin registro';
                    ws.getCell(`F${rowIdx}`).value = 'Expediente';
                    ws.getCell(`G${rowIdx}`).value = 'Técnico';
                    ['A','C','E','F','G'].forEach(c => { const cell = ws.getCell(`${c}${rowIdx}`); cell.font = { bold: true, color: { argb: 'FFFFFFFF' } }; cell.fill = darkHeader; cell.alignment = { horizontal: 'center' }; });
                    rowIdx++;

                    // Sub-encabezados
                    const headers = ['Asig','Liq','Liq Coordiapp','Sin registro Coordiapp','Folio PISA sin registro','Expediente','Técnico'];
                    headers.forEach((h, i) => {
                        const cell = ws.getCell(rowIdx, i+1);
                        cell.value = h; cell.font = { bold: true }; cell.alignment = { horizontal: 'center' };
                        cell.border = { top: borderThin, left: borderThin, bottom: borderThin, right: borderThin };
                        if (i === 0 || i === 1) cell.fill = tacFill; // TAC cols A,B
                        if (i === 2 || i === 3) cell.fill = coordFill; // COORDIAPP cols C,D
                    });
                    rowIdx++;

                    // Filas de detalle (folios faltantes)
                    const folios = Array.isArray(cope.folios_faltantes) ? cope.folios_faltantes : [];
                    folios.forEach(item => {
                        const expediente = item.expediente || item.EXPEDIENTE || '';
                        const tecnico = item.tecnico || item.TECNICO || '';
                        const folio = item.folio || item.FOLIO || '';
                        const row = ws.getRow(rowIdx);
                        // A-D vacías para filas de detalle
                        row.getCell(5).value = folio || '';
                        row.getCell(6).value = (expediente !== undefined && expediente !== null) ? String(expediente) : '';
                        row.getCell(7).value = tecnico || '';
                        for (let c = 1; c <= 7; c++) {
                            const cell = row.getCell(c);
                            cell.border = { top: borderThin, left: borderThin, bottom: borderThin, right: borderThin };
                        }
                        rowIdx++;
                    });

                    // Fila de totales del bloque (solo números en A-D segun diseño)
                    const totalRow = ws.getRow(rowIdx);
                    totalRow.getCell(1).value = Number(cope.total_asignadas || 0); // Asig
                    totalRow.getCell(2).value = Number(cope.total_tac || 0);      // Liq TAC
                    totalRow.getCell(3).value = Number(cope.registradas || 0);    // Liq Coordiapp
                    totalRow.getCell(4).value = Number(cope.faltantes || 0);      // Sin registro Coordiapp
                    totalRow.font = { bold: false };
                    // color de fondo por columnas A-D
                    totalRow.getCell(1).fill = tacFill; totalRow.getCell(2).fill = tacFill;
                    totalRow.getCell(3).fill = coordFill; totalRow.getCell(4).fill = coordFill;
                    for (let c = 1; c <= 7; c++) {
                        totalRow.getCell(c).border = { top: borderThin, left: borderThin, bottom: borderThin, right: borderThin };
                    }
                    rowIdx++;

                    // Espacio entre bloques
                    rowIdx++;
                });
            });

            // Descargar archivo
            const nombre = fi && ff ? `Seguimiento_${fi}_a_${ff}.xlsx` : 'Seguimiento.xlsx';
            const buffer = await wb.xlsx.writeBuffer();
            saveAs(new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }), nombre);
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
        
        function generarGraficoCopes(copes) {
            const ctx = document.getElementById('chartCopes').getContext('2d');
            
            if (chartCopes) {
                chartCopes.destroy();
            }
            
            // Verificar si copes está definido y tiene datos
            if (!copes || !Array.isArray(copes) || !copes.length) {
                console.warn('No hay datos de COPEs disponibles para el gráfico');
                return;
            }
            
            // Asegurarse de que los datos tienen la estructura correcta
            copes = copes.filter(cope => cope && cope.cope && typeof cope.registradas === 'number' && typeof cope.total_tac === 'number');
            
            // Tomar solo los primeros 10 COPEs ordenados por cumplimiento
            const top10Copes = copes.slice(0, 10);
            
            chartCopes = new Chart(ctx, {
                type: 'horizontalBar',
                data: {
                    labels: top10Copes.map(cope => cope.cope ? (cope.cope.length > 15 ? cope.cope.substring(0, 15) + '...' : cope.cope) : 'Sin COPE'),
                    datasets: [{
                        label: 'Cumplimiento (%)',
                        data: top10Copes.map(cope => cope.total_tac > 0 ? (cope.registradas / cope.total_tac * 100) : 0),
                        backgroundColor: top10Copes.map(cope => {
                            const porcentaje = cope.total_tac > 0 ? (cope.registradas / cope.total_tac * 100) : 0;
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
        
        // Función para cargar y mostrar el gráfico comparativo por períodos
        let chartComparativo;
        function cargarComparativo(periodo = 'mes', valor = null) {
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();
            
            console.log('=== INICIO cargarComparativo ===');
            console.log('Parámetros recibidos:', {
                periodo: periodo,
                valor: valor,
                fechaInicio: fechaInicio,
                fechaFin: fechaFin
            });
            
            if (!fechaInicio || !fechaFin) {
                console.warn('Se requieren fechas de inicio y fin para cargar datos comparativos');
                return;
            }
            
            const ajaxData = {
                accion: 'datosComparativos',
                periodo: periodo,
                valor: valor,
                fechaInicio: fechaInicio,
                fechaFin: fechaFin
            };
            
            console.log('Datos que se enviarán al servidor:', ajaxData);
            
            $.ajax({
                url: 'modal/ComparativaData.php',
                type: 'GET',
                data: ajaxData,
                beforeSend: function() {
                    console.log('Enviando petición AJAX a modal/ComparativaData.php');
                },
                success: function(response) {
                    console.log('DEBUG: respuesta AJAX comparativa =>', response);
                    if (response.error) {
                        console.error('Error:', response.error);
                        mostrarAlerta('warning', 'Error al cargar datos comparativos: ' + response.error);
                        return;
                    }
                    if (!Array.isArray(response)) {
                        console.error('Formato de respuesta inválido:', response);
                        mostrarAlerta('warning', 'Error: Formato de datos inválido');
                        return;
                    }
                    console.log('Total de registros recibidos:', response.length);
                    mostrarGraficoComparativo(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud AJAX:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    if (xhr && xhr.responseText) {
                        console.error('Respuesta del servidor:', xhr.responseText);
                        mostrarAlerta('danger', 'Error al cargar datos comparativos. Ver consola para más detalles.');
                    }
                }
            });
            console.log('=== FIN cargarComparativo (petición enviada) ===');
        }
        
        function mostrarGraficoComparativo(datos) {
            console.log('DEBUG: mostrarGraficoComparativo recibe:', datos);
            
            // Validar que el canvas existe
            const canvas = document.getElementById('comparativaChart');
            if (!canvas) {
                console.error('ERROR: No se encontró el elemento canvas con id "comparativaChart"');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('ERROR: No se pudo obtener el contexto 2d del canvas');
                return;
            }
            
            // Destruir gráfico anterior si existe
            if (chartComparativo) {
                try {
                    chartComparativo.destroy();
                } catch (e) {
                    console.warn('Error al destruir gráfico anterior:', e);
                }
            }
            
            // Validar datos
            if (!datos || !Array.isArray(datos) || datos.length === 0) {
                console.warn('No hay datos para mostrar en el gráfico comparativo');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.font = '14px Arial';
                ctx.fillStyle = '#6c757d';
                ctx.textAlign = 'center';
                ctx.fillText('No hay datos disponibles para el rango seleccionado', canvas.width/2, canvas.height/2);
                return;
            }
            
            // Obtener el tipo de periodo seleccionado
            const tipoPeriodo = $('#tipoPeriodo').val() || 'mes';
            
            console.log('DEBUG: Tipo de período para mapeo:', tipoPeriodo);
            console.log('DEBUG: Datos crudos recibidos:', datos);
            
            // Procesar fechas según el tipo de periodo
            const fechas = datos.map(item => {
                // Asegurarse de que la fecha se interprete correctamente sin problemas de timezone
                const fechaStr = item.fecha;
                const fecha = new Date(fechaStr + 'T12:00:00'); // Agregar hora del mediodía para evitar problemas de timezone
                
                console.log('DEBUG: Procesando fecha:', fechaStr, '-> Date object:', fecha);
                
                switch(tipoPeriodo) {
                    case 'año':
                        // Mostrar solo meses para el periodo anual
                        return fecha.toLocaleDateString('es-MX', { month: 'short', year: 'numeric' });
                    case 'mes':
                        // Mostrar día del mes con mes corto
                        const dia = fecha.getDate();
                        const mes = fecha.toLocaleDateString('es-MX', { month: 'short' });
                        return `${dia} ${mes}`;
                    case 'semana':
                        // Mostrar días de la semana con fecha completa
                        return fecha.toLocaleDateString('es-MX', { weekday: 'short', day: 'numeric', month: 'short' });
                    default:
                        return fecha.toLocaleDateString('es-MX', { year: 'numeric', month: 'short', day: 'numeric' });
                }
            });
            
            console.log('DEBUG: Fechas formateadas:', fechas);
            
            console.log('DEBUG: Fechas formateadas:', fechas);
            
            const tacData = datos.map(item => item.registros_tac || 0);
            const coordiappData = datos.map(item => item.registros_coordiapp || 0);
            const cumplimientoData = datos.map(item => item.cumplimiento || 0);
            
            console.log('DEBUG: Tipo de período:', tipoPeriodo);
            console.log('DEBUG: Datos procesados - Fechas:', fechas);
            console.log('DEBUG: TAC:', tacData);
            console.log('DEBUG: Coordiapp:', coordiappData);
            console.log('DEBUG: Cumplimiento:', cumplimientoData);
            
            try {
                // Crear gráfico con sintaxis de Chart.js v2
                chartComparativo = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: fechas,
                        datasets: [
                            {
                                label: 'TAC',
                                data: tacData,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                                borderWidth: 2,
                                fill: false,
                                lineTension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6
                                // NO asignar yAxisID para que use el eje por defecto
                            },
                            {
                                label: 'Coordiapp',
                                data: coordiappData,
                                borderColor: '#1cc88a',
                                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                                borderWidth: 2,
                                fill: false,
                                lineTension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6
                                // NO asignar yAxisID para que use el eje por defecto
                            },
                            {
                                label: 'Cumplimiento %',
                                data: cumplimientoData,
                                borderColor: '#f6c23e',
                                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                                borderWidth: 2,
                                fill: false,
                                lineTension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                yAxisID: 'y-axis-percent'  // Solo el porcentaje usa el eje derecho
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(255,255,255,0.9)',
                            titleFontColor: '#333',
                            bodyFontColor: '#666',
                            borderColor: '#ddd',
                            borderWidth: 1,
                            xPadding: 10,
                            yPadding: 10,
                            displayColors: true,
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].xLabel;
                                },
                                label: function(tooltipItem, data) {
                                    let label = data.datasets[tooltipItem.datasetIndex].label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    
                                    // Si es el dataset de cumplimiento (índice 2)
                                    if (tooltipItem.datasetIndex === 2) {
                                        label += tooltipItem.yLabel.toFixed(1) + '%';
                                    } else {
                                        label += tooltipItem.yLabel + ' órdenes';
                                    }
                                    return label;
                                }
                            }
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: getTituloEjeX(tipoPeriodo)
                                },
                                gridLines: {
                                    display: false
                                },
                                ticks: {
                                    autoSkip: true,
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            }],
                            yAxes: [
                                {
                                    // Eje principal para TAC y Coordiapp (izquierda)
                                    id: 'y-axis-0',  // Eje por defecto
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Cantidad de Órdenes'
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        callback: function(value) {
                                            // Solo mostrar números enteros
                                            if (Math.floor(value) === value) {
                                                return value;
                                            }
                                        }
                                    }
                                },
                                {
                                    // Eje secundario para porcentaje (derecha)
                                    id: 'y-axis-percent',
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Porcentaje de Cumplimiento (%)'
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        max: 100,
                                        callback: function(value) {
                                            return value + '%';
                                        }
                                    },
                                    gridLines: {
                                        drawOnChartArea: false
                                    }
                                }
                            ]
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                });
                
                console.log('DEBUG: Gráfico comparativo creado exitosamente');
                
            } catch (error) {
                console.error('ERROR al crear el gráfico comparativo:', error);
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.font = '14px Arial';
                ctx.fillStyle = '#dc3545';
                ctx.textAlign = 'center';
                ctx.fillText('Error al generar el gráfico', canvas.width/2, canvas.height/2 - 10);
                ctx.font = '12px Arial';
                ctx.fillStyle = '#6c757d';
                ctx.fillText('Revise la consola para más detalles', canvas.width/2, canvas.height/2 + 10);
            }
        }
        
        // Función auxiliar para obtener el título del eje X según el periodo
        function getTituloEjeX(tipoPeriodo) {
            switch(tipoPeriodo) {
                case 'año':
                    return 'Meses del Año';
                case 'mes':
                    return 'Semanas/Días del Mes';
                case 'semana':
                    return 'Días de la Semana';
                default:
                    return 'Periodo';
            }
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
        
        // ============================================
        // INICIALIZACIÓN DE SELECTORES DE PERÍODOS
        // ============================================
        
        // Esta función actualiza el selector de períodos según el tipo seleccionado
        function actualizarSelectorPeriodos() {
            console.log('=== actualizarSelectorPeriodos ===');
            const tipoPeriodo = $('#tipoPeriodo').val();
            const periodoSelect = $('#periodoSelect');
            periodoSelect.empty();
            
            console.log('Tipo de período seleccionado:', tipoPeriodo);
            
            const fechaActual = new Date();
            let opciones = [];
            
            switch(tipoPeriodo) {
                case 'semana':
                    console.log('Generando opciones para SEMANA');
                    // Últimas 12 semanas
                    for(let i = 0; i < 12; i++) {
                        const fecha = new Date(fechaActual);
                        fecha.setDate(fecha.getDate() - (i * 7));
                        const numSemana = getWeekNumber(fecha)[1];
                        const anio = getWeekNumber(fecha)[0];
                        opciones.push({
                            value: `${anio}-W${String(numSemana).padStart(2, '0')}`,
                            text: `Semana ${numSemana} (${fecha.toLocaleDateString('es-MX', {month: 'short', year: 'numeric'})})`
                        });
                    }
                    break;
                    
                case 'mes':
                    console.log('Generando opciones para MES');
                    // Mes actual y últimos 11 meses (total 12 meses)
                    for(let i = 0; i < 12; i++) {
                        const fecha = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1);
                        const valorMes = fecha.toISOString().slice(0,7); // YYYY-MM
                        opciones.push({
                            value: valorMes,
                            text: fecha.toLocaleDateString('es-MX', {month: 'long', year: 'numeric'})
                        });
                    }
                    break;
                    
                case 'año':
                    console.log('Generando opciones para AÑO');
                    // Últimos 5 años
                    const anioActual = fechaActual.getFullYear();
                    for(let i = 0; i < 5; i++) {
                        const anio = anioActual - i;
                        opciones.push({
                            value: anio.toString(),
                            text: anio.toString()
                        });
                    }
                    break;
            }
            
            console.log('Opciones generadas:', opciones);
            
            // Agregar opciones al select
            opciones.forEach(opcion => {
                periodoSelect.append(new Option(opcion.text, opcion.value));
            });
            
            const valorSeleccionado = periodoSelect.val();
            console.log('Valor seleccionado después de llenar el select:', valorSeleccionado);
            
            // Cargar datos con el nuevo período seleccionado
            cargarComparativo(tipoPeriodo, valorSeleccionado);
        }
        
        // Función para obtener el número de semana del año
        function getWeekNumber(d) {
            d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
            var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
            var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
            return [d.getUTCFullYear(), weekNo];
        }
        
        // Event delegation para elementos dinámicos
        $(document).on('change', '#tipoPeriodo', function() {
            console.log('=== Evento: Cambio en #tipoPeriodo ===');
            console.log('Nuevo valor:', $(this).val());
            actualizarSelectorPeriodos();
        });
        
        $(document).on('change', '#periodoSelect', function() {
            console.log('=== Evento: Cambio en #periodoSelect ===');
            const periodo = $('#tipoPeriodo').val();
            const valor = $(this).val();
            console.log('Tipo de período:', periodo);
            console.log('Valor seleccionado:', valor);
            cargarComparativo(periodo, valor);
        });
    </script>
</body>
</html>
