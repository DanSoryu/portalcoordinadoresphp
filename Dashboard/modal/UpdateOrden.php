<?php

    date_default_timezone_set('America/Monterrey');

    require_once("../../Login/controller/Class_Login.php");
    require_once("../controller/ClassOps.php");
    require_once("cargar.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    $Usuario = $_SESSION['Usuario'];
    $Nombres = $_SESSION['Nombres'];
    $idUsuario = $_SESSION['idUsuarios'];

    $idtecnico_instalaciones_coordiapp = $_GET['idtecnico_instalaciones_coordiapp'];

    if (empty($Usuario)) {
        header("location: ../../Login/modal/poweroff.php");
    }

    // Generar token CSRF si no existe
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    $modal = new Ops();
    $Filas = $modal->GetOrdenUpdate($idtecnico_instalaciones_coordiapp);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ERP - OPERACIONES</title>

    <!-- Custom fonts for this template -->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>
    <style>
        .carousel-item{
            width: 500px;
            height: 600px;
            background-position: center center;
            background-repeat: no-repeat;
            background-color: #000;
            margin: auto;
        }

        .image-cover{
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .image-contain{
            -webkit-background-size: contain;
            -moz-background-size: contain;
            -o-background-size: contain;
            background-size: contain;
        }

        .image-custom1{
            background-size: 100%;
        }

        @media (max-width: 600px) {
            .carousel-item {
                height: 200px;
            }
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
                <div class="sidebar-brand-text mx-3">OPERACIONES</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
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
                <a class="nav-link" href="../OrdenesCoordiapp.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION COORDIAPP COMPLETAS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../OrdenesIncompletas.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION COORDIAPP INCOMPLETAS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../OrdenesTac.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION TAC</span>
                </a>
            </li>

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

                    <H1>Actualizar Orden</H1>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $Nombres;?></span>
                                <img class="img-profile rounded-circle"
                                    src="../img/undraw_profile.svg">
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

                    <form action="../controller/UpdateOrdenFlexible.php" method="post" accept-charset="utf-8" id="FormQueryUnidades">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="idtecnico_instalaciones_coordiapp" value="<?php echo htmlspecialchars($idtecnico_instalaciones_coordiapp, ENT_QUOTES, 'UTF-8'); ?>">
                        
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-lg-12">     
                                <?php
                                    if(!empty($Filas)){
                                        foreach($Filas as $row){
                                            echo '
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Folio Pisa</span>
                                                </div>
                                                <input type="text" class="form-control" name="Folio_Pisa" required="" value="' . htmlspecialchars($row['Folio_Pisa'], ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Telefono</span>
                                                </div>
                                                <input type="text" class="form-control" name="Telefono" required="" value="' . htmlspecialchars($row['Telefono'], ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Terminal</span>
                                                </div>
                                                <input type="text" class="form-control" name="Terminal" value="' . htmlspecialchars($row['Terminal'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Puerto</span>
                                                </div>
                                                <input type="text" class="form-control" name="Puerto" value="' . htmlspecialchars($row['Puerto'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Metraje</span>
                                                </div>
                                                <input type="number" class="form-control" name="Metraje" value="' . htmlspecialchars($row['Metraje'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Distrito</span>
                                                </div>
                                                <input type="text" class="form-control" name="Distrito" value="' . htmlspecialchars($row['Distrito'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Tecnologia</span>
                                                </div>
                                                <select class="form-control" name="Tecnologia">
                                                    <option value="">Seleccionar tecnología</option>
                                                    <option value="FIBRA OPTICA" ' . (($row['Tecnologia'] ?? '') === 'FIBRA OPTICA' ? 'selected' : '') . '>FIBRA OPTICA</option>
                                                    <option value="COBRE" ' . (($row['Tecnologia'] ?? '') === 'COBRE' ? 'selected' : '') . '>COBRE</option>
                                                    <option value="ADSL" ' . (($row['Tecnologia'] ?? '') === 'ADSL' ? 'selected' : '') . '>ADSL</option>
                                                </select>
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Tipo Tarea</span>
                                                </div>
                                                <input type="text" class="form-control" name="Tipo_Tarea" value="' . htmlspecialchars($row['Tipo_Tarea'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Tipo Instalacion</span>
                                                </div>
                                                <select class="form-control" name="Tipo_Instalacion">
                                                    <option value="">Seleccionar tipo</option>
                                                    <option value="AREA" ' . (($row['Tipo_Instalacion'] ?? '') === 'AREA' ? 'selected' : '') . '>AREA</option>
                                                    <option value="SUBTERRANEA" ' . (($row['Tipo_Instalacion'] ?? '') === 'SUBTERRANEA' ? 'selected' : '') . '>SUBTERRANEA</option>
                                                </select>
                                            </div>

                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Cliente Titular</span>
                                                </div>
                                                <input type="text" class="form-control" name="Cliente_Titular" value="' . htmlspecialchars($row['Cliente_Titular'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Dirección Cliente</span>
                                                </div>
                                                <input type="text" class="form-control" name="Direccion_Cliente" value="' . htmlspecialchars($row['Direccion_Cliente'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Teléfono Cliente</span>
                                                </div>
                                                <input type="text" class="form-control" name="Telefono_Cliente" value="' . htmlspecialchars($row['Telefono_Cliente'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>

                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Paso Registro</span>
                                                </div>
                                                <select class="form-control" name="Step_Registro">
                                                    <option value="">Seleccionar paso</option>
                                                    <option value="1" ' . (($row['Step_Registro'] ?? '') == '1' ? 'selected' : '') . '>1</option>
                                                    <option value="2" ' . (($row['Step_Registro'] ?? '') == '2' ? 'selected' : '') . '>2</option>
                                                    <option value="3" ' . (($row['Step_Registro'] ?? '') == '3' ? 'selected' : '') . '>3</option>
                                                    <option value="4" ' . (($row['Step_Registro'] ?? '') == '4' ? 'selected' : '') . '>4</option>
                                                    <option value="5" ' . (($row['Step_Registro'] ?? '') == '5' ? 'selected' : '') . '>5</option>
                                                </select>
                                            </div>
                                            
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Estatus Orden</span>
                                                </div>
                                                <select class="form-control" name="Estatus_Orden">
                                                    <option value="">Seleccionar estatus</option>
                                                    <option value="COMPLETADA" ' . (($row['Estatus_Orden'] ?? '') === 'COMPLETADA' ? 'selected' : '') . '>COMPLETADA</option>
                                                    <option value="INCOMPLETA" ' . (($row['Estatus_Orden'] ?? '') === 'INCOMPLETA' ? 'selected' : '') . '>INCOMPLETA</option>
                                                </select>
                                            </div>';

                                            echo '<div class="input-group mb-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Cope</span>
                                                    </div>
                                                    <select class="form-control selectpicker" name="FK_Cope" data-live-search="true">
                                                        <option value="">Selecciona Cope</option>';
                                                        
                                                        // Si existe FK_Cope, mostrar la opción actual primero
                                                        if (!empty($row['FK_Cope'])) {
                                                            echo '<option value="' . htmlspecialchars($row['FK_Cope'], ENT_QUOTES, 'UTF-8') . '" selected>' . htmlspecialchars($row['COPE'] ?? 'Cope ID: ' . $row['FK_Cope'], ENT_QUOTES, 'UTF-8') . '</option>';
                                                        }
                                                        
                                                        // Siempre mostrar todas las opciones de ListCopes
                                                        ListCopes();
                                            echo    '</select>
                                            </div>';

                                            echo '
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">ONT</span>
                                                </div>
                                                <input type="text" class="form-control" name="Ont" value="' . htmlspecialchars($row['Ont'] ?? '', ENT_QUOTES, 'UTF-8') . '">
                                            </div>';
                                            
                                        }
                                    } else {
                                        echo '<div class="alert alert-warning">No se encontraron datos para esta orden.</div>';
                                    }
                                ?>
                            </div>
                        </div>
                        <button class="btn btn-success form-control">Modificar Orden</button>
                    </form>

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
                    <a class="btn btn-primary" href="../../Login/modal/poweroff.php">SALIR</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
</body>

</html>