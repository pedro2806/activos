<?php
// 1. Incluir conexión y librería FPDF
include 'conn.php'; // Cambia esto si tu archivo de conexión se llama distinto
require 'fpdf/fpdf.php';

if (!isset($_GET['id'])) {
    die("Error: No se proporcionó un ID de préstamo.");
}

$id_prestamo = (int) $_GET['id'];

// 2. Consultar los datos del préstamo principal
$sql = "SELECT p.*, 
               COALESCE(a.descripcion, 'Activo Desconocido') as activo_desc,
               COALESCE(c.cliente_largo, 'Cliente Desconocido') as cliente_nombre
        FROM prestamos_activos p
        LEFT JOIN activos a ON p.id_activo = a.id
        LEFT JOIN clientes c ON p.id_cliente = c.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_prestamo);
$stmt->execute();
$result = $stmt->get_result();
$prestamo = $result->fetch_assoc();

if (!$prestamo) {
    die("Error: Préstamo no encontrado.");
}
$stmt->close();

// 3. Iniciar el PDF
$pdf = new FPDF('P', 'mm', 'Letter'); // Formato carta vertical
$pdf->AddPage();
$pdf->SetMargins(15, 20, 15);

// --- ENCABEZADO ---
$pdf->SetFont('Arial', 'B', 16);
// Si tienes un logo, descomenta esta línea y pon la ruta:
// $pdf->Image('img/logo.png', 15, 15, 30); 
$pdf->Cell(0, 10, utf8_decode('RECIBO DE PRÉSTAMO DE EQUIPO'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode('Folio de Registro: #' . $prestamo['id']), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Fecha de Impresión: ' . date('d/m/Y H:i')), 0, 1, 'C');
$pdf->Ln(10);

// --- DATOS DEL CLIENTE Y RESPONSABLE ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(0, 8, utf8_decode(' DATOS DEL RESPONSABLE'), 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Empresa/Cliente:'), 'L,T,B', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(145, 7, utf8_decode($prestamo['cliente_nombre']), 'R,T,B', 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Nombre:'), 'L,B', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(145, 7, utf8_decode($prestamo['responsable']), 'R,B', 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Contacto/Teléfono:'), 'L,B', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(145, 7, utf8_decode($prestamo['contacto_responsable']), 'R,B', 1);
$pdf->Ln(5);

// --- DATOS DEL PERIODO ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode(' DETALLES DEL PRÉSTAMO'), 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Fecha Entrega:'), 'L,B', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(52, 7, utf8_decode($prestamo['fecha_entrega']), 'B', 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 7, utf8_decode('Periodo Oficial:'), 'B', 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(53, 7, utf8_decode($prestamo['fecha_inicio'] . ' al ' . $prestamo['fecha_fin']), 'R,B', 1);
$pdf->Ln(5);

// --- EQUIPO PRINCIPAL ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode(' EQUIPO ENTREGADO'), 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 8, utf8_decode($prestamo['activo_desc']), 1, 'C');
$pdf->Ln(5);

// --- ACCESORIOS ADICIONALES ---
// Consultamos si hay items extra
$sqlItems = "SELECT pa.cantidad, pa.comentarios, a.descripcion 
            FROM prestamos_items_adicionales pa
            LEFT JOIN activos a ON pa.id_item = a.id
            WHERE pa.id_prestamo = ?";
$stmtI = $conn->prepare($sqlItems);
$stmtI->bind_param("i", $id_prestamo);
$stmtI->execute();
$resI = $stmtI->get_result();

if ($resI->num_rows > 0) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, utf8_decode(' ITEMS ADICIONALES'), 1, 1, 'L', true);
    
    // Encabezados de tabla de items
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 7, 'Cant.', 1, 0, 'C');
    $pdf->Cell(95, 7, utf8_decode('Descripción'), 1, 0, 'C');
    $pdf->Cell(70, 7, 'Comentarios', 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 10);
    while($item = $resI->fetch_assoc()) {
        $pdf->Cell(20, 7, $item['cantidad'], 1, 0, 'C');
        $pdf->Cell(95, 7, utf8_decode($item['descripcion']), 1, 0, 'L');
        $pdf->Cell(70, 7, utf8_decode($item['comentarios']), 1, 1, 'L');
    }
}
$stmtI->close();
$pdf->Ln(15);

// --- TÉRMINOS Y CONDICIONES BÁSICOS ---
$pdf->SetFont('Arial', '', 8);
$terminos = "Al firmar este documento, el RESPONSABLE acepta que recibe el equipo y sus accesorios en las condiciones descritas y se compromete a devolverlos en la misma condición y en la fecha acordada. Cualquier daño, pérdida o robo será responsabilidad del firmante.";
$pdf->MultiCell(0, 4, utf8_decode($terminos), 0, 'J');
$pdf->Ln(25); // Espacio para firmas

// --- FIRMAS ---
$pdf->SetFont('Arial', 'B', 10);
// Usamos Cell para poner las firmas lado a lado
$pdf->Cell(90, 5, '___________________________________', 0, 0, 'C');
$pdf->Cell(95, 5, '___________________________________', 0, 1, 'C');
$pdf->Cell(90, 5, utf8_decode('Firma de quien ENTREGA'), 0, 0, 'C');
$pdf->Cell(95, 5, utf8_decode('Firma del RESPONSABLE (Recibe)'), 0, 1, 'C');

// Generar el PDF en el navegador
$pdf->Output('I', 'Recibo_Prestamo_'.$id_prestamo.'.pdf');
?>