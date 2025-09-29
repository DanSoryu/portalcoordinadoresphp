<?php
require_once __DIR__ . "/../db/Usuarios_coordinadores.php";
require_once(__DIR__ . "/../../../Login/controller/Class_Login.php");

session_start();

$Usuario = $_SESSION['Usuario'];
$Nombres = $_SESSION['Nombres'];
$idUsuario = $_SESSION['idUsuarios'];

if (empty($Usuario)) {
    header("location: ../../Login/modal/poweroff.php");
}

$login = new Login();
$modulos = $login->cargar_modulos($idUsuario);

$usuarios = new Usuarios_coordinadores();
$copes = $usuarios->obtenerCopes();
$listaUsuarios = $usuarios->obtenerUsuariosCoordinadores();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ERP - Gestión de Usuarios Coordinadores</title>

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
    <link rel="stylesheet" href="assets/css/preloader.css">

    <style>
        /* Reset de márgenes para body */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Nunito', sans-serif;
        }

        /* Header principal */
        .main-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
        }

        /* Navegación principal */
        .main-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link i {
            font-size: 1rem;
        }

        /* Dropdown de módulos */
        .dropdown-toggle {
            background: none;
            border: none;
            cursor: pointer;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 0.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            min-width: 250px;
            padding: 0.5rem 0;
            display: none;
            z-index: 1001;
        }

        .dropdown-menu-custom.show {
            display: block;
        }

        .dropdown-header-custom {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .dropdown-item-custom {
            display: block;
            padding: 0.5rem 1rem;
            color: #374151;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .dropdown-item-custom:hover {
            background-color: #f3f4f6;
            color: #2563eb;
        }

        /* Menú de usuario */
        .user-menu {
            position: relative;
        }

        .user-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .user-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-avatar svg {
            width: 20px;
            height: 20px;
            color: #2563eb;
        }

        .user-name {
            font-weight: 500;
            font-size: 0.875rem;
        }

        .user-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            padding: 0.5rem 0;
            display: none;
            z-index: 1001;
        }

        .user-dropdown.show {
            display: block;
        }

        .user-dropdown-item {
            display: block;
            width: 100%;
            padding: 0.5rem 1rem;
            color: #374151;
            text-decoration: none;
            background: none;
            border: none;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .user-dropdown-item:hover {
            background-color: #f3f4f6;
            color: #dc2626;
        }

        .user-dropdown-item i {
            margin-right: 0.5rem;
            width: 16px;
        }

        /* Contenido principal */
        .main-content {
            margin-top: 64px;
            padding: 2rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .header-left {
                gap: 1rem;
            }

            .nav-link {
                padding: 0.5rem;
                font-size: 0.8rem;
            }

            .nav-link span {
                display: none;
            }

            .nav-link i {
                margin: 0;
            }
        }

        @media (max-width: 768px) {
            .header-title {
                font-size: 1rem;
            }

            .main-nav {
                display: none;
            }

            .user-name {
                display: none;
            }
        }

        /* Chevron icon */
        .chevron-icon {
            width: 16px;
            height: 16px;
            transition: transform 0.2s;
        }

        .chevron-icon.rotate {
            transform: rotate(180deg);
        }

        /* Scroll to top button adjustment */
        .scroll-to-top {
            bottom: 20px !important;
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
                                <a class="dropdown-item-custom" href="../../../index.php">
                                    <i class="fas fa-cogs"></i> Operaciones
                                </a>
                                <a class="dropdown-item-custom" href="../../../AlmacenNuevo/index.php">
                                    <i class="fas fa-warehouse"></i> Almacén
                                </a>
                            </div>
                        </div>

                        <div class="nav-item">
                            <a class="nav-link" href="../../../index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>

                        <div class="nav-item">
                            <a class="nav-link" href="../../OrdenesCoordiapp.php">
                                <i class="fas fa-table"></i>
                                <span>Ordenes Coordiapp</span>
                            </a>
                        </div>

                        <div class="nav-item">
                            <a class="nav-link" href="../../OrdenesTac.php">
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
                        <span class="user-name"><?php echo $Nombres; ?></span>
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

    <!-- Contenido principal -->
    <div class="main-content">
        <div class="container-fluid">

            <!-- Incluir el preloader -->
            <?php include('components/preloader.php'); ?>

            <?php
            // Mostrar toast si existe en sesión
            if (isset($_SESSION['toast'])) {
                $toast = $_SESSION['toast'];
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Toastify({
                            text: '" . addslashes($toast['mensaje']) . "',
                            duration: 7000,
                            gravity: 'bottom',
                            position: 'right',
                            style: {
                                background: '" . ($toast['tipo'] === 'success' ? '#28a745' : '#dc3545') . "'
                            },
                            stopOnFocus: true,
                            close: true
                        }).showToast();
                    });
                </script>";
                unset($_SESSION['toast']);
            }
            ?>

            <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-users-cog"></i> Gestión de Usuarios Coordinadores</h1>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalAgregarUsuario">
                        <i class="fas fa-user-plus"></i> Agregar Usuario
                    </button>
                    <div class="table-responsive">
                        <table id="tablaUsuarios" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>COPEs Asignados</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listaUsuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                                        <td>
                                            <?php if (!empty($usuario['copes_asignados'])): ?>
                                                <?php 
                                                    $copes_array = explode(', ', $usuario['copes_asignados']);
                                                    $cope_count = count($copes_array);
                                                ?>
                                                <span class="badge badge-primary"><?php echo $cope_count; ?> COPEs</span>
                                                <a href="#" 
                                                   class="ml-2" 
                                                   data-toggle="popover" 
                                                   data-trigger="hover"
                                                   title="COPEs Asignados" 
                                                   data-content="<?php echo htmlspecialchars($usuario['copes_asignados']); ?>"
                                                   data-html="true">
                                                    <i class="fas fa-info-circle text-info"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Sin COPEs</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                                // Preparar el objeto usuario con copes como array
                                                $usuario_obj = $usuario;
                                                $usuario_obj['copes'] = !empty($usuario['copes_asignados']) ? explode(', ', $usuario['copes_asignados']) : [];
                                            ?>
                                            <button type="button" class="btn btn-sm btn-primary btn-editar" 
                                                    data-id="<?php echo $usuario['idusuarios_coordinadores']; ?>" 
                                                    data-usuario="<?php echo htmlspecialchars($usuario['usuario']); ?>" 
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminar(<?php echo $usuario['idusuarios_coordinadores']; ?>, '<?php echo htmlspecialchars($usuario['usuario']); ?>')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
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
    <?php 
    include('../../Login/vistas/components/Logout.php');
    include('components/modal_agregar_usuario.php');
    include('components/modal_editar_usuario.php');
    include('components/modal_eliminar_usuario.php');
    ?>

    <!-- jQuery primero -->
    <script src="/Operaciones/vendor/jquery/jquery.min.js"></script>
    
    <!-- Bootstrap core JavaScript (Bootstrap 4 for sb-admin-2)-->
    <script src="/Operaciones/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Core plugin JavaScript-->
    <script src="/Operaciones/vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <!-- Custom scripts for all pages-->
    <script src="/Operaciones/js/sb-admin-2.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js"></script>
    
    <!-- Toastify -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/registro.js"></script>
    <script src="assets/js/toasts.js"></script>
    <script src="assets/js/notifications.js"></script>
    <script src="assets/js/preloader.js"></script>
    <script src="assets/js/datatable_config.js"></script>
    <script src="assets/js/modal_agregar_usuario.js"></script>
    <script src="assets/js/modal_editar_usuario.js"></script>
    <script src="assets/js/modal_eliminar_usuario.js"></script>
    <script src="assets/js/prevent-multiple-submits.js"></script>

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

        // Manejar el clic en el botón de editar
        $(document).ready(function() {
            $('.btn-editar').on('click', function() {
                const idUsuario = $(this).data('id');
                const usuario = $(this).data('usuario');

                if (!idUsuario || !usuario) {
                    console.error('ID o nombre de usuario no definidos. Verifica los atributos data-id y data-usuario en el botón.');
                    Toast.error('Error: No se pudo cargar la información del usuario.');
                    return;
                }

                console.log('ID del usuario:', idUsuario);
                console.log('Nombre del usuario:', usuario);

                // Llenar los campos del modal con los datos del usuario
                $('#edit_id').val(idUsuario);
                $('#edit_usuario').val(usuario);
                $('#edit_password').val(''); // Limpiar el campo de contraseña

                // Limpiar los checkboxes de COPEs
                $('#edit-cope-checkboxes').empty();

                // Cargar los COPEs asignados y disponibles
                showPreloader(); // Mostrar preloader antes de la petición
                $.ajax({
                    url: '../requests/obtener_copes_usuario.php', // Corregir la ruta si es necesario
                    method: 'POST',
                    data: { idUsuario: idUsuario },
                    dataType: 'json',
                    success: function(response) {
                        hidePreloader(); // Ocultar preloader cuando se recibe respuesta
                        console.log('Respuesta de obtener_copes_usuario:', response);

                        if (response.success) {
                            // Limpiar los checkboxes existentes
                            $('#edit-cope-checkboxes').empty();
                            
                            // Crear un mapa de COPEs asignados para verificación rápida
                            const copesAsignadosMap = {};
                            if (response.copesAsignados) {
                                response.copesAsignados.forEach(cope => {
                                    copesAsignadosMap[cope.id] = true;
                                });
                            }
                            
                            // Crear un mapa de COPEs disponibles para evitar duplicados
                            const copesDisponiblesMap = {};
                            if (response.copesDisponibles) {
                                response.copesDisponibles.forEach(cope => {
                                    copesDisponiblesMap[cope.id] = cope;
                                });
                            }
                            
                            // Combinar COPEs asignados y disponibles sin duplicados
                            const todosLosCopes = [];
                            
                            // Agregar COPEs asignados
                            if (response.copesAsignados) {
                                response.copesAsignados.forEach(cope => {
                                    todosLosCopes.push(cope);
                                });
                            }
                            
                            // Agregar COPEs disponibles que no estén ya en asignados
                            if (response.copesDisponibles) {
                                response.copesDisponibles.forEach(cope => {
                                    if (!copesAsignadosMap[cope.id]) {
                                        todosLosCopes.push(cope);
                                    }
                                });
                            }
                            
                            // Crear checkboxes para todos los COPEs combinados
                            todosLosCopes.forEach(cope => {
                                const isChecked = copesAsignadosMap[cope.id] ? 'checked' : '';
                                const checkbox = `<div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="copes[]" value="${cope.id}" id="edit_cope${cope.id}" ${isChecked}>
                                    <label class="form-check-label" for="edit_cope${cope.id}">${cope.COPE}</label>
                                </div>`;
                                $('#edit-cope-checkboxes').append(checkbox);
                            });
                        } else {
                            console.warn('Error al cargar COPEs:', response.error || 'No se pudieron cargar los COPEs');
                            Toast.error('Error al cargar los COPEs. Por favor, intente nuevamente.');
                        }
                    },
                    error: function(xhr, status, error) {
                        hidePreloader(); // Ocultar preloader en caso de error
                        console.error('Error en la petición AJAX:', error);
                        console.log('Respuesta del servidor:', xhr.responseText);
                        Toast.error('Error al cargar los COPEs del usuario. Por favor, intente nuevamente.');
                    }
                });

                // Mostrar el modal de edición
                $('#modalEditarUsuario').modal('show');
            });

            // Inicializar popovers de Bootstrap para los COPEs asignados
            $('[data-toggle="popover"]').popover({
                trigger: 'hover',
                html: true
            });
        });
    </script>
</body>
</html>