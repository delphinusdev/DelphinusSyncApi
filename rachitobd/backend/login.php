<?php
// login.php

require_once 'debug_config.php'; // Asegúrate de tener esta línea al inicio
require_once 'db_connection.php'; // Incluir el nuevo archivo de conexión
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$serverName = $input['serverName'] ?? '';
$database   = $input['database'] ?? '';
$uid        = $input['uid'] ?? '';
$pwd        = $input['pwd'] ?? '';

$encrypt = isset($input['encrypt']) ? (bool)$input['encrypt'] : false;
$trustServerCertificate = isset($input['trustServerCertificate']) ? (bool)$input['trustServerCertificate'] : false;

if(empty($database)) {
    $database = 'master'; // Conectar a master si no se especifica una base de datos
}

if (empty($serverName) || empty($uid) || empty($pwd)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos de conexión (Servidor, Usuario, Contraseña).']);
    exit;
}

try {
    // Usar la función getPdoConnection para establecer la conexión
    $pdo = getPdoConnection($serverName, $database, $uid, $pwd, $trustServerCertificate);

    // Si la conexión es exitosa, guarda la información de conexión en la sesión
    $_SESSION['db_connection'] = [
        'serverName' => $serverName,
        'database'   => $database,
        'uid'        => $uid,
        'pwd'        => $pwd,
        'encrypt'    => $encrypt,
        'trustServerCertificate' => $trustServerCertificate // Guardar esto también en sesión
    ];

    echo json_encode(['success' => true, 'message' => 'Conexión exitosa. ¡Bienvenido!', 'redirect' => 'sql_editor.html']);

} catch (Exception $e) { // Capturar tanto PDOException como la excepción de la extensión no cargada
    error_log("Error en login.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al intentar conectar: ' . $e->getMessage()]);
    exit;
} finally {
    $pdo = null; // Cerrar la conexión PDO
}
?>