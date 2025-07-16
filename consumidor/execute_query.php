<?php
header('Content-Type: application/json'); // Indicar que la respuesta será JSON

// --- Configuración de la Conexión a SQL Server ---
// ¡IMPORTANTE! Reemplaza estos valores con los de tu entorno
$serverName = "192.168.8.97,1433"; // Ejemplo: "localhost\SQLEXPRESS,1433" o "192.168.1.100,1433"
$database = "delphinus_etravel_3f"; // Nombre de tu base de datos
$uid = "sa";           // Nombre de usuario de SQL Server
$pwd = "t1c9gvd$";        // Contraseña del usuario

// Construir el array de información de conexión
// Asegúrate de que 'CharacterSet' sea adecuado para tus datos (UTF-8 es buena práctica)
$connectionInfo = array(
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pwd,
    "CharacterSet" => "UTF-8"
);

// --- Obtener la consulta SQL del frontend ---
$input = json_decode(file_get_contents('php://input'), true);
$sqlQuery = $input['query'] ?? '';

if (empty($sqlQuery)) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó ninguna consulta SQL.']);
    exit;
}

$conn = null; // Inicializar conexión a null
try {
    // Establecer la conexión con SQL Server
    // Usamos sqlsrv_connect por ser el driver de Microsoft, puedes cambiar a PDO si lo prefieres
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn === false) {
        // Si la conexión falla, obtener errores y devolverlos
        $errors = sqlsrv_errors();
        $errorMessage = "Error de conexión a la base de datos: ";
        foreach ($errors as $error) {
            $errorMessage .= "[SQLSTATE: " . $error['SQLSTATE'] . "] Código: " . $error['code'] . " Mensaje: " . $error['message'] . "; ";
        }
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    }

    // Preparar y ejecutar la consulta
    $stmt = sqlsrv_query($conn, $sqlQuery);

    if ($stmt === false) {
        // Si la ejecución de la consulta falla, obtener errores
        $errors = sqlsrv_errors();
        $errorMessage = "Error al ejecutar la consulta SQL: ";
        foreach ($errors as $error) {
            $errorMessage .= "[SQLSTATE: " . $error['SQLSTATE'] . "] Código: " . $error['code'] . " Mensaje: " . $error['message'] . "; ";
        }
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    }

    $columns = [];
    $rows = [];

    // Determinar si la consulta devuelve filas (SELECT) o afecta filas (INSERT/UPDATE/DELETE)
    if (sqlsrv_has_rows($stmt)) {
        // Si hay filas, obtener los nombres de las columnas
        foreach (sqlsrv_field_metadata($stmt) as $field) {
            $columns[] = $field['Name'];
        }

        // Obtener todas las filas
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_NUMERIC)) {
            $rows[] = $row;
        }
        echo json_encode(['success' => true, 'columns' => $columns, 'rows' => $rows]);

    } else {
        // Para consultas que afectan filas (INSERT, UPDATE, DELETE, etc.)
        $rowsAffected = sqlsrv_rows_affected($stmt);
        echo json_encode(['success' => true, 'message' => 'Consulta ejecutada con éxito.', 'rowsAffected' => $rowsAffected]);
    }

} catch (Exception $e) {
    // Capturar cualquier excepción inesperada
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
} finally {
    // Cerrar la conexión si está abierta
    if ($conn) {
        sqlsrv_close($conn);
    }
}
?>