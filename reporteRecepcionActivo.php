<?php
include 'conn.php';
require 'fpdf/fpdf.php';

if (!isset($_GET['id'])) {
    die("Error: No se proporcionó el ID del activo.");
}

$id_activo = (int) $_GET['id'];

// Consultar los datos del activo
$sql = "SELECT a.*, ta.nombre AS tipoActivo, u.nombre AS nombreUsuario, n.nombre AS nombreNave
        FROM activos a 
        INNER JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id
        INNER JOIN mess_rrhh.usuarios u ON a.id_usuario = u.noEmpleado
        INNER JOIN cat_naves n ON a.id_nave = n.id
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_activo);
$stmt->execute();
$result = $stmt->get_result();
$activo = $result->fetch_assoc();

if (!$activo) {
    die("Error: Activo no encontrado.");
}
$stmt->close();

// Configuración del PDF
$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20); // Márgenes un poco más ajustados para que quepa todo perfecto

// --- ENCABEZADO INSTITUCIONAL ---
// $pdf->Image('img/logo.png', 20, 20, 35); // Descomenta al tener el logo
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, utf8_decode('GRUPO MESS'), 0, 1, 'C');
/*$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode('Departamento de Sistemas / Tecnologías de la Información'), 0, 1, 'R');*/
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('CARTA DE RECEPCIÓN DE ACTIVO'), 0, 1, 'C');
$pdf->SetLineWidth(0.5);
$pdf->Line(20, 53, 195, 53);
$pdf->Ln(6);

// --- FECHA Y LUGAR ---
$pdf->SetFont('Arial', '', 10);
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_hoy = date('d') . " de " . $meses[date('n')-1] . " de " . date('Y');
$pdf->Cell(0, 6, utf8_decode('Santiago de Querétaro, Qro., a ' . $fecha_hoy), 0, 1, 'R');
$pdf->Ln(10);

// --- CUERPO DE LA CARTA ---
$texto_intro = "Por medio de la presente, se hace entrega del equipo y/o herramientas que se detallan a continuación, los cuales son propiedad de GRUPO MESS, quedando bajo el resguardo y responsabilidad del colaborador.";
$pdf->MultiCell(0, 5, utf8_decode($texto_intro), 0, 'J');
$pdf->Ln(10);

// =====================================================================================
// 1. DATOS GENERALES Y DE ADQUISICIÓN
// =====================================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(235, 235, 235);
$pdf->Cell(0, 7, utf8_decode('DATOS GENERALES Y ADQUISICIÓN'), 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Tipo de Activo:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(52, 7, utf8_decode($activo['tipoActivo'] ?? 'N/A'), 1, 0, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Para Renta:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
// Valida el campo 'renta' según como lo tengas en tu BD (puede ser 1/0, Sí/No, etc.)
$texto_renta = (!empty($activo['renta']) && $activo['renta'] != 0 && $activo['renta'] != 'No') ? 'Sí' : 'No';
$pdf->Cell(54, 7, utf8_decode($texto_renta), 1, 1, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Cantidad:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(52, 7, utf8_decode($activo['cantidad'] ?? '1'), 1, 0, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Fecha Adquis.:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(54, 7, utf8_decode($activo['fecha_adquisicion'] ?? 'N/A'), 1, 1, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Descripción:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(141, 7, utf8_decode($activo['descripcion'] ?? 'N/A'), 1, 1, 'L');

$pdf->Ln(4);

// =====================================================================================
// 2. ESPECIFICACIONES TÉCNICAS
// =====================================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, utf8_decode(' ESPECIFICACIONES TÉCNICAS'), 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Marca:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(52, 7, utf8_decode($activo['marca'] ?? 'N/A'), 1, 0, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Modelo:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(54, 7, utf8_decode($activo['modelo'] ?? 'N/A'), 1, 1, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Núm. Serie:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(52, 7, utf8_decode($activo['serie'] ?? 'N/A'), 1, 0, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('ID:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(54, 7, utf8_decode($activo['id_interno'] ?? $activo['id']), 1, 1, 'L');

$pdf->Ln(4);

// =====================================================================================
// 3. UBICACIÓN Y ASIGNACIÓN
// =====================================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, utf8_decode(' UBICACIÓN Y ASIGNACIÓN'), 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Nave:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(52, 7, utf8_decode($activo['nave'] ?? 'N/A'), 1, 0, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Área / Laboratorio:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(54, 7, utf8_decode($activo['ubicacion'] ?? 'N/A'), 1, 1, 'L');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 7, utf8_decode('Responsable:'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(141, 7, utf8_decode($activo['responsable'] ?? 'N/A'), 1, 1, 'L');

$pdf->Ln(4);

// =====================================================================================
// 4. OBSERVACIONES
// =====================================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, utf8_decode(' OBSERVACIONES ADICIONALES'), 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 9);
$obs = empty($activo['observaciones']) ? 'Ninguna observación registrada al momento de la entrega.' : $activo['observaciones'];
// MultiCell se ajusta solo si el texto de observaciones es muy largo
$pdf->MultiCell(0, 6, utf8_decode($obs), 1, 'J');

$pdf->Ln(8);
/*
// --- COMPROMISOS ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, utf8_decode('CLÁUSULAS DE RESPONSABILIDAD:'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 8);
$clausulas = "1. El equipo es para uso exclusivo de las actividades laborales de la empresa.\n" .
             "2. El colaborador se compromete a mantener el equipo en buen estado físico y funcional.\n" .
             "3. Cualquier falla técnica debe ser reportada inmediatamente al área de Sistemas.\n" .
             "4. En caso de robo o extravío por negligencia, el colaborador aceptará los cargos correspondientes.\n" .
             "5. Al término de la relación laboral, el equipo deberá ser entregado en las mismas condiciones recibidas.";
$pdf->MultiCell(0, 4, utf8_decode($clausulas), 0, 'J');
*/
$pdf->Ln(20);

// --- FIRMAS ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(85, 5, '__________________________', 0, 0, 'C');
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(85, 5, '__________________________', 0, 1, 'C');

$pdf->Cell(85, 5, utf8_decode('Entrega'), 0, 0, 'C');
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(85, 5, utf8_decode('Recibe'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(85, 5, utf8_decode('Nombre y Firma'), 0, 0, 'C');
$pdf->Cell(5, 5, '', 0, 0, 'C');
// Imprime el nombre del responsable en la firma, si está en blanco pone "Nombre y Firma"
$nombre_firma = empty($activo['responsable']) ? 'Nombre y Firma' : $activo['responsable'];
$pdf->Cell(85, 5, utf8_decode($nombre_firma), 0, 1, 'C');

// Salida del PDF
$pdf->Output('I', 'Carta_Recepcion_Activo_' . $id_activo . '.pdf');