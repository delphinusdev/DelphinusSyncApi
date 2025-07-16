<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class VentasCatTipoCambioModel extends BaseModel
{
    protected static string $tableName = 'ventas_cat_tipo_cambio';

    public int $id_tipo_cambio;
    public int $id_moneda;
    public float $paridad;
    public ?\DateTime $fecha_inicial;
    public ?\DateTime $fecha_final;
    public ?int $status;
    public ?int $id_locacion;
    public string $rowguid;
    public ?int $id_clasificacion;
    public ?int $id_subclas;
    public ?int $id_cliente;
    public ?int $id_tipo_servicio;

    /**
     * Representa la columna 'id_tipo_cambio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdTipoCambio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_tipo_cambio', $alias, $tableAlias);
    }

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
     * Representa la columna 'paridad' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Paridad(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('paridad', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'fecha_inicial' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FechaInicial(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('fecha_inicial', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'fecha_final' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FechaFinal(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('fecha_final', $alias, $tableAlias);
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
     * Representa la columna 'id_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_locacion', $alias, $tableAlias);
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
     * Representa la columna 'id_clasificacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdClasificacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_clasificacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_subclas' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdSubclas(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_subclas', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_cliente' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdCliente(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_cliente', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_tipo_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdTipoServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_tipo_servicio', $alias, $tableAlias);
    }
}
