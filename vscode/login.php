<?php
// login.php

require_once 'debug_config.php'; // Asegúrate de tener esta línea al inicio
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$serverName = $input['serverName'] ?? '';
$database   = $input['database'] ?? '';
$uid        = $input['uid'] ?? '';
$pwd        = $input['pwd'] ?? '';

// --- RECUPERAR VALORES DE ENCRYPT Y TRUSTSERVERCERTIFICATE DEL INPUT JSON ---
// Si los checkboxes están marcados, llegarán como true. Si no, no estarán presentes
// o podrían llegar como false si se envían explícitamente.
// Usamos coalescencia nula (??) para asegurar un valor predeterminado si no se envían.
$encrypt = isset($input['encrypt']) ? (bool)$input['encrypt'] : false; // Asegura que sea booleano
$trustServerCertificate = isset($input['trustServerCertificate']) ? (bool)$input['trustServerCertificate'] : false; // Asegura que sea booleano
// --- FIN DE RECUPERACIÓN ---
if(empty($database))
{
    $database = 'master';
}
if (empty($serverName) || empty($database) || empty($uid) || empty($pwd)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos de conexión.']);
    exit;
}

$connectionInfo = array(
    "Database"       => $database,
    "Uid"            => $uid,
    "PWD"            => $pwd,
    "CharacterSet"   => "UTF-8",
    "Encrypt"        => $encrypt,
    "TrustServerCertificate" => $trustServerCertificate
);

$conn = null;
try {
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn === false) {
        $errors = sqlsrv_errors();
        $errorMessage = "Error de conexión: ";
        if ($errors) {
            foreach ($errors as $error) {
                $errorMessage .= "[SQLSTATE: " . $error['SQLSTATE'] . "] Código: " . $error['code'] . " Mensaje: " . $error['message'] . "; ";
            }
        } else {
            $errorMessage .= "No se pudieron obtener detalles específicos del error de conexión. Verifica los datos e inténtalo de nuevo.";
        }
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit;
    }

    // Si la conexión es exitosa, guarda la información de conexión en la sesión
    $_SESSION['db_connection'] = [
        'serverName' => $serverName,
        'database'   => $database,
        'uid'        => $uid,
        'pwd'        => $pwd,
        'encrypt'    => $encrypt,
        'trustServerCertificate' => $trustServerCertificate
    ];

    echo json_encode(['success' => true, 'message' => 'Conexión exitosa. ¡Bienvenido!', 'redirect' => 'sql_editor.html']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error inesperado al intentar conectar: ' . $e->getMessage()]);
} finally {
    if ($conn) {
        sqlsrv_close($conn);
    }
}