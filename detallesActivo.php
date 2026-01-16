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
                            <div class="card shadow-lg border-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Regresar
                                </a>
                                <h4 class="text-primary fw-bold mb-2">Ficha Técnica del Activo</h4>
                                <!--<button class="btn btn-primary" onclick="window.print()">
                                    <i class="bi bi-printer"></i> Imprimir
                                </button>-->
                            </div>
                            <div class="card-header bg-white p-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div id="detalleTipoBadge" class="mb-2">
                                            <h3><span class="badge bg-secondary">Cargando...</span></h3>
                                        </div>
                                        <h2 class="fw-bold text-dark mb-2" id="detalleDescripcion">...</h2>
                                        <small class="text-muted">Descripción del Activo</small>
                                    </div>
                                    <div class="text-end bg-light p-3 rounded border">
                                        <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">ID Interno / Etiqueta</small>
                                        <div class="fs-4 fw-bold text-dark" id="detalleIdInterno">...</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-2">                                
                                <h6 class="text-secondary text-uppercase border-bottom pb-2 mb-2">1. Especificaciones Generales</h6>
                                <div class="row mb-2">    
                                    <div class="col-md-8">
                                        <div class="row mb-2">
                                            <div class="col-md-4 mb-2">
                                                <label class="fw-bold text-muted small">Marca</label>
                                                <div class="fs-5" id="detalleMarca">...</div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="fw-bold text-muted small">Modelo</label>
                                                <div class="fs-5" id="detalleModelo">...</div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="fw-bold text-muted small">No. de Serie</label>
                                                <div class="fs-5 font-monospace" id="detalleNoSerie">...</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="seccionComputo" class="d-none bg-primary bg-opacity-10 p-3 rounded mb-2 border border-primary">
                                            <h6 class="text-primary text-uppercase mb-2"><i class="bi bi-cpu"></i> Especificaciones IT</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="fw-bold text-primary small">Procesador / CPU</label>
                                                    <div class="fw-bold text-dark" id="detalleCpu"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="fw-bold text-primary small">Monitor / Pantalla</label>
                                                    <div class="fw-bold text-dark" id="detalleMonitor"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                <h6 class="text-secondary text-uppercase border-bottom pb-2 mb-2">2. Ubicación y Resguardo</h6>
                                <div class="row mb-2">
                                    <div class="col-md-6 mb-2">
                                        <label class="fw-bold text-muted small">Usuario Responsable</label>
                                        <div class="d-flex align-items-center mt-1">
                                            <i class="bi bi-person-circle fs-4 text-secondary me-2"></i>
                                            <div class="fs-5" id="detalleUsuario">...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="fw-bold text-muted small">Región</label>
                                        <div class="fs-5" id="detalleRegion">...</div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="fw-bold text-muted small">Nave / Planta</label>
                                        <div class="fs-5" id="detalleNave">...</div>
                                    </div>
                                </div>

                                <h6 class="text-secondary text-uppercase border-bottom pb-2 mb-4">3. Fotos</h6>
                                <div class="row mb-4">
                                    <div id="detalleFotos" class="d-flex flex-wrap gap-3">
                                        <!-- Fotos se cargarán aquí -->
                                    </div>
                                </div>

                                <h6 class="text-secondary text-uppercase border-bottom pb-2 mb-4">4. Datos Financieros</h6>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded border">
                                            <label class="fw-bold text-muted small d-block">MOI (Inversión)</label>
                                            <span class="fs-5" id="detalleMoi">$ 0.00</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded border">
                                            <label class="fw-bold text-muted small d-block">Depreciación Acum.</label>
                                            <span class="fs-5 text-danger" id="detalleDepreciacion">$ 0.00</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded border border-success bg-opacity-25" style="background-color: #e8f5e9;">
                                            <label class="fw-bold text-success small d-block">Remanente (Valor Actual)</label>
                                            <span class="fs-4 fw-bold text-success" id="detalleRemanente">$ 0.00</span>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-secondary text-uppercase border-bottom pb-2 mb-2">Observaciones</h6>
                                <div class="bg-light p-3 rounded fst-italic text-muted">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    <span id="detalleObservaciones">Sin comentarios registrados.</span>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="js/funciones_activos.js"></script>

    <script type="text/javascript">
        
        $(document).ready(function() {            
            // Iniciar la carga de detalles del activo
            cargarDetalleActivo();
            obtenerfotosActivo();
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

        function obtenerfotosActivo() {
            var urlParams = new URLSearchParams(window.location.search);
            var idActivo = urlParams.get('id');

            $.ajax({
                url: 'acciones_activos.php',
                type: 'POST',
                data: {
                    opcion: 'obtener_fotos',
                    id_activo: idActivo
                },
                dataType: 'json',
                success: function(response) {
                    //if (response.success) {
                        let fotosContainer = $('#detalleFotos');
                        fotosContainer.empty(); // Limpiar contenedor

                        if (response.fotos.length > 0) {
                            response.fotos.forEach(function(foto) {
                                let fotoUrl = foto.ruta_foto;
                                let fotoElement = `
                                    <div class="card" style="width: 150px;">
                                        <img src="${fotoUrl}" class="card-img-top" alt="Foto del Activo">
                                    </div>
                                `;
                                fotosContainer.append(fotoElement);
                            });
                        } else {
                            fotosContainer.append('<p class="text-muted">No hay fotos disponibles para este activo.</p>');
                        }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud AJAX:', error);
                }
            });
        }

    </script>
</body>

</html>
