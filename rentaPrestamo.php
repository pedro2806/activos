<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard Activos">
    <meta name="author" content="MESS">

    <title>ACTIVOS MESS - Dashboard</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">    

    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">        
        <?php include 'menu.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">            
            <div id="content">                
                <?php include 'encabezado.php'; ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-2">
                        <h1 class="h3 mb-0 text-gray-800">Prestamos/Rentas de activos</h1>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-1">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="m-0 font-weight-bold text-primary">Nuevo préstamo</h6>
                                <button type="button" class="btn btn-primary btn-sm" onclick="guardarPrestamo()" id="btnGuardarPrestamo">
                                    Registrar préstamo
                                </button>
                            </div>                            
                        </div>
                        <div class="card-body">
                            <form id="prestamoForm" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="activoSelect" class="form-label">Tipo de movimiento</label>
                                        <select class="form-select" id="tipoMovimiento" name="tipo_movimiento" required>
                                            <option value="">Selecciona...</option>
                                            <option value="prestamo" selected>Préstamo</option>
                                            <option value="renta">Renta</option>                                        
                                        </select>
                                    </div>                                    
                                    <div class="col-md-6">
                                        <label for="activoSelect" class="form-label">Selecciona el activo a prestar</label>
                                        <select class="form-select" id="activoSelect" name="activo_id" required>
                                            <option value="">Selecciona...</option>                                        
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ovInput" class="form-label">Orden de venta</label>
                                        <input type="text" class="form-control" id="ovInput" name="orden_venta" placeholder="Ej. MESS-OV-0000-2026">
                                    </div>

                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="destinatarioInput" class="form-label">Cliente préstamo</label>
                                        <select name="clienteSelect" id="clienteSelect" class="form-select" required>
                                            <option value="">Selecciona...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="fechaEntregaInput" class="form-label">Fecha de entrega</label>
                                        <input type="date" class="form-control" id="fechaEntregaInput" name="fecha_entrega" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="responsableInput" class="form-label">Nombre responsable</label>
                                        <input type="text" class="form-control" id="responsableInput" name="responsable" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="contactoResponsable">Contacto del responsable</label>
                                        <input type="text" class="form-control" id="contactoResponsable" name="contacto_responsable" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="fechaInicioInput" class="form-label">Fecha de inicio</label>
                                        <input type="date" class="form-control" id="fechaInicioInput" name="fecha_inicio" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="fechaFinInput" class="form-label">Fecha de fin</label>
                                        <input type="date" class="form-control" id="fechaFinInput" name="fecha_fin" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="monedaInput" class="form-label">Moneda</label>
                                        <select class="form-select" id="monedaInput" name="moneda" required>
                                            <option value="MXN" selected>MXN - Pesos</option>
                                            <option value="USD">USD - Dólares</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="rentaDiaInput" class="form-label">Renta por Día</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" class="form-control" id="rentaDiaInput" name="renta_dia" placeholder="0.00" required>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <hr class="mt-4 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="font-weight-bold text-primary m-0"><i class="fas fa-boxes mr-2"></i>Items Adicionales (Opcional)</h6>
                                    <button type="button" class="btn btn-sm btn-success shadow-sm" onclick="agregarItemAdicional()">
                                        <i class="fas fa-plus"></i> Agregar Item
                                    </button>
                                </div>

                                <div id="rentaAdicionales">
                                    <div class="row mb-3 align-items-end" id="fila_adicional_1">
                                        <div class="col-md-4">
                                            <label class="form-label text-muted small">Item adicional 1</label> 
                                            <select name="item_adicional_1" id="item_adicional_1" class="form-select">
                                                <option value="">Seleccionar item</option>                                                
                                                </select>
                                        </div> 
                                        <div class="col-md-1">
                                            <label class="form-label text-muted small">Cantidad</label>
                                            <input type="number" min="0" class="form-control" name="cantidad_adicional_1" placeholder="0">
                                        </div>                                        
                                        <div class="col-md-2 text-center">
                                            <label class="form-label text-muted small d-block mb-1">Evidencia</label>
                                            <input type="file" class="d-none" id="imagen_adicional_1" name="imagen_adicional_1" accept="image/*" onchange="cambiarEstadoImagen(this, 1)">
                                            <label for="imagen_adicional_1" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm w-100" style="cursor: pointer;">
                                                <i class="fas fa-camera"></i> <span id="img_text_1">Subir foto</span>
                                            </label>                                            
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label text-muted small">Comentarios</label>
                                            <textarea class="form-control" name="comentarios_adicional_1" rows="1" placeholder="Ingrese comentarios"></textarea>        
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="total_items_adicionales" id="totalItemsInput" value="1">

                                <button type="button" class="btn btn-primary" onclick="guardarPrestamo()" id="btnGuardarPrestamo">
                                    Registrar préstamo
                                </button>
                            </form>
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

    <!-- 1. Core de jQuery y Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>    
    
    <!-- 2. Plugins -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- 3. DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script type="text/javascript">
                

        $(document).ready(function() {
            llenaSelectActivos('activoSelect');
            llenaSelectActivos('item_adicional_1');
            llenaSelectClientes();
        });

        // Cuando cambia la fecha de inicio, actualizamos el límite mínimo de la fecha fin
        $('#fechaInicioInput').on('change', function() {
            let fechaInicio = $(this).val();
            $('#fechaFinInput').attr('min', fechaInicio);
        });

        // --- FUNCIÓN PARA GUARDAR EL PRÉSTAMO ---
        function guardarPrestamo() {
            const form = document.getElementById('prestamoForm');

            if (!form.checkValidity()) {
                form.reportValidity(); 
                return; 
            }

            //Usamos FormData para poder mandar las imágenes ---
            let formData = new FormData(form);
            formData.append('opcion', 'registrar_prestamo');

            let btn = $('#btnGuardarPrestamo');
            let textoOriginal = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm"></span> Guardando...');
            btn.prop('disabled', true);

            $.ajax({
                url: 'acciones_renta.php',
                method: 'POST',
                data: formData,
                contentType: false, // OBLIGATORIO PARA ARCHIVOS
                processData: false, // OBLIGATORIO PARA ARCHIVOS
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'El préstamo se registró correctamente',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        form.reset(); 
                        $('#activoSelect, #clienteSelect').val('').trigger('change'); 
                        
                        // Limpiamos también las filas dinámicas extras que se hayan agregado
                        $('.fila-dinamica').remove();
                        contadorItems = 1;
                        $('#totalItemsInput').val(contadorItems);

                        window.location.href = 'administrar_prestRenta.php';
                        
                    } else {
                        Swal.fire('Error', response.message || 'Error al guardar en base de datos.', 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error', 'Hubo un problema de comunicación con el servidor.', 'error');
                },
                complete: function() {
                    btn.html(textoOriginal);
                    btn.prop('disabled', false);
                }
            });
        }

        function llenaSelectActivos(selectId) {
            $.ajax({
                url: 'acciones_renta.php',
                method: 'POST',
                data: { opcion: 'obtener_activos_disponibles' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var activos = response.data;
                        var $activoSelect = $('#' + selectId);
                        var $selectitemAdicional = $('#ItemAdicionalInput');
                        $activoSelect.empty().append('<option value="">Selecciona...</option>');
                        activos.forEach(function(activo) {
                            $activoSelect.append('<option value="' + activo.id + '">' + activo.descripcion + ' - Ma: ' + activo.marca + '  - Mod: ' + activo.modelo + '   (NS:' + activo.no_serie + ')</option>');
                            $selectitemAdicional.append('<option value="' + activo.id + '">' + activo.descripcion + ' - Ma: ' + activo.marca + '  - Mod: ' + activo.modelo + '   (NS:' + activo.no_serie + ')</option>');
                        });                        
                        $activoSelect.select2({
                            theme: 'bootstrap-5'
                        });
                        $selectitemAdicional.select2({
                            theme: 'bootstrap-5'
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudieron cargar los activos.', 'error');
                }
            });
        }

        function llenaSelectClientes() {
            $.ajax({
                url: 'acciones_renta.php',
                method: 'POST',
                data: { opcion: 'obtener_clientes' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var clientes = response.data;
                        var $clienteSelect = $('#clienteSelect');
                        $clienteSelect.empty().append('<option value="">Selecciona...</option>');
                        clientes.forEach(function(cliente) {
                            $clienteSelect.append('<option value="' + cliente.id + '">' + cliente.cliente_largo + '</option>');
                        });
                        $clienteSelect.select2({
                            theme: 'bootstrap-5'
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'No se pudieron cargar los clientes.', 'error');
                }
            });
        }

        // --- LÓGICA DE ITEMS DINÁMICOS ---
    let contadorItems = 1;

    function agregarItemAdicional() {
        contadorItems++;
        
        // Actualizamos el input oculto para que PHP sepa cuántos procesar
        $('#totalItemsInput').val(contadorItems);

        llenaSelectActivos('item_adicional_' + contadorItems); // Llenamos el nuevo select con los activos disponibles

        let nuevaFila = `
            <div class="row mb-3 align-items-end fila-dinamica" id="fila_adicional_${contadorItems}">
                <div class="col-md-4">
                    <label class="form-label text-muted small">Item adicional ${contadorItems}</label> 
                    <select name="item_adicional_${contadorItems}" id="item_adicional_${contadorItems}" class="form-select">
                        <option value="">Seleccionar item</option>
                        </select>
                </div> 
                <div class="col-md-1">
                    <label class="form-label text-muted small">Cantidad</label>
                    <input type="number" min="0" class="form-control" name="cantidad_adicional_${contadorItems}" placeholder="0">
                </div>                                        
                <div class="col-md-2 text-center">
                    <label class="form-label text-muted small d-block mb-1">Evidencia</label>
                    <input type="file" class="d-none" id="img_input_${contadorItems}" name="imagen_adicional_${contadorItems}" accept="image/*" onchange="cambiarEstadoImagen(this, ${contadorItems})">
                    <label for="img_input_${contadorItems}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm w-100" style="cursor: pointer;">
                        <i class="fas fa-camera"></i> <span id="img_text_${contadorItems}">Subir foto</span>
                    </label>                                            
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Comentarios</label>
                    <textarea class="form-control" name="comentarios_adicional_${contadorItems}" rows="1" placeholder="Ingrese comentarios"></textarea>        
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-outline-danger" onclick="eliminarItemAdicional(${contadorItems})" title="Eliminar fila">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Inyectamos la nueva fila al final del contenedor
        $('#rentaAdicionales').append(nuevaFila);
    }

    function eliminarItemAdicional(id) {
        // Eliminamos el div completo de la pantalla
        $(`#fila_adicional_${id}`).remove();
        // Nota: No restamos el 'contadorItems' para evitar que se repitan los IDs y names si agregan otro después.
    }

    // --- FUNCIÓN PARA CAMBIAR ICONO Y TEXTO CUANDO SE SELECCIONA UNA IMAGEN ---
    function cambiarEstadoImagen(input, id) {
        let labelText = document.getElementById(`img_text_${id}`);
        let labelButton = input.nextElementSibling; // El <label> que usamos como botón

        if (input.files && input.files[0]) {
            // Archivo seleccionado: Cambiar a verde y mostrar check
            labelText.innerHTML = "Foto lista";
            labelButton.classList.remove('btn-outline-secondary');
            labelButton.classList.add('btn-outline-success');
        } else {
            // Se canceló la selección: Regresar a gris
            labelText.innerHTML = "Subir foto";
            labelButton.classList.remove('btn-outline-success');
            labelButton.classList.add('btn-outline-secondary');
        }
    }
    </script>
</body>
</html>