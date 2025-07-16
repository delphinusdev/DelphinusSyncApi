<?php
// execute_query.php

require_once 'debug_config.php';
require_once 'db_connection.php'; // Incluir el nuevo archivo de conexión
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['db_connection']) || empty($_SESSION['db_connection'])) {
    echo json_encode(['success' => false, 'message' => 'No hay una sesión de base de datos activa. Por favor, inicie sesión.']);
    exit;
}

$serverName = $_SESSION['db_connection']['serverName'];
$database = $_SESSION['db_connection']['database'];
$uid = $_SESSION['db_connection']['uid'];
$pwd = $_SESSION['db_connection']['pwd'];
$trustServerCertificate = $_SESSION['db_connection']['trustServerCertificate'] ?? false; // Recuperar de la sesión

$input = json_decode(file_get_contents('php://input'), true);
$sqlQuery = $input['query'] ?? '';

if (empty($sqlQuery)) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó ninguna consulta SQL.']);
    exit;
}

$pdo = null;
try {
    // Usar la función getPdoConnection para establecer la conexión
    $pdo = getPdoConnection($serverName, $database, $uid, $pwd, $trustServerCertificate);

    $stmt = $pdo->query($sqlQuery);

    // Determine if it's a SELECT query or a DML statement
    if ($stmt->columnCount() > 0) {
        // It's a SELECT query, fetch results
        $columns = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $meta = $stmt->getColumnMeta($i);
            $columns[] = $meta['name'];
        }

        $rows = [];
        // *** CAMBIO CLAVE: Fetch PDO::FETCH_ASSOC directly ***
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // Fetch associative array
            $processedRow = [];
            foreach ($row as $key => $value) {
                // Handle different data types for JSON encoding
                if (is_a($value, 'DateTime')) {
                    $processedRow[$key] = $value->format('Y-m-d H:i:s.v'); // Format DateTime objects
                } elseif (is_float($value)) {
                    $processedRow[$key] = (string)$value; // Convert float to string to avoid precision issues
                } else {
                    $processedRow[$key] = $value; // Pass other types directly
                }
            }
            $rows[] = $processedRow;
        }
        echo json_encode(['success' => true, 'columns' => $columns, 'rows' => $rows]);

    } else {
        // It's a DML query (INSERT, UPDATE, DELETE, etc.)
        $rowsAffected = $stmt->rowCount();
        echo json_encode(['success' => true, 'message' => 'Consulta ejecutada con éxito.', 'rowsAffected' => $rowsAffected]);
    }

} catch (Exception $e) { // Capturar tanto PDOException como la excepción de la extensión no cargada
    error_log("Error en execute_query.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta: ' . $e->getMessage()]);
    exit;
} finally {
    $pdo = null; // Close PDO connection
}
?>