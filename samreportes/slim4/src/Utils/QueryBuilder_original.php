<?php
// src/Utils/QueryBuilder.php
namespace App\Utils;

// Ya no necesitamos importar DelphinusETravelContext aquí
// use App\Database\DelphinusETravelContext; // <-- REMOVIDO

class QueryBuilder_original
{
    private string $_dialect = 'standard'; // 'standard', 'sqlserver', 'mysql', 'pgsql', etc.
    private string $_select = '';
    private string $_from = '';
    private array $_joins = [];
    private array $_where = [];
    private string $_groupBy = '';
    private string $_limit = ''; // Almacena la cadena LIMIT X
    private string $_offset = ''; // Almacena la cadena OFFSET Y
    private array $_orderBy = []; // Cambio a array para múltiples ordenaciones

    private string $_insertTable = '';
    private array $_insertData = [];


    private array $_params = []; // Para almacenar los parámetros de las consultas preparadas
    private int $_paramCounter = 0; // Para generar nombres de placeholder únicos

    // Propiedades para almacenar los valores numéricos de limit/offset para lógica interna
    private ?int $_limitNum = null;
    private ?int $_offsetNum = null;

    // Propiedad para almacenar los alias de tabla definidos en from()
    private array $currentTableAliases = [];

    /**
     * Limpia el estado del constructor para una nueva consulta.
     */
    public function __construct(string $dialect = 'standard')
    {
        $this->setDialect($dialect);
        $this->newQuery(); // Llama a newQuery para inicializar el estado
    }

    public function setDialect(string $dialect): self
    {
        $this->_dialect = strtolower($dialect);
        return $this;
    }

    public function newQuery(): self
    {
        $this->_select = '';
        $this->_from = '';
        $this->_joins = [];
        $this->_where = [];
        $this->_groupBy = '';
        $this->_orderBy = [];
        $this->_limit = '';
        $this->_offset = '';
        $this->_limitNum = null;   // Resetear valores numéricos
        $this->_offsetNum = null; // Resetear valores numéricos
        $this->_params = [];
        $this->_paramCounter = 0;
        $this->currentTableAliases = []; // Resetear alias de tabla

        $this->_insertTable = '';
        $this->_insertData = [];


        return $this;
    }

    /**
     * Establece la cláusula SELECT.
     * Ahora puede aceptar un array de strings (nombres de columna) o Column objects.
     *
     * @param string|array<\App\Utils\Column|string> $fields
     * @return self
     */
    public function select($fields = '*'): self
    {
        if (is_array($fields)) {
            $parsedFields = [];
            foreach ($fields as $field) {
                if ($field instanceof Column) {
                    $parsedFields[] = (string)$field; // Utiliza el __toString() de Column
                } elseif (is_string($field)) {
                    $parsedFields[] = $field;
                } else {
                    throw new \InvalidArgumentException("Los elementos de select deben ser strings o instancias de \\App\\Utils\\Column.");
                }
            }
            $this->_select = 'SELECT ' . implode(', ', $parsedFields);
        } elseif (str_starts_with(strtoupper(trim($fields)), 'SELECT')) {
            $this->_select = trim($fields); // El usuario ya proveyó "SELECT"
        } else {
            $this->_select = 'SELECT ' . $fields;
        }
        return $this;
    }

    public function insert(string $table, array $data): self
    {
        // Limpiamos cualquier estado anterior para asegurar que es una consulta de inserción pura.
        $this->newQuery();

        if (empty($data)) {
            throw new \InvalidArgumentException("El array de datos para INSERT no puede estar vacío.");
        }

        $this->_insertTable = $table;
        $this->_insertData = $data;

        return $this;
    }

    /**
     * Establece la cláusula FROM.
     *
     * @param string $table El nombre de la tabla.
     * @param string|null $alias Opcional, el alias para la tabla.
     * @return self
     */
    public function from(string $table, ?string $alias = null): self
    {
        // Si el usuario escribe "FROM users", quitamos el FROM.
        if (str_starts_with(strtoupper(trim($table)), 'FROM')) {
            $table = trim(substr(trim($table), 4));
        }

        $this->_from = 'FROM ' . trim($table);
        if ($alias) {
            $this->_from .= ' AS ' . $alias;
            // Almacenar el alias para referenciar en select/where/orderby
            $this->currentTableAliases[$table] = $alias;
        }
        return $this;
    }

    /**
     * Añade una cláusula JOIN.
     *
     * @param string $table La tabla a unir.
     * @param string|Column $conditionLeft La columna izquierda de la condición (ej. "u.id" o Users::Id()).
     * @param string $operator El operador de la condición (ej. "=").
     * @param string|Column $conditionRight La columna derecha o valor de la condición (ej. "p.user_id" o Posts::UserId()).
     * @param string $type El tipo de JOIN (INNER, LEFT, RIGHT, etc.). Por defecto 'INNER'.
     * @return self
     */
    public function join(string $table, Column|string $conditionLeft, string $operator, Column|string $conditionRight, string $type = 'INNER'): self
    {
        // Procesar las condiciones para obtener los nombres de columna correctos
        $leftCol = ($conditionLeft instanceof Column) ? (string)$conditionLeft : $conditionLeft;
        $rightCol = ($conditionRight instanceof Column) ? (string)$conditionRight : $conditionRight;

        $this->_joins[] = strtoupper($type) . ' JOIN ' . $table . ' ON ' . $leftCol . ' ' . $operator . ' ' . $rightCol;
        return $this;
    }

    public function innerJoin(string $table, Column|string $conditionLeft, string $operator, Column|string $conditionRight): self
    {
        return $this->join($table, $conditionLeft, $operator, $conditionRight, 'INNER');
    }

    public function leftJoin(string $table, Column|string $conditionLeft, string $operator, Column|string $conditionRight): self
    {
        return $this->join($table, $conditionLeft, $operator, $conditionRight, 'LEFT');
    }

    public function rightJoin(string $table, Column|string $conditionLeft, string $operator, Column|string $conditionRight): self
    {
        return $this->join($table, $conditionLeft, $operator, $conditionRight, 'RIGHT');
    }

    /**
     * Añade una condición WHERE.
     *
     * Formas de uso:
     * - where('id = 1') -> condición cruda
     * - where('id', 1) -> id = ? (se genera placeholder)
     * - where('status', '=', 'active') -> status = ?
     * - where(RsrvReservas::IdReservacion(), '=', 123) -> r.id_reservacion = ?
     *
     * @param string|Column $columnOrCondition
     * @param mixed $operatorOrValue (puede ser el operador o el valor si el operador es '=')
     * @param mixed $value (el valor si se especificó un operador)
     * @param string $boolean ('AND' o 'OR')
     * @return self
     */
    public function where(string|Column $columnOrCondition, $operatorOrValue = null, $value = null, string $boolean = 'AND'): self
    {
        $conditionStr = '';
        if ($columnOrCondition instanceof Column) {
            $columnName = (string)$columnOrCondition; // Obtiene "r.id_reservacion"
            if ($operatorOrValue === null && $value === null) {
                 // Esto no debería suceder si se usa un Column object como condición cruda,
                 // pero lo manejo por completitud.
                $conditionStr = $columnName;
            } else {
                $operator = ($value === null) ? '=' : (string)$operatorOrValue;
                $actualValue = ($value === null) ? $operatorOrValue : $value;
                $placeholder = $this->addParam($actualValue);
                $conditionStr = $columnName . ' ' . $operator . ' ' . $placeholder;
            }
        } elseif ($operatorOrValue === null && $value === null) {
            // Condición cruda: where('user_id = 1 AND status = "active"')
            $conditionStr = $columnOrCondition;
        } else {
            $column = $columnOrCondition;
            $operator = ($value === null) ? '=' : (string)$operatorOrValue;
            $actualValue = ($value === null) ? $operatorOrValue : $value;
            $placeholder = $this->addParam($actualValue);
            $conditionStr = $column . ' ' . $operator . ' ' . $placeholder;
        }

        $this->_where[] = ['condition' => $conditionStr, 'boolean' => $boolean];
        return $this;
    }

    public function orWhere(string|Column $columnOrCondition, $operatorOrValue = null, $value = null): self
    {
        return $this->where($columnOrCondition, $operatorOrValue, $value, 'OR');
    }

    /**
     * Añade una condición WHERE IN.
     *
     * @param string|Column $column
     * @param array $values
     * @param string $boolean
     * @param bool $notIn Si es true, usa "NOT IN"
     * @return self
     */
    public function whereIn(string|Column $column, array $values, string $boolean = 'AND', bool $notIn = false): self
    {
        $columnName = ($column instanceof Column) ? (string)$column : $column;

        if (empty($values)) {
            $this->_where[] = ['condition' => ($notIn ? '1=1' : '1=0'), 'boolean' => $boolean];
            return $this;
        }
        $placeholders = [];
        foreach ($values as $value) {
            $placeholders[] = $this->addParam($value);
        }
        $operator = $notIn ? 'NOT IN' : 'IN';
        $this->_where[] = [
            'condition' => $columnName . ' ' . $operator . ' (' . implode(', ', $placeholders) . ')',
            'boolean' => $boolean
        ];
        return $this;
    }

    public function orWhereIn(string|Column $column, array $values): self
    {
        return $this->whereIn($column, $values, 'OR');
    }

    public function whereNotIn(string|Column $column, array $values, string $boolean = 'AND'): self
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    public function orWhereNotIn(string|Column $column, array $values): self
    {
        return $this->whereIn($column, $values, 'OR', true);
    }


    /**
     * Añade una cláusula GROUP BY.
     *
     * @param string|array<\App\Utils\Column|string> $fields Campos por los que agrupar, separados por coma o un array.
     * @return self
     */
    public function groupBy($fields): self
    {
        if (is_array($fields)) {
            $parsedFields = [];
            foreach ($fields as $field) {
                if ($field instanceof Column) {
                    $parsedFields[] = (string)$field; // Utiliza el __toString() de Column
                } elseif (is_string($field)) {
                    $parsedFields[] = $field;
                } else {
                    throw new \InvalidArgumentException("Los elementos de groupBy deben ser strings o instancias de \\App\\Utils\\Column.");
                }
            }
            $this->_groupBy = 'GROUP BY ' . implode(', ', $parsedFields);
        } else {
            $this->_groupBy = 'GROUP BY ' . $fields; // Asume que el string ya tiene el formato correcto
        }
        return $this;
    }

    /**
     * Añade una cláusula ORDER BY.
     *
     * @param string|Column $field Campo por el que ordenar.
     * @param string $direction Dirección ('ASC' o 'DESC').
     * @return self
     */
    public function orderBy(string|Column $field, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction);
        if ($direction !== 'ASC' && $direction !== 'DESC') {
            $direction = 'ASC'; // Default seguro
        }

        $fieldName = ($field instanceof Column) ? (string)$field : $field;

        $this->_orderBy[] = $fieldName . ' ' . $direction;
        return $this;
    }

    /**
     * Añade una cláusula LIMIT.
     *
     * @param int $limit
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->_limitNum = $limit; // Almacenar el valor numérico
        $this->_limit = 'LIMIT ' . $limit; // Para dialectos estándar que usan 'LIMIT X'
        return $this;
    }

    /**
     * Añade un OFFSET (usualmente con LIMIT).
     *
     * @param int $offset
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->_offsetNum = $offset; // Almacenar el valor numérico
        $this->_offset = 'OFFSET ' . $offset; // Para dialectos estándar que usan 'OFFSET Y'
        return $this;
    }


    /**
     * Añade un parámetro a la lista y devuelve su placeholder.
     *
     * @param mixed $value
     * @return string
     */
    private function addParam($value): string
    {
        $placeholder = ':param' . $this->_paramCounter++;
        $this->_params[$placeholder] = $value;
        return $placeholder;
    }

    /**
     * Obtiene los parámetros recolectados para la consulta preparada.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->_params;
    }

    /**
     * Construye y devuelve la cadena SQL final.
     *
     * @return string
     */
    public function getSql(): string
    {
        // --- INICIO: Lógica para INSERT ---
        if (!empty($this->_insertTable)) {
            $columns = array_keys($this->_insertData);
            $placeholders = [];
            
            foreach ($this->_insertData as $value) {
                // Usamos el método existente para añadir parámetros y obtener el placeholder
                $placeholders[] = $this->addParam($value);
            }

            $sql = sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $this->_insertTable,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );
            return $sql;
        }
        // --- FIN: Lógica para INSERT ---

        $selectClause = $this->_select;
        if (empty($selectClause)) {
            $selectClause = 'SELECT *';
        }

        // Modificar SELECT para SQL Server TOP si solo hay límite (no offset)
        if ($this->_dialect === 'sqlserver' && $this->_limitNum > 0 && $this->_offsetNum === null) {
            if (preg_match('/^SELECT\s+DISTINCT/i', $selectClause)) {
                $selectClause = preg_replace('/^SELECT\s+DISTINCT/i', 'SELECT DISTINCT TOP ' . $this->_limitNum, $selectClause, 1);
            } elseif (preg_match('/^SELECT/i', $selectClause)) {
                $selectClause = preg_replace('/^SELECT/i', 'SELECT TOP ' . $this->_limitNum, $selectClause, 1);
            }
        }

        $query = [$selectClause];

        if (!empty($this->_from)) $query[] = $this->_from;
        if (!empty($this->_joins)) $query[] = implode(' ', $this->_joins);

        // --- Add the WHERE clause construction here ---
        if (!empty($this->_where)) {
            $whereClauses = [];
            foreach ($this->_where as $index => $wherePart) {
                $condition = $wherePart['condition'];
                $boolean = $wherePart['boolean'];

                if ($index === 0) {
                    $whereClauses[] = $condition; // First condition doesn't need an 'AND'/'OR' prefix
                } else {
                    $whereClauses[] = $boolean . ' ' . $condition;
                }
            }
            $query[] = 'WHERE ' . implode(' ', $whereClauses);
        }
        // --- End of WHERE clause construction ---

        if (!empty($this->_groupBy)) $query[] = $this->_groupBy;

        // ORDER BY es crucial para OFFSET FETCH en SQL Server
        $orderByClause = '';
        if (!empty($this->_orderBy)) {
            $orderByClause = 'ORDER BY ' . implode(', ', $this->_orderBy);
            $query[] = $orderByClause;
        } elseif ($this->_dialect === 'sqlserver' && $this->_offsetNum > 0) {
            // SQL Server requiere ORDER BY para OFFSET FETCH.
            // Si el usuario no proporcionó uno, agregamos un ORDER BY por defecto seguro.
            // Podrías lanzar una excepción o usar una columna de la tabla principal si conoces una pk.
            // Para ser robustos, asumimos que no hay una columna conocida y usamos una constante.
            $query[] = 'ORDER BY (SELECT NULL)'; // Aceptado por SQL Server para paginación sin orden específico
            $orderByClause = 'ORDER BY (SELECT NULL)'; // Actualizar para que la siguiente lógica lo detecte
        }


        // Construcción de cláusulas de límite/offset/top
        if ($this->_dialect === 'sqlserver') {
            if ($this->_offsetNum > 0) { // Paginación con OFFSET
                if (empty($orderByClause)) { // Doble chequeo por si no se agregó antes
                    // Esto debería ser manejado por la lógica anterior, pero como fallback
                    // para asegurar que ORDER BY exista si hay OFFSET.
                    $query[] = 'ORDER BY (SELECT NULL)';
                }
                $query[] = 'OFFSET ' . $this->_offsetNum . ' ROWS';
                if ($this->_limitNum > 0) {
                    $query[] = 'FETCH NEXT ' . $this->_limitNum . ' ROWS ONLY';
                }
            }
            // else if ($this->_limitNum > 0 && $this->_offsetNum === null) {
            // TOP ya fue añadido al SELECT en este caso.
            // }
        } else { // Dialectos estándar (MySQL, PostgreSQL, SQLite)
            if ($this->_limitNum > 0) {
                $query[] = 'LIMIT ' . $this->_limitNum;
            }
            if ($this->_offsetNum > 0) {
                // En MySQL, PostgreSQL, SQLite se usa OFFSET después de LIMIT.
                $query[] = 'OFFSET ' . $this->_offsetNum;
            }
        }
        // ...
        return preg_replace('/\s+/', ' ', trim(implode(' ', $query)));
    }

    /**
     * Método de conveniencia para obtener SQL y parámetros.
     * @return array ['sql' => string, 'params' => array]
     */
    public function build(): array
    {
        return [
            'sql' => $this->getSql(),
            'params' => $this->getParams()
        ];
    }
}