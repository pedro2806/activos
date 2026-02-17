<!DOCTYPE html>
<html>
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

    <style>
        /* Estilos personalizados para el Dashboard */
        .card-kpi {
            border-left: 0.25rem solid #4e73df; /* Borde azul estilo SB Admin */
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .card-kpi:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; /* Redondo estilo Admin */
            font-size: 1.5rem;
            color: white;
        }
        /* Colores específicos */
        .border-left-primary { border-left-color: #4e73df !important; }
        .border-left-success { border-left-color: #1cc88a !important; }
        .border-left-info    { border-left-color: #36b9cc !important; }
        .border-left-danger  { border-left-color: #e74a3b !important; }

        .bg-icon-primary { background-color: #4e73df; }
        .bg-icon-success { background-color: #1cc88a; }
        .bg-icon-info    { background-color: #36b9cc; }
        .bg-icon-danger  { background-color: #e74a3b; }
    </style>

</head>

<body id="page-top">

    <div id="wrapper">
        <?php include 'menu.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'encabezado.php'; ?>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Resumen</h1>                        
                    </div>

                    <div class="row">
                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card card-kpi border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Activos</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpiTotal">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon-box bg-icon-primary"><i class="fas fa-laptop"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card card-kpi border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Inversión (MOI)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpiMoi">$0</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon-box bg-icon-info"><i class="fas fa-dollar-sign"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card card-kpi border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valor Actual (Remanente)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpiRemanente">$0</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon-box bg-icon-success"><i class="fas fa-chart-line"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-kpi border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Depreciados (Baja)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpiDepreciados">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon-box bg-icon-danger"><i class="fas fa-exclamation-triangle"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        -->
                    </div>

                    <div class="row">
                        
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Distribución por Tipo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="chartTipos"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Últimos Registros</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Estatus</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaRecientes">
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Activos por Región</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="chartRegion"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        -->
                    </div>

                </div>
                </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; MESS 2025</span>
                    </div>
                </div>
            </footer>
            </div>
        </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

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

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        
        // Variables globales para los gráficos
        let chartInstanceTipos = null;
        let chartInstanceRegion = null;

        $(document).ready(function() {
            // Cargar datos al iniciar
            cargarDashboard();
        });

        function cargarDashboard() {
            $.ajax({
                url: 'dashboard_data.php', // El archivo PHP que creamos antes
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // 1. Llenar KPIs
                    const f = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 });
                    
                    $('#kpiTotal').text(data.kpi.total_equipos);
                    $('#kpiMoi').text(f.format(data.kpi.inversion_total));
                    $('#kpiRemanente').text(f.format(data.kpi.valor_actual));
                    ///$('#kpiDepreciados').text(data.kpi.totalmente_depreciados);

                    // 2. Renderizar Gráficos
                    renderChartTipos(data.chartTipos);
                    ///renderChartRegion(data.chartRegion);

                    // 3. Llenar lista recientes
                    let listaHtml = '';
                    if(data.recientes && data.recientes.length > 0){
                        data.recientes.forEach(item => {
                            listaHtml += `
                                <tr>
                                    <td>${item.descripcion_completa}</td>
                                    <td>${item.created_at}</td>
                                    <td><span class="badge bg-success text-white">Activo</span></td>
                                </tr>
                            `;
                        });
                    } else {
                        listaHtml = '<tr><td colspan="3" class="text-center">No hay registros recientes</td></tr>';
                    }
                    $('#tablaRecientes').html(listaHtml);
                },
                error: function(xhr, status, error) {
                    console.error("Error cargando dashboard: ", error);
                }
            });
        }

        // --- Gráfico de Dona ---
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
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    cutout: '70%',
                },
            });
        }

        // --- Gráfico de Barras ---
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
                    layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 6 } },
                        y: { ticks: { maxTicksLimit: 5, padding: 10, callback: function(value) { return value; } }, grid: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2] } },
                    },
                    legend: { display: false },
                },
            });
        }

        function convertirTexto(e) {
            e.value = e.value.toUpperCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }
    </script>
</body>
</html>