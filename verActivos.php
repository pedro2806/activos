<!DOCTYPE html>

<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE = edge">
    <meta name="viewport" content="width = device-width, initial-scale = 1, shrink-to-fit = no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ACTIVOS MESS</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">    

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php
            include 'menu.php';
        ?>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <?php
                    include 'encabezado.php';
                ?>
                <!-- Begin Page Content -->
                <div class="container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="text-primary fw-bold mb-0">Inventario de Activos</h3>
                    </div>
                    <a href="nuevoActivo" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Nuevo Activo
                    </a>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaActivos" name="tablaActivos">
                                <thead class=" table-primary">
                                    <tr class="text-secondary text-small">
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Descripción</th>
                                        <th scope="col">Marca / Modelo</th>
                                        <th scope="col">Ubicación</th>
                                        <th scope="col">Asignado</th>
                                        <th scope="col">Costo / Remanente</th>
                                        <th scope="col">Observaciones</th>
                                        <th scope="col" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>                                   
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer bg-white border-0 py-3">
                        </div>

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
                        <span>Copyright &copy; MESS 2025</span>
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
                    <h5 class="modal-title" id="exampleModalLabel">Cerrar sesión</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">¿Estas seguro?</div>
                <div class="modal-footer">
                    <button class="btn btn-info" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-danger" href="logout">Salir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript
    <script src = "vendor/jquery/jquery.min.js"></script>-->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js" defer="defer"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/funciones_activos.js"></script>
    <script src="../loginMaster/funcionesGlobales.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {            
            // Iniciar la carga de activos al cargar la página            
            verActivos();             
            
            // INICIALIZACIÓN DE DATATABLES            
            tablaEquiposDataTable = $('#tablaActivos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                paging: true,
                pageLength: 10,
                responsive: true,
                ordering: true,
                searching: true,
            });
        });

        function convertirTexto(e) {
            // Convertir a mayúsculas y quitar acentos
            e.value = e.value
            .toUpperCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
        }

        function getCookie(name) {
            let value = "; " + document.cookie;
            let parts = value.split("; " + name + "=");
            if (parts.length === 2) return parts.pop().split(";").shift();
        }

        async function verificarAcceso() {
            // 1. Agregamos await para esperar la respuesta
            const respuesta = await validaOpciones('activos', 'editarActivo');
            
            // 2. Accedemos a la estructura correcta según tu PHP: respuesta.data[0].cuantos
            const cuantos = (respuesta && respuesta.status === 'success') 
                            ? parseInt(respuesta.data[0].cuantos) 
                            : 0;
                                        
            if (cuantos <= 0) {            
                return "NoEdita";
            }else {
                return "Edita";
            }
        }


    </script>
</body>

</html>
