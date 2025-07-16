<?php
// execute_query.php

// Incluir configuración de depuración (¡siempre lo primero!)
require_once 'debug_config.php';

session_start(); // Iniciar la sesión PHP

header('Content-Type: application/json'); // Indicar que la respuesta será JSON

// Verificar si la información de conexión está en la sesión
if (!isset($_SESSION['db_connection']) || empty($_SESSION['db_connection'])) {
    echo json_encode(['success' => false, 'message' => 'No hay una sesión de base de datos activa. Por favor, inicie sesión.']);
    exit;
}



// Recuperar la información de conexión de la sesión
$serverName = $_SESSION['db_connection']['serverName'];
$database   = $_SESSION['db_connection']['database'];
$uid        = $_SESSION['db_connection']['uid'];
$pwd        = $_SESSION['db_connection']['pwd'];

$connectionInfo = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pwd,
    "CharacterSet" => "UTF-8"
];


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
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn === false) {
        // Si la conexión falla, obtener errores y devolverlos
        $errors = sqlsrv_errors();
        $errorMessage = "Error de conexión a la base de datos: ";
        if ($errors) { // Asegurarse de que $errors no sea nulo
            foreach ($errors as $error) {
                $errorMessage .= "[SQLSTATE: " . $error['SQLSTATE'] . "] Código: " . $error['code'] . " Mensaje: " . $error['message'] . "; ";
            }
        } else {
             $errorMessage .= "No se pudieron obtener detalles específicos del error de conexión.";
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
        if ($errors) { // Asegurarse de que $errors no sea nulo
            foreach ($errors as $error) {
                $errorMessage .= "[SQLSTATE: " . $error['SQLSTATE'] . "] Código: " . $error['code'] . " Mensaje: " . $error['message'] . "; ";
            }
        } else {
            $errorMessage .= "No se pudieron obtener detalles específicos del error de consulta.";
        }
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    }

    $columns = [];
    $rows = [];


   // Determinar si la consulta devuelve filas (SELECT) o afecta filas (INSERT/UPDATE/DELETE)
   if (sqlsrv_num_fields($stmt) > 0) { // Si hay metadatos de campo, es probablemente un SELECT
    // Si hay filas, obtener los nombres de las columnas
    $fieldMetadata = sqlsrv_field_metadata($stmt); // Obtener metadatos
    foreach ($fieldMetadata as $field) {
        $columns[] = $field['Name'];
    }

    // Obtener todas las filas y procesar datos problemáticos
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_NUMERIC)) {
        $processedRow = [];
        foreach ($row as $index => $value) {
            $fieldType = $fieldMetadata[$index]['Type']; // Acceder al tipo de campo

            // Si el valor es un recurso (stream para tipos como varbinary(max), image)
            if (is_resource($value)) {
                $processedRow[] = base64_encode(stream_get_contents($value));
            }
            // Manejar GUIDs (uniqueidentifier) o datos binarios que sqlsrv pueda convertir a string no UTF-8
            else if (
                is_string($value) &&
                !mb_check_encoding($value, 'UTF-8') && // No es una cadena UTF-8 válida
                (
                    $fieldType == SQLSRV_SQLTYPE_BINARY ||
                    $fieldType == SQLSRV_SQLTYPE_VARBINARY ||
                    $fieldType == SQLSRV_SQLTYPE_IMAGE ||
                    $fieldType == SQLSRV_SQLTYPE_UDT || // User-Defined Type, puede ser binario
                    $fieldType == SQLSRV_SQLTYPE_GUID // uniqueidentifier
                )
            ) {
                $processedRow[] = base64_encode($value);
            }
            // Si es un objeto DateTime (para tipos datetime, datetime2, date, etc.)
            else if ($value instanceof DateTime) {
                $processedRow[] = $value->format('Y-m-d H:i:s.u'); // Formatear a string con microsegundos
            }
            // Manejar float/double para evitar problemas de precisión en JSON
            else if (is_float($value)) {
                $processedRow[] = (string)$value; // Convertir a string para evitar problemas de precisión JSON
            }
            // Para todos los demás tipos (int, string, bool, null), pasarlos directamente
            else {
                $processedRow[] = ($value !== null) ? $value : null;
            }
        }
        $rows[] = $processedRow;
    }

    // ¡IMPORTANTE! ELIMINA O COMENTA LA SIGUIENTE LÍNEA:
    // print_r($rows); // <-- ESTA ES LA CAUSA DEL ERROR "Unexpected end of JSON input"

    echo json_encode(['success' => true, 'columns' => $columns, 'rows' => $rows]);

} else {
        // Para consultas que afectan filas (INSERT, UPDATE, DELETE, etc.)
        $rowsAffected = sqlsrv_rows_affected($stmt);
        if ($rowsAffected === false) {
             echo json_encode(['success' => true, 'message' => 'Consulta ejecutada con éxito. (Número de filas afectadas no disponible o no aplicable).']);
        } else {
             echo json_encode(['success' => true, 'message' => 'Consulta ejecutada con éxito.', 'rowsAffected' => $rowsAffected]);
        }
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