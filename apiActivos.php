<?php

// 1. Configuración de cabeceras y seguridad
header('Content-Type: application/json; charset=utf-8');
include_once 'conn.php';

/*$api_key_autorizada = ""; // Define tu API Key autorizada aquí

if (!isset($_POST['api_key']) || $_POST['api_key'] !== $api_key_autorizada) {
    http_response_code(401); // No autorizado
    echo json_encode(['status' => 'error', 'message' => 'API Key inválida o no proporcionada.']);
    exit;
}*/

// Capturar la acción
$accion = $_POST['opcion'] ?? $_GET['opcion'] ?? '';

if (empty($accion)) {
    echo json_encode(['status' => 'error', 'message' => 'Acción no especificada.']);
    exit;
}

// ---------------------------------------------------------
// ACCIÓN: REGISTRAR NUEVO ACTIVO
// ---------------------------------------------------------
if ($accion == 'nuevoActivo') {
    $tipoActivo    = $_POST['selectTipoActivo'] ?? '';
    $descripcion   = $_POST['descripcion'] ?? '';
    $marca         = $_POST['marca'] ?? '';
    $modelo        = $_POST['modelo'] ?? '';
    $noSerie       = $_POST['no_serie'] ?? '';
    $idInterno     = $_POST['id_interno'] ?? '';
    $usuario       = !empty($_POST['usuario']) ? $_POST['usuario'] : null;
    $nave          = !empty($_POST['selectNave']) ? $_POST['selectNave'] : null;
    $cpuInfo       = $_POST['cpuInfo'] ?? '';
    $monitorInfo   = $_POST['monitorInfo'] ?? '';
    $moi           = $_POST['moi'] ?? 0;
    $costo         = $_POST['costo'] ?? 0;
    $depreciacion  = $_POST['depreciacion'] ?? 0;
    $remanente     = $_POST['remanente'] ?? 0;
    $observaciones = $_POST['observaciones'] ?? '';
    $EsAccesorio   = $_POST['EsAccesorio'] ?? 0;
    $ubicacion     = $_POST['ubicacion'] ?? '';
    $cantidad      = 1;
    $estatus       = 1;

    if (empty($tipoActivo) || empty($descripcion)) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios.']);
        exit;
    }

    $sqlInsert = "INSERT INTO activos(id_tipo_activo, descripcion, marca, modelo, no_serie, id_interno, id_usuario, id_nave, cpu_info, monitor_info, cantidad, moi, costo, depreciacion, remanente, observaciones, created_at, es_accesorio, estatus, ubicacion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";

    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param("isssssiissiddddsiis", 
        $tipoActivo, $descripcion, $marca, $modelo, $noSerie, $idInterno, $usuario, $nave, $cpuInfo, $monitorInfo, $cantidad, $moi, 
        $costo, $depreciacion, $remanente, $observaciones, $EsAccesorio, $estatus, $ubicacion
    );

    if ($stmt->execute()) {
        $ultimoId = $conn->insert_id;
        $errorFotos = false;

        // Subida de Archivos (Fotos)
        if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
            $fotos = $_FILES['fotos'];
            $directorio = 'imgActivos/';
            if (!file_exists($directorio)) mkdir($directorio, 0777, true);

            for ($i = 0; $i < count($fotos['name']); $i++) {
                if ($fotos['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($fotos['name'][$i], PATHINFO_EXTENSION);
                    $nuevoNombre = 'activo_' . $ultimoId . '_' . time() . '_' . $i . '.' . $ext;
                    $rutaDestino = $directorio . $nuevoNombre;

                    if (move_uploaded_file($fotos['tmp_name'][$i], $rutaDestino)) {
                        $stmtF = $conn->prepare("INSERT INTO fotos_activos(id_activo, ruta_foto) VALUES (?, ?)");
                        $stmtF->bind_param("is", $ultimoId, $rutaDestino);
                        $stmtF->execute();
                        $stmtF->close();
                    } else { $errorFotos = true; }
                }
            }
        }
        echo json_encode(['status' => 'success', 'message' => 'Activo guardado' . ($errorFotos ? ' con errores en fotos' : '')]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
}

// ---------------------------------------------------------
// ACCIÓN: LISTAR ACTIVOS
// ---------------------------------------------------------
if ($accion == 'verActivos') {
    $sql = "SELECT a.*, ta.nombre as tipo_activo, u.nombre AS usuario, n.nombre AS nave 
            FROM activos a
            LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id
            LEFT JOIN mess_rrhh.usuarios u ON a.id_usuario = u.id_usuario
            LEFT JOIN cat_naves n ON a.id_nave = n.id
            WHERE a.estatus = 1 ORDER BY a.id DESC";
    $result = $conn->query($sql);
    $data = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($data);
}

// ---------------------------------------------------------
// ACCIÓN: DETALLE DE UN ACTIVO
// ---------------------------------------------------------
if ($accion == 'detalleActivo') {
    $id = $_POST['idActivo'] ?? 0;
    $sql = "SELECT a.*, ta.nombre as tipo_activo, r.region 
            FROM activos a
            LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id
            LEFT JOIN cat_naves n ON a.id_nave = n.id
            LEFT JOIN cat_regiones r ON n.id_region = r.id
            WHERE a.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    
    if ($res) {
        echo json_encode(['status' => 'success', 'activo' => $res]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No encontrado']);
    }
    $stmt->close();
}

// ---------------------------------------------------------
// ACCIÓN: ELIMINAR (BAJA LÓGICA)
// ---------------------------------------------------------
if ($accion == 'eliminarActivo') {
    $id = $_POST['idActivo'] ?? 0;
    $stmt = $conn->prepare("UPDATE activos SET estatus = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Activo dado de baja']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar']);
    }
    $stmt->close();
}

// ---------------------------------------------------------
// ACCIÓN: GUARDAR EDICIÓN
// ---------------------------------------------------------
if ($accion == 'guardarEdicion') {
    $sql = "UPDATE activos SET 
                                id_tipo_activo=?, 
                                es_accesorio=?, 
                                descripcion=?, 
                                marca=?, 
                                modelo=?, 
                                no_serie=?, 
                                id_interno=?, 
                                cpu_info=?, 
                                monitor_info=?, 
                                id_nave=?, 
                                id_usuario=?, 
                                moi=?, 
                                depreciacion=?, 
                                remanente=?, 
                                observaciones=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    // Asignación de tipos de datos: i = int, s = string, d = double
    $stmt->bind_param("iisssssssiidddsi", 
        $_POST['id_tipo_activo'], $_POST['es_accesorio'], $_POST['descripcion'], $_POST['marca'], 
        $_POST['modelo'], $_POST['no_serie'], $_POST['id_interno'], $_POST['cpu_info'], 
        $_POST['monitor_info'], $_POST['id_nave'], $_POST['id_usuario'], $_POST['moi'], 
        $_POST['depreciacion'], $_POST['remanente'], $_POST['observaciones'], $_POST['id']
    );
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Activo actualizado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
}

// ---------------------------------------------------------
// ACCIÓN: OBTENER FOTOS
// ---------------------------------------------------------
if ($accion == 'obtener_fotos') {
    $id = $_POST['id_activo'] ?? 0;
    $stmt = $conn->prepare("SELECT id, ruta_foto FROM fotos_activos WHERE id_activo = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fotos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['status' => 'success', 'fotos' => $fotos]);
    $stmt->close();
}

$conn->close();