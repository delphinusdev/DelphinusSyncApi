<?php
// get_databases.php
session_start();
require_once 'db_connection.php'; // Incluir el nuevo archivo de conexión

header('Content-Type: application/json');

if (!isset($_SESSION['db_connection']) || empty($_SESSION['db_connection'])) {
    echo json_encode(['success' => false, 'message' => 'No hay una sesión de base de datos activa. Por favor, inicie sesión.']);
    exit;
}

$serverName = $_SESSION['db_connection']['serverName'];
$uid = $_SESSION['db_connection']['uid'];
$pwd = $_SESSION['db_connection']['pwd'];
$trustServerCertificate = $_SESSION['db_connection']['trustServerCertificate'] ?? false; // Recuperar de la sesión

$initialDatabase = 'master'; // Conectar a master para listar todas las bases de datos

$pdo = null;
try {
    // Usar la función getPdoConnection para establecer la conexión
    $pdo = getPdoConnection($serverName, $initialDatabase, $uid, $pwd, $trustServerCertificate);

    $databases = [];
    $currentDatabaseInSession = $_SESSION['db_connection']['database'] ?? null;

    $query = "SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb') ORDER BY name;";
    $stmt = $pdo->query($query);

    while ($row = $stmt->fetch()) {
        $databases[] = $row['name'];
    }

    echo json_encode([
        'success' => true,
        'databases' => $databases,
        'currentDatabase' => $currentDatabaseInSession
    ]);

} catch (Exception $e) { // Capturar tanto PDOException como la excepción de la extensión no cargada
    error_log("Error en get_databases.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener bases de datos: ' . $e->getMessage()]);
    exit;
} finally {
    $pdo = null; // Cerrar la conexión PDO
}
?>