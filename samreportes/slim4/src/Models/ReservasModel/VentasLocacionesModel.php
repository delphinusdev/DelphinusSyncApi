<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class VentasLocacionesModel extends BaseModel
{
    protected static string $tableName = 'ventas_locaciones';

    public int $id_locacion;
    public string $cod_locacion;
    public string $nom_locacion;
    public ?string $direccion_locacion;
    public ?string $rfc_locacion;
    public ?string $slogan_locacion;
    public int $status;
    public string $rowguid;
    public ?float $monto_cb;
    public ?float $monto_dp;
    public ?float $monto_tf;
    public ?int $id_saturno;
    public ?string $inter_id_saturno;
    public ?int $tipo_disponibilidad;
    public ?string $cod_checkfront;
    public ?int $folio_cbele;

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
     * Representa la columna 'cod_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_locacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nom_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NomLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nom_locacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'direccion_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DireccionLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('direccion_locacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'rfc_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function RfcLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('rfc_locacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'slogan_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function SloganLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('slogan_locacion', $alias, $tableAlias);
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
     * Representa la columna 'monto_cb' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MontoCb(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('monto_cb', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'monto_dp' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MontoDp(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('monto_dp', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'monto_tf' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MontoTf(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('monto_tf', $alias, $tableAlias);
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
     * Representa la columna 'tipo_disponibilidad' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TipoDisponibilidad(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('tipo_disponibilidad', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cod_checkfront' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodCheckfront(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_checkfront', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folio_cbele' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolioCbele(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folio_cbele', $alias, $tableAlias);
    }
}