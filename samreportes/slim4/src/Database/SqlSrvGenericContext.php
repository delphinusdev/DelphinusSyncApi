<?php

// En App\Database\SqlSrvGenericContext.php
namespace App\Database;

use App\Repositories\GenericRepository;
use PDO;

class SqlSrvGenericContext extends GenericRepository
{
    private PDO $pdo;

    public function __construct(PDOConnection $connectionProvider)
    {
        try {
            $this->pdo = $connectionProvider->getConnection();
        } catch (\Exception $e) {
            // Es buena práctica añadir el nombre de la conexión en el mensaje
            // si pudieras pasarlo o inferirlo aquí.
            throw new \Exception("No se pudo establecer la conexión para un SqlSrvGenericContext: " . $e->getMessage(), 0, $e);
        }
        parent::__construct($this->pdo);
    }
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}