<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class RsrvPasajerosModel extends BaseModel
{
    protected static string $tableName = 'rsrv_pasajeros';

    public int $id_pasajero;
    public string $nom_pasajero;
    public ?string $correo_pasajero;
    public ?string $tel_pasajero;
    public ?string $cod_pais;
    public string $cod_idioma;
    public int $status;
    public int $id_locacion;
    public ?string $uniqid;
    public string $rowguid;
    public ?int $anio_rp;
    public ?int $id_loc_alta;
    public ?int $id_loc_op;
    public ?int $id_loc_up;
    public ?int $id_loc_tf;

    /**
     * Representa la columna 'id_pasajero' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdPasajero(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_pasajero', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nom_pasajero' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NomPasajero(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nom_pasajero', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'correo_pasajero' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CorreoPasajero(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('correo_pasajero', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'tel_pasajero' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TelPasajero(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('tel_pasajero', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cod_pais' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodPais(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_pais', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cod_idioma' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodIdioma(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_idioma', $alias, $tableAlias);
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
     * Representa la columna 'uniqid' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Uniqid(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('uniqid', $alias, $tableAlias);
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
     * Representa la columna 'anio_rp' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function AnioRp(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('anio_rp', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_loc_alta' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdLocAlta(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_loc_alta', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_loc_op' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdLocOp(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_loc_op', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_loc_up' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdLocUp(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_loc_up', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_loc_tf' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdLocTf(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_loc_tf', $alias, $tableAlias);
    }
}