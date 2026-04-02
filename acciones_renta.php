<?php
include_once 'conn.php';

    $accion = isset($_POST['opcion']) ? $_POST['opcion'] : '';

    $noEmpleado = isset($_COOKIE['noEmpleado']) ? intval($_COOKIE['noEmpleado']) : 0;


    // Función para registrar movimientos en el Log
    function registrarLog($conn, $idActivo, $idUsuario, $accion, $detalles) {
        $sqlLog = "INSERT INTO log_activos (id_activo, id_usuario, accion, detalles) VALUES (?, ?, ?, ?)";
        if ($stmtLog = $conn->prepare($sqlLog)) {
            $stmtLog->bind_param("iiss", $idActivo, $idUsuario, $accion, $detalles);
            $stmtLog->execute();
            $stmtLog->close();
        }
    }

    if ($accion == 'obtener_activos_disponibles') {
        $sql = "SELECT id, descripcion FROM activos WHERE estatus = 1 AND prestamo=1";
        $result = $conn->query($sql);
        $activos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $activos[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $activos]);
        exit;
    }

    if ($accion == 'obtener_clientes') {
        $sql = "SELECT id,   cliente_largo FROM clientes";
        $result = $conn->query($sql);
        $clientes = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $clientes[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $clientes]);
        exit;
    }

    if ($accion == 'registrar_prestamo') {
    
        // 1. Recibir los datos del formulario
        $id_activo = (int) $_POST['activo_id'];
        $id_cliente = (int) $_POST['clienteSelect'];
        $fecha_entrega = $conn->real_escape_string($_POST['fecha_entrega']);
        $responsable = $conn->real_escape_string($_POST['responsable']);
        $contacto = $conn->real_escape_string($_POST['contacto_responsable']);
        $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
        $fecha_fin = $conn->real_escape_string($_POST['fecha_fin']);
        $moneda = $conn->real_escape_string($_POST['moneda']);
        $renta_dia = (float) $_POST['renta_dia'];
        $tipo_movimiento = $conn->real_escape_string($_POST['tipo_movimiento']);

        // 2. Validar que no vengan vacíos los selectores principales
        if(empty($id_activo) || empty($id_cliente)) {
            echo json_encode(['status' => 'error', 'message' => 'El activo y el cliente son obligatorios.']);
            exit;
        }

        // 3. Preparar la consulta SQL
        $sql = "INSERT INTO prestamos_activos 
                (id_activo, id_cliente, fecha_entrega, responsable, contacto_responsable, moneda, renta_dia, fecha_inicio, fecha_fin, tipo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iissssssss", $id_activo, $id_cliente, $fecha_entrega, $responsable, $contacto, $moneda, $renta_dia, $fecha_inicio, $fecha_fin, $tipo_movimiento);
            
            // 4. Ejecutar y responder a JavaScript
            if ($stmt->execute()) {
                
                // --- REGISTRAR EN EL LOG ---                
                registrarLog($conn, $id_activo, $noEmpleado, 'PRESTAMO_REGISTRADO', "Se registró un nuevo préstamo para el activo con ID: $id_activo");                
                
                $sqlUpdateActivo = "UPDATE activos SET estatus = 2 WHERE id = ?"; // Suponiendo que 2 es prestado
                $stmtActivo = $conn->prepare($sqlUpdateActivo);
                $stmtActivo->bind_param("i", $id_activo);
                $stmtActivo->execute();
                

                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar en la base de datos: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en la consulta SQL.']);
        }
        exit;
    }

    if ($accion == 'obtener_prestamos') {
    
        // Hacemos un JOIN para traer el nombre del activo y el nombre del cliente
        $sql = "SELECT 
                    p.id, 
                    p.id_activo,
                    p.fecha_inicio, 
                    p.fecha_fin, 
                    p.responsable, 
                    p.contacto_responsable, 
                    p.estatus,
                    COALESCE(a.descripcion, 'Activo Desconocido') as activo_desc,
                    COALESCE(c.cliente_largo, 'Cliente Desconocido') as cliente_nombre,
                    p.moneda,
                    p.renta_dia,
                    p.tipo as tipo_movimiento
                FROM prestamos_activos p
                LEFT JOIN activos a ON p.id_activo = a.id
                LEFT JOIN clientes c ON p.id_cliente = c.id
                ORDER BY p.id DESC, p.estatus ASC, p.tipo DESC";
                
        $result = $conn->query($sql);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        echo json_encode(['status' => 'success', 'data' => $data]);
        exit;
    }

    if ($accion == 'devolver_prestamo') {
        $id_prestamo = (int) $_POST['id_prestamo'];
        $id_activo = (int) $_POST['id_activo'];

        if ($id_prestamo <= 0 || $id_activo <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Datos inválidos.']);
            exit;
        }

        // Iniciamos una transacción para asegurar que ambos UPDATE ocurran o ninguno
        $conn->begin_transaction();

        try {
            // 1. Marcar el préstamo como devuelto (estatus = 0)
            $sqlPrestamo = "UPDATE prestamos_activos SET estatus = 0 WHERE id = ?";
            $stmtPrestamo = $conn->prepare($sqlPrestamo);
            $stmtPrestamo->bind_param("i", $id_prestamo);
            $stmtPrestamo->execute();
            registrarLog($conn, $id_activo, $noEmpleado, 'PRESTAMO_DEVUELTO', "Se devolvió el préstamo con ID: $id_prestamo para el activo con ID: $id_activo");
            
            $sqlActivo = "UPDATE activos SET estatus = 1 WHERE id = ?";
            $stmtActivo = $conn->prepare($sqlActivo);
            $stmtActivo->bind_param("i", $id_activo);
            $stmtActivo->execute();
            
            // Confirmamos los cambios
            $conn->commit();
            echo json_encode(['status' => 'success']);

        } catch (Exception $e) {
            // Si algo falla, deshacemos los cambios
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error al procesar la devolución: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($accion == 'obtener_detalle_prestamo') {
        $id_prestamo = (int) $_POST['id_prestamo'];

        $sql = "SELECT 
                    p.*,
                    COALESCE(a.descripcion, 'Activo Desconocido') as activo_desc,
                    COALESCE(c.cliente_largo, 'Cliente Desconocido') as cliente_nombre
                FROM prestamos_activos p
                LEFT JOIN activos a ON p.id_activo = a.id
                LEFT JOIN clientes c ON p.id_cliente = c.id
                WHERE p.id = ?";
                
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $id_prestamo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                echo json_encode(['status' => 'success', 'data' => $row]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Préstamo no encontrado.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos.']);
        }
        exit;
    }

    if ($accion == 'editar_prestamo') {
        
        $id_prestamo = (int) $_POST['id_prestamo'];
        $responsable = $conn->real_escape_string($_POST['responsable']);
        $contacto = $conn->real_escape_string($_POST['contacto_responsable']);
        $fecha_entrega = $conn->real_escape_string($_POST['fecha_entrega']);
        $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
        $fecha_fin = $conn->real_escape_string($_POST['fecha_fin']);
        $moneda = $conn->real_escape_string($_POST['moneda']);
        $renta_dia = (float) $_POST['renta_dia'];
        $tipo_movimiento = $conn->real_escape_string($_POST['tipo_movimiento']);

        if ($id_prestamo <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID de préstamo inválido.']);
            exit;
        }

        $sql = "UPDATE prestamos_activos 
            SET responsable = ?, 
                contacto_responsable = ?, 
                moneda = ?,
                renta_dia = ?,
                fecha_entrega = ?, 
                fecha_inicio = ?, 
                fecha_fin = ?,
                tipo = ? 
            WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssssssi", $responsable, $contacto, $moneda, $renta_dia, $fecha_entrega, $fecha_inicio, $fecha_fin, $tipo_movimiento, $id_prestamo);
            
            if ($stmt->execute()) {
                registrarLog($conn, $id_prestamo, $noEmpleado, 'PRESTAMO_EDITADO', "Se editó el préstamo con ID: $id_prestamo");
                echo json_encode(['status' => 'success']);                
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en la consulta SQL.']);
        }
        exit;
    }
$conn->close();

?>