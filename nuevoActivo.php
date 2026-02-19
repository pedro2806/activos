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
                    <!-- Content Row -->
                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <form id="formActivos" action="/ruta-para-guardar" method="POST">
                                <div class="card shadow-sm mb-2">
                                    <div class="card-body">
                                        <h6 class="section-title">1. Clasificación</h6>
                                        
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-5">
                                                <label class="form-label fw-bold">Tipo de Activo <span class="text-danger">*</span></label>
                                                <select class="form-select" id="selectTipoActivo" name="selectTipoActivo" required>
                                                    <option value="">Seleccione tipo...</option>
                                                    <option value="1">EQ COMPUTO</option>
                                                    <option value="2">MOBILIARIO y EQ DE OFICINA</option>
                                                    <option value="3">MAQUINAS Y EQUIPOS</option>
                                                    <option value="4">HERRAMIENTAS GENERALES</option>
                                                </select>
                                            </div>

                                            <div class="col-md-5">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" id="checkEsAccesorio" name="es_accesorio" value="0" onchange="conjunto_computadora()">
                                                    <label class="form-check-label" for="checkEsAccesorio">
                                                        ¿Va en conjunto con computadora?
                                                        <small class="d-block text-muted" style="font-size: 0.75rem;">(Monitor extra, Docking, Teclado, etc.)</small>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <label class="form-label">Cantidad</label>
                                                <input type="number" class="form-control" id="cantidad" name="cantidad" value="1" min="1">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-label">Descripción del Activo <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Ej. LAPTOP, ESCRITORIO EN L, MONITOR 24 PULGADAS" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm mb-2">
                                    <div class="card-body">
                                        <h6 class="section-title">2. Datos de Identificación</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Marca</label>
                                                <input type="text" class="form-control" name="marca" placeholder="Ej. Dell">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Modelo</label>
                                                <input type="text" class="form-control" name="modelo" placeholder="Ej. INSPIRON 54">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">No. de Serie</label>
                                                <input type="text" class="form-control" name="no_serie" placeholder="Ej. 8157SZ1">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">ID Interno</label>
                                                <input type="text" class="form-control" name="id_interno" placeholder="Etiqueta Empresa">
                                            </div>
                                        </div>

                                        <div id="techFields" class="p-3 bg-tech rounded d-none">
                                            <h6 class="text-primary mb-3"><i class="bi bi-cpu"></i> Especificaciones Técnicas equipo computo en conjunto</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Procesador / CPU</label>
                                                    <input type="text" class="form-control" name="cpu_info" placeholder="Ej. Intel i5 11va Gen, 16GB RAM">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Detalles de Monitor / Pantalla</label>
                                                    <input type="text" class="form-control" name="monitor_info" placeholder="Ej. 14 Pulgadas FHD">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm mb-2">
                                    <div class="card-body">
                                        <h6 class="section-title">3. Ubicación y Asignación</h6>
                                        <div class="row">
                                            <div style="display: none;">
                                                <label class="form-label">Región</label>
                                                <select class="form-select" id="selectRegion" name="selectRegion">
                                                    <option value="">Seleccione...</option>                                                                                                        
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Nave / Planta</label>
                                                <select class="form-select" id="selectNave" name="selectNave">
                                                    <option selected disabled>Seleccione Región primero</option>
                                                    <option value="1">SFG</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Ubicación</label>
                                                <input type="text" class="form-control" name="ubicacion" id="ubicacion" placeholder="Ej. Estante, Bodega...">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Usuario Responsable</label>
                                                <select class="form-select" id="slcRespoonsable" name="slcRespoonsable">
                                                    <option value="">Seleccione...</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm mb-2">
                                    <div class="card-body">
                                        <h6 class="section-title">4. Información Financiera</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label">MOI (Inversión)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control calc-financial" name="moi" id="inputMoi" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Costo Actual</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" onblur="calcularDepreciacion()" name="costo" id="costo" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Depreciación Acum.</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control calc-financial" onblur="calcularRemanente()" name="depreciacion" id="inputDepreciacion" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Remanente</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control bg-light" name="remanente" id="inputRemanente" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-sm mb-2">
                                    <div class="card-body">
                                        <h6 class="section-title">5. Fotos del Activo</h6>
                                        <div class="mb-3">
                                            <input class="form-control" type="file" name="fotos[]" accept="image/*" multiple>
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">Puedes subir varias imágenes a la vez.</small>
                                        </div>
                                    </div>
                                </div>


                                <div class="card shadow-sm mb-2">
                                    <div class="card-body">
                                        <label class="form-label fw-bold">Observaciones / Comentarios Adicionales</label>
                                        <textarea class="form-control" name="observaciones" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                                    <button type="button" class="btn btn-secondary me-md-2">Cancelar</button>
                                    <button type="button" class="btn btn-primary px-5" onclick="guardarActivo()">Guardar Registro</button>
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
    <script src="../loginMaster/funcionesGlobales.js"></script>

    <script type="text/javascript">
        
    function calcularRemanente() {
        var costo = parseFloat(document.getElementById('costo').value) || 0;
        var depreciacion = parseFloat(document.getElementById('inputDepreciacion').value) || 0;
        var remanente = costo - depreciacion;
        document.getElementById('inputRemanente').value = remanente.toFixed(2);
        calcularDepreciacion();
    }

    function calcularDepreciacion() {
        var moi = parseFloat(document.getElementById('inputMoi').value) || 0;
        var costo = parseFloat(document.getElementById('costo').value) || 0;
        var Depreciacion = moi - costo;
        document.getElementById('inputDepreciacion').value = Depreciacion.toFixed(2);
        calcularRemanente();
    }

    function conjunto_computadora() {
        var checkBox = document.getElementById('checkEsAccesorio');
        if (checkBox.checked == true){
            // El activo es un accesorio
            $("#techFields").removeClass("d-none");
        } else {
            // El activo no es un accesorio
            $("#techFields").addClass("d-none");
        }
    }

    async function verificarAcceso() {
        // 1. Agregamos await para esperar la respuesta
        const respuesta = await validaOpciones('activos', 'agregarActivoNuevo');
        
        // 2. Accedemos a la estructura correcta según tu PHP: respuesta.data[0].cuantos
        const cuantos = (respuesta && respuesta.status === 'success') 
                        ? parseInt(respuesta.data[0].cuantos) 
                        : 0;

        if (cuantos <= 0) {            
            window.location.href = "verActivos.php";
        }else {
            
        }
    }

    $(document).ready(function() {        
        verificarAcceso();
        conjunto_computadora();
        
        getEmpleados('#slcRespoonsable');
        getRegiones('#selectRegion');
    });

    </script>
</body>

</html>
