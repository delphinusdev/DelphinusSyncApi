<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class VentasTipoTarjetaModel extends BaseModel
{
    protected static string $tableName = 'ventas_tipo_tarjeta';

    public int $id_tipo_tarjeta;
    public string $nom_tarjeta;
    public int $status;
    public ?string $cod_tarjeta;
    public string $rowguid;
    public ?int $id_saturno;

    /**
     * Representa la columna 'id_tipo_tarjeta' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdTipoTarjeta(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_tipo_tarjeta', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nom_tarjeta' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NomTarjeta(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nom_tarjeta', $alias, $tableAlias);
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
     * Representa la columna 'cod_tarjeta' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodTarjeta(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_tarjeta', $alias, $tableAlias);
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
