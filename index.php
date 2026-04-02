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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">

    <style>
        /* Estilos personalizados para KPIs */
        .card-kpi {
            border-left: 0.25rem solid #4e73df;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .card-kpi:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            font-size: 1.1rem;
            color: white;
        }
        .border-left-primary { border-left-color: #4e73df !important; }
        .border-left-success { border-left-color: #1cc88a !important; }
        .border-left-info    { border-left-color: #36b9cc !important; }
        .border-left-danger  { border-left-color: #e74a3b !important; }

        .bg-icon-primary { background-color: #4e73df; }
        .bg-icon-success { background-color: #1cc88a; }
        .bg-icon-info    { background-color: #36b9cc; }
        .bg-icon-danger  { background-color: #e74a3b; }
        
        /* Ajuste de scroll para tablas */
        .table-responsive-custom {
            max-height: 350px;
            overflow-y: auto;
        }
    </style>

</head>

<body id="page-top">

    <div id="wrapper">
        
        <?php include 'menu.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            
            <div id="content">
                
                <?php include 'encabezado.php'; ?>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-2">
                        <h1 class="h3 mb-0 text-gray-800">Resumen</h1>
                        
                        <div>
                            <button class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2 text-white" data-bs-toggle="modal" data-bs-target="#modalBitacora">
                                <i class="fas fa-history fa-sm text-white-50"></i> Ver Bitácora
                            </button>
                            <button onclick="exportarActivosDirecto()" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm mr-2">
                                <i class="fas fa-file-excel fa-sm text-white-50"></i> Exportar BD Completa
                            </button>
                            
                            <button onclick="cargarDashboard()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-sync-alt fa-sm text-white-50"></i> Actualizar Datos
                            </button>
                        </div>
                    </div>

                    <div class="card shadow mb-2 border-left-secondary">
                        <div class="card-body py-3">
                            <form id="formFiltros" class="row align-items-end" onsubmit="event.preventDefault(); cargarDashboard();">
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-muted">Tipo de Activo</label>
                                    <select class="form-select form-control-sm" id="filtroTipo">
                                        <option value="">Todos los tipos</option>
                                        <option value="1">EQ COMPUTO</option>
                                        <option value="2">MOBILIARIO y EQ DE OFICINA</option>
                                        <option value="3">MAQUINAS Y EQUIPOS</option>
                                        <option value="4">HERRAMIENTAS GENERALES</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-muted">Región</label>
                                    <select class="form-select form-control-sm" id="filtroRegion">
                                        <option value="">Todas</option>
                                        <option value="1">AGS</option>
                                        <option value="2">QRO</option>
                                        <option value="3">NTE</option>
                                        <option value="4">SLP</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-muted">Fecha Adquisición (Rango)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="date" class="form-control" id="filtroFechaInicio">
                                        <span class="input-group-text">a</span>
                                        <input type="date" class="form-control" id="filtroFechaFin">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-50" id="btnAplicar">
                                        <i class="fas fa-filter"></i> Aplicar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-50" onclick="limpiarFiltros()">
                                        <i class="fas fa-eraser"></i> Limpiar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card card-kpi border-left-primary shadow h-100 py-0">
                                <div class="card-body p-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 0.7rem;">Total Activos</div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800" id="kpiTotal">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon-box bg-icon-primary"><i class="fas fa-laptop"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card card-kpi border-left-info shadow h-100 py-0">
                                <div class="card-body p-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 0.7rem;">Inversión (MOI)</div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800" id="kpiMoi">$0</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon-box bg-icon-info"><i class="fas fa-dollar-sign"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card card-kpi border-left-success shadow h-100 py-0">
                                <div class="card-body p-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.7rem;">Valor Actual (Remanente)</div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800" id="kpiRemanente">$0</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon-box bg-icon-success"><i class="fas fa-chart-line"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card card-kpi border-left-danger shadow h-100 py-0">
                                <div class="card-body p-3 pb-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" style="font-size: 0.7rem;">Depreciados</div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800" id="kpiDepreciados">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon-box bg-icon-danger"><i class="fas fa-exclamation-triangle"></i></div>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-warning w-100 font-weight-bold text-dark shadow-sm mt-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#modalRefresh">
                                        <i class="fas fa-clock"></i> <span id="kpiAlertas">0</span> próximos a caducar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-money-check-alt mr-2"></i>Resumen Financiero por Región y Tipo</h6>
                                    <button class="btn btn-sm btn-success shadow-sm" onclick="exportarExcel()">
                                        <i class="fas fa-file-excel"></i> Exportar a Excel
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive table-responsive-custom">
                                        <table class="table table-hover table-bordered mb-0 text-center small" id="tablaResumenExportar">
                                            <thead class="bg-dark text-white sticky-top">
                                                <tr>
                                                    <th>Región</th>
                                                    <th>Tipo de Activo</th>
                                                    <th>Inversión (MOI)</th>
                                                    <th>Depreciación</th>
                                                    <th>Valor Actual</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaResumenFinanciero">
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="col-xl-4 col-lg-4 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Distribución por Tipo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="chartTipos"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Activos por Región</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="chartRegion"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Nuevos Registros (Este Mes)</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive table-responsive-custom">
                                        <table class="table table-striped mb-0 small">
                                            <thead class="bg-light sticky-top">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Fecha Reg.</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaRecientes">
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
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

    <div class="modal fade" id="modalRefresh" tabindex="-1" aria-labelledby="modalRefreshLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title font-weight-bold" id="modalRefreshLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Activos en Fin de Vida Útil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <p class="small text-muted mb-3">
                        La siguiente lista muestra los activos que han perdido <strong>más del 85% de su valor original</strong> (remanente actual del 15% o menor).
                    </p>
                    <div class="table-responsive bg-white shadow-sm rounded border">
                        <table class="table table-hover table-sm text-center mb-0" id="tablaExportarAlertas">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Región</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Costo Inicial</th>
                                    <th>Remanente</th>
                                </tr>
                            </thead>
                            <tbody id="tablaAlertasRefresh">
                                </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="exportarTablaAlertas()">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Lista
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBitacora" tabindex="-1" aria-labelledby="modalBitacoraLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalBitacoraLabel">
                        <i class="fas fa-history mr-2"></i> Historial de Movimientos
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light p-0">
                    <div class="table-responsive">
                        <table id="tablaBitacoraI" class="table table-hover table-sm text-center mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-dark text-white sticky-top">
                                <tr>
                                    <th width="15%">Usuario Log</th>
                                    <th width="10%">Fecha y Hora</th>
                                    <th width="15%">Acción</th>
                                    <th width="30%">Activo</th>
                                    <th width="30%">Detalles</th>
                                </tr>
                            </thead>
                            <tbody id="tablaBitacora">
                                </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>    
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script type="text/javascript">
        
        let chartInstanceTipos = null;
        let chartInstanceRegion = null;

        $(document).ready(function() {
            cargarDashboard();
        });

        function cargarDashboard() {
            // 1. Leer Filtros
            const tipo = $('#filtroTipo').val();
            const region = $('#filtroRegion').val();
            const fecha_inicio = $('#filtroFechaInicio').val();
            const fecha_fin = $('#filtroFechaFin').val();

            // Activar botón de carga (Loading Spinner)
            $('#btnAplicar').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando...');
            $('#btnAplicar').prop('disabled', true);

            // 2. Llamada AJAX
            $.ajax({
                url: 'dashboard_data.php', 
                method: 'GET',
                data: { tipo: tipo, region: region, fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
                dataType: 'json',
                success: function(data) {
                    const f = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 });
                    
                    // --- KPIs ---
                    $('#kpiTotal').text(data.kpi.total_equipos || 0);
                    $('#kpiMoi').text(f.format(data.kpi.inversion_total || 0));
                    $('#kpiRemanente').text(f.format(data.kpi.valor_actual || 0));
                    $('#kpiDepreciados').text(data.kpi.totalmente_depreciados || 0);

                    // --- Renderizar Gráficos ---
                    renderChartTipos(data.chartTipos);
                    renderChartRegion(data.chartRegion);

                    // --- Tabla Recientes (Del Mes) ---
                    let listaHtml = '';
                    if(data.recientes && data.recientes.length > 0){
                        data.recientes.forEach(item => {
                            listaHtml += `
                                <tr>
                                    <td class="font-weight-bold text-dark">${item.descripcion}</td>
                                    <td class="text-muted">${item.created_at ? item.created_at.substring(0,10) : ''}</td>
                                </tr>
                            `;
                        });
                    } else {
                        listaHtml = '<tr><td colspan="2" class="text-center text-muted">No hay registros este mes</td></tr>';
                    }
                    $('#tablaRecientes').html(listaHtml);

                    // --- Tabla Resumen Financiero ---
                    let resumenHtml = '';
                    if(data.resumen && data.resumen.length > 0) {
                        data.resumen.forEach(row => {
                            let remanenteVal = parseFloat(row.total_remanente) || 0;
                            let remClass = remanenteVal <= 0 ? 'text-danger font-weight-bold' : 'text-success font-weight-bold';
                            
                            resumenHtml += `
                                <tr class="align-middle">
                                    <td class="font-weight-bold bg-light">${row.region_nombre}</td>
                                    <td class="text-primary font-weight-bold">${row.tipo_nombre}</td>
                                    <td>${f.format(row.total_moi || 0)}</td>
                                    <td class="text-muted">${f.format(row.total_depreciacion || 0)}</td>
                                    <td class="${remClass}">${f.format(remanenteVal)}</td>
                                </tr>
                            `;
                        });
                    } else {
                        resumenHtml = '<tr><td colspan="5" class="text-center text-muted py-4">No hay datos financieros con los filtros seleccionados</td></tr>';
                    }
                    $('#tablaResumenFinanciero').html(resumenHtml);
                    
                    //Llenar Alertas de Refresh Tecnológico ---                    
                    // 1. Actualizar el número en el botón amarillo de la tarjeta
                    $('#kpiAlertas').text(data.alertas ? data.alertas.length : 0);

                    // 2. Llenar la tabla del modal
                    let alertasHtml = '';
                    if(data.alertas && data.alertas.length > 0) {
                        data.alertas.forEach(row => {
                            alertasHtml += `
                                <tr class="align-middle">
                                    <td class="small">${row.region_nombre}</td>
                                    <td class="small">${row.tipo_nombre}</td>
                                    <td class="text-left small">
                                        <strong>${row.descripcion}</strong><br>
                                        <span class="text-muted">${row.marca} / ${row.modelo}</span>
                                    </td>
                                    <td class="small">${f.format(row.moi || 0)}</td>
                                    <td class="text-danger font-weight-bold">${f.format(row.remanente || 0)}</td>
                                </tr>
                            `;
                        });
                    } else {
                        alertasHtml = '<tr><td colspan="5" class="text-center text-muted py-4">No hay equipos próximos a caducar</td></tr>';
                    }
                    $('#tablaAlertasRefresh').html(alertasHtml);

                    // Llenar Bitácora de Movimientos ---
                    let bitacoraHtml = '';
                    if(data.bitacora && data.bitacora.length > 0) {
                        data.bitacora.forEach(row => {
                            
                            let badgeColor = 'bg-secondary';
                            if(row.accion === 'CREADO') badgeColor = 'bg-success';
                            else if(row.accion === 'EDITADO') badgeColor = 'bg-primary';
                            else if(row.accion === 'FOTO_AGREGADA') badgeColor = 'bg-info text-dark';
                            else if(row.accion === 'FOTO_ELIMINADA') badgeColor = 'bg-danger';
                            else if(row.accion === 'PRESTAMO_EDITADO') badgeColor = 'bg-dark';
                            else if(row.accion === 'PRESTAMO_CREADO') badgeColor = 'bg-warning text-dark';
                            else if(row.accion === 'PRESTAMO_FINALIZADO') badgeColor = 'bg-success';
                            else if(row.accion === 'PRESTAMO_REGISTRADO') badgeColor = 'bg-primary';
                            else if(row.accion === 'PRESTAMO_DEVUELTO') badgeColor = 'bg-info text-dark';
                            else if(row.accion === 'ELIMINADO') badgeColor = 'bg-danger';

                            bitacoraHtml += `
                                <tr class="align-middle">
                                    <td class="text-left text-muted">${row.nombre}</td>
                                    <td class="text-muted font-weight-bold">${row.fecha_registro}</td>
                                    <td><span class="badge ${badgeColor}">${row.accion}</span></td>
                                    <td class="font-weight-bold text-dark">${row.activo_desc}</td>
                                    <td class="text-left text-muted">${row.detalles}</td>                                    
                                </tr>
                            `;
                        });
                    } else {
                        bitacoraHtml = '<tr><td colspan="4" class="text-center text-muted py-4">No hay movimientos registrados aún.</td></tr>';
                    }
                    $('#tablaBitacora').html(bitacoraHtml);

                    if(data.bitacora && data.bitacora.length > 0) {
                        $('#tablaBitacoraI').DataTable({
                            pageLength: 5,
                            lengthChange: false,
                            ordering: true,
                            info: true,
                            search: false,
                            language: {
                                url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                            }
                        });
                    } else {
                        $('#tablaBitacora').html('<tr><td colspan="4" class="text-center text-muted py-4">No hay movimientos registrados aún.</td></tr>');
                    }


                    
                },
                error: function(xhr, status, error) {
                    console.error("Error cargando dashboard: ", error);
                    Swal.fire('Error', 'Hubo un problema al cargar los datos.', 'error');
                },
                complete: function() {
                    // Restaurar el botón al terminar
                    $('#btnAplicar').html('<i class="fas fa-filter"></i> Aplicar');
                    $('#btnAplicar').prop('disabled', false);
                }
            });
        }

        // --- Funciones de Filtros y Exportación ---
        function limpiarFiltros() {
            document.getElementById('formFiltros').reset();
            cargarDashboard();
        }

        function exportarExcel() {
            let tabla = document.getElementById("tablaResumenExportar");
            let filas = tabla.querySelectorAll("tr");
            let csv = [];
            
            for (let i = 0; i < filas.length; i++) {
                let fila = [];
                let columnas = filas[i].querySelectorAll("td, th");
                
                for (let j = 0; j < columnas.length; j++) {
                    // Limpiar símbolos para que Excel detecte los números reales
                    let texto = columnas[j].innerText.replace(/,/g, '').replace(/\$/g, '').trim();
                    fila.push('"' + texto + '"');
                }
                csv.push(fila.join(","));
            }
            
            // BOM para UTF-8 (acentos y ñ)
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF" + csv.join("\n");
            let encodedUri = encodeURI(csvContent);
            
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            let fecha = new Date().toISOString().slice(0,10);
            link.setAttribute("download", "Resumen_Financiero_MESS_" + fecha + ".csv");
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // --- Configuración Gráfico de Dona ---
        function renderChartTipos(data) {
            const ctx = document.getElementById('chartTipos').getContext('2d');
            if (chartInstanceTipos) chartInstanceTipos.destroy();

            chartInstanceTipos = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: { maintainAspectRatio: false, legend: { display: true, position: 'bottom' }, cutout: '70%' },
            });
        }

        // --- Configuración Gráfico de Barras ---
        function renderChartRegion(data) {
            const ctx = document.getElementById('chartRegion').getContext('2d');
            if (chartInstanceRegion) chartInstanceRegion.destroy();

            chartInstanceRegion = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: "Cantidad",
                        backgroundColor: "#4e73df",
                        hoverBackgroundColor: "#2e59d9",
                        borderColor: "#4e73df",
                        data: data.values,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        x: { grid: { display: false } },
                        y: { ticks: { precision: 0 }, grid: { borderDash: [2] } },
                    },
                    plugins: { legend: { display: false } },
                },
            });
        }

        // --- NUEVA FUNCIÓN: Exportar BD Completa vía AJAX ---
        async function exportarActivosDirecto() {
            
            // Si tienes una función verificarAcceso() global, descomenta la siguiente línea:
            // const permiso = await verificarAcceso();

            // Mostrar alerta de carga (ideal si tienes muchos registros)
            Swal.fire({ 
                title: 'Generando archivo Excel...', 
                text: 'Por favor espera un momento.',
                allowOutsideClick: false, 
                didOpen: () => { Swal.showLoading() } 
            });

            $.ajax({
                url: 'acciones_activos.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    opcion: 'verActivos'
                },
                success: function(data) {                    
                    
                    // Validar que tengamos datos
                    if (!data || data.length === 0) {
                        Swal.fire('Información', 'No hay activos para exportar.', 'info');
                        return;
                    }

                    let csv = [];
                    
                    // 2. Crear la fila de Encabezados del Excel
                    let encabezados = [
                        '"Fecha de registro"',
                        '"Fecha de adquisicion"',
                        '"Tipo de Activo"', 
                        '"Descripción"', 
                        '"Marca / Modelo"', 
                        '"Region"',
                        '"Nave / Ubicación"', 
                        '"Usuario Asignado"', 
                        '"Costo Inicial"', 
                        '"Valor Remanente"', 
                        '"Observaciones"'
                    ];
                    csv.push(encabezados.join(","));

                    // Función auxiliar para limpiar textos (evita que comillas o saltos de línea rompan el Excel)
                    const limpiarTexto = (texto) => {
                        if (!texto) return '';
                        return texto.toString().replace(/"/g, "'").replace(/\n/g, ' ').trim();
                    };

                    // 3. Iterar directamente sobre el JSON y construir las filas
                    data.forEach(function(activo) {
                        let fila = [];
                        fila.push('"' + (activo.fecha_registro ? activo.fecha_registro.substring(0,10) : '') + '"');
                        fila.push('"' + (activo.fecha_adquisicion ? activo.fecha_adquisicion.substring(0,10) : '') + '"');
                        fila.push('"' + limpiarTexto(activo.tipo_activo) + '"');
                        fila.push('"' + limpiarTexto(activo.descripcion) + '"');
                        fila.push('"' + limpiarTexto(activo.marca + ' / ' + activo.modelo) + '"');
                        fila.push('"' + limpiarTexto(activo.region) + '"');
                        fila.push('"' + limpiarTexto(activo.nave + ' / ' + activo.ubicacion) + '"');
                        fila.push('"' + limpiarTexto(activo.usuario) + '"');
                        
                        // Montos limpios para poder sumar en Excel
                        fila.push('"' + (activo.costo || 0) + '"');
                        fila.push('"' + (activo.remanente || 0) + '"');
                        
                        fila.push('"' + limpiarTexto(activo.observaciones) + '"');

                        csv.push(fila.join(","));
                    });

                    // 4. Preparar el archivo CSV con soporte para acentos (BOM UTF-8)
                    let csvContent = "data:text/csv;charset=utf-8,\uFEFF" + csv.join("\n");
                    let encodedUri = encodeURI(csvContent);
                    
                    // 5. Crear enlace oculto y descargar
                    let link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    
                    let fecha = new Date().toISOString().slice(0,10);
                    link.setAttribute("download", "Reporte_General_Activos_" + fecha + ".csv");
                    
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Cerrar la alerta de carga al terminar
                    Swal.close();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error', 'Hubo un problema al consultar la base de datos.', 'error');
                }
            });
        }

        // --- Exportar Lista de Refresh a Excel ---
        function exportarTablaAlertas() {
            let tabla = document.getElementById("tablaExportarAlertas");
            let filas = tabla.querySelectorAll("tr");
            let csv = [];
            
            for (let i = 0; i < filas.length; i++) {
                let fila = [];
                let columnas = filas[i].querySelectorAll("td, th");
                
                for (let j = 0; j < columnas.length; j++) {
                    // Limpiar saltos de línea y simbolos de pesos para Excel
                    let texto = columnas[j].innerText.replace(/,/g, '').replace(/\$/g, '').replace(/\n/g, ' ').trim();
                    fila.push('"' + texto + '"');
                }
                csv.push(fila.join(","));
            }
            
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF" + csv.join("\n");
            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "Proyeccion_Refresh_Tecnologico.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>