<?php
// db_connection.php

/**
 * Establece y devuelve una conexión PDO a la base de datos SQL Server.
 *
 * @param string $serverName Nombre o IP del servidor SQL Server.
 * @param string $database Nombre de la base de datos a la que conectar.
 * @param string $uid Nombre de usuario para la conexión.
 * @param string $pwd Contraseña para la conexión.
 * @param bool $trustServerCertificate Indica si se debe confiar en el certificado del servidor (para desarrollo/pruebas).
 * @return PDO La instancia de conexión PDO.
 * @throws PDOException Si la conexión falla.
 */
function getPdoConnection($serverName, $database, $uid, $pwd, $trustServerCertificate = false) {
    // Comprobar si la extensión pdo_sqlsrv está cargada
    if (!extension_loaded('pdo_sqlsrv')) {
        throw new Exception('La extensión pdo_sqlsrv no está instalada o habilitada en PHP.');
    }

    $dsn = "sqlsrv:Server=$serverName;Database=$database";

    // Añadir TrustServerCertificate a la cadena DSN si es necesario
    if ($trustServerCertificate) {
        $dsn .= ";TrustServerCertificate=true";
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Puedes añadir más opciones PDO aquí si son necesarias globalmente
    ];

    try {
        $pdo = new PDO($dsn, $uid, $pwd, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Registrar el error detallado y lanzar una excepción más genérica para el usuario
        error_log("Error de conexión PDO: " . $e->getMessage());
        throw new PDOException("No se pudo conectar al servidor de base de datos. Por favor, verifica la configuración y las credenciales.");
    }
}
?>