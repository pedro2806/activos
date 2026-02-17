<?php
// dashboard_data.php
include 'conn.php'; // Tu archivo de conexión

header('Content-Type: application/json');

// 1. TARJETAS SUPERIORES (KPIs)
// Sumamos solo los activos (estatus = 1)
$sqlKPI = "SELECT 
            COUNT(*) as total_equipos,
            SUM(moi) as inversion_total,
            SUM(remanente) as valor_actual,
            (SELECT COUNT(*) FROM activos WHERE remanente <= 0 AND estatus=1) as totalmente_depreciados
            FROM activos 
            WHERE estatus = 1";
$resKPI = $conn->query($sqlKPI);
$kpi = $resKPI->fetch_assoc();

// 2. GRÁFICO DE DONA: TIPOS DE ACTIVOS
$sqlTipos = "SELECT ta.nombre, COUNT(a.id) as cantidad 
                FROM activos a 
                JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id 
                WHERE a.estatus = 1 
                GROUP BY ta.nombre";
$resTipos = $conn->query($sqlTipos);
$dataTipos = [];
while($row = $resTipos->fetch_assoc()) {
    $dataTipos['labels'][] = $row['nombre'];
    $dataTipos['values'][] = $row['cantidad'];
}

// 3. GRÁFICO DE BARRAS: ACTIVOS POR REGIÓN (Relación Activo -> Nave -> Región)
// Asumiendo que tienes la relación correcta en tu BD
$sqlRegion = "SELECT r.region, COUNT(a.id) as cantidad 
                    FROM activos a
                    JOIN cat_naves n ON a.id_nave = n.id
                    JOIN cat_regiones r ON n.id_region = r.id
                    WHERE a.estatus = 1
                GROUP BY r.region";
$resRegion = $conn->query($sqlRegion);
$dataRegion = [];
while($row = $resRegion->fetch_assoc()) {
    $dataRegion['labels'][] = $row['nombre']; // Ej: QRO, MTY
    $dataRegion['values'][] = $row['cantidad'];
}

// 4. TABLA RÁPIDA: ÚLTIMOS 5 INGRESOS
$sqlRecientes = "SELECT CONCAT(descripcion, ' - ', marca, ' - ', modelo) as descripcion_completa, created_at FROM activos WHERE estatus = 1 ORDER BY id DESC LIMIT 5";
$resRecientes = $conn->query($sqlRecientes);
$recientes = [];
while($row = $resRecientes->fetch_assoc()) {
    $recientes[] = $row;
}

echo json_encode([
    'kpi' => $kpi,
    'chartTipos' => $dataTipos,
    'chartRegion' => $dataRegion,
    'recientes' => $recientes
]);
?>