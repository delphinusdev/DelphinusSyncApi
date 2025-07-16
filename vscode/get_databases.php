<?php
// get_databases.php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['db_connection']) || empty($_SESSION['db_connection'])) {
    echo json_encode(['success' => false, 'message' => 'No hay una sesión de base de datos activa. Por favor, inicie sesión.']);
    exit;
}

$serverName = $_SESSION['db_connection']['serverName'];
// For listing databases, we can initially connect to 'master' or just let SQL Server connect to the default.
// However, to reliably query sys.databases, connecting to 'master' or a common system DB is safer.
$initialDatabase = 'master'; // Or the database your connecting user has permissions to see sys.databases from
$uid = $_SESSION['db_connection']['uid'];
$pwd = $_SESSION['db_connection']['pwd'];

$connectionInfo = [
    "Database" => $initialDatabase, // Connect to master to list all databases
    "Uid" => $uid,
    "PWD" => $pwd,
    "CharacterSet" => "UTF-8",
    "ReturnDatesAsStrings" => true // Important for date handling
];

$conn = sqlsrv_connect($serverName, $connectionInfo);

$databases = [];
$currentDatabaseInSession = $_SESSION['db_connection']['database'] ?? null;

if ($conn) {
    // Query sys.databases to get all accessible databases
    $query = "SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb') ORDER BY name;";
    $stmt = sqlsrv_query($conn, $query);

    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $databases[] = $row['name'];
        }
        sqlsrv_free_stmt($stmt);
    } else {
        // Handle query error
        error_log("Error querying databases: " . print_r(sqlsrv_errors(), true));
    }
    sqlsrv_close($conn);
} else {
    // Handle connection error
    error_log("Error connecting to SQL Server for database list: " . print_r(sqlsrv_errors(), true));
}

echo json_encode([
    'success' => true,
    'databases' => $databases,
    'currentDatabase' => $currentDatabaseInSession
]);
?>