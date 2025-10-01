<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../Login/Login.php');
    exit();
}

$Usuario = $_SESSION['usuario'];
$idUsuario = isset($_SESSION['idusuarios_coordinadores']) ? $_SESSION['idusuarios_coordinadores'] : '';

// Obtener los COPEs asignados al coordinador
require_once('./Ordenes.php');
$ordenesObj = new Ordenes();
$copesData = $ordenesObj->obtenerCopesCoordinador($idUsuario);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Órdenes Coordinador</title>
    <!-- Custom fonts for this template -->
    <link href="/Operaciones/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/Operaciones/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        /* Corrige el botón de módulos para que no tenga fondo ni contorno y sea blanco */
        #modulosDropdown.nav-link {
            background: transparent !important;
            color: white !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
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
        .table-responsive { overflow-x: auto; }
        @media (max-width: 1024px) { .header-left { gap: 1rem; } .nav-link { padding: 0.5rem; font-size: 0.8rem; } .nav-link span { display: none; } .nav-link i { margin: 0; } }
        @media (max-width: 768px) { .header-title { font-size: 1rem; } .main-nav { display: none; } .user-name { display: none; } }
        .chevron-icon { width: 16px; height: 16px; transition: transform 0.2s; }
        .chevron-icon.rotate { transform: rotate(180deg); }
        .scroll-to-top { bottom: 20px !important; }
        
        /* Modern button styles */
        .btn-modern {
            padding: 0.5rem;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-modern:active {
            transform: translateY(0);
        }
        
        .btn-photos {
            background: linear-gradient(135deg, #00b4db, #0083b0);
            color: white;
        }
        
        .btn-photos:hover {
            background: linear-gradient(135deg, #0083b0, #00b4db);
            color: white;
        }
        
        .btn-pdf {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: white;
        }
        
        .btn-pdf:hover {
            background: linear-gradient(135deg, #ff4b2b, #ff416c);
            color: white;
        }
        
        .btn-map {
            background: linear-gradient(135deg, #02aab0, #00cdac);
            color: white;
        }
        
        .btn-map:hover {
            background: linear-gradient(135deg, #00cdac, #02aab0);
            color: white;
        }
        
        .gap-2 {
            gap: 0.5rem;
        }

        /* Estilos del modal de fotos */
        .modal-xl {
            max-width: 1140px;
        }

        .photo-grid-container {
            max-height: calc(90vh - 80px);
            overflow-y: auto;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
            padding: 1rem;
        }

        @media (min-width: 768px) {
            .photo-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .photo-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .photo-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .photo-title {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        .photo-description {
            font-size: 0.875rem;
            color: #6B7280;
            margin: 0 0 0.5rem 0;
        }

        .photo-frame {
            position: relative;
            padding-top: 75%; /* Aspect ratio 4:3 para más espacio vertical */
            background-color: #F3F4F6;
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .photo-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Cambiado a cover para llenar el espacio */
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .photo-image:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Overlay para cuando la imagen está expandida */
        .photo-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9000;
            cursor: pointer;
        }

        /* Estilos para la vista ampliada de la imagen */
        .photo-image.expanded {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: auto;
            max-width: 90vw;
            height: auto;
            max-height: 90vh;
            z-index: 9001;
            background-color: #000;
            object-fit: contain;
            padding: 20px;
            margin: 0;
            border-radius: 0;
            cursor: zoom-out;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 10px rgba(0, 0, 0, 0.8);
        }

        /* Estilos adicionales para el modal */
        .bg-gray-50 {
            background-color: #F9FAFB;
        }

        .text-gray-800 {
            color: #1F2937;
        }

        .text-blue-600 {
            color: #2563EB;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }

        .btn-modern-secondary {
            background-color: #4B5563;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-modern-secondary:hover {
            background-color: #374151;
            color: white;
            transform: translateY(-1px);
        }

        /* Mejoras visuales para el modal */
        .modal-content {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-header .close {
            padding: 1rem;
            margin: -1rem -1rem -1rem auto;
        }

        .modal-header {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            padding: 1.5rem;
        }

        .modal-footer {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
            padding: 1.5rem;
        }
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
                                <a class="dropdown-item-custom" href="../Dashboard/Dashboard.php">
                                    <i class="fas fa-cogs"></i> Operaciones
                                </a>
                                <a class="dropdown-item-custom" href="../Almacen/index.php">
                                    <i class="fas fa-warehouse"></i> Almacén
                                </a>
                            </div>
                        </div>
                        <div class="nav-item">
                            <a href="../Dashboard/Dashboard.php" class="nav-link">
                                <i class="fas fa-chart-line"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="./OrdenesCoordinador.php" class="nav-link">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Órdenes Coordiapp</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="../OrdenesTac/OrdenesTAC.php" class="nav-link">
                                <i class="fas fa-tasks"></i>
                                <span>Órdenes TAC</span>
                            </a>
                        </div>
                    </nav>
                </div>
                <!-- Menú de usuario -->
                <div class="user-menu">
                    <button class="user-button" id="userMenuButton">
                        <div class="user-avatar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="user-name"><?php echo $Usuario; ?></span>
                        <svg class="chevron-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
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
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-file-invoice text-primary"></i> Órdenes Coordiapp
                    </h1>
                    <p class="text-muted mb-0">Consulta de órdenes Coordiapp filtradas por tus COPEs asignados</p>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-filter"></i> Filtros de Búsqueda
                        </h6>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="filtrosForm">
                        <div class="row">
                            <?php
                                $currentDate = date('Y-m-d');
                                $minDate = date('Y-m-d', strtotime('-2 years'));
                                $startDate = date('Y-m-d', strtotime('-1 month'));
                            ?>
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                    value="<?php echo $startDate; ?>"
                                    min="<?php echo $minDate; ?>"
                                    max="<?php echo $currentDate; ?>">
                                <span class="text-xs text-gray-500 mt-1 d-block">Máximo 2 años atrás</span>
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                                    value="<?php echo $currentDate; ?>"
                                    min="<?php echo $minDate; ?>"
                                    max="<?php echo $currentDate; ?>">
                                <span class="text-xs text-gray-500 mt-1 d-block">Máximo fecha actual</span>
                            </div>
                            <div class="col-md-3">
                                <label for="estatus" class="form-label">Estatus</label>
                                <select class="form-control" id="estatus" name="estatus">
                                    <option value="">Todos los estados</option>
                                    <option value="COMPLETADA">Completada</option>
                                    <option value="INCOMPLETA">Incompleta</option>
                                    <option value="OBJETADA">Objetada</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="cope" class="form-label">COPE</label>
                                <select class="form-control" id="cope" name="cope">
                                    <?php if (!empty($copesData)): ?>
                                        <option value="">Todos los COPEs</option>
                                        <?php foreach ($copesData as $cope): ?>
                                            <option value="<?php echo htmlspecialchars($cope['id']); ?>">
                                                <?php echo htmlspecialchars($cope['COPE']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabla de Resultados -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table"></i> Órdenes Coordiapp
                    </h6>
                    <div>
                        <button class="btn btn-success btn-sm" onclick="exportarExcel()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaOrdenes" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Folio Pisa</th>
                                    <th>Teléfono</th>
                                    <th>ONT</th>
                                    <th>N° Expediente</th>
                                    <th>Cliente</th>
                                    <th>Dirección</th>
                                    <th>Contratista</th>
                                    <th>Técnico</th>
                                    <th>COPE</th>
                                    <th>Área</th>
                                    <th>División</th>
                                    <th>Distrito</th>
                                    <th>Tecnología</th>
                                    <th>Tipo Tarea</th>
                                    <th>Tipo Instalación</th>
                                    <th>Metraje</th>
                                    <th>Terminal</th>
                                    <th>Puerto</th>
                                    <th>Paso</th>
                                    <th>Coordenadas</th>
                                    <th>Coord. Terminal</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para fotos -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 bg-gray-50">
                    <h5 class="modal-title font-semibold text-xl text-gray-800" id="photoModalLabel">
                        Fotos de la Orden <span id="modalFolioPisa" class="text-blue-600"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body photo-grid-container p-6">
                    <div class="photo-grid">
                        <!-- Foto ONT -->
                        <div class="photo-item">
                            <h4 class="photo-title">Foto ONT</h4>
                            <p class="photo-description">Una fotografía de la parte frontal de la ONT</p>
                            <div class="photo-frame">
                                <img id="modalFotoOnt" class="photo-image" alt="Foto ONT">
                            </div>
                        </div>

                        <!-- Foto Casa Cliente -->
                        <div class="photo-item">
                            <h4 class="photo-title">Foto Casa Cliente</h4>
                            <p class="photo-description">Una fotografía de la casa del cliente</p>
                            <div class="photo-frame">
                                <img id="modalFotoCasaCliente" class="photo-image" alt="Foto Casa Cliente">
                            </div>
                        </div>

                        <!-- No. Serie ONT -->
                        <div class="photo-item">
                            <h4 class="photo-title">No. Serie ONT</h4>
                            <p class="photo-description">Una fotografía de la parte trasera de la ONT</p>
                            <div class="photo-frame">
                                <img id="modalNoSerieONT" class="photo-image" alt="No. Serie ONT">
                            </div>
                        </div>

                        <!-- Foto Puerto -->
                        <div class="photo-item">
                            <h4 class="photo-title">Foto Terminal</h4>
                            <p class="photo-description">Una fotografía de la terminal</p>
                            <div class="photo-frame">
                                <img id="modalFotoPuerto" class="photo-image" alt="Foto Puerto">
                            </div>
                        </div>

                        <!-- Foto INE -->
                        <div class="photo-item">
                            <h4 class="photo-title">Foto SO</h4>
                            <p class="photo-description">Una fotografía de la hoja de servicio</p>
                            <div class="photo-frame">
                                <img id="modalFotoINE" class="photo-image" alt="Foto INE">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-gray-50">
                    <button type="button" class="btn btn-modern-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery desde CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <!-- Toastify -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- ExcelJS + FileSaver para exporte -->
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
    
    <script>
        // Variables globales
        let tablaOrdenes = null;
        let datosOrdenes = [];
        const idUsuario = '<?php echo $idUsuario; ?>';

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
        
        // Inicializar select de COPEs
        function inicializarSelectCopes() {
            const copeSelect = $('#cope');
            if (!copeSelect.length) return;

            // Mantener el valor seleccionado si existe
            const valorActual = copeSelect.val();
            
            // Limpiar opciones actuales
            copeSelect.empty();
            
            // Agregar opción por defecto
            copeSelect.append($('<option>', {
                value: '',
                text: 'Todos los COPEs'
            }));
            
            // Si hay COPEs disponibles, agregarlos al select
            <?php if (!empty($copesData)): ?>
                <?php foreach ($copesData as $cope): ?>
                    copeSelect.append($('<option>', {
                        value: '<?php echo htmlspecialchars($cope['id']); ?>',
                        text: '<?php echo htmlspecialchars($cope['COPE']); ?>'
                    }));
                <?php endforeach; ?>
            <?php endif; ?>
            
            // Restaurar el valor seleccionado si existía
            if (valorActual) {
                copeSelect.val(valorActual);
            }
        }

        function inicializarTabla() {
            if (tablaOrdenes) {
                tablaOrdenes.destroy();
            }
            
            tablaOrdenes = $('#tablaOrdenes').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                searching: false,
                ajax: {
                    url: 'obtener_ordenes_coordinador.php',
                    type: 'POST',
                    data: function(d) {
                        return {
                            ...d,
                            idUsuario: idUsuario,
                            fecha_inicio: $('#fecha_inicio').val(),
                            fecha_fin: $('#fecha_fin').val(),
                            estatus: $('#estatus').val(),
                            cope: $('#cope').val()
                        };
                    }
                },
                columns: [
                    { data: 'Folio_Pisa' },
                    { data: 'Telefono' },
                    { data: 'Ont' },
                    { data: 'NExpediente' },
                    { data: 'nombre_completo_cliente' },
                    { data: 'Direccion_Cliente' },
                    { data: 'nombre_completo_contratista' },
                    { data: 'nombre_completo_tecnico' },
                    { data: 'COPE' },
                    { data: 'area' },
                    { data: 'Division' },
                    { data: 'Distrito' },
                    { data: 'Tecnologia' },
                    { data: 'Tipo_Tarea' },
                    { data: 'Tipo_Instalacion' },
                    { data: 'Metraje' },
                    { data: 'Terminal' },
                    { data: 'Puerto' },
                    { 
                        data: 'Step_Registro',
                        render: function(data) {
                            return `Paso ${data}/5`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            if (data.Latitud && data.Longitud) {
                                return `${data.Latitud}, ${data.Longitud}`;
                            }
                            return 'N/A';
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            if (data.Latitud_Terminal && data.Longitud_Terminal) {
                                return `${data.Latitud_Terminal}, ${data.Longitud_Terminal}`;
                            }
                            return 'N/A';
                        }
                    },
                    { 
                        data: 'Fecha_Coordiapp',
                        render: function(data) {
                            if (!data) return 'N/A';
                            return new Date(data).toLocaleDateString('es-MX');
                        }
                    },
                    { 
                        data: 'Estatus_Real',
                        render: function(data) {
                            const clase = data === 'COMPLETADA' ? 'success' : 'warning';
                            return `<span class="badge badge-${clase}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            const ordenJSON = encodeURIComponent(JSON.stringify(data));
                            return `
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-modern btn-photos" onclick="viewPhotos(decodeURIComponent('${ordenJSON}'))" data-toggle="tooltip" title="Ver fotos">
                                        <i class="fas fa-images"></i>
                                    </button>
                                    <button class="btn btn-modern btn-pdf" onclick="openPDF(decodeURIComponent('${ordenJSON}'))" data-toggle="tooltip" title="Ver PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button class="btn btn-modern btn-map" onclick="viewMap(decodeURIComponent('${ordenJSON}'))" data-toggle="tooltip" title="Ver en mapa">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-MX.json'
                },
                pageLength: 20
            });
        }
        
        // Debounce utilitario
        function debounce(fn, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        const recargarTablaDebounced = debounce(() => {
            if (tablaOrdenes) {
                tablaOrdenes.ajax.reload();
            }
        }, 300);

        // Auto-aplicar filtros al instante
        $('#fecha_inicio, #fecha_fin, #estatus, #cope').on('change', recargarTablaDebounced);
        
        function limpiarFiltros() {
            // Obtener la fecha actual
            const currentDate = new Date();
            const fechaFin = currentDate.toISOString().split('T')[0];
            
            // Calcular la fecha de inicio (1 mes atrás)
            const fechaInicio = new Date(currentDate);
            fechaInicio.setMonth(fechaInicio.getMonth() - 1);
            
            // Establecer los valores por defecto
            $('#fecha_inicio').val(fechaInicio.toISOString().split('T')[0]);
            $('#fecha_fin').val(fechaFin);
            $('#estatus').val('');
            $('#cope').val('');
            
            recargarTablaDebounced();
            mostrarAlerta('success', 'Filtros restablecidos a valores predeterminados');
        }
        
        // Modal de fotos
        function viewPhotos(orden) {
            try {
                const ordenData = typeof orden === 'string' ? JSON.parse(orden) : orden;
                const noDisponible = 'nodisponible.webp';
                document.getElementById('modalFolioPisa').textContent = ordenData.Folio_Pisa || '';
                
                // Helper para setear src y fallback
                function setImg(id, path) {
                    const img = document.getElementById(id);
                    if (path) {
                        img.src = 'https://api.ed-intra.com/' + path.replace('../', '');
                    } else {
                        img.src = noDisponible;
                    }
                    img.onerror = function() { this.onerror = null; this.src = noDisponible; };
                }
                
                setImg('modalFotoOnt', ordenData.Foto_Ont);
                setImg('modalFotoCasaCliente', ordenData.Foto_Casa_Cliente);
                setImg('modalNoSerieONT', ordenData.No_Serie_ONT);
                setImg('modalFotoPuerto', ordenData.Foto_Puerto);
                setImg('modalFotoINE', ordenData.Foto_INE);
                
                $('#photoModal').modal('show');
            } catch (error) {
                console.error('Error al procesar datos de fotos:', error);
                mostrarAlerta('error', 'Error al cargar las fotos');
            }
        }

        // Abrir PDF en nueva pestaña
        function openPDF(orden) {
            try {
                const ordenData = typeof orden === 'string' ? JSON.parse(orden) : orden;
                if (ordenData.Folio_Pisa) {
                    const pdfUrl = `https://erp.ed-intra.com/Operaciones/modal/R20.php?Folio_Pisa=${ordenData.Folio_Pisa}`;
                    window.open(pdfUrl, '_blank');
                } else {
                    mostrarAlerta('warning', 'No se encontró el folio PISA');
                }
            } catch (error) {
                console.error('Error al abrir PDF:', error);
                mostrarAlerta('error', 'Error al abrir el PDF');
            }
        }

        // Ver mapa en Google Maps
        function viewMap(orden) {
            try {
                const ordenData = typeof orden === 'string' ? JSON.parse(orden) : orden;
                if (ordenData.Latitud && ordenData.Longitud) {
                    let mapsUrl;
                    if (ordenData.Latitud_Terminal && ordenData.Longitud_Terminal) {
                        mapsUrl = `https://www.google.com/maps/dir/${ordenData.Latitud},${ordenData.Longitud}/${ordenData.Latitud_Terminal},${ordenData.Longitud_Terminal}`;
                    } else {
                        mapsUrl = `https://www.google.com/maps/search/?api=1&query=${ordenData.Latitud},${ordenData.Longitud}`;
                    }
                    window.open(mapsUrl, '_blank');
                } else {
                    mostrarAlerta('warning', 'No hay coordenadas disponibles para este registro');
                }
            } catch (error) {
                console.error('Error al abrir mapa:', error);
                mostrarAlerta('error', 'Error al abrir el mapa');
            }
        }
        
        async function exportarExcel() {
            const fecha_inicio = $('#fecha_inicio').val();
            const fecha_fin = $('#fecha_fin').val();
            const estatus = $('#estatus').val();
            const cope = $('#cope').val();

            // Cargar dependencias si no están presentes
            const loadScript = (src) => new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = src;
                s.onload = resolve;
                s.onerror = reject;
                document.head.appendChild(s);
            });
            if (typeof ExcelJS === 'undefined') {
                try { await loadScript('https://cdn.jsdelivr.net/npm/exceljs@4.5.0/dist/exceljs.min.js'); } catch(e) {}
            }
            if (typeof saveAs === 'undefined') {
                try { await loadScript('https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js'); } catch(e) {}
            }

            // Toast con barra de progreso
            let progress = 0;
            const toastNode = document.createElement('div');
            toastNode.style.minWidth = '280px';
            toastNode.innerHTML = `
                <div style="font-weight:600;margin-bottom:6px">Generando Excel...</div>
                <div style="background:#e5e7eb;border-radius:6px;overflow:hidden;height:10px">
                    <div id="toastProgressBar" style="height:10px;width:0%;background:#2563eb;transition:width .2s ease"></div>
                </div>
                <div id="toastProgressPct" style="font-size:12px;margin-top:6px;color:#374151;text-align:right">0%</div>
            `;
            const progressToast = Toastify({ node: toastNode, duration: -1, gravity: 'top', position: 'right', close: true, stopOnFocus: false, style: { background: '#fff', color:'#111827', boxShadow:'0 10px 25px rgba(0,0,0,.15)' } });
            progressToast.showToast();
            const setProgress = (pct)=>{
                const bar = toastNode.querySelector('#toastProgressBar');
                const pctEl = toastNode.querySelector('#toastProgressPct');
                const val = Math.max(0, Math.min(100, Math.round(pct)));
                if (bar) bar.style.width = val + '%';
                if (pctEl) pctEl.textContent = val + '%';
            };
            setProgress(5);

            // Verificar que ExcelJS esté cargado
            if (typeof ExcelJS === 'undefined') {
                mostrarAlerta('error', 'Error: No se pudo cargar la librería ExcelJS');
                progressToast.hideToast();
                return;
            }

            const wb = new ExcelJS.Workbook();
            const ws = wb.addWorksheet('Órdenes');
            
            // Definir encabezados
            const headers = [
                'Folio Pisa', 'Teléfono', 'ONT', 'N° Expediente', 'Cliente',
                'Dirección', 'Contratista', 'Técnico', 'COPE', 'Área',
                'División', 'Distrito', 'Tecnología', 'Tipo Tarea',
                'Tipo Instalación', 'Metraje', 'Terminal', 'Puerto',
                'Paso', 'Coordenadas', 'Coord. Terminal', 'Fecha', 'Estado'
            ];
            
            ws.addRow(headers);
            
            // Formatear encabezados
            ws.getRow(1).font = { bold: true, color: { argb: 'FFFFFFFF' } };
            ws.getRow(1).fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FF0066CC' }
            };
            
            // Ajustar ancho de columnas
            ws.columns.forEach((column) => {
                column.width = 15;
            });
            
            // Obtener y agregar datos
            $.ajax({
                url: 'exportar_excel_ordenes.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    idUsuario: idUsuario,
                    fecha_inicio: fecha_inicio,
                    fecha_fin: fecha_fin,
                    estatus: estatus || '',
                    cope: cope || '',
                    export: true,
                    allRecords: true
                },
                success: async function(response) {
                    if (!(response && response.data && Array.isArray(response.data) && response.data.length)) {
                        mostrarAlerta('warning', 'No hay datos para exportar con los filtros seleccionados');
                        progressToast.hideToast();
                        return;
                    }

                    const datos = response.data;
                    const total = datos.length;
                    setProgress(10);

                    // Insertar filas en bloques para poder actualizar progreso y no bloquear UI
                    const chunkSize = 500;
                    for (let i = 0; i < total; i += chunkSize) {
                        const slice = datos.slice(i, i + chunkSize);
                        slice.forEach(orden => {
                            ws.addRow([
                                orden.Folio_Pisa,
                                orden.Telefono,
                                orden.Ont,
                                orden.NExpediente,
                                orden.nombre_completo_cliente,
                                orden.Direccion_Cliente,
                                orden.nombre_completo_contratista,
                                orden.nombre_completo_tecnico,
                                orden.COPE,
                                orden.area,
                                orden.Division,
                                orden.Distrito,
                                orden.Tecnologia,
                                orden.Tipo_Tarea,
                                orden.Tipo_Instalacion,
                                orden.Metraje,
                                orden.Terminal,
                                orden.Puerto,
                                orden.Step_Registro || '',
                                orden.Latitud && orden.Longitud ? `${orden.Latitud}, ${orden.Longitud}` : '',
                                orden.Latitud_Terminal && orden.Longitud_Terminal ? `${orden.Latitud_Terminal}, ${orden.Longitud_Terminal}` : '',
                                orden.Fecha_Coordiapp || '',
                                orden.Estatus_Real || ''
                            ]);
                        });
                        setProgress(10 + ((i + slice.length) / total) * 80); // hasta 90%
                        await new Promise(r => setTimeout(r));
                    }

                    setProgress(90);
                    const nombreArchivo = `OrdenesCoordinador_${fecha_inicio}_a_${fecha_fin}.xlsx`;
                    const buffer = await wb.xlsx.writeBuffer();
                    setProgress(98);
                    saveAs(new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }), nombreArchivo);
                    setProgress(100);
                    setTimeout(()=>{ progressToast.hideToast(); }, 700);
                    mostrarAlerta('success', 'Archivo exportado correctamente');
                },
                error: function(xhr) {
                    mostrarAlerta('error', 'Error al exportar datos');
                    progressToast.hideToast();
                }
            });
        }
        
        function mostrarAlerta(tipo, mensaje) {
            const bgColor = tipo === 'error' ? '#dc3545' : '#28a745';
            Toastify({
                text: mensaje,
                duration: 3000,
                gravity: "top",
                position: 'right',
                backgroundColor: bgColor,
                stopOnFocus: true
            }).showToast();
        }

        // Función para manejar la expansión de imágenes
        function setupImageExpansion() {
            // Crear el overlay si no existe
            if (!document.querySelector('.photo-overlay')) {
                const overlay = document.createElement('div');
                overlay.className = 'photo-overlay';
                document.body.appendChild(overlay);
            }

            // Agregar listeners a todas las imágenes
            document.querySelectorAll('.photo-image').forEach(img => {
                img.onclick = function() {
                    const overlay = document.querySelector('.photo-overlay');
                    if (this.classList.contains('expanded')) {
                        // Contraer imagen
                        this.classList.remove('expanded');
                        overlay.style.display = 'none';
                        document.body.style.overflow = '';
                    } else {
                        // Expandir imagen
                        this.classList.add('expanded');
                        overlay.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    }
                };
            });

            // Cerrar al hacer clic en el overlay
            document.querySelector('.photo-overlay').onclick = function() {
                const expandedImg = document.querySelector('.photo-image.expanded');
                if (expandedImg) {
                    expandedImg.classList.remove('expanded');
                    this.style.display = 'none';
                    document.body.style.overflow = '';
                }
            };
        }

        // Inicializar cuando el documento esté listo
        $(document).ready(function() {
            inicializarSelectCopes();
            inicializarTabla();
        });

        // Agregar listener para el evento show.bs.modal
        $('#photoModal').on('shown.bs.modal', function () {
            setupImageExpansion();
        });
    </script>
</body>
</html>
