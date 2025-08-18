<?php
namespace App\Database;

use PDO;
use PDOException;
use App\Interfaces\IPDOConnection;
use App\Config\Configuration;

/**
 * Manages PDO connections by providing an instance.
 * Each instance of this class is for a specific connection.
 */
class PDOConnection implements IPDOConnection
{
    private PDO $pdo;

    /**
     * @var string The name of the database connection (e.g., 'ebphotodelphinus').
     */
    private string $connectionName;

    /**
     * PDOConnection constructor.
     *
     * @param string $connectionName The name of the connection to retrieve from configuration.
     * @throws \Exception If the DSN is not found or connection fails.
     */
    public function __construct(string $connectionName)
    {
        $this->connectionName = $connectionName;
        $dsn         = Configuration::getConnectionString($connectionName);
        $credentials = Configuration::getDbCredentials($connectionName);

        if (!$dsn) {
            throw new \Exception("DSN no encontrado para '$connectionName'. Por favor, verifica tu configuración.");
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::SQLSRV_ATTR_ENCODING    => PDO::SQLSRV_ENCODING_UTF8,
            PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE   => true,
        ];

        try {
            $this->pdo = new PDO(
                $dsn,
                $credentials['Username'] ?? null,
                $credentials['Password'] ?? null,
                $options
            );
        } catch (PDOException $e) {
            throw new \Exception("Error al conectar a la base de datos '$connectionName': " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}