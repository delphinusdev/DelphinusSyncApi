<?php
declare(strict_types=1); // Habilita el modo estricto de tipos

namespace App\Repositories;

use PDO;
use PDOStatement; // Útil para type hinting interno si es necesario
use PDOException; // Aunque no se capture explícitamente aquí, es la excepción esperada de PDO

/**
 * Repositorio Genérico para operaciones comunes con PDO.
 *
 * Esta clase asume que la instancia de PDO está configurada para lanzar excepciones en caso de error
 * (ej. $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);).
 * Es compatible con SQL Server y aprovecha características de PHP 8.1+ como propiedades readonly.
 */
class GenericRepository
{
    /**
     * Constructor que recibe la conexión PDO.
     *
     * @param PDO $db Instancia de la conexión PDO. Usamos una propiedad readonly (PHP 8.1+).
     */
    public function __construct(
        readonly private PDO $db
    ) {}

    /**
     * Ejecuta una consulta SELECT y devuelve todos los resultados.
     *
     * @param string $sql La consulta SQL a ejecutar.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @param int $fetchMode Modo de obtención de resultados de PDO (ej. PDO::FETCH_ASSOC, PDO::FETCH_OBJ).
     * @return array<int, mixed> Un array de resultados. Estará vacío si no hay resultados.
     * La estructura de cada elemento del array dependerá del $fetchMode.
     */
    public function select(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll($fetchMode);
    }

    /**
     * Ejecuta una consulta SELECT y devuelve una única fila.
     *
     * @param string $sql La consulta SQL a ejecutar.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @param int $fetchMode Modo de obtención de resultados de PDO.
     * @return array<string, mixed>|object|false El resultado como array u objeto, o false si no se encuentra la fila.
     * El tipo exacto depende del $fetchMode.
     */
    public function selectOne(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array|object|false
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch($fetchMode);
    }

    /**
     * Ejecuta una consulta y devuelve el valor de una única columna de la primera fila.
     *
     * @param string $sql La consulta SQL a ejecutar.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @param int $columnIndex Índice (basado en 0) de la columna a obtener.
     * @return mixed El valor de la columna, o false si no se encuentra la fila o columna.
     */
    public function fetchValue(string $sql, array $params = [], int $columnIndex = 0): mixed
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn($columnIndex);
    }

    /**
     * Ejecuta una sentencia INSERT y devuelve el número de filas afectadas.
     * Para un INSERT de una sola fila, normalmente será 1.
     *
     * @param string $sql La consulta SQL INSERT.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @return int Número de filas afectadas.
     */
    public function insert(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Ejecuta un INSERT y devuelve el último ID generado por una columna IDENTITY.
     * Para SQL Server, esto usualmente se mapea a SCOPE_IDENTITY().
     * Es importante notar que PDO::lastInsertId() devuelve un string.
     * Para casos más complejos o para obtener otros valores generados (ej. UUIDs),
     * considera usar `insertWithOutput` con la cláusula OUTPUT de SQL Server.
     *
     * @param string $sql La consulta SQL INSERT.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @param string|null $sequenceName Nombre de la secuencia (relevante para PostgreSQL u Oracle, usualmente null para SQL Server con IDENTITY).
     * @return string|false El último ID insertado como string, o false en caso de error o si no es aplicable.
     */
    public function insertAndGetLastId(string $sql, array $params = [], ?string $sequenceName = null): string|false
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->db->lastInsertId($sequenceName);
    }

    /**
     * Ejecuta un INSERT (u otra DML) que incluye una cláusula OUTPUT (específica de SQL Server)
     * y devuelve el valor de la primera columna de la primera fila del conjunto de resultados de OUTPUT.
     * Ejemplo SQL: "INSERT INTO MiTabla (Nombre) OUTPUT INSERTED.ID VALUES (?)"
     *
     * @param string $sql La consulta SQL con la cláusula OUTPUT.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @return mixed El valor devuelto por la cláusula OUTPUT (ej. el ID generado), o false si no hay salida.
     */
    public function insertWithOutput(string $sql, array $params = []): mixed
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn(); // Devuelve la primera columna de la primera fila del resultado de OUTPUT
    }

    /**
     * Ejecuta una sentencia UPDATE y devuelve el número de filas afectadas.
     *
     * @param string $sql La consulta SQL UPDATE.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @return int Número de filas afectadas.
     */
    public function update(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Ejecuta una sentencia DELETE y devuelve el número de filas afectadas.
     *
     * @param string $sql La consulta SQL DELETE.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @return int Número de filas afectadas.
     */
    public function delete(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Ejecuta una sentencia SQL arbitraria (generalmente DML como INSERT, UPDATE, DELETE, o DDL)
     * y devuelve el número de filas afectadas, si es aplicable.
     * Para sentencias DDL, el valor devuelto podría ser 0 o depender del driver.
     *
     * @param string $sql La consulta SQL.
     * @param array<string|int, mixed> $params Parámetros para la consulta preparada.
     * @return int Número de filas afectadas.
     */
    public function executeStatement(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    // --- Métodos para Transacciones ---

    /**
     * Inicia una transacción.
     *
     * @return bool True si la transacción se inició correctamente, false en caso contrario.
     * @throws PDOException Si ya hay una transacción activa y el driver no soporta transacciones anidadas.
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Confirma la transacción actual.
     *
     * @return bool True si la transacción se confirmó correctamente, false en caso contrario.
     * @throws PDOException Si no hay una transacción activa.
     */
    public function commitTransaction(): bool
    {
        return $this->db->commit();
    }

    /**
     * Revierte la transacción actual.
     *
     * @return bool True si la transacción se revirtió correctamente, false en caso contrario.
     * @throws PDOException Si no hay una transacción activa.
     */
    public function rollbackTransaction(): bool
    {
        return $this->db->rollBack();
    }

    /**
     * Verifica si hay una transacción activa.
     *
     * @return bool True si hay una transacción activa, false en caso contrario.
     */
    public function inTransaction(): bool
    {
        return $this->db->inTransaction();
    }
}