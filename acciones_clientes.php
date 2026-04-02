<?php
include_once 'conn.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'subir_csv_clientes':
        if (!isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'Error al subir el archivo.']);
            exit;
        }

        $file = $_FILES['archivo_csv']['tmp_name'];
        $handle = fopen($file, "r");
        
        // Saltar la primera línea (cabeceras)
        fgetcsv($handle, 1000, ",");

        $conn->begin_transaction();

        try {
            $sql = "INSERT INTO clientes 
                    (id, region, estado, municipio, parque_ind, zona, idVendedorAsig, Ranking, 
                     cliente_largo, cliente_corto, cliente, CP, calle, ciudad, created, 
                     numero, credit_hold, pagoAnticipado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    region=VALUES(region), estado=VALUES(estado), municipio=VALUES(municipio), 
                    parque_ind=VALUES(parque_ind), zona=VALUES(zona), idVendedorAsig=VALUES(idVendedorAsig),
                    Ranking=VALUES(Ranking), cliente_largo=VALUES(cliente_largo), 
                    cliente_corto=VALUES(cliente_corto), cliente=VALUES(cliente), 
                    CP=VALUES(CP), calle=VALUES(calle), ciudad=VALUES(ciudad), 
                    numero=VALUES(numero), credit_hold=VALUES(credit_hold), pagoAnticipado=VALUES(pagoAnticipado)";

            $stmt = $conn->prepare($sql);
            $count = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Ajustamos los índices según el orden del CSV:
                // 0:IDCLTE, 1:REG, 2:ESTADO, 3:MUNICIPIO, 4:PARQUE-IND, 5:ZONA, 6:IDVENDASIG, 7:RNK, 
                // 8:CLIENTE_LARGO, 9:CLIENTE_CORTO, 10:CLIENTE, 11:CP, 12:CALLE, 13:CIUDAD, 14:FechaCreacion, 
                // 15:NUMERO, 16:CREDIT_HOLD, 17:pagoAnticipado
                
                $stmt->bind_param("isssssisssssssssii", 
                    $data[0], $data[1], $data[2], $data[3], $data[4],
                    $data[5], $data[6], $data[7], $data[8], $data[9],
                    $data[10], $data[11], $data[12], $data[13], $data[14],
                    $data[15], $data[16], $data[17]
                );
                $stmt->execute();
                $count++;
            }

            fclose($handle);
            $conn->commit();

            echo json_encode(['status' => 'success', 'message' => "Se importaron $count clientes correctamente."]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error al procesar: ' . $e->getMessage()]);
        }
        exit;
        break;
}