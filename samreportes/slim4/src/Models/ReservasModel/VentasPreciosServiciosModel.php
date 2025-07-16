<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class VentasPreciosServiciosModel extends BaseModel
{
    protected static string $tableName = 'ventas_precios_servicios';

    public int $id_precio;
    public int $id_servicio;
    public int $id_locacion;
    public ?int $id_cliente;
    public int $id_moneda;
    public ?int $id_idioma;
    public ?\DateTime $hora;
    public float $precio;
    public ?float $precion;
    public ?\DateTime $fecha_ini;
    public ?\DateTime $fecha_fin;
    public ?int $status;
    public string $rowguid;
    public int $id_notificacion;

    /**
     * Representa la columna 'id_precio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdPrecio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_precio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_servicio', $alias, $tableAlias);
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
     * Representa la columna 'id_idioma' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdIdioma(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_idioma', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'hora' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Hora(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('hora', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'precio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Precio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('precio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'precion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Precion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('precion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'fecha_ini' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FechaIni(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('fecha_ini', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'fecha_fin' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FechaFin(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('fecha_fin', $alias, $tableAlias);
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
     * Representa la columna 'id_notificacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdNotificacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_notificacion', $alias, $tableAlias);
    }
}