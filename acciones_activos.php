<?php
include_once 'conn.php';

    $accion = isset($_POST['opcion']) ? $_POST['opcion'] : '';

    $tipoActivo = isset($_POST['tipoActivo']) ? $_POST['tipoActivo'] : '';
    $descripcion = $_POST['descripcion'];   
    $marca = isset($_POST['marca']) ? $_POST['marca'] : '';
    $modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
    $noSerie = isset($_POST['noSerie']) ? $_POST['noSerie'] : '';
    $idInterno = isset($_POST['idInterno']) ? $_POST['idInterno'] : '';
    $cpuInfo = isset($_POST['cpuInfo']) ? $_POST['cpuInfo'] : '';
    $monitorInfo = isset($_POST['monitorInfo']) ? $_POST['monitorInfo'] : '';
    //$region = isset($_POST['region']) ? $_POST['region'] : '';
    $nave = isset($_POST['selectNave']) ? $_POST['selectNave'] : '';
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $moi = isset($_POST['moi']) ? $_POST['moi'] : '';
    $costo = isset($_POST['costo']) ? floatval($_POST['costo']) : 0.0;
    $depreciacion = isset($_POST['depreciacion']) ? floatval($_POST['depreciacion']) : 0.0;
    $remanente = isset($_POST['remanente']) ? floatval($_POST['remanente']) : 0.0;
    $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';
    $EsAccesorio = isset($_POST['EsAccesorio']) ? intval($_POST['EsAccesorio']) : 0;
    $ubicacion = isset($_POST['ubicacion']) ? $_POST['ubicacion'] : '';
    $fotos = isset($_POST['fotos']) ? $_POST['fotos'] : '';

    if ($accion == 'nuevoActivo') {
        
        // 1. RECIBIR VARIABLES (Usamos $_POST directo porque viene de FormData)        
        $tipoActivo     = $_POST['selectTipoActivo'] ?? '';
        $descripcion    = $_POST['descripcion'] ?? '';
        $marca          = $_POST['marca'] ?? '';
        $modelo         = $_POST['modelo'] ?? '';
        $noSerie        = $_POST['no_serie'] ?? '';
        $idInterno      = $_POST['id_interno'] ?? '';
        $usuario        = $_POST['usuario'] ?? null; // Puede ser null si no asignan
        $nave           = $_POST['selectNave'] ?? null;
        $cpuInfo        = $_POST['cpuInfo'] ?? '';
        $monitorInfo    = $_POST['monitorInfo'] ?? '';
        $moi            = $_POST['moi'] ?? 0;
        $costo          = $_POST['costo'] ?? 0;
        $depreciacion   = $_POST['depreciacion'] ?? 0;
        $remanente      = $_POST['remanente'] ?? 0;
        $observaciones  = $_POST['observaciones'] ?? '';
        $EsAccesorio    = $_POST['EsAccesorio'] ?? 0; // Viene como '1' o '0' desde JS
        $ubicacion      = $_POST['ubicacion'] ?? '';
                
        $cantidad = 1;
        $estatus = 1; // Activo

        //Validar datos obligatorios
        if (empty($tipoActivo)) {   
            $response = array(
                'status' => 'error', 
                'message' => 'Faltan datos obligatorios: Tipo de Activo, Descripción, Marca o Nave.'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        // 2. INSERT SEGURO (Prepared Statement)
        $sqlInsert = "INSERT INTO activos(id_tipo_activo, descripcion, marca, modelo, no_serie, id_interno, id_usuario, id_nave, cpu_info, monitor_info, cantidad, moi, 
                                            costo, depreciacion, remanente, observaciones, created_at, es_accesorio, estatus, ubicacion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";

        $stmt = $conn->prepare($sqlInsert);
        
        // "s" = string, "i" = integer, "d" = double (decimales)
        // Ajusta las letras según tus tipos de dato exactos en MySQL
        $stmt->bind_param("isssssiissiddddsiis", 
                            $tipoActivo, $descripcion, $marca, $modelo, $noSerie, $idInterno, $usuario, $nave, $cpuInfo, $monitorInfo, $cantidad, $moi, 
                            $costo, $depreciacion, $remanente, $observaciones, $EsAccesorio, $estatus, $ubicacion
        );

        if ($stmt->execute()) {
            $ultimoId = $conn->insert_id; // Obtenemos ID para las fotos
            $errorFotos = false;

            // 3. MANEJO DE FOTOS 
            if (isset($_FILES['fotos'])) {
                $fotos = $_FILES['fotos'];
                // Contamos cuántos archivos vienen
                $totalArchivos = count($fotos['name']);

                // Creamos carpeta si no existe
                $directorio = 'imgActivos/';
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0777, true);
                }

                for ($i = 0; $i < $totalArchivos; $i++) {
                    // Verificar que no hubo error en la subida y que tiene nombre
                    if ($fotos['error'][$i] === UPLOAD_ERR_OK && !empty($fotos['name'][$i])) {
                        
                        $nombreOriginal = $fotos['name'][$i];
                        $tmpName        = $fotos['tmp_name'][$i];
                        
                        // Generar nombre único: activo_15_TIMESTAMP_nombre.jpg
                        $nuevoNombre = 'activo_' . $ultimoId . '_' . time() . '_' . $i;
                        $rutaDestino = $directorio . $nuevoNombre;

                        if (move_uploaded_file($tmpName, $rutaDestino)) {
                            // Insertar ruta en BD
                            // Aquí podemos usar query normal o prepare (prepare es mejor)
                            $sqlFoto = "INSERT INTO fotos_activos(id_activo, ruta_foto) VALUES (?, ?)";
                            $stmtFoto = $conn->prepare($sqlFoto);
                            $stmtFoto->bind_param("is", $ultimoId, $rutaDestino);
                            $stmtFoto->execute();
                            $stmtFoto->close();
                        } else {
                            $errorFotos = true;
                        }
                    }
                }
            }

            $response = array(
                'status' => 'success', 
                'message' => 'Activo registrado con éxito.' . ($errorFotos ? ' (Hubo error al subir algunas fotos)' : '')
            );

        } else {
            $response = array(
                'status' => 'error', 
                'message' => 'Error al guardar en BD: ' . $stmt->error
            );
        }

        $stmt->close();

        // Devolver JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit; // Terminar script
    }

    // Acción para cargar los activos
    if ($accion == 'verActivos') {
        $sqlSelect = "SELECT 
    a.id, ta.nombre as tipo_activo, a.descripcion, a.marca, a.modelo, a.no_serie, a.id_interno, 
    u.nombre AS usuario, n.nombre AS nave, a.cpu_info, a.monitor_info, a.cantidad, a.moi, 
    a.costo, a.depreciacion, a.remanente, a.observaciones, a.created_at, a.ubicacion
FROM activos a
LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id
LEFT JOIN mess_rrhh.usuarios u ON (
    (a.id_tipo_activo = 1 AND a.id_usuario = u.noEmpleado) OR 
    (a.id_tipo_activo != 1 AND a.id_usuario = u.id_usuario)
)
LEFT JOIN cat_naves n ON a.id_nave = n.id
WHERE a.estatus = 1
ORDER BY a.id DESC";
        
        $result = $conn->query($sqlSelect);
        $activos = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $activos[] = $row;
            }
        }
        // Devolver la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($activos);
    }

    if($accion == 'detalleActivo'){
        // 1. Definir cabecera JSON al principio para evitar errores
        header('Content-Type: application/json');

        $idActivo = isset($_POST['idActivo']) ? $_POST['idActivo'] : 0;

        // 2. Consulta SQL (Usamos ? para mayor seguridad)
        $sqlDetalle = "SELECT 
                        a.id, 
                        ta.nombre as tipo_activo, 
                        a.descripcion, 
                        a.marca, 
                        a.modelo, 
                        a.no_serie, 
                        a.id_interno, 
                        u.nombre AS usuario, 
                        n.nombre AS nave, 
                        r.region AS region, 
                        a.cpu_info, 
                        a.monitor_info, 
                        a.cantidad, 
                        a.moi, 
                        a.costo, 
                        a.depreciacion, 
                        a.remanente, 
                        a.observaciones, 
                        a.created_at,
                        a.es_accesorio 
                    FROM activos a
                    LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id
                    LEFT JOIN mess_rrhh.usuarios u ON a.id_usuario = u.noEmpleado
                    LEFT JOIN cat_naves n ON a.id_nave = n.id
                    LEFT JOIN cat_regiones r ON n.id_region = r.id
                    WHERE a.id = ?"; // Usamos placeholder ?
        
        // 3. Preparamos la consulta (Seguridad)
        if ($stmt = $conn->prepare($sqlDetalle)) {
            $stmt->bind_param("i", $idActivo); // "i" indica que es un entero
            $stmt->execute();
            $result = $stmt->get_result();
            
            // 4. Obtenemos UNA sola fila (fetch_assoc directo, sin while)
            if ($row = $result->fetch_assoc()) {
                echo json_encode([
                    'status' => 'success',
                    'activo' => $row 
                ]);
            } else {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'ID no encontrado'
                ]);
            }
            $stmt->close();
        } else {
            // Error en la consulta SQL
            echo json_encode([
                'status' => 'error', 
                'message' => 'Error en la consulta: ' . $conn->error
            ]);
        }
        exit; // Buena práctica terminar el script aquí
    }

    if ($accion == 'eliminarActivo') {
        $idActivo = isset($_POST['idActivo']) ? $_POST['idActivo'] : 0;

        // 1. Validar ID
        if(!is_numeric($idActivo) || $idActivo <= 0){
            echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
            exit;
        }

        // 2. CAMBIO PRINCIPAL: Update en lugar de Delete
        // Asumimos que la columna se llama 'estatus' y que 0 significa 'Baja/Inactivo'
        $sql = "UPDATE activos SET estatus = 0 WHERE id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $idActivo);
            
            if ($stmt->execute()) {
                // Verificamos si se afectó alguna fila
                if ($stmt->affected_rows > 0) {
                    echo json_encode([
                        'status' => 'success', 
                        'message' => 'El activo ha sido dado de baja correctamente'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'No se pudo dar de baja (Tal vez ya estaba inactivo o no existe)'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Error al actualizar el estatus'
                ]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        }
    }

    if ($accion == 'guardarEdicion') {
        
        // 1. Recolección de datos
        $id             = $_POST['id'];
        $id_tipo_activo = $_POST['id_tipo_activo'];
        $descripcion    = $_POST['descripcion'];
        $marca          = $_POST['marca'];
        $modelo         = $_POST['modelo'];
        $no_serie       = $_POST['no_serie'];
        $id_interno     = $_POST['id_interno'];
        
        // Checkbox: Si viene post, es 1, si no, es 0
        $es_accesorio   = isset($_POST['es_accesorio']) ? 1 : 0; 
        
        $cpu_info       = $_POST['cpu_info'] ?? ''; // Operador ?? para evitar error si viene vacío
        $monitor_info   = $_POST['monitor_info'] ?? '';
        
        $id_nave        = $_POST['id_nave'];
        $id_usuario     = $_POST['id_usuario']; // Asegúrate de recibir el ID numérico
        
        $moi            = $_POST['moi'];
        $depreciacion   = $_POST['depreciacion'];
        $remanente      = $_POST['remanente'];
        $observaciones  = $_POST['observaciones'];

        // 2. Consulta SQL UPDATE (Preparada)
        $sql = "UPDATE activos SET 
                    id_tipo_activo = ?,
                    es_accesorio = ?,
                    descripcion = ?,
                    marca = ?,
                    modelo = ?,
                    no_serie = ?,
                    id_interno = ?,
                    cpu_info = ?,
                    monitor_info = ?,
                    id_nave = ?,
                    id_usuario = ?,
                    moi = ?,
                    depreciacion = ?,
                    remanente = ?,
                    observaciones = ?
                WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Tipos de datos: i=integer, s=string, d=decimal (double)
            // Ajusta según tu BD exacta. Aquí asumo decimales como 'd' o 's'
            $stmt->bind_param("iisssssssiidddsi", 
                $id_tipo_activo, 
                $es_accesorio,
                $descripcion,
                $marca,
                $modelo,
                $no_serie,
                $id_interno,
                $cpu_info,
                $monitor_info,
                $id_nave,
                $id_usuario,
                $moi,
                $depreciacion,
                $remanente,
                $observaciones,
                $id // El ID va al final por el WHERE
            );

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Activo actualizado']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error SQL: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error preparando consulta: ' . $conn->error]);
        }
    }

    if ($accion == 'getEmpleados') {
        include '../incidencias/conn.php';

            $sqlEmpleados = "SELECT noEmpleado, nombre FROM usuarios WHERE estatus = 1 ORDER BY nombre ASC";
            $result = $conn->query($sqlEmpleados);
            $empleados = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $empleados[] = $row;
                }
            }
            // Devolver la respuesta en formato JSON
            header('Content-Type: application/json');
            echo json_encode($empleados);
        $conn->close();
        exit;
    }

    if ($accion == 'getRegiones') {
        $sqlRegiones = "SELECT id, region FROM cat_regiones ORDER BY region ASC";
        $result = $conn->query($sqlRegiones);
        $regiones = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $regiones[] = $row;
            }
        }
        // Devolver la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($regiones);
        exit;
    }

    if ($accion =='obtener_fotos'){
        $idActivo = isset($_POST['id_activo']) ? $_POST['id_activo'] : 0;

        $sqlFotos = "SELECT id, ruta_foto FROM fotos_activos WHERE id_activo = ?";
        ///echo $sqlFotos;
        if ($stmt = $conn->prepare($sqlFotos)) {
            $stmt->bind_param("i", $idActivo);
            $stmt->execute();
            $result = $stmt->get_result();
            $fotos = array();

            while ($row = $result->fetch_assoc()) {
                $fotos[] = $row;
            }

            // Devolver JSON
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'fotos' => $fotos]);
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
        }
        exit;
    }

$conn->close();

?>