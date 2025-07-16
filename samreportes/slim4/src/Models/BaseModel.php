<?php
// src/Models/BaseModel.php
namespace App\Models;

use App\Utils\Column;
use App\Utils\QueryBuilder;
use Psr\Container\ContainerInterface;

abstract class BaseModel
{
    // Propiedad estática para el nombre de la tabla (debe ser definida por cada modelo concreto)
     
    protected static string $tableName = '';
    
    private static ?QueryBuilder $queryBuilder = null;

    /**
     * Define la tabla principal para la consulta, asignándole un alias.
     * Retorna una instancia del QueryBuilder con la tabla configurada.
     *
     * @param string $alias El alias para la tabla (ej. 'r').
     * @return \App\Utils\QueryBuilder
     */

     public static function setQueryBuilder(QueryBuilder $queryBuilderInstance): void
    {
        self::$queryBuilder = $queryBuilderInstance;
    }
     
     public static function tableName(string $alias): string
    {
        if (empty(static::$tableName)) {
            // Esto es una precaución. Cada modelo debería definir su $tableName.
            throw new \LogicException("Static property \$tableName must be set in " . static::class);
        }
        return static::$tableName . ' ' . $alias;
    }
     
     public static function from(string $alias): QueryBuilder
    {
        if (self::$queryBuilder === null) {
            throw new \LogicException("QueryBuilder has not been set in BaseModel. Call BaseModel::setQueryBuilder() during bootstrap.");
        }

        // Limpia el QueryBuilder y establece la tabla y su alias
        return self::$queryBuilder->newQuery()->from(static::$tableName, $alias);
    }

     public static function insert(array $data): QueryBuilder
    {
        if (self::$queryBuilder === null) {
            throw new \LogicException("QueryBuilder has not been set in BaseModel. Call BaseModel::setQueryBuilder() during bootstrap.");
        }
        if (empty(static::$tableName)) {
            throw new \LogicException("Static property \$tableName must be set in " . static::class . " to perform an insert.");
        }

        // Inicia una nueva consulta de inserción usando el QueryBuilder
        return self::$queryBuilder->newQuery()->insert(static::$tableName,$data);
    }
}