<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class VentasTipoPagoModel extends BaseModel
{
    protected static string $tableName = 'ventas_tipo_pago';

    public int $id_tipo_pago;
    public string $cod_pago;
    public string $nom_pago;
    public ?int $folio_control;
    public int $status;
    public string $rowguid;
    public ?int $id_saturno;
    public int $referencia;

    /**
     * Representa la columna 'id_tipo_pago' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdTipoPago(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_tipo_pago', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cod_pago' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodPago(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_pago', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nom_pago' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NomPago(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nom_pago', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folio_control' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolioControl(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folio_control', $alias, $tableAlias);
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

    /**
     * Representa la columna 'referencia' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Referencia(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('referencia', $alias, $tableAlias);
    }
}
