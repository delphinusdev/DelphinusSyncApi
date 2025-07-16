<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class RsrvServiciosModel extends BaseModel
{
    protected static string $tableName = 'rsrv_servicios';

    public int $id_servicio;
    public ?string $cod_servicio;
    public string $nom_servicio;
    public ?string $foto_servicio;
    public ?float $costo_adulto;
    public ?float $costo_menor;
    public ?int $status;
    public int $id_locacion;
    public ?int $id_servicio_padre;
    public string $rowguid;
    public ?string $uniqid;
    public ?int $activa_disponibilidad;
    public ?int $id_saturno;
    public ?string $inter_id_saturno;
    public ?string $inter_id_saturno_xc;
    public ?string $codigo_sl;
    public ?string $descipcion_sl;
    public ?int $prf_omite_conc;
    public ?int $prf_programa;
    public ?int $prf_servicio;
    public ?int $prf_omite_general;
    public int $id_tipo_servicio;
    public bool $activa_isepaaa;

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
     * Representa la columna 'cod_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_servicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nom_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NomServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nom_servicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'foto_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FotoServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('foto_servicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'costo_adulto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CostoAdulto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('costo_adulto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'costo_menor' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CostoMenor(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('costo_menor', $alias, $tableAlias);
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
     * Representa la columna 'id_servicio_padre' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdServicioPadre(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_servicio_padre', $alias, $tableAlias);
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
     * Representa la columna 'activa_disponibilidad' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ActivaDisponibilidad(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('activa_disponibilidad', $alias, $tableAlias);
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
     * Representa la columna 'inter_id_saturno' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function InterIdSaturno(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('inter_id_saturno', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'inter_id_saturno_xc' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function InterIdSaturnoXc(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('inter_id_saturno_xc', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'codigo_sl' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodigoSl(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('codigo_sl', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'descipcion_sl' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DescipcionSl(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('descipcion_sl', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'prf_omite_conc' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PrfOmiteConc(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('prf_omite_conc', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'prf_programa' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PrfPrograma(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('prf_programa', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'prf_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PrfServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('prf_servicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'prf_omite_general' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PrfOmiteGeneral(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('prf_omite_general', $alias, $tableAlias);
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

    /**
     * Representa la columna 'activa_isepaaa' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ActivaIsepaaa(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('activa_isepaaa', $alias, $tableAlias);
    }
}