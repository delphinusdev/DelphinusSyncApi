<?php
namespace App\Models\FotosStagingModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class StgClientesFotosModel extends BaseModel
{
    protected static string $tableName = 'stg_clientes_fotos';

    public int $IDCLIENTE;
    public ?int $ID_VENTA;
    public string $NOMBRE;
    public string $PATERNO;
    public ?string $MATERNO;
    public string $EMAIL;
    public string $TELEFONO;
    public ?string $PAIS;
    public ?string $ESTADO;
    public ?string $CIUDAD;
    public ?string $CP;
    public string $CONTRASENA;
    public ?string $TOKEN;
    public ?\DateTime $FECHA;
    public ?string $ULTIMO_TOKEN;
    public int $IDLOCATION;
    public ?int $IDCLIENTELOC;
    public ?\DateTime $FECHA_PAGO_ACTUALIZADO;
    public ?string $origen_server;
    public ?\DateTime $fecha_sync;
    public ?string $id_batch_sync;

    /**
     * Representa la columna 'IDCLIENTE' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDCLIENTE(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('IDCLIENTE', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'ID_VENTA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDVENTA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ID_VENTA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'NOMBRE' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NOMBRE(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('NOMBRE', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'PATERNO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PATERNO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('PATERNO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'MATERNO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MATERNO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('MATERNO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'EMAIL' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function EMAIL(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('EMAIL', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'TELEFONO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TELEFONO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('TELEFONO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'PAIS' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PAIS(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('PAIS', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'ESTADO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ESTADO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ESTADO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CIUDAD' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CIUDAD(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CIUDAD', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CP' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CP(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CP', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CONTRASENA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CONTRASENA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CONTRASENA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'TOKEN' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TOKEN(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('TOKEN', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'FECHA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FECHA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FECHA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'ULTIMO_TOKEN' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ULTIMOTOKEN(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ULTIMO_TOKEN', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'IDLOCATION' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDLOCATION(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('IDLOCATION', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'IDCLIENTELOC' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDCLIENTELOC(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('IDCLIENTELOC', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'FECHA_PAGO_ACTUALIZADO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FECHAPAGOACTUALIZADO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FECHA_PAGO_ACTUALIZADO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'origen_server' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function OrigenServer(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('origen_server', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'fecha_sync' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FechaSync(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('fecha_sync', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_batch_sync' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdBatchSync(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_batch_sync', $alias, $tableAlias);
    }
}