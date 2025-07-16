<?php
namespace App\Models\ReservasModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class RsrvReservasModel extends BaseModel
{
    protected static string $tableName = 'rsrv_reservas';
    protected static string $primaryKey = 'id_reservacion';
    protected static string $Identity = 'id_reservacion';

    public int $id_reservacion;
    public ?string $folio_reserva;
    public ?int $folio_checkin;
    public ?int $id_cliente;
    public ?int $id_rep;
    public ?string $cod_pais;
    public ?string $cod_idioma;
    public ?int $id_clasificacion;
    public ?int $id_subclasificacion;
    public ?int $id_medio;
    public ?int $id_servicio;
    public ?int $id_horario;
    public ?int $id_hotel;
    public ?int $id_promo;
    public ?int $id_pickup;
    public ?int $id_cortesia;
    public ?int $id_autorizo;
    public ?float $paridad;
    public ?int $id_pasajero;
    public ?string $habitacion;
    public ?\DateTime $fecha_servicio;
    public ?int $pax;
    public ?int $paxn;
    public ?int $pax_finales;
    public ?int $paxn_finales;
    public ?float $precio;
    public ?float $precion;
    public ?string $confirma;
    public ?string $cupon;
    public ?\DateTime $momento_alta;
    public ?int $id_usuario;
    public ?int $status;
    public int $id_locacion;
    public ?int $aplica_noshow;
    public ?int $no_pickup;
    public ?int $cod_reserva;
    public ?float $total_mon_default;
    public ?float $total_mon_base;
    public ?string $hora_pickup;
    public ?\DateTime $hora_servicio;
    public ?int $pax_incentivos;
    public ?string $cupon_incentivos;
    public ?float $precio_neto;
    public ?float $precio_neton;
    public ?float $total_mon_default_neto;
    public ?float $total_mon_base_neto;
    public ?string $uniqid;
    public ?int $id_descuento;
    public ?int $divisa;
    public ?int $pax_incentivos_finales;
    public ?int $aplica_goshow;
    public ?int $edita_precio;
    public ?int $pax_cobra;
    public ?int $paxn_cobra;
    public ?float $descuento;
    public ?int $folio_cancela;
    public ?int $facturada;
    public ?\DateTime $fecha_cupon;
    public ?int $up_aplica;
    public ?int $up_id_servicio;
    public ?float $up_monto;
    public ?int $up_adulto;
    public ?int $up_menor;
    public ?string $up_cupon;
    public ?int $folio_pago;
    public ?int $cb_aplica;
    public ?float $cb_monto;
    public ?string $cb_inicio;
    public ?string $cb_final;
    public ?int $iva_porc;
    public ?float $comision;
    public ?float $comision_n;
    public ?int $tipo;
    public string $rowguid;
    public ?int $folio_ticket;
    public ?string $up_pickup;
    public ?int $up_id_vendedor;
    public ?float $up_comision;
    public ?\DateTime $up_hora_servicio;
    public ?int $dp_aplica;
    public ?float $dp_monto;
    public ?float $comision_v;
    public ?int $precio_muestra;
    public ?int $con_credito;
    public ?int $up_id;
    public ?int $up_id_locacion;
    public ?int $tf_aplica;
    public ?float $tf_monto;
    public ?int $folio_boleto;
    public ?\DateTime $up_fecha_alta;
    public ?string $uniqid_reserva;
    public ?int $tf_id_locacion;
    public ?\DateTime $tf_fecha;
    public ?\DateTime $dp_fecha;
    public ?int $alta_id_locacion;
    public ?int $cont_ticket;
    public ?\DateTime $momento_edicion;
    public ?\DateTime $tf_hota;
    public ?int $cb_cashback;
    public ?int $anio_rp;
    public ?string $cupon_foto;
    public ?int $pax_noshow;
    public ?int $id_us_alta;

    /**
     * Representa la columna 'id_reservacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdReservacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_reservacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folio_reserva' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolioReserva(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folio_reserva', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folio_checkin' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolioCheckin(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folio_checkin', $alias, $tableAlias);
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
     * Representa la columna 'id_rep' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdRep(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_rep', $alias, $tableAlias);
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
     * Representa la columna 'id_subclasificacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdSubclasificacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_subclasificacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_medio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdMedio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_medio', $alias, $tableAlias);
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
     * Representa la columna 'id_horario' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdHorario(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_horario', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_hotel' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdHotel(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_hotel', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_promo' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdPromo(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_promo', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_pickup' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdPickup(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_pickup', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_cortesia' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdCortesia(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_cortesia', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_autorizo' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdAutorizo(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_autorizo', $alias, $tableAlias);
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
     * Representa la columna 'habitacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Habitacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('habitacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'fecha_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FechaServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('fecha_servicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'pax' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Pax(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('pax', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'paxn' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Paxn(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('paxn', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'pax_finales' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PaxFinales(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('pax_finales', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'paxn_finales' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PaxnFinales(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('paxn_finales', $alias, $tableAlias);
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
     * Representa la columna 'confirma' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Confirma(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('confirma', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cupon' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Cupon(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cupon', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'momento_alta' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MomentoAlta(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('momento_alta', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_usuario' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdUsuario(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_usuario', $alias, $tableAlias);
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
     * Representa la columna 'aplica_noshow' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function AplicaNoshow(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('aplica_noshow', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'no_pickup' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NoPickup(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('no_pickup', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cod_reserva' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CodReserva(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cod_reserva', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'total_mon_default' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TotalMonDefault(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('total_mon_default', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'total_mon_base' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TotalMonBase(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('total_mon_base', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'hora_pickup' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function HoraPickup(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('hora_pickup', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'hora_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function HoraServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('hora_servicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'pax_incentivos' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PaxIncentivos(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('pax_incentivos', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cupon_incentivos' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CuponIncentivos(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cupon_incentivos', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'precio_neto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PrecioNeto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('precio_neto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'precio_neton' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PrecioNeton(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('precio_neton', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'total_mon_default_neto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TotalMonDefaultNeto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('total_mon_default_neto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'total_mon_base_neto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TotalMonBaseNeto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('total_mon_base_neto', $alias, $tableAlias);
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
     * Representa la columna 'id_descuento' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdDescuento(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_descuento', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'divisa' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Divisa(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('divisa', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'pax_incentivos_finales' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PaxIncentivosFinales(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('pax_incentivos_finales', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'aplica_goshow' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function AplicaGoshow(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('aplica_goshow', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'edita_precio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function EditaPrecio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('edita_precio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'pax_cobra' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PaxCobra(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('pax_cobra', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'paxn_cobra' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PaxnCobra(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('paxn_cobra', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'descuento' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Descuento(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('descuento', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folio_cancela' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolioCancela(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folio_cancela', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'facturada' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Facturada(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('facturada', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'fecha_cupon' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FechaCupon(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('fecha_cupon', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_aplica' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpAplica(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_aplica', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_id_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpIdServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_id_servicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_monto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpMonto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_monto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_adulto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpAdulto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_adulto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_menor' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpMenor(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_menor', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_cupon' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpCupon(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_cupon', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folio_pago' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolioPago(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folio_pago', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cb_aplica' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CbAplica(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cb_aplica', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cb_monto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CbMonto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cb_monto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cb_inicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CbInicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cb_inicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cb_final' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CbFinal(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cb_final', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'iva_porc' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IvaPorc(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('iva_porc', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'comision' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Comision(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('comision', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'comision_n' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ComisionN(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('comision_n', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'tipo' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Tipo(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('tipo', $alias, $tableAlias);
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
     * Representa la columna 'folio_ticket' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolioTicket(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folio_ticket', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_pickup' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpPickup(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_pickup', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_id_vendedor' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpIdVendedor(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_id_vendedor', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_comision' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpComision(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_comision', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_hora_servicio' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpHoraServicio(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_hora_servicio', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'dp_aplica' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DpAplica(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('dp_aplica', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'dp_monto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DpMonto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('dp_monto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'comision_v' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ComisionV(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('comision_v', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'precio_muestra' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PrecioMuestra(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('precio_muestra', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'con_credito' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ConCredito(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('con_credito', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_id' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpId(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_id', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_id_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpIdLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_id_locacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'tf_aplica' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TfAplica(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('tf_aplica', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'tf_monto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TfMonto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('tf_monto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folio_boleto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolioBoleto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folio_boleto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'up_fecha_alta' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpFechaAlta(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('up_fecha_alta', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'uniqid_reserva' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UniqidReserva(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('uniqid_reserva', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'tf_id_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TfIdLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('tf_id_locacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'tf_fecha' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TfFecha(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('tf_fecha', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'dp_fecha' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DpFecha(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('dp_fecha', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'alta_id_locacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function AltaIdLocacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('alta_id_locacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cont_ticket' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ContTicket(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cont_ticket', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'momento_edicion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MomentoEdicion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('momento_edicion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'tf_hota' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TfHota(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('tf_hota', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'cb_cashback' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CbCashback(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cb_cashback', $alias, $tableAlias);
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
     * Representa la columna 'cupon_foto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CuponFoto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cupon_foto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'pax_noshow' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PaxNoshow(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('pax_noshow', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_us_alta' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdUsAlta(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_us_alta', $alias, $tableAlias);
    }
}