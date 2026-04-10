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
        $sql = "SELECT id, descripcion, marca, modelo, no_serie FROM activos WHERE estatus = 1 AND prestamo=1";
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
        $ov = $conn->real_escape_string($_POST['orden_venta']);


        // 2. Validar que no vengan vacíos los selectores principales
        if(empty($id_activo) || empty($id_cliente)) {
            echo json_encode(['status' => 'error', 'message' => 'El activo y el cliente son obligatorios.']);
            exit;
        }

        // 3. Preparar la consulta SQL
        $sql = "INSERT INTO prestamos_activos 
                (id_activo, id_cliente, fecha_entrega, responsable, contacto_responsable, moneda, renta_dia, fecha_inicio, fecha_fin, tipo, ov) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iisssssssss", $id_activo, $id_cliente, $fecha_entrega, $responsable, $contacto, $moneda, $renta_dia, $fecha_inicio, $fecha_fin, $tipo_movimiento, $ov);
            
            // 4. Ejecutar
            if ($stmt->execute()) {

                // --- 1. CAPTURAR EL ID INMEDIATAMENTE ANTES DE CUALQUIER OTRA CONSULTA ---
                $id_nuevo_prestamo = $conn->insert_id;

                // --- 2. REGISTRAR EN EL LOG ---                                
                registrarLog($conn, $id_activo, $noEmpleado, 'PRESTAMO_REGISTRADO', "Se registró un nuevo préstamo (Folio: $id_nuevo_prestamo) para el activo con ID: $id_activo");                
                
                // --- 3. ACTUALIZAR ESTATUS DEL ACTIVO PRINCIPAL ---
                $sqlUpdateActivo = "UPDATE activos SET estatus = 2 WHERE id = ?"; // 2 es prestado
                $stmtActivo = $conn->prepare($sqlUpdateActivo);
                $stmtActivo->bind_param("i", $id_activo);
                $stmtActivo->execute();
                $stmtActivo->close();
            
                // --- 4. PROCESAR ITEMS ADICIONALES ---
                $total_items = isset($_POST['total_items_adicionales']) ? (int)$_POST['total_items_adicionales'] : 0;

                // Carpeta base para guardar las imágenes de evidencia
                $directorio_subida = 'img/evidencias_prestamos/';
                if (!file_exists($directorio_subida)) {
                    mkdir($directorio_subida, 0777, true); 
                }

                for ($i = 1; $i <= $total_items; $i++) {
                    
                    // Solo procesamos si seleccionaron un Item en esa fila
                    if (!empty($_POST["item_adicional_$i"])) {
                        
                        $id_item = (int)$_POST["item_adicional_$i"];
                        $cantidad = (int)$_POST["cantidad_adicional_$i"];
                        $comentarios = $conn->real_escape_string($_POST["comentarios_adicional_$i"]);
                        $ruta_imagen_final = null;

                        // Procesar la imagen de evidencia
                        if (isset($_FILES["imagen_adicional_$i"]) && $_FILES["imagen_adicional_$i"]['error'] == UPLOAD_ERR_OK) {
                            
                            $extension = pathinfo($_FILES["imagen_adicional_$i"]['name'], PATHINFO_EXTENSION);
                            $nombre_foto = "evidencia_pres" . $id_nuevo_prestamo . "_item" . $i . "_" . time() . "." . $extension;
                            $ruta_destino = $directorio_subida . $nombre_foto;

                            if (move_uploaded_file($_FILES["imagen_adicional_$i"]['tmp_name'], $ruta_destino)) {
                                $ruta_imagen_final = $ruta_destino;
                            }
                        }

                        // Guardar en la tabla de items adicionales
                        $sql_item = "INSERT INTO prestamos_items_adicionales (id_prestamo, id_item, cantidad, ruta_imagen, comentarios) VALUES (?, ?, ?, ?, ?)";
                        
                        if ($stmt_item = $conn->prepare($sql_item)) {
                            $stmt_item->bind_param("iiiss", $id_nuevo_prestamo, $id_item, $cantidad, $ruta_imagen_final, $comentarios);
                            
                            if ($stmt_item->execute()) {
                                // Cambiar estatus del activo adicional a "PRESTADO"                                
                                $sqlUpdateEstatus = "UPDATE activos SET estatus = 2 WHERE id = ?";
                                $stmtUpdateEst = $conn->prepare($sqlUpdateEstatus);
                                $stmtUpdateEst->bind_param("i", $id_item);
                                $stmtUpdateEst->execute();
                                $stmtUpdateEst->close();
                            }
                            $stmt_item->close();
                        }
                    }
                }

                // Todo se guardó correctamente, enviamos success
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
                    p.tipo as tipo_movimiento,
                    p.ov
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
        $comentarios = $conn->real_escape_string($_POST['comentarios']);

        $conn->begin_transaction();

        try {
            // 1. Marcar como devuelto (0), guardar fecha real y comentarios
            $sqlPrestamo = "UPDATE prestamos_activos 
                            SET estatus = 0, 
                                fecha_devolucion_real = NOW(), 
                                comentarios_devolucion = ? 
                            WHERE id = ?";
            $stmtPrestamo = $conn->prepare($sqlPrestamo);
            $stmtPrestamo->bind_param("si", $comentarios, $id_prestamo);
            $stmtPrestamo->execute();
            $stmtPrestamo->close();

            // 2. Liberar el activo principal (Estatus 1 = Disponible)
            $sqlActivo = "UPDATE activos SET estatus = 1 WHERE id = ?";
            $stmtActivo = $conn->prepare($sqlActivo);
            $stmtActivo->bind_param("i", $id_activo);
            $stmtActivo->execute();
            $stmtActivo->close();

            // 3. Buscar y liberar los items adicionales
            $sqlBuscarItems = "SELECT id_item FROM prestamos_items_adicionales WHERE id_prestamo = ?";
            $stmtBuscar = $conn->prepare($sqlBuscarItems);
            $stmtBuscar->bind_param("i", $id_prestamo);
            $stmtBuscar->execute();
            $resultItems = $stmtBuscar->get_result();

            while($item = $resultItems->fetch_assoc()) {
                $id_item_extra = $item['id_item'];

                $sqlRegresarEstatus = "UPDATE activos SET estatus = 1 WHERE id = ?";
                $stmtRegresarEst = $conn->prepare($sqlRegresarEstatus);
                $stmtRegresarEst->bind_param("i", $id_item_extra);
                $stmtRegresarEst->execute();
                $stmtRegresarEst->close();
            }
            $stmtBuscar->close();

            // OPCIONAL PERO RECOMENDADO: Registrar en el log general
            // registrarLog($conn, $id_activo, $_SESSION['id_usuario'], 'PRESTAMO_DEVUELTO', "Se devolvió el préstamo Folio: $id_prestamo. Notas: $comentarios");

            $conn->commit();
            echo json_encode(['status' => 'success']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
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

    if ($accion == 'obtener_detalle_prestamo') {
        $id_prestamo = (int) $_POST['id_prestamo'];

        // 1. Consulta principal (El Préstamo)
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
                
                // --- NUEVO: 2. Buscar los items adicionales de este préstamo ---
                $items_adicionales = [];
                $sqlItems = "SELECT 
                                pa.cantidad, 
                                pa.comentarios, 
                                pa.ruta_imagen,
                                a.descripcion as item_nombre
                            FROM prestamos_items_adicionales pa
                            LEFT JOIN activos a ON pa.id_item = a.id
                            WHERE pa.id_prestamo = ?";
                            
                $stmtItems = $conn->prepare($sqlItems);
                $stmtItems->bind_param("i", $id_prestamo);
                $stmtItems->execute();
                $resItems = $stmtItems->get_result();
                
                while ($itemRow = $resItems->fetch_assoc()) {
                    $items_adicionales[] = $itemRow;
                }
                $stmtItems->close();

                // Adjuntamos los items al arreglo principal
                $row['items_extra'] = $items_adicionales;

                // Respondemos todo el paquete completo
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
$conn->close();

?>