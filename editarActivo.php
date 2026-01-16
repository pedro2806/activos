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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-primary fw-bold"><i class="bi bi-pencil-square"></i> Editar Activo</h3>
                        <button onclick="history.back()" class="btn btn-outline-secondary btn-sm">Cancelar</button>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            
                            <form id="formEditarActivo">
                                <input type="hidden" id="editId" name="id">

                                <h6 class="text-muted text-uppercase mb-3 small fw-bold border-bottom pb-2">Clasificación</h6>
                                <div class="row mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label">Tipo de Activo</label>
                                        <select class="form-select" id="editTipoActivo" name="id_tipo_activo" required>
                                            <option value="1">EQ COMPUTO</option>
                                            <option value="2">MOBILIARIO y EQ DE OFICINA</option>
                                            <option value="3">MAQUINAS Y EQUIPOS</option>
                                            <option value="4">HERRAMIENTAS GENERALES</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="editEsAccesorio" name="es_accesorio">
                                            <label class="form-check-label" for="editEsAccesorio">¿Es un accesorio?</label>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-muted text-uppercase mb-3 mt-4 small fw-bold border-bottom pb-2">Datos del Equipo</h6>
                                <div class="row mb-3">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Descripción</label>
                                        <input type="text" class="form-control" id="editDescripcion" name="descripcion" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Marca</label>
                                        <input type="text" class="form-control" id="editMarca" name="marca">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Modelo</label>
                                        <input type="text" class="form-control" id="editModelo" name="modelo">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">No. Serie</label>
                                        <input type="text" class="form-control" id="editSerie" name="no_serie">
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <label class="form-label">ID Interno</label>
                                        <input type="text" class="form-control" id="editIdInterno" name="id_interno">
                                    </div>
                                </div>

                                <div id="seccionComputoEdit" class="d-none bg-light p-3 rounded mb-4 border">
                                    <h6 class="text-primary small mb-3"><i class="bi bi-cpu"></i> Especificaciones IT</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Procesador / CPU</label>
                                            <input type="text" class="form-control" id="editCpu" name="cpu_info">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Monitor / Pantalla</label>
                                            <input type="text" class="form-control" id="editMonitor" name="monitor_info">
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-muted text-uppercase mb-3 mt-4 small fw-bold border-bottom pb-2">Ubicación</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nave / Planta</label>
                                        <select class="form-select" id="editNave" name="id_nave">
                                            <option value="1">SFG</option>
                                            <option value="2">EL MARQUES</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Usuario Responsable</label>
                                        <select class="form-select" id="editUsuario" name="id_usuario">
                                            <option value="1">OMAR MORA</option>
                                            <option value="2">OTRO USUARIO</option>
                                        </select>
                                    </div>
                                </div>

                                <h6 class="text-muted text-uppercase mb-3 mt-4 small fw-bold border-bottom pb-2">Financiero</h6>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">MOI</label>
                                        <input type="number" step="0.01" class="form-control calc-edit" id="editMoi" name="moi">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Depreciación</label>
                                        <input type="number" step="0.01" class="form-control calc-edit" id="editDepreciacion" name="depreciacion">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Remanente</label>
                                        <input type="number" step="0.01" class="form-control bg-light" id="editRemanente" name="remanente" readonly>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="editObservaciones" name="observaciones" rows="3"></textarea>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary px-5">Guardar Cambios</button>
                                </div>

                            </form>
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
        
            // 1. OBTENER ID DE LA URL Y CARGAR DATOS
            const urlParams = new URLSearchParams(window.location.search);
            const idActivo = urlParams.get('id');

            if(idActivo) {
                cargarDatosParaEditar(idActivo);
            } else {
                Swal.fire('Error', 'No se especificó un ID de activo', 'error');
            }

            // 2. LÓGICA VISUAL (Mostrar/Ocultar Cómputo)
            $('#editTipoActivo').change(function() {
                const texto = $("#editTipoActivo option:selected").text();
                if(texto.includes('COMPUTO')) {
                    $('#seccionComputoEdit').removeClass('d-none');
                } else {
                    $('#seccionComputoEdit').addClass('d-none');
                }
            });

            // 3. CÁLCULO AUTOMÁTICO
            $('.calc-edit').on('input', function() {
                let moi = parseFloat($('#editMoi').val()) || 0;
                let dep = parseFloat($('#editDepreciacion').val()) || 0;
                $('#editRemanente').val((moi - dep).toFixed(2));
            });

            // 4. GUARDAR CAMBIOS (SUBMIT)
            $('#formEditarActivo').on('submit', function(e) {
                e.preventDefault();
                
                // Recolectar datos incluyendo el checkbox manualmente si es necesario
                let formData = $(this).serialize(); 
                // Agregar opción para el backend
                formData += '&opcion=guardarEdicion';

                $.ajax({
                    url: 'acciones_activos.php',
                    method: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        if(response.status === 'success') {
                            Swal.fire({
                                title: '¡Actualizado!',
                                text: 'El activo se modificó correctamente',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = 'verActivos'; // Volver al listado
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Fallo al guardar los cambios', 'error');
                    }
                });
            });
        });
        document.getElementById('selectTipoActivo').addEventListener('change', function() {
            var techDiv = document.getElementById('techFields');
            // Asumiendo que el texto de la opción seleccionada contiene 'COMPUTO'
            var textoSeleccionado = this.options[this.selectedIndex].text;
            
            if(textoSeleccionado.includes('COMPUTO')) {
                techDiv.classList.remove('d-none');
            } else {
                techDiv.classList.add('d-none');
            }
                
            //cargar empleados
            //empleadoSolicita('#slcRespoonsable');
            

            // Inicializa Select2 en el campo de responsable
            $('#slcRespoonsable').select2({            
                placeholder: "Seleccione...",
                width: '100%'
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

    </script>
</body>

</html>
