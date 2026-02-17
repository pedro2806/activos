<?php
// 1. Configuración de conexión
$host = 'localhost';
$db   = 'mess_activos_fijos';
$user = 'mess_incidencias';
$pass = 'Pipmytrade123';
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (\PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 2. Archivo
$archivo = 'ActivosTI.csv';

if (($handle = fopen($archivo, "r")) !== FALSE) {
    // SQL preparado
    $sql = "INSERT INTO activos (
                folio, id_tipo_activo, descripcion, marca, modelo, 
                no_serie, id_interno, id_usuario, nombre_usuario, id_nave, 
                ubicacion, cpu_info, monitor_info, cantidad, moi, 
                costo, depreciacion, remanente, observaciones, created_at, 
                es_accesorio, estatus
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $fila = 0;

    while (($datos = fgetcsv($handle, 1000, ",")) !== FALSE) { // Delimitador ahora es COMA
        $fila++;

        // Limpieza de datos: Convertir vacíos a NULL para la BD
        foreach ($datos as $key => $valor) {
            $valor = trim($valor);
            $datos[$key] = ($valor === '' || $valor === 'NA' || $valor === 'NaN') ? null : $valor;
        }

        try {
            // Ejecutamos con los 22 campos del CSV
            $stmt->execute([
                $datos[0],  $datos[1],  $datos[2],  $datos[3],  $datos[4],
                $datos[5],  $datos[6],  $datos[7],  $datos[8],  $datos[9],
                $datos[10], $datos[11], $datos[12], $datos[13], $datos[14],
                $datos[15], $datos[16], $datos[17], $datos[18], $datos[19],
                $datos[20], $datos[21]
            ]);
        } catch (Exception $e) {
            echo "Error en fila $fila (Folio: {$datos[0]}): " . $e->getMessage() . "<br>";
        }
    }
    fclose($handle);
    echo "Importación exitosa. Total de registros: $fila";
}
?>