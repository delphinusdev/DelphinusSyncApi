<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class VentasCatMonedasModel extends BaseModel
{
    protected static string $tableName = 'ventas_cat_monedas';

    public int $id_moneda;
    public string $cod_moneda;
    public string $nom_moneda;
    public int $status;
    public string $rowguid;
    public ?int $id_saturno;

    /**
     * Representa la columna 'id_moneda' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdMoneda(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_moneda', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cod_moneda' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodMoneda(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_moneda', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nom_moneda' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NomMoneda(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nom_moneda', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'status' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Status(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('status', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'rowguid' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Rowguid(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('rowguid', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_saturno' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdSaturno(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_saturno', $alias, $tableAlias);
    }
}
