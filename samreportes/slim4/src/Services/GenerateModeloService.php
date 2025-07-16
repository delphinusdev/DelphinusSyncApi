<?php

namespace App\Services;

use App\Database\SqlSrvGenericContext;
use App\Utils\Column;
use App\Utils\QueryBuilder;
use App\Models\BaseModel; // <-- Importamos BaseModel

class GenerateModeloService
{
    private QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getTableColumns(string $tableName): QueryBuilder
    {
        $this->queryBuilder->newQuery(); // Ensure a fresh query

        // Build the query
        $queryParts = $this->queryBuilder
            ->select([
                new Column('COLUMN_NAME'), // Use Column objects for clarity and consistency
                new Column('DATA_TYPE'),
                new Column('IS_NULLABLE')
            ])
            ->from('INFORMATION_SCHEMA.COLUMNS', 'isc') // Alias for INFORMATION_SCHEMA.COLUMNS
            ->where(new Column('TABLE_NAME', null, 'isc'), '=', $tableName) // Use Column for where
            ->orderBy(new Column('ORDINAL_POSITION', null, 'isc'), 'ASC'); // Use Column for orderBy

        // Execute the query using the context and return the actual results
        return $queryParts;
    }

    public function generateModelClass(string $tableName, array $columns, string $namespace = 'App\\Models'): string
    {
        $className = $this->snakeCaseToCamelCase($tableName, true);
        $properties = [];
        $staticMethods = [];

        $typeMap = [
            // ... (tu mapa de tipos de datos actual) ...
            'int'              => 'int',
            'float'            => 'float',
            'decimal'          => 'float',
            'varchar'          => 'string',
            'nvarchar'         => 'string',
            'text'             => 'string',
            'ntext'            => 'string',
            'char'             => 'string',
            'nchar'            => 'string',
            'date'             => '\\DateTime',
            'datetime'         => '\\DateTime',
            'datetime2'        => '\\DateTime',
            'smalldatetime'    => '\\DateTime',
            'time'             => '\\DateTime',
            'uniqueidentifier' => 'string',
            'bit'              => 'bool',
            'tinyint'          => 'int',
            'smallint'         => 'int',
            'bigint'           => 'int',
            'money'            => 'float',
            'smallmoney'       => 'float',
            'varbinary'        => 'string',
            'image'            => 'string',
            'xml'              => 'string',
        ];

        foreach ($columns as $col) {
            $columnName = $col['COLUMN_NAME'];
            $dataType = strtolower($col['DATA_TYPE']);
            $isNullable = $col['IS_NULLABLE'] === 'YES';

            $phpType = $typeMap[$dataType] ?? 'mixed';
            $nullablePrefix = $isNullable ? '?' : '';

            $properties[] = "    public {$nullablePrefix}{$phpType} \${$columnName};";

            $methodName = $this->snakeCaseToCamelCase($columnName, true);
            $staticMethods[] = <<<PHP
    /**
     * Representa la columna '{$columnName}' en la base de datos.
     * @param string|null \$alias Un alias opcional para la columna en la consulta.
     * @param string \$tableAlias El alias de la tabla (ej. 'r').
     * @return \\App\\Utils\\Column
     */
    public static function {$methodName}(?string \$alias = null, string \$tableAlias = 'self'): Column
    {
        return new Column('{$columnName}', \$alias, \$tableAlias);
    }
PHP;
        }

        $classContent = "<?php\n";
        $classContent .= "namespace {$namespace};\n\n";
        $classContent .= "use \\App\\Utils\\Column;\n";
        $classContent .= "use \\App\\Models\\BaseModel;\n\n"; // Nueva importación de BaseModel
        $classContent .= "class {$className}Model extends BaseModel\n{\n"; // Extiende BaseModel
        $classContent .= "    protected static string \$tableName = '{$tableName}';\n\n"; // Define la tabla

        $classContent .= implode("\n", $properties);
        $classContent .= "\n\n";
        $classContent .= implode("\n\n", $staticMethods);
        $classContent .= "\n}\n";

        return $classContent;
    }

    private function snakeCaseToCamelCase(string $string, bool $capitalizeFirstCharacter = false): string
    {
        $str = str_replace('_', '', ucwords($string, '_'));
        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }
        return $str;
    }
}
