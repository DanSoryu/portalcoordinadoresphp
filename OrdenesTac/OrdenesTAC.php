<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../Login/Login.php');
    exit();
}

$Usuario = $_SESSION['usuario'];
$idUsuario = isset($_SESSION['idusuarios_coordinadores']) ? $_SESSION['idusuarios_coordinadores'] : '';
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
                                <a class="dropdown-item-custom" href="../../../index.php">
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
                            <a class="nav-link" href="../OrdenesCoordinador/OrdenesCoordinador.php">
                                <i class="fas fa-table"></i>
                                <span>Ordenes Coordiapp</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="./OrdenesTAC.php">
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
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-file-invoice text-primary"></i> Órdenes TAC
                    </h1>
                    <p class="text-muted mb-0">Consulta de órdenes TAC filtradas por tus COPEs asignados</p>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter"></i> Filtros de Búsqueda
                    </h6>
                </div>
                <div class="card-body">
                    <form id="filtrosForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="estatus" class="form-label">Estatus</label>
                                <select class="form-control" id="estatus" name="estatus">
                                    <option value="">Todos</option>
                                    <option value="COMPLETADA">Completada</option>
                                    <option value="ASIGNADA">Asignada</option>
                                    <option value="EN_PROCESO">En Proceso</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary mr-2" onclick="cargarOrdenesTAC()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabla de Resultados -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table"></i> Órdenes TAC
                    </h6>
                    <div>
                        <button class="btn btn-success btn-sm" onclick="exportarExcel()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaOrdenesTAC" class="table table-bordered table-striped" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Folio Pisa</th>
                                    <th>Teléfono</th>
                                    <th>Expediente</th>
                                    <th>Técnico</th>
                                    <th>COPE</th>
                                    <th>Área</th>
                                    <th>División</th>
                                    <th>Distrito</th>
                                    <th>Tecnología</th>
                                    <th>Tipo Tarea</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery desde CDN (más confiable) -->
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
        let tablaOrdenesTAC = null;
        let datosOrdenes = [];

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
        
        // Verificar que jQuery esté cargado
        function verificarJQuery() {
            if (typeof jQuery === 'undefined') {
                console.error('jQuery no está cargado');
                setTimeout(verificarJQuery, 100);
                return;
            }
            console.log('jQuery cargado correctamente');
            inicializarAplicacion();
        }
        
        function inicializarAplicacion() {
            $(document).ready(function() {
                console.log('Documento listo, inicializando aplicación');
                // Inicializar DataTable
                inicializarTabla();
                
                // Cargar datos iniciales
                cargarOrdenesTAC();
            });
        }
        
        // Iniciar verificación cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', verificarJQuery);
        } else {
            verificarJQuery();
        }
        
        function inicializarTabla() {
            if (tablaOrdenesTAC) {
                tablaOrdenesTAC.destroy();
            }
            
            tablaOrdenesTAC = $('#tablaOrdenesTAC').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                },
                "pageLength": 25,
                "order": [[10, "desc"]], // Ordenar por fecha descendente
                "columnDefs": [
                    { "orderable": false, "targets": [] }
                ],
                "responsive": true,
                "scrollX": true,
                "deferRender": true
            });

            // Búsqueda global instantánea
            $('#filtro_global').on('input', function() {
                const term = this.value || '';
                tablaOrdenesTAC.search(term).draw();
            });
        }
        
        // Debounce utilitario
        function debounce(fn, wait) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        const cargarOrdenesTACDebounced = debounce(cargarOrdenesTAC, 300);

        function cargarOrdenesTAC() {
            console.log('Iniciando carga de órdenes TAC');
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();
            const estatus = $('#estatus').val();
            
            console.log('Fechas:', fechaInicio, fechaFin, 'Estatus:', estatus);
            
            // Validar fechas
            if (!fechaInicio || !fechaFin) {
                mostrarAlerta('warning', 'Por favor selecciona ambas fechas');
                return;
            }
            
            if (fechaInicio > fechaFin) {
                mostrarAlerta('warning', 'La fecha de inicio no puede ser mayor que la fecha fin');
                return;
            }
            
            // Mostrar loading
            $('#tablaOrdenesTAC tbody').html(`
                <tr>
                    <td colspan="11" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando órdenes TAC...</p>
                    </td>
                </tr>
            `);
            
            console.log('Enviando petición AJAX...');
            $.ajax({
                url: 'requests/obtener_ordenes_tac.php',
                type: 'POST',
                data: {
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    estatus: estatus
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta recibida:', response);
                    if (response.success) {
                        console.log('Datos recibidos:', response.data);
                        mostrarOrdenes(response.data);
                        datosOrdenes = response.data; // Guardar para exportación
                        mostrarAlerta('success', `Se encontraron ${response.data.length} órdenes TAC`);
                    } else {
                        console.error('Error en respuesta:', response.message);
                        mostrarAlerta('danger', 'Error: ' + response.message);
                        $('#tablaOrdenesTAC tbody').html(`
                            <tr>
                                <td colspan="11" class="text-center text-danger">
                                    <i class="fas fa-exclamation-triangle"></i> ${response.message}
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX completo:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    mostrarAlerta('danger', 'Error al cargar las órdenes TAC');
                    $('#tablaOrdenesTAC tbody').html(`
                        <tr>
                            <td colspan="11" class="text-center text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Auto-aplicar filtros al instante
        $('#fecha_inicio, #fecha_fin').on('change', function() { cargarOrdenesTACDebounced(); });
        $('#estatus').on('change', function() { cargarOrdenesTACDebounced(); });
        
        function mostrarOrdenes(ordenes) {
            if (tablaOrdenesTAC) {
                tablaOrdenesTAC.clear();
                
                if (ordenes && ordenes.length > 0) {
                    ordenes.forEach(function(orden) {
                        tablaOrdenesTAC.row.add([
                            orden.Folio_Pisa || '',
                            orden.TELEFONO || '',
                            orden.Expediente || '',
                            orden.Tecnico || '',
                            orden.NOM_CT || '',
                            orden.NOM_AREA || '',
                            orden.NOM_DIVISION || '',
                            orden.Distrito || '',
                            orden.TECNOLOGIA || '',
                            orden.Tipo_tarea || '',
                            orden.FECHA_LIQ || ''
                        ]);
                    });
                } else {
                    tablaOrdenesTAC.row.add([
                        '', '', '', '', '', '', '', '', '', '', 'No se encontraron órdenes'
                    ]);
                }
                
                tablaOrdenesTAC.draw();
            }
        }
        
        function limpiarFiltros() {
            $('#fecha_inicio').val('<?php echo date('Y-m-d'); ?>');
            $('#fecha_fin').val('<?php echo date('Y-m-d'); ?>');
            $('#estatus').val('');
            cargarOrdenesTAC();
            mostrarAlerta('info', 'Filtros restablecidos');
        }
        
        function exportarExcel() {
            if (!datosOrdenes || datosOrdenes.length === 0) {
                mostrarAlerta('warning', 'No hay datos para exportar');
                return;
            }
            
            try {
                const wb = new ExcelJS.Workbook();
                const ws = wb.addWorksheet('Órdenes TAC');
                
                // Definir columnas
                ws.columns = [
                    { key: 'Folio_Pisa', width: 15 },
                    { key: 'TELEFONO', width: 15 },
                    { key: 'Expediente', width: 15 },
                    { key: 'Tecnico', width: 25 },
                    { key: 'NOM_CT', width: 15 },
                    { key: 'NOM_AREA', width: 20 },
                    { key: 'NOM_DIVISION', width: 20 },
                    { key: 'Distrito', width: 15 },
                    { key: 'TECNOLOGIA', width: 15 },
                    { key: 'Tipo_tarea', width: 20 },
                    { key: 'FECHA_LIQ', width: 15 }
                ];
                
                // Agregar encabezados
                ws.addRow([
                    'Folio Pisa', 'Teléfono', 'Expediente', 'Técnico', 'COPE', 
                    'Área', 'División', 'Distrito', 'Tecnología', 'Tipo Tarea', 'Fecha'
                ]);
                
                // Estilizar encabezados
                const headerRow = ws.getRow(1);
                headerRow.font = { bold: true };
                headerRow.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FF264653' }
                };
                headerRow.font = { color: { argb: 'FFFFFFFF' }, bold: true };
                
                // Agregar datos
                datosOrdenes.forEach(orden => {
                    ws.addRow([
                        orden.Folio_Pisa || '',
                        orden.TELEFONO || '',
                        orden.Expediente || '',
                        orden.Tecnico || '',
                        orden.NOM_CT || '',
                        orden.NOM_AREA || '',
                        orden.NOM_DIVISION || '',
                        orden.Distrito || '',
                        orden.TECNOLOGIA || '',
                        orden.Tipo_tarea || '',
                        orden.FECHA_LIQ || ''
                    ]);
                });
                
                // Generar nombre del archivo
                const fechaInicio = $('#fecha_inicio').val();
                const fechaFin = $('#fecha_fin').val();
                const nombreArchivo = `OrdenesTAC_${fechaInicio}_a_${fechaFin}.xlsx`;
                
                // Descargar archivo
                wb.xlsx.writeBuffer().then(function(buffer) {
                    saveAs(new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }), nombreArchivo);
                    mostrarAlerta('success', 'Archivo exportado correctamente');
                });
                
            } catch (error) {
                console.error('Error al exportar:', error);
                mostrarAlerta('danger', 'Error al exportar el archivo');
            }
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
            
            // Remover alertas anteriores
            $('.alert').remove();
            
            // Agregar nueva alerta
            $('.main-content .container-fluid').prepend(alertHtml);
            
            // Auto-hide después de 5 segundos
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
</body>
</html>