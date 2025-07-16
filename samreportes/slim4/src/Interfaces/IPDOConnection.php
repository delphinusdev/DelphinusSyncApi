<?php

namespace App\Interfaces;

use PDO;

/**
 * Defines the contract for a PDO connection provider.
 */
interface IPDOConnection
{
    /**
     * Gets a PDO connection instance.
     *
     * @return PDO The PDO connection instance.
     */
    public function getConnection(): PDO;
}