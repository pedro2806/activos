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
    $region = isset($_POST['region']) ? $_POST['region'] : '';
    $nave = isset($_POST['selectNave']) ? $_POST['selectNave'] : '';
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $moi = isset($_POST['moi']) ? $_POST['moi'] : '';
    $costo = isset($_POST['costo']) ? floatval($_POST['costo']) : 0.0;
    $depreciacion = isset($_POST['depreciacion']) ? floatval($_POST['depreciacion']) : 0.0;
    $remanente = isset($_POST['remanente']) ? floatval($_POST['remanente']) : 0.0;
    $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';
    $EsAccesorio = isset($_POST['EsAccesorio']) ? intval($_POST['EsAccesorio']) : 0;
    $ubicacion = isset($_POST['ubicacion']) ? $_POST['ubicacion'] : '';
    $fechaAdquisicion = isset($_POST['fecha_adquisicion']) ? $_POST['fecha_adquisicion'] : null;
    $fotos = isset($_POST['fotos']) ? $_POST['fotos'] : '';

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

    if ($accion == 'nuevoActivo') {
        
        // 1. RECIBIR VARIABLES (Usamos $_POST directo porque viene de FormData)        
        $tipoActivo     = $_POST['selectTipoActivo'] ?? '';
        $descripcion    = $_POST['descripcion'] ?? '';
        $marca          = $_POST['marca'] ?? '';
        $modelo         = $_POST['modelo'] ?? '';
        $noSerie        = $_POST['no_serie'] ?? '';
        $idInterno      = $_POST['id_interno'] ?? '';
        $usuario        = $_POST['slcResponsable'] ?? null;
        $nave           = $_POST['selectNave'] ?? null;
        $cpuInfo        = $_POST['cpu_info'] ?? '';
        $monitorInfo    = $_POST['monitor_info'] ?? '';
        $moi            = $_POST['moi'] ?? 0;
        $costo          = $_POST['costo'] ?? 0;
        $depreciacion   = $_POST['depreciacion'] ?? 0;
        $remanente      = $_POST['remanente'] ?? 0;
        $observaciones  = $_POST['observaciones'] ?? '';
        $EsAccesorio    = $_POST['es_accesorio'] ?? 0;
        $ubicacion      = $_POST['ubicacion'] ?? '';
        $region         = $_POST['selectRegion'] ?? ''; 
        $fechaAdquisicion = $_POST['fecha_adquisicion'] ?? null;                
        $cantidad = $_POST['cantidad'] ?? 1; // Asumimos que si no viene, es 1
        $estatus = 1; // Activo
        $esPrestamo = $_POST['es_prestamo'] ?? 0;        

        // Validar datos obligatorios
        if (empty($tipoActivo) || empty($descripcion) || empty($marca) || empty($nave)) {   
            $response = array(
                'status' => 'error', 
                'message' => 'Faltan datos obligatorios: Tipo de Activo, Descripción, Marca o Nave.'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // 2. INSERT SEGURO (Prepared Statement)        
        $sqlInsert = "INSERT INTO activos(
                            id_tipo_activo, descripcion, marca, modelo, no_serie, 
                            id_interno, id_usuario, id_nave, cpu_info, monitor_info, 
                            cantidad, moi, costo, depreciacion, remanente, 
                            observaciones, es_accesorio, estatus, ubicacion, region, 
                            fecha_adquisicion, fecha_registro, registrado_por, prestamo
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

        $stmt = $conn->prepare($sqlInsert);
        
        if (!$stmt) {
    
            echo json_encode(['status' => 'error', 'message' => 'Error en prepare: ' . $conn->error]);
            exit;
        }        

            
        $stmt->bind_param("isssssiissiddddsiisisii", 
            $tipoActivo, 
            $descripcion, 
            $marca, 
            $modelo, 
            $noSerie, 
            $idInterno, 
            $usuario, 
            $nave, 
            $cpuInfo, 
            $monitorInfo, 
            $cantidad, 
            $moi, 
            $costo, 
            $depreciacion, 
            $remanente, 
            $observaciones, 
            $EsAccesorio, 
            $estatus, 
            $ubicacion, 
            $region, 
            $fechaAdquisicion,
            $noEmpleado,
            $esPrestamo
        );

        if ($stmt->execute()) {
            $ultimoId = $conn->insert_id; 
            $errorFotos = false;

            // --- REGISTRAR EN EL LOG ---
            $idNuevoActivo = $conn->insert_id;
            $idUsuarioAccion = $noEmpleado;                        
            registrarLog($conn, $idNuevoActivo, $idUsuarioAccion, 'CREADO', "Se dio de alta un nuevo activo en el sistema.");

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
                        $tipoArchivo     = $fotos['type'][$i];
                        
                        // Generar nombre único: activo_15_TIMESTAMP_nombre.jpg
                        $nuevoNombre = 'activo_' . $ultimoId . '_' . time() . '_' . $i;
                        $rutaDestino = $directorio . $nuevoNombre.$tipoArchivo; // Guardamos con su extensión original

                        if (move_uploaded_file($tmpName, $rutaDestino)) {
                            // Insertar ruta en BD
                            // Aquí podemos usar query normal o prepare (prepare es mejor)
                            $sqlFoto = "INSERT INTO fotos_activos(id_activo, ruta_foto) VALUES (?, ?)";
                            $stmtFoto = $conn->prepare($sqlFoto);
                            $stmtFoto->bind_param("is", $ultimoId, $rutaDestino);
                            if($stmtFoto->execute()) {
                                $fotosSubidasExito++; 
                                
                                // --- REGISTRAR EN EL LOG ---
                                $idUsuarioAccion = $noEmpleado;
                                registrarLog($conn, $ultimoId, $idUsuarioAccion, 'FOTO_AGREGADA', "Se subió la imagen: $nuevoNombre");
                            }
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
                        a.costo, a.depreciacion, a.remanente, a.observaciones, a.fecha_adquisicion, a.ubicacion, DATE_FORMAT(a.fecha_registro, '%Y-%m-%d') AS fecha_registro,
                            a.es_accesorio, a.region, a.id_usuario, a.id_nave, a.id_tipo_activo, a.prestamo,
                            (SELECT pa.estatus FROM prestamos_activos pa WHERE pa.id_activo = a.id AND pa.estatus = 1 LIMIT 1) AS estatus_prestamo
                    FROM activos a
                    LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id
                    LEFT JOIN mess_rrhh.usuarios u ON (
                        (a.id_tipo_activo = 1 AND a.id_usuario = u.noEmpleado) OR 
                        (a.id_tipo_activo != 1 AND a.id_usuario = u.id_usuario)
                    )
                    LEFT JOIN cat_naves n ON a.id_nave = n.id                                        
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
                        a.fecha_adquisicion,
                        a.es_accesorio, 
                        a.region as id_region,
                        a.id_usuario, 
                        a.id_nave, a.id_tipo_activo, a.prestamo
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
        
        // 1. Recolección de datos (con manejo de nulos por seguridad)
        $id             = $_POST['id'] ?? 0;
        $id_tipo_activo = $_POST['editTipoActivo'] ?? 0;
        $descripcion    = $_POST['descripcion'] ?? '';
        $marca          = $_POST['marca'] ?? '';
        $modelo         = $_POST['modelo'] ?? '';
        $no_serie       = $_POST['no_serie'] ?? '';
        $id_interno     = $_POST['id_interno'] ?? '';
        
        // Checkbox: Si viene post, es 1, si no, es 0
        $es_accesorio   = isset($_POST['es_accesorio']) ? 1 : 0;
        $es_prestamo     = isset($_POST['es_prestamo']) ? 1 : 0;
        
        $cpu_info       = $_POST['cpu_info'] ?? ''; 
        $monitor_info   = $_POST['monitor_info'] ?? '';
        
        $id_nave        = $_POST['id_nave'] ?? 0;
        $id_usuario     = $_POST['editSlcResponsable'] ?? 0;
        
        $moi            = $_POST['moi'] ?? 0;
        $depreciacion   = $_POST['depreciacion'] ?? 0;
        $remanente      = $_POST['remanente'] ?? 0;
        $observaciones  = $_POST['observaciones'] ?? '';

        $fechaAdquisicion = empty($_POST['editFechaAdquisicion']) ? null : $_POST['editFechaAdquisicion'];
        $region         = $_POST['selectRegion'] ?? null; 

        // Validar que tengamos un ID válido para actualizar
        if ($id == 0) {
            echo json_encode(['status' => 'error', 'message' => 'No se recibió un ID válido para actualizar.']);
            exit;
        }

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
                    observaciones = ?,
                    fecha_adquisicion = ?,
                    region = ?,
                    prestamo = ?
                WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            
            // CORREGIDO: 18 letras exactas para 18 variables
            // i=int, s=string, d=double (decimales)
            // Asumo que 'region' es un ID numérico (i). Si guardas texto, cambia la penúltima 'i' por 's'.
            $stmt->bind_param("iisssssssiidddssiii", 
                $id_tipo_activo,    // i
                $es_accesorio,      // i
                $descripcion,       // s
                $marca,             // s
                $modelo,            // s
                $no_serie,          // s
                $id_interno,        // s
                $cpu_info,          // s
                $monitor_info,      // s
                $id_nave,           // i
                $id_usuario,        // i
                $moi,               // d
                $depreciacion,      // d
                $remanente,         // d
                $observaciones,     // s
                $fechaAdquisicion,  // s 
                $region,            // i
                $es_prestamo,       // i 
                $id                 // i (El ID va al final por el WHERE)
            );

            if ($stmt->execute()) {
                $idUsuarioAccion = $noEmpleado;
    
                // --- REGISTRAR EN EL LOG ---
                registrarLog($conn, $id, $idUsuarioAccion, 'EDITADO', "Se actualizaron los datos generales del activo.");

                echo json_encode(['status' => 'success', 'message' => 'Activo actualizado correctamente.']);
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

    if($accion == 'getFotos') {
        $idActivo = isset($_POST['idActivo']) ? $_POST['idActivo'] : 0;

        $sqlFotos = "SELECT id, ruta_foto FROM fotos_activos WHERE id_activo = ?";
        
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
    // Acción para eliminar fotos
    if($accion == 'eliminarFoto') {
        $idFoto = isset($_POST['idFoto']) ? $_POST['idFoto'] : 0;

        // Primero obtenemos la ruta de la foto para eliminar el archivo físico
        $sqlGetFoto = "SELECT ruta_foto FROM fotos_activos WHERE id = ?";
        if ($stmtGet = $conn->prepare($sqlGetFoto)) {
            $stmtGet->bind_param("i", $idFoto);
            $stmtGet->execute();
            $result = $stmtGet->get_result();

            if ($row = $result->fetch_assoc()) {
                $rutaFoto = $row['ruta_foto'];

                // Eliminamos el archivo físico
                if (file_exists($rutaFoto)) {
                    unlink($rutaFoto);
                }

                // Luego eliminamos el registro de la base de datos
                $sqlDelete = "DELETE FROM fotos_activos WHERE id = ?";
                if ($stmtDelete = $conn->prepare($sqlDelete)) {
                    $stmtDelete->bind_param("i", $idFoto);
                    if ($stmtDelete->execute()) {
                        // --- REGISTRAR EN EL LOG ---
                        $idUsuarioAccion = $noEmpleado;
                        //Obtener id de activo desde URL
                        $nombreFoto = substr($rutaFoto, strrpos($rutaFoto, 'imgActivos/') + 11);
                        $idActivo = isset($_POST['idActivo']) ? $_POST['idActivo'] : 0;
                        registrarLog($conn, $idActivo, $idUsuarioAccion, 'FOTO_ELIMINADA', "Se eliminó imagen: $nombreFoto");                        

                        echo json_encode(['status' => 'success', 'message' => 'Foto eliminada']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar de BD']);
                    }
                    $stmtDelete->close();
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error en consulta de eliminación']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Foto no encontrada']);
            }
            $stmtGet->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en consulta para obtener foto']);
        }
        exit;
    }

    //subir fotos para activo editado
// subir fotos para activo editado
    if($accion == 'subirFotos') {
        
        $idActivo = (int) $_POST['idActivo']; // Forzamos a entero por seguridad

        //Validar que vengan los datos esperados
        if (!isset($_FILES['fotos']) || !isset($_POST['idActivo'])) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos para subir las fotos']);
            exit;
        }

        //Validar que la suma de las fotos en BD y la nueva sea mejor o igual a 3
        $sqlCountFotos = "SELECT COUNT(*) as total FROM fotos_activos WHERE id_activo = ?";
        if ($stmtCount = $conn->prepare($sqlCountFotos)) {
            $stmtCount->bind_param("i", $idActivo);
            $stmtCount->execute();
            $result = $stmtCount->get_result();
            $row = $result->fetch_assoc();
            $totalFotosBD = (int) $row['total'];            
            $stmtCount->close();
            $totalNuevasFotos = count($_FILES['fotos']['name']);
            if ($totalFotosBD + $totalNuevasFotos > 3) {
                echo json_encode(['status' => 'error', 'message' => "No se pueden subir más de 3 fotos por activo. Actualmente tiene $totalFotosBD fotos. Intente subir menos fotos."]);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en consulta para contar fotos']);
            exit;
        }

        $fotos = $_FILES['fotos'];
        
        $totalArchivos = count($fotos['name']);
        
        $fotosSubidasExito = 0; // Contador para saber si todo salió bien

        $directorio = 'imgActivos/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        // Extensiones permitidas (Seguridad)
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        for ($i = 0; $i < $totalArchivos; $i++) {
            if ($fotos['error'][$i] === UPLOAD_ERR_OK && !empty($fotos['name'][$i])) {
                
                $nombreOriginal = $fotos['name'][$i];
                $tmpName        = $fotos['tmp_name'][$i];
                
                // Extraer la extensión real del archivo (ej: "jpg")
                $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

                // Validar que sea una imagen
                if (in_array($extension, $extensionesPermitidas)) {
                    
                    // Crear el nuevo nombre agregando el punto y la extensión CORRECTA
                    $nuevoNombre = 'activo_' . $idActivo . '_' . time() . '_' . $i . '.' . $extension;
                    $rutaDestino = $directorio . $nuevoNombre;

                    if (move_uploaded_file($tmpName, $rutaDestino)) {
                        $sqlFoto = "INSERT INTO fotos_activos(id_activo, ruta_foto) VALUES (?, ?)";
                        $stmtFoto = $conn->prepare($sqlFoto);
                        $stmtFoto->bind_param("is", $idActivo, $rutaDestino);
                        
                        if($stmtFoto->execute()) {
                            $fotosSubidasExito++; // Sumamos un éxito
                            // --- REGISTRAR EN EL LOG ---
                            $idUsuarioAccion = $noEmpleado;
                            registrarLog($conn, $idActivo, $idUsuarioAccion, 'FOTO_AGREGADA', "Se subió la imagen: $nuevoNombre");
                        }
                        $stmtFoto->close();
                    }
                }
            }
        }

        // 3. Responder según el resultado
        if ($fotosSubidasExito > 0) {
            echo json_encode(['status' => 'success', 'message' => "$fotosSubidasExito fotos subidas correctamente"]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo subir ninguna foto válida. Verifique el formato.']);
        }
        exit;
    }

    

$conn->close();

?>