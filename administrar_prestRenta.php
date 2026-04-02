<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Administración de Préstamos - MESS">
    <meta name="author" content="MESS">

    <title>ACTIVOS MESS - Administrar Préstamos / Renta</title>

    <!-- Fuentes y Estilos Base -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">    
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS (Versión compatible con Bootstrap 5) -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">        
        
        <?php include 'menu.php'; ?>
        
        <div id="content-wrapper" class="d-flex flex-column">            
            <div id="content">                
                
                <?php include 'encabezado.php'; ?>
                
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Administración de Préstamos / Renta</h1>
                        <a href="rentaPrestamo.php" class="btn btn-sm btn-success shadow-sm">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Préstamo
                        </a>
                    </div>

                    <!-- Tarjeta de la Tabla -->
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i>Historial de Préstamos</h6>                            
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tablaPrestamos">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <!-- <th>Folio</th> -->
                                            <th>Activo/Tipo</th>
                                            <th>Cliente</th>
                                            <th>Responsable</th>        
                                            <th>Periodo</th>
                                            <th>Renta Estimada</th>
                                            <th>Estatus</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small" id="tbodyPrestamos">                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; MESS 2026</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Modal Ver Detalles de Préstamo -->
    <div class="modal fade" id="modalDetallesPrestamo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Detalles del Préstamo #<span id="detFolio"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label text-muted small mb-0">Tipo</label>
                            <div class="h6 font-weight-bold text-dark" id="detTipo"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-0">Activo Prestado</label>
                            <div class="h6 font-weight-bold text-dark" id="detActivo"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-0">Cliente</label>
                            <div class="h6 font-weight-bold text-dark" id="detCliente"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-0">Responsable / Contacto</label>
                            <div class="h6 text-dark"><span id="detResponsable"> </span>  <small class="text-muted"><i class="fas fa-phone"></i> <span id="detContacto"></span></small></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-0">Estatus</label>
                            <div class="h6" id="detEstatus"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-0">Fecha de Entrega</label>
                            <div class="h6 text-dark" id="detEntrega"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-0">Periodo Oficial</label>
                            <div class="h6 text-dark"><span id="detInicio"></span> al <span id="detFin"></span></div>
                        </div>
                        <div class="col-md-4" style="display: none;">
                            <label class="form-label text-muted small mb-0">Fecha de Registro</label>
                            <div class="h6 text-dark" id="detRegistro"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-0">Costo Renta (Por Día)</label>
                            <div class="h6 text-success font-weight-bold"><span id="detMoneda"></span> $<span id="detRentaDia"></span></div>
                        </div>
                        <div class="col-md-4 border-left-primary shadow-sm rounded p-2 bg-white">
                            <label class="form-label text-primary small font-weight-bold mb-0">Total Estimado a Cobrar</label>
                            <div class="h5 text-dark font-weight-bold mb-0">
                                <span id="detTotalMoneda"></span> $<span id="detTotalRenta"></span>
                                <small class="text-muted" id="detDiasCalculados"></small>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Préstamo -->
    <div class="modal fade" id="modalEditarPrestamo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i> Editar Préstamo #<span id="editFolioTexto"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <form id="formEditarPrestamo">
                        <input type="hidden" id="editIdPrestamo" name="id_prestamo">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Responsable</label>
                                <input type="text" class="form-control" id="editResponsable" name="responsable" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Contacto del Responsable</label>
                                <input type="text" class="form-control" id="editContacto" name="contacto_responsable" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Fecha de Entrega</label>
                                <input type="date" class="form-control" id="editEntrega" name="fecha_entrega" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="editInicio" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Fecha de Fin</label>
                                <input type="date" class="form-control" id="editFin" name="fecha_fin" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Tipo de Movimiento</label>
                                <select class="form-select" id="editTipoMovimiento" name="tipo_movimiento" required>
                                    <option value="renta">Renta</option>
                                    <option value="prestamo">Préstamo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Moneda</label>
                                <select class="form-control" id="editMoneda" name="moneda" required>
                                    <option value="MXN">MXN - Pesos</option>
                                    <option value="USD">USD - Dólares</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Renta por Día</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="editRentaDia" name="renta_dia" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEdicion()" id="btnGuardarEdicion">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 1. Core de jQuery y Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>    
    
    <!-- 2. Plugins de Plantilla -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- 3. DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            cargarPrestamos();
        });

        function cargarPrestamos() {
            // Mostrar estado de carga
            $('#tbodyPrestamos').html('<tr><td colspan="7" py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Cargando datos...</td></tr>');

            $.ajax({
                url: 'acciones_renta.php',
                method: 'POST',
                data: { opcion: 'obtener_prestamos' },
                dataType: 'json',
                success: function(response) {
                    
                    // 1. Destruir DataTable si ya existe
                    if ($.fn.DataTable.isDataTable('#tablaPrestamos')) {
                        $('#tablaPrestamos').DataTable().destroy();
                    }

                    let html = '';                    
                    
                    if (response.status === 'success' && response.data.length > 0) {
                        response.data.forEach(function(p) {
                            let fechaInicio = new Date(p.fecha_inicio + 'T00:00:00');
                            let fechaFin = new Date(p.fecha_fin + 'T00:00:00');

                            let diferenciaMilisegundos = fechaFin.getTime() - fechaInicio.getTime();
                            let diasPrestamo = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24)) + 1;

                            let costoDiario = parseFloat(p.renta_dia) || 0;
                            let totalEstimado = diasPrestamo * costoDiario;
                            
                            // Diseño visual del estatus
                            let badgeEstatus = p.estatus == 1 
                                ? '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Prestado</span>' 
                                : '<span class="badge bg-success"><i class="fas fa-check"></i> Devuelto</span>';

                            //Diseño visual de info con estatus Devuelto
                            let classDevuelto = p.estatus == 0
                                ? 'text-decoration: line-through'
                                :'';
                            
                                //Diseño visual del tipo de movimiento
                            let badgeTipoMovimiento = p.tipo_movimiento === 'renta' 
                                ? '<span class="badge bg-black text-white"><i class="fas fa-file-invoice-dollar"></i> Renta</span>' 
                                : '<span class="badge bg-primary text-white"><i class="fas fa-hand-holding"></i> Préstamo</span>';

                            // Botón para devolver (Solo se muestra si el estatus es 1)
                            let btnDevolver = p.estatus == 1 
                                ? `<button class="btn btn-sm btn-outline-success" onclick="devolverActivo(${p.id}, ${p.id_activo})" title="Marcar como Devuelto"><i class="fas fa-undo"></i></button>` 
                                : '';

                            html += `<tr style="${classDevuelto}">                                
                                <td><strong>${p.activo_desc}</strong><br> ${badgeTipoMovimiento}</td>
                                <td>${p.cliente_nombre}</td>
                                <td>${p.responsable}<br><small class="text-muted"><i class="fas fa-phone"></i> ${p.contacto_responsable}</small></td>
                                <td><span class="text-success"><strong>${p.fecha_inicio}</strong></span> a <br> <span class="text-danger"><strong>${p.fecha_fin}</strong></span></td>
                                <td>${diasPrestamo} días: <strong>${p.moneda} ${totalEstimado.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                                <td>${badgeEstatus}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        ${btnDevolver}
                                        <button class="btn btn-sm btn-outline-primary" onclick="abrirModalEditar(${p.id})" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-info" title="Ver Detalles" onclick="verDetalles(${p.id})"><i class="fas fa-eye"></i></button>                                        
                                    </div>
                                </td>
                            </tr>`;
                        });
                    } else {
                        html = '<tr><td class="text-muted py-4">No hay préstamos registrados.</td></tr>';
                    }

                    // 2. Inyectar HTML
                    $('#tbodyPrestamos').html(html);

                    // 3. Inicializar DataTables
                    $('#tablaPrestamos').DataTable({
                        language: { url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" },
                        order: [[0, 'desc']], // Ordenar siempre por el folio más reciente
                        responsive: true
                    });
                },
                error: function() {
                    $('#tbodyPrestamos').html('<tr><td colspan="7" class="text-danger py-4">Error al conectar con el servidor.</td></tr>');
                    Swal.fire('Error', 'No se pudo cargar la tabla de préstamos.', 'error');
                }
            });
        }

        function devolverActivo(idPrestamo, idActivo) {
            Swal.fire({
                title: '¿Marcar como devuelto?',
                text: "El préstamo se cerrará y el activo volverá a estar disponible en el inventario.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1cc88a',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, devolver',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Petición AJAX para devolver
                    $.ajax({
                        url: 'acciones_renta.php',
                        method: 'POST',
                        data: { 
                            opcion: 'devolver_prestamo',
                            id_prestamo: idPrestamo,
                            id_activo: idActivo 
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('¡Devuelto!', 'El activo ha regresado al inventario.', 'success');
                                cargarPrestamos(); // Recargar la tabla automáticamente
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Hubo un problema de comunicación con el servidor.', 'error');
                        }
                    });

                }
            });
        }

        function verDetalles(idPrestamo) {            
            $.ajax({
                url: 'acciones_renta.php',
                method: 'POST',
                data: { opcion: 'obtener_detalle_prestamo', id_prestamo: idPrestamo },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let p = response.data;
                        
                        let fechaInicio = new Date(p.fecha_inicio + 'T00:00:00');
                        let fechaFin = new Date(p.fecha_fin + 'T00:00:00');

                        let diferenciaMilisegundos = fechaFin.getTime() - fechaInicio.getTime();
                        let diasPrestamo = Math.round(diferenciaMilisegundos / (1000 * 60 * 60 * 24)) + 1;

                        let costoDiario = parseFloat(p.renta_dia) || 0;
                        let totalEstimado = diasPrestamo * costoDiario;


                        // Llenar los datos en el modal
                        $('#detFolio').text(p.id);
                        $('#detActivo').text(p.activo_desc);
                        $('#detCliente').text(p.cliente_nombre);
                        $('#detResponsable').text(p.responsable);
                        $('#detContacto').text(p.contacto_responsable);
                        $('#detEntrega').text(p.fecha_entrega);
                        $('#detInicio').text(p.fecha_inicio);
                        $('#detFin').text(p.fecha_fin);
                        $('#detRegistro').text(p.fecha_registro);
                        $('#detMoneda').text(p.moneda);
                        $('#detRentaDia').text(parseFloat(p.renta_dia).toLocaleString('en-US', {minimumFractionDigits: 2}));
                        $('#detTipo').text(p.tipo_movimiento == 'renta' ? 'Renta' : 'Préstamo');

                        $('#detTotalMoneda').text(p.moneda);
                        $('#detTotalRenta').text(totalEstimado.toLocaleString('en-US', {minimumFractionDigits: 2}));
                        $('#detDiasCalculados').text(`(${diasPrestamo} días)`);
                        
                        // Estatus visual
                        let badge = p.estatus == 1 
                            ? '<span class="badge bg-warning text-dark">Prestado</span>' 
                            : '<span class="badge bg-success">Devuelto</span>';
                        $('#detEstatus').html(badge);

                        // Mostrar el modal
                        var myModal = new bootstrap.Modal(document.getElementById('modalDetallesPrestamo'));
                        myModal.show();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudieron obtener los detalles.', 'error');
                }
            });
        }

        // --- ABRIR MODAL Y LLENAR DATOS ---
        function abrirModalEditar(idPrestamo) {
            $.ajax({
                url: 'acciones_renta.php',
                method: 'POST',
                data: { opcion: 'obtener_detalle_prestamo', id_prestamo: idPrestamo }, // Reutilizamos tu consulta existente
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let p = response.data;
                        
                        // Llenar los inputs del formulario
                        $('#editIdPrestamo').val(p.id);
                        $('#editFolioTexto').text(p.id);
                        $('#editResponsable').val(p.responsable);
                        $('#editContacto').val(p.contacto_responsable);
                        $('#editEntrega').val(p.fecha_entrega);
                        $('#editInicio').val(p.fecha_inicio);
                        $('#editFin').val(p.fecha_fin);
                        $('#editMoneda').val(p.moneda);
                        $('#editRentaDia').val(p.renta_dia);
                        
                        // Validar fechas dinámicamente
                        $('#editInicio').on('change', function() {
                            $('#editFin').attr('min', $(this).val());
                        });

                        // Mostrar el modal
                        var myModal = new bootstrap.Modal(document.getElementById('modalEditarPrestamo'));
                        myModal.show();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudieron obtener los datos para editar.', 'error');
                }
            });
        }

        // --- GUARDAR LOS CAMBIOS ---
        function guardarEdicion() {
            const form = document.getElementById('formEditarPrestamo');

            // Validar campos vacíos
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            let formData = $(form).serialize();
            formData += '&opcion=editar_prestamo';

            let btn = $('#btnGuardarEdicion');
            let textoOriginal = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm"></span> Guardando...');
            btn.prop('disabled', true);

            $.ajax({
                url: 'acciones_renta.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Cerrar modal
                        $('#modalEditarPrestamo').modal('hide');
                        // Mensaje de éxito
                        Swal.fire({
                            title: '¡Actualizado!',
                            text: 'Los datos del préstamo se modificaron correctamente.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Recargar la tabla
                        cargarPrestamos();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Hubo un problema al guardar los cambios.', 'error');
                },
                complete: function() {
                    btn.html(textoOriginal);
                    btn.prop('disabled', false);
                }
            });
        }
    </script>
</body>
</html>