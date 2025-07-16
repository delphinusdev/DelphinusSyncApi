<?php
// check_session.php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['db_connection']) && !empty($_SESSION['db_connection'])) {
    echo json_encode(['loggedIn' => true]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>