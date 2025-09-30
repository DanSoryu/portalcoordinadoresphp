<?php
// Vista principal para mostrar la tabla de órdenes coordinador
require_once __DIR__ . "/db/Ordenes.php";

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../Login/Login.php');
    exit();
}

$Usuario = $_SESSION['usuario'];
$idUsuario = $_SESSION['idusuarios_coordinadores'];

$ordenesObj = new Ordenes();
// Obtener copes del coordinador mediante la función pivote
$copesData = $ordenesObj->obtenerCopesCoordinador($idUsuario);
$copes = [];
if (!empty($copesData)) {
    foreach ($copesData as $copeRow) {
        if (isset($copeRow['id'])) {
            $copes[] = $copeRow['id'];
        }
    }
}
// Ya no cargamos órdenes en servidor; DataTables las pedirá con paginación
    $ordenes = ['data' => []];
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
                                <a class="dropdown-item-custom" href="../Dashboard/Dashboard.php">
                                    <i class="fas fa-cogs"></i> Operaciones
                                </a>
                                <a class="dropdown-item-custom" href="../../../AlmacenNuevo/index.php">
                                    <i class="fas fa-warehouse"></i> Almacén
                                </a>
                            </div>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="../Dashboard/Dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="./OrdenesCoordinador.php">
                                <i class="fas fa-table"></i>
                                <span>Ordenes Coordiapp</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="../OrdenesTAC/OrdenesTAC.php">
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
                        <a class="user-dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalLogout">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Contenido principal -->
    <div class="main-content">
        <div class="container-fluid">
            
            <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-tasks"></i> Órdenes Coordinador</h1>
            <input type="hidden" id="idUsuario" value="<?php echo htmlspecialchars($_SESSION['idusuarios_coordinadores']); ?>">

            <!-- Filtros -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-filter"></i> Filtros de Búsqueda
                        </h6>
                        <div>
                            <button type="button" class="btn btn-primary mr-2" onclick="cargarOrdenesCoordinador()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
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
                        <i class="fas fa-table"></i> Órdenes Coordinador
                    </h6>
                    <div>
                        <button class="btn btn-success btn-sm" onclick="exportarExcel()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaOrdenes" class="table table-bordered table-striped" style="width:100%">
                            <thead class="thead-dark">
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
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>Copyright &copy; ENLACE DIGITAL 2025</span>
            </div>
        </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
        <!-- Incluir modales -->
        <?php include('../Login/vistas/components/Logout.php'); ?>

        <!-- Modal de Fotos -->
        <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="photoModalLabel">Fotografías Orden: <span id="modalFolioPisa"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <h6>Foto ONT</h6>
                                <p class="text-muted small">Una fotografía de la parte frontal de la ONT</p>
                                <div class="bg-light rounded p-2 text-center">
                                    <img id="modalFotoOnt" src="" alt="Foto ONT" class="img-fluid rounded">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>Foto Casa Cliente</h6>
                                <p class="text-muted small">Una fotografía de la casa del cliente</p>
                                <div class="bg-light rounded p-2 text-center">
                                    <img id="modalFotoCasaCliente" src="" alt="Foto Casa Cliente" class="img-fluid rounded">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>No. Serie ONT</h6>
                                <p class="text-muted small">Una fotografía de la parte trasera de la ONT</p>
                                <div class="bg-light rounded p-2 text-center">
                                    <img id="modalNoSerieONT" src="" alt="No. Serie ONT" class="img-fluid rounded">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>Foto Terminal</h6>
                                <p class="text-muted small">Una fotografía de la terminal</p>
                                <div class="bg-light rounded p-2 text-center">
                                    <img id="modalFotoPuerto" src="" alt="Foto Puerto" class="img-fluid rounded">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>Foto SO</h6>
                                <p class="text-muted small">Una fotografía de la hoja de servicio</p>
                                <div class="bg-light rounded p-2 text-center">
                                    <img id="modalFotoINE" src="" alt="Foto INE" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery y dependencias necesarias -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- DataTables (requiere jQuery) -->
        <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js"></script>
        <!-- Bootstrap core JavaScript (Bootstrap 5) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Toastify -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <!-- Custom JS -->
        
        <script src="vistas/assets/js/toasts.js"></script>
        <script src="vistas/assets/js/notifications.js"></script>
        <script src="vistas/assets/js/ordenes.js"></script>
        <!-- Dependencias para exportación a Excel -->
        <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
        <!-- ExcelJS y FileSaver para exportación -->
        <script src="https://cdn.jsdelivr.net/npm/exceljs@4.5.0/dist/exceljs.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
        <script>
        // Actualizar min y max dinámicamente en los inputs de fecha
        $(document).ready(function() {
            const $fechaInicio = $('#fecha_inicio');
            const $fechaFin = $('#fecha_fin');
            const minDate = $fechaInicio.attr('min');
            const maxDate = $fechaInicio.attr('max');

            $fechaInicio.on('change', function() {
                $fechaFin.attr('min', this.value || minDate);
            });
            $fechaFin.on('change', function() {
                $fechaInicio.attr('max', this.value || maxDate);
            });
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

                // Modal de fotos
                function viewPhotos(orden) {
                    if (typeof orden === 'string') {
                        orden = JSON.parse(orden);
                    }
                    const noDisponible = 'vistas/assets/nodisponible.webp';
                    document.getElementById('modalFolioPisa').textContent = orden.Folio_Pisa || '';
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
                    setImg('modalFotoOnt', orden.Foto_Ont);
                    setImg('modalFotoCasaCliente', orden.Foto_Casa_Cliente);
                    setImg('modalNoSerieONT', orden.No_Serie_ONT);
                    setImg('modalFotoPuerto', orden.Foto_Puerto);
                    setImg('modalFotoINE', orden.Foto_INE);
                    var modal = new bootstrap.Modal(document.getElementById('photoModal'));
                    modal.show();
                }

                // Abrir PDF en nueva pestaña
                function openPDF(orden) {
                    if (typeof orden === 'string') {
                        orden = JSON.parse(orden);
                    }
                    if (orden.Folio_Pisa) {
                        const pdfUrl = `https://erp.ed-intra.com/Operaciones/modal/R20.php?Folio_Pisa=${orden.Folio_Pisa}`;
                        window.open(pdfUrl, '_blank');
                    }
                }

                // Ver mapa en Google Maps
                function viewMap(orden) {
                    if (typeof orden === 'string') {
                        orden = JSON.parse(orden);
                    }
                    if (orden.Latitud && orden.Longitud) {
                        let mapsUrl;
                        if (orden.Latitud_Terminal && orden.Longitud_Terminal) {
                            mapsUrl = `https://www.google.com/maps/dir/${orden.Latitud},${orden.Longitud}/${orden.Latitud_Terminal},${orden.Longitud_Terminal}`;
                        } else {
                            mapsUrl = `https://www.google.com/maps/search/?api=1&query=${orden.Latitud},${orden.Longitud}`;
                        }
                        window.open(mapsUrl, '_blank');
                    } else {
                        alert('No hay coordenadas disponibles para este registro');
                    }
                }

                async function exportarExcel() {
                    const idUsuario = $('#idUsuario').val();
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

                    $.ajax({
                        url: '../OrdenesCoordinador/requests/exportar_excel_ordenes.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            idUsuario: idUsuario,
                            fecha_inicio: fecha_inicio,
                            fecha_fin: fecha_fin,
                            estatus: estatus || '',
                            cope: cope || ''
                        },
                        success: async function(response) {
                            if (!(response && response.success && Array.isArray(response.data) && response.data.length)) {
                                if (typeof Toast !== 'undefined' && Toast.warning) { Toast.warning('No hay datos para exportar con los filtros seleccionados'); }
                                progressToast.hideToast();
                                return;
                            }
                            const datos = response.data;
                            const total = datos.length;
                                const wb = new ExcelJS.Workbook();
                                const ws = wb.addWorksheet('Órdenes Coordinador');

                                ws.columns = [
                                    { header: 'Folio Pisa', key: 'Folio_Pisa', width: 15 },
                                    { header: 'Teléfono', key: 'Telefono', width: 15 },
                                    { header: 'ONT', key: 'Ont', width: 15 },
                                    { header: 'N° Expediente', key: 'NExpediente', width: 15 },
                                    { header: 'Cliente', key: 'nombre_completo_cliente', width: 25 },
                                    { header: 'Dirección', key: 'Direccion_Cliente', width: 25 },
                                    { header: 'Contratista', key: 'nombre_completo_contratista', width: 25 },
                                    { header: 'Técnico', key: 'nombre_completo_tecnico', width: 25 },
                                    { header: 'COPE', key: 'COPE', width: 10 },
                                    { header: 'Área', key: 'area', width: 15 },
                                    { header: 'División', key: 'Division', width: 15 },
                                    { header: 'Distrito', key: 'Distrito', width: 15 },
                                    { header: 'Tecnología', key: 'Tecnologia', width: 15 },
                                    { header: 'Tipo Tarea', key: 'Tipo_Tarea', width: 15 },
                                    { header: 'Tipo Instalación', key: 'Tipo_Instalacion', width: 15 },
                                    { header: 'Metraje', key: 'Metraje', width: 10 },
                                    { header: 'Terminal', key: 'Terminal', width: 10 },
                                    { header: 'Puerto', key: 'Puerto', width: 10 },
                                    { header: 'Paso', key: 'Step_Registro', width: 10 },
                                { header: 'Coordenadas', key: 'LatLon', width: 20 },
                                { header: 'Coord. Terminal', key: 'LatLon_Term', width: 20 },
                                    { header: 'Fecha', key: 'Fecha_Coordiapp', width: 15 },
                                { header: 'Estado', key: 'Estado', width: 15 }
                            ];

                            // Insertar filas en bloques para poder actualizar progreso y no bloquear UI
                            const chunkSize = 500;
                            for (let i = 0; i < total; i += chunkSize) {
                                const slice = datos.slice(i, i + chunkSize);
                                slice.forEach(orden => {
                                    // Estado calculado como en la tabla
                                    const est = (orden.Estatus_Orden || '').toString().toUpperCase();
                                    const step = parseInt(orden.Step_Registro || 0, 10);
                                    let estadoFinal = 'INCOMPLETA';
                                    if (est === 'OBJETADA') estadoFinal = 'OBJETADA';
                                    else if (step === 5) estadoFinal = 'COMPLETADA';

                                    ws.addRow({
                                        Folio_Pisa: orden.Folio_Pisa || '',
                                        Telefono: orden.Telefono || '',
                                        Ont: orden.Ont || '',
                                        NExpediente: orden.NExpediente || '',
                                        nombre_completo_cliente: orden.nombre_completo_cliente || '',
                                        Direccion_Cliente: orden.Direccion_Cliente || '',
                                        nombre_completo_contratista: orden.nombre_completo_contratista || '',
                                        nombre_completo_tecnico: orden.nombre_completo_tecnico || '',
                                        COPE: orden.COPE || '',
                                        area: orden.area || '',
                                        Division: orden.Division || '',
                                        Distrito: orden.Distrito || '',
                                        Tecnologia: orden.Tecnologia || '',
                                        Tipo_Tarea: orden.Tipo_Tarea || '',
                                        Tipo_Instalacion: orden.Tipo_Instalacion || '',
                                        Metraje: orden.Metraje || '',
                                        Terminal: orden.Terminal || '',
                                        Puerto: orden.Puerto || '',
                                        Step_Registro: orden.Step_Registro || '',
                                        LatLon: (orden.Latitud || '') + ', ' + (orden.Longitud || ''),
                                        LatLon_Term: (orden.Latitud_Terminal || '') + ', ' + (orden.Longitud_Terminal || ''),
                                        Fecha_Coordiapp: orden.Fecha_Coordiapp || '',
                                        Estado: estadoFinal
                                    });
                                });
                                setProgress(5 + ((i + slice.length) / total) * 80); // hasta 85%
                                await new Promise(r => setTimeout(r));
                            }

                            setProgress(90);
                            const nombreArchivo = `OrdenesCoordinador_${fecha_inicio}_a_${fecha_fin}.xlsx`;
                            const buffer = await wb.xlsx.writeBuffer();
                            setProgress(98);
                            saveAs(new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }), nombreArchivo);
                            setProgress(100);
                            setTimeout(()=>{ progressToast.hideToast(); }, 700);
                            if (typeof Toast !== 'undefined' && Toast.success) { Toast.success('Archivo exportado correctamente'); }
                        },
                        error: function(xhr) {
                            if (typeof Toast !== 'undefined' && Toast.error) { Toast.error('Error al exportar el archivo'); }
                            progressToast.hideToast();
                        }
                    });
                }

                // Inicialización DataTables con server-side processing
                $(document).ready(function() {
                    // Desactivar errores por defecto de DataTables y manejarlos manualmente
                    $.fn.dataTable.ext.errMode = 'none';
                        const tabla = $('#tablaOrdenes').DataTable({
                        processing: true,
                        serverSide: true,
                        searching: false,
                            responsive: true,
                            scrollX: true,
                        language: {
                            url: 'https://cdn.datatables.net/plug-ins/2.0.0/i18n/es-ES.json',
                            processing: '',
                            lengthMenu: 'Mostrar _MENU_ registros por página'
                        },
                        dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-md-end"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        pageLength: 25,
                        ajax: {
                            url: '../OrdenesCoordinador/requests/get_copes_ordenes.php',
                            type: 'POST',
                            data: function(d) {
                                console.group('DT -> Preparando petición');
                                console.log('draw:', d.draw, 'start:', d.start, 'length:', d.length);
                                console.log('filtros actuales', {
                                    idUsuario: $('#idUsuario').val(),
                                    fecha_inicio: $('#fecha_inicio').val(),
                                    fecha_fin: $('#fecha_fin').val(),
                                    estatus: $('#estatus').val(),
                                    cope: $('#cope').val()
                                });
                                console.groupEnd();
                                d.idUsuario = $('#idUsuario').val();
                                d.fecha_inicio = $('#fecha_inicio').val();
                                d.fecha_fin = $('#fecha_fin').val();
                                d.estatus = $('#estatus').val();
                                d.cope = $('#cope').val();
                            },
                            beforeSend: function() {
                                console.info('DT -> Enviando petición al servidor (get_copes_ordenes.php) ...');
                                if (typeof Toast !== 'undefined' && Toast.info) {
                                    Toast.info('Cargando datos...');
                                }
                            },
                            complete: function(jqXHR) {
                                try {
                                    const resp = jqXHR.responseJSON || JSON.parse(jqXHR.responseText || '{}');
                                    if (resp && resp.debug) {
                                        console.group('DT <- Respuesta con debug');
                                        console.log('debug:', resp.debug);
                                        console.groupEnd();
                                    } else {
                                        console.debug('DT <- Respuesta recibida (sin campo debug)');
                                    }
                                } catch(e) {
                                    console.warn('No se pudo parsear la respuesta para debug:', e);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('DT XHR Error:', { status: jqXHR.status, textStatus, errorThrown, response: jqXHR.responseText });
                                if (typeof Toast !== 'undefined' && Toast.error) {
                                    Toast.error('Error al cargar la tabla (' + (jqXHR.status || '0') + ')');
                                }
                            }
                        },
                        stateSave: true,
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
                            { data: 'Step_Registro' },
                            { data: null, render: function(row){ return (row.Latitud || '') + ', ' + (row.Longitud || ''); } },
                            { data: null, render: function(row){ return (row.Latitud_Terminal || '') + ', ' + (row.Longitud_Terminal || ''); } },
                            { data: 'Fecha_Coordiapp' },
                            { data: null, render: function(row){
                                const estatus = (row.Estatus_Orden || '').toString().toUpperCase();
                                const step = parseInt(row.Step_Registro || 0, 10);
                                let label;
                                if (estatus === 'OBJETADA') {
                                    label = 'OBJETADA';
                                } else if (step === 5) {
                                    label = 'COMPLETADA';
                                } else {
                                    label = 'INCOMPLETA';
                                }
                                // Mapear a estilos de badge (Bootstrap 5.3)
                                let cls = 'badge rounded-pill';
                                if (label === 'COMPLETADA') {
                                    cls += ' border border-success text-success bg-success-subtle';
                                } else if (label === 'OBJETADA') {
                                    cls += ' border border-danger text-danger bg-danger-subtle';
                                } else {
                                    cls += ' border border-warning text-warning bg-warning-subtle';
                                }
                                return '<span class="' + cls + '">' + label + '</span>';
                            } },
                            { data: null, orderable: false, render: function(row){
                                const json = JSON.stringify(row).replace(/"/g, '&quot;');
                                return `
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button onclick="viewMap(\`${json}\`)" class="btn btn-icon btn-primary shadow-sm" title="Ver Mapa" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;"><i class=\"fas fa-map-marker-alt\"></i></button>
                                        <button onclick="viewPhotos(\`${json}\`)" class="btn btn-icon btn-success shadow-sm" title="Ver Fotos" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border: none;"><i class=\"fas fa-image\"></i></button>
                                        <button onclick="openPDF(\`${json}\`)" class="btn btn-icon btn-danger shadow-sm" title="Ver PDF" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); border: none;"><i class=\"fas fa-file-pdf\"></i></button>
                                    </div>
                                `;
                            } }
                        ],
                        order: [[21, 'desc']],
                        drawCallback: function(settings) {
                            console.log('DT -> drawCallback', {
                                page: this.api().page.info(),
                                recordsTotal: settings._iRecordsTotal,
                                recordsDisplay: settings._iRecordsDisplay
                            });
                            if (settings.json && settings.json.debug) {
                                console.debug('DT -> debug (server):', settings.json.debug);
                            }
                        },
                        initComplete: function(settings, json) {
                            console.log('DT -> initComplete');
                            if (json && json.debug) {
                                console.debug('DT -> debug inicial (server):', json.debug);
                            }
                        }
                    });

                    // Capturar errores de DataTables
                    $('#tablaOrdenes').on('error.dt', function(e, settings, techNote, message) {
                        console.error('DataTables error:', message);
                        Toastify({ text: 'Error de DataTables: ' + message, duration: 4000, gravity: 'top', position: 'right', backgroundColor: '#dc2626' }).showToast();
                    });

                    // Botones de filtros
                    window.cargarOrdenesCoordinador = function(){
                        if (typeof Toast !== 'undefined' && Toast.info) {
                            Toast.info('Cargando datos con filtros...');
                        }
                        tabla.ajax.reload();
                    };
                    window.limpiarFiltros = function(){
                        const $fechaInicio = $('#fecha_inicio');
                        const $fechaFin = $('#fecha_fin');
                        const hoy = $fechaInicio.attr('max');
                        // Un mes atrás desde hoy
                        const d = new Date(hoy);
                        d.setMonth(d.getMonth() - 1);
                        const unMesAtras = d.toISOString().slice(0,10);
                        $fechaInicio.val(unMesAtras);
                        $fechaFin.val(hoy);
                        $('#estatus').val('');
                        $('#cope').val('');
                        if (typeof Toast !== 'undefined' && Toast.info) {
                            Toast.info('Restableciendo filtros y cargando...');
                        }
                        tabla.ajax.reload();
                    };
                });
        </script>
</body>
</html>
