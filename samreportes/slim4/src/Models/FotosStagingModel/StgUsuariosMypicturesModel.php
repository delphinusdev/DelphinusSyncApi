<?php
namespace App\Models\FotosStagingModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class StgUsuariosMypicturesModel extends BaseModel
{
    protected static string $tableName = 'stg_usuarios_mypictures';

    public int $ID;
    public string $NOMBRE;
    public string $USUARIO;
    public string $PASSWOR;
    public ?string $MAIL;
    public int $ID_VENTAS;
    public int $ID_GRUPO_FOTOS;
    public ?string $KEY_GENERADO;
    public ?int $CONT_LOGIN;
    public ?\DateTime $FECHA;
    public ?\DateTime $HORA;
    public ?string $ORIGINAL;
    public ?string $origen_server;
    public ?\DateTime $fecha_sync;
    public ?string $id_batch_sync;

    /**
     * Representa la columna 'ID' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ID(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ID', $alias, $tableAlias);
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
     * Representa la columna 'USUARIO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function USUARIO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('USUARIO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'PASSWOR' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PASSWOR(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('PASSWOR', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'MAIL' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MAIL(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('MAIL', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'ID_VENTAS' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDVENTAS(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ID_VENTAS', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'ID_GRUPO_FOTOS' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDGRUPOFOTOS(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ID_GRUPO_FOTOS', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'KEY_GENERADO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function KEYGENERADO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('KEY_GENERADO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CONT_LOGIN' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CONTLOGIN(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CONT_LOGIN', $alias, $tableAlias);
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
     * Representa la columna 'HORA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function HORA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('HORA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'ORIGINAL' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ORIGINAL(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ORIGINAL', $alias, $tableAlias);
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