<?php
class ActivosAPI {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Respuesta estandarizada
    private function response($status, $message, $data = null) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function registrarActivo($post, $files) {
        $tipo       = $post['selectTipoActivo'] ?? '';
        $desc       = $post['descripcion'] ?? '';
        $marca      = $post['marca'] ?? '';
        $modelo     = $post['modelo'] ?? '';
        $serie      = $post['no_serie'] ?? '';
        $idInt      = $post['id_interno'] ?? '';
        $usuario    = !empty($post['usuario']) ? $post['usuario'] : null;
        $nave       = !empty($post['selectNave']) ? $post['selectNave'] : null;
        $cpu        = $post['cpuInfo'] ?? '';
        $mon        = $post['monitorInfo'] ?? '';
        $moi        = $post['moi'] ?? 0;
        $costo      = $post['costo'] ?? 0;
        $depre      = $post['depreciacion'] ?? 0;
        $rem        = $post['remanente'] ?? 0;
        $obs        = $post['observaciones'] ?? '';
        $accesorio  = $post['EsAccesorio'] ?? 0;
        $ubicacion  = $post['ubicacion'] ?? '';

        if (empty($tipo) || empty($desc)) {
            $this->response('error', 'Faltan campos obligatorios.');
        }

        $sql = "INSERT INTO activos(id_tipo_activo, descripcion, marca, modelo, no_serie, id_interno, id_usuario, id_nave, cpu_info, monitor_info, cantidad, moi, costo, depreciacion, remanente, observaciones, created_at, es_accesorio, estatus, ubicacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, NOW(), ?, 1, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssssiissiddddsis", $tipo, $desc, $marca, $modelo, $serie, $idInt, $usuario, $nave, $cpu, $mon, $moi, $costo, $depre, $rem, $obs, $accesorio, $ubicacion);

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $this->subirFotos($id, $files);
            $this->response('success', 'Activo registrado correctamente.');
        } else {
            $this->response('error', 'Error BD: ' . $stmt->error);
        }
    }

    private function subirFotos($id, $files) {
        if (!isset($files['fotos'])) return;
        $dir = 'imgActivos/';
        if (!file_exists($dir)) mkdir($dir, 0777, true);

        for ($i = 0; $i < count($files['fotos']['name']); $i++) {
            if ($files['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = pathinfo($files['fotos']['name'][$i], PATHINFO_EXTENSION);
                $name = "activo_{$id}_" . time() . "_{$i}.{$ext}";
                $path = $dir . $name;

                if (move_uploaded_file($files['fotos']['tmp_name'][$i], $path)) {
                    $st = $this->conn->prepare("INSERT INTO fotos_activos(id_activo, ruta_foto) VALUES (?, ?)");
                    $st->bind_param("is", $id, $path);
                    $st->execute();
                }
            }
        }
    }

    public function listarActivos() {
        $sql = "SELECT a.*, ta.nombre as tipo_activo, u.nombre AS usuario, n.nombre AS nave 
                FROM activos a 
                LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id 
                LEFT JOIN mess_rrhh.usuarios u ON a.id_usuario = u.id_usuario
                LEFT JOIN cat_naves n ON a.id_nave = n.id
                WHERE a.estatus = 1 ORDER BY a.id DESC";
        $res = $this->conn->query($sql);
        $this->response('success', 'Lista cargada', $res->fetch_all(MYSQLI_ASSOC));
    }

    public function detalleActivo($id) {
        $sql = "SELECT a.*, ta.nombre as tipo_activo, r.region FROM activos a 
                LEFT JOIN cat_tipos_activos ta ON a.id_tipo_activo = ta.id 
                LEFT JOIN cat_naves n ON a.id_nave = n.id
                LEFT JOIN cat_regiones r ON n.id_region = r.id
                WHERE a.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $this->response($res ? 'success' : 'error', $res ? 'Detalle' : 'No encontrado', $res);
    }

    public function editarActivo($post) {
        $sql = "UPDATE activos SET id_tipo_activo=?, es_accesorio=?, descripcion=?, marca=?, modelo=?, no_serie=?, id_interno=?, cpu_info=?, monitor_info=?, id_nave=?, id_usuario=?, moi=?, depreciacion=?, remanente=?, observaciones=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisssssssiidddsi", $post['id_tipo_activo'], $post['es_accesorio'], $post['descripcion'], $post['marca'], $post['modelo'], $post['no_serie'], $post['id_interno'], $post['cpu_info'], $post['monitor_info'], $post['id_nave'], $post['id_usuario'], $post['moi'], $post['depreciacion'], $post['remanente'], $post['observaciones'], $post['id']);
        
        if ($stmt->execute()) $this->response('success', 'Activo actualizado');
        else $this->response('error', $stmt->error);
    }

    public function eliminarActivo($id) {
        $stmt = $this->conn->prepare("UPDATE activos SET estatus = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $this->response($stmt->affected_rows > 0 ? 'success' : 'error', 'Estatus actualizado');
    }

    public function obtenerFotos($id) {
        $stmt = $this->conn->prepare("SELECT id, ruta_foto FROM fotos_activos WHERE id_activo = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $this->response('success', 'Fotos', $stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    }
}