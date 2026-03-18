<?php
// dashboard_data.php
include 'conn.php'; // Tu archivo de conexión a la BD

header('Content-Type: application/json');

// --- 1. RECEPCIÓN DE FILTROS ---
$donde = "WHERE a.estatus = 1"; // Condición base: Solo activos dados de alta

if (!empty($_GET['tipo'])) {
    $tipo = (int) $_GET['tipo'];
    $donde .= " AND a.id_tipo_activo = $tipo";
}
if (!empty($_GET['region'])) {
    $region = (int) $_GET['region'];
    $donde .= " AND a.region = $region"; 
}
if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
    $inicio = $conn->real_escape_string($_GET['fecha_inicio']);
    $fin = $conn->real_escape_string($_GET['fecha_fin']);
    $donde .= " AND a.fecha_adquisicion BETWEEN '$inicio' AND '$fin'";
}

// --- 2. CONSULTAS CON FILTROS APLICADOS ---

// A. KPIs Superiores
$sqlKPI = "SELECT 
                COUNT(*) as total_equipos,
                SUM(moi) as inversion_total,
                SUM(remanente) as valor_actual,
                (SELECT COUNT(*) FROM activos a2 WHERE a2.remanente <= 0 AND a2.estatus=1) as totalmente_depreciados
            FROM activos a $donde";
$resKPI = $conn->query($sqlKPI);
$kpi = $resKPI->fetch_assoc();

// B. Gráfico: Tipos de Activos
$sqlTipos = "SELECT ta.nombre, COUNT(a.id) as cantidad 
                FROM activos a 
                    LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id 
                    $donde 
                    GROUP BY ta.nombre";
$resTipos = $conn->query($sqlTipos);
$dataTipos = ['labels' => [], 'values' => []];
while($row = $resTipos->fetch_assoc()) {
    $dataTipos['labels'][] = $row['nombre'] ?? 'Sin Tipo';
    $dataTipos['values'][] = $row['cantidad'];
}

// C. Gráfico: Regiones
$sqlRegion = "SELECT r.region as nombre, COUNT(a.id) as cantidad 
                FROM activos a
                    LEFT JOIN cat_regiones r ON a.region = r.id
                    $donde
                    GROUP BY r.region";
$resRegion = $conn->query($sqlRegion);
$dataRegion = ['labels' => [], 'values' => []];
while($row = $resRegion->fetch_assoc()) {
    $dataRegion['labels'][] = $row['nombre'] ?? 'Sin Región';
    $dataRegion['values'][] = $row['cantidad'];
}

// D. Tabla: Registros del Mes Actual (Con ALIAS 'created_at' para JS)
$sqlRecientes = "SELECT descripcion, fecha_registro AS created_at 
                FROM activos a 
                $donde 
                AND MONTH(a.fecha_registro) = MONTH(CURRENT_DATE()) 
                AND YEAR(a.fecha_registro) = YEAR(CURRENT_DATE()) 
                ORDER BY a.fecha_registro DESC";
$resRecientes = $conn->query($sqlRecientes);
$recientes = [];
while($row = $resRecientes->fetch_assoc()) {
    $recientes[] = $row;
}

// E. NUEVA TABLA: Resumen Financiero por Región y Tipo
$sqlResumen = "SELECT 
                    COALESCE(r.region, 'Sin Región') AS region_nombre,
                    COALESCE(ta.nombre, 'Sin Tipo') AS tipo_nombre,
                    SUM(a.moi) AS total_moi,
                    SUM(a.depreciacion) AS total_depreciacion,
                    SUM(a.remanente) AS total_remanente
                FROM activos a
                    LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id
                    LEFT JOIN cat_regiones r ON a.region = r.id
                    $donde
                GROUP BY r.region, ta.nombre
                ORDER BY r.region, ta.nombre";

$resResumen = $conn->query($sqlResumen);
$resumenFinanciero = [];
while($row = $resResumen->fetch_assoc()) {
    $resumenFinanciero[] = $row;
}

// F. ALERTAS DE REFRESH (Equipos con 15% o menos de remanente, pero mayor a 0)
$sqlAlertas = "SELECT 
                    a.id, a.descripcion, a.marca, a.modelo, a.moi, a.remanente,
                    COALESCE(ta.nombre, 'Sin Tipo') AS tipo_nombre,
                    COALESCE(r.region, 'Sin Región') AS region_nombre
                FROM activos a
                    LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id
                    LEFT JOIN cat_regiones r ON a.region = r.id
                    $donde AND a.remanente > 0 AND a.remanente <= (a.moi * 0.15)
                    ORDER BY a.remanente ASC";

$resAlertas = $conn->query($sqlAlertas);
$alertas = [];
while($row = $resAlertas->fetch_assoc()) {
    $alertas[] = $row;
}

// Devolver todo el paquete JSON limpio
echo json_encode([
    'kpi' => $kpi,
    'chartTipos' => $dataTipos,
    'chartRegion' => $dataRegion,
    'recientes' => $recientes,
    'resumen' => $resumenFinanciero,
    'alertas' => $alertas
]);
?>