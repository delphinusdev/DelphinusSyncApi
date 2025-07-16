<?php
// src/Utils/Column.php
namespace App\Utils;

class Column
{
    public string $name;
    public ?string $alias;
    public ?string $tableAlias;

    public function __construct(string $name, ?string $alias = null, ?string $tableAlias = null)
    {
        $this->name = $name;
        $this->alias = $alias;
        $this->tableAlias = $tableAlias;
    }

    public function __toString(): string
    {
        $output = '';
        if ($this->tableAlias && $this->tableAlias !== 'self') { // 'self' es un placeholder si no se especifica
            $output .= $this->tableAlias . '.';
        }
        $output .= $this->name;

        if ($this->alias) {
            $output .= ' AS ' . $this->alias;
        }
        return $output;
    }
}