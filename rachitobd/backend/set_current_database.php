<?php
// set_current_database.php
session_start();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['db_connection'])) {
    $response['message'] = 'No hay una sesión de base de datos activa.';
    echo json_encode($response);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$newDatabase = $input['databaseName'] ?? null;

if ($newDatabase) {
    $_SESSION['db_connection']['database'] = $newDatabase;
    $response['success'] = true;
    $response['message'] = 'Base de datos actualizada con éxito.';
    $response['currentDatabase'] = $newDatabase;
} else {
    $response['message'] = 'Nombre de base de datos no proporcionado.';
}

echo json_encode($response);
?>