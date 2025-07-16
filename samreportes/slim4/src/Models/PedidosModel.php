<?php
namespace App\Models;

use \App\Utils\Column;
use \App\Models\BaseModel;

class PedidosModel extends BaseModel
{
    protected static string $tableName = 'PEDIDOS';

    public int $IDPEDIDO;
    public string $PEDIDO;
    public \DateTime $FECHA;
    public string $FOTOS;
    public int $IDCLIENTE;
    public float $COSTO;
    public int $ESTADO;
    public int $ACTIVO;
    public ?int $IDLOCACION;
    public ?string $TXNID;
    public int $FOTOENTREGADA;
    public ?string $FOTOSPHOTOBOOK;
    public int $FOTOPHOTOBOOKENTREGADA;
    public ?int $FORMA_PAGO;
    public ?int $MONEDA;
    public ?string $ID_NOTIFICACION;
    public ?string $NOTIFICACION;
    public ?string $MENSAJE;
    public ?int $ID_GRUPO;
    public ?int $VIDEO;
    public ?float $DESCUENTO;
    public ?int $CORREO_ENVIADO;
    public ?int $VIDEO_ENTREGADO;
    public int $SINCRONIZADO_HUBSPOT;
    public string $IDIOMA;
    public ?string $FOTOGRAFO;
    public ?\DateTime $FECHA_PAGO;
    public ?int $STATUS_FOTOGRAFO;
    public ?int $total_fotos;
    public ?float $CARGOPESOS;
    public ?float $COMISION;
    public ?float $DEPOSITADO;
    public ?float $CARGOTIPOCAMBIO;
    public ?string $CARGOINFO;
    public ?string $CARGOCARDINFO;
    public ?int $VENTA_OFF_LINE;
    public ?int $ID_VENTA;
    public ?int $IDPEDIDOLOC;
    public ?int $video_clip;

    /**
     * Representa la columna 'IDPEDIDO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDPEDIDO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('IDPEDIDO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'PEDIDO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function PEDIDO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('PEDIDO', $alias, $tableAlias);
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
     * Representa la columna 'FOTOS' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FOTOS(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FOTOS', $alias, $tableAlias);
    }

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
     * Representa la columna 'COSTO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function COSTO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('COSTO', $alias, $tableAlias);
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
     * Representa la columna 'ACTIVO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ACTIVO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ACTIVO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'IDLOCACION' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDLOCACION(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('IDLOCACION', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'TXNID' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TXNID(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('TXNID', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'FOTOENTREGADA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FOTOENTREGADA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FOTOENTREGADA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'FOTOSPHOTOBOOK' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FOTOSPHOTOBOOK(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FOTOSPHOTOBOOK', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'FOTOPHOTOBOOKENTREGADA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FOTOPHOTOBOOKENTREGADA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FOTOPHOTOBOOKENTREGADA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'FORMA_PAGO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FORMAPAGO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FORMA_PAGO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'MONEDA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MONEDA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('MONEDA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'ID_NOTIFICACION' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDNOTIFICACION(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ID_NOTIFICACION', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'NOTIFICACION' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NOTIFICACION(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('NOTIFICACION', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'MENSAJE' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MENSAJE(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('MENSAJE', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'ID_GRUPO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDGRUPO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('ID_GRUPO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'VIDEO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function VIDEO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('VIDEO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'DESCUENTO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DESCUENTO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('DESCUENTO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CORREO_ENVIADO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CORREOENVIADO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CORREO_ENVIADO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'VIDEO_ENTREGADO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function VIDEOENTREGADO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('VIDEO_ENTREGADO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'SINCRONIZADO_HUBSPOT' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function SINCRONIZADOHUBSPOT(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('SINCRONIZADO_HUBSPOT', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'IDIOMA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDIOMA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('IDIOMA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'FOTOGRAFO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FOTOGRAFO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FOTOGRAFO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'FECHA_PAGO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FECHAPAGO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('FECHA_PAGO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'STATUS_FOTOGRAFO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function STATUSFOTOGRAFO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('STATUS_FOTOGRAFO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'total_fotos' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function TotalFotos(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('total_fotos', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CARGOPESOS' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CARGOPESOS(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CARGOPESOS', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'COMISION' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function COMISION(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('COMISION', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'DEPOSITADO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DEPOSITADO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('DEPOSITADO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CARGOTIPOCAMBIO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CARGOTIPOCAMBIO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CARGOTIPOCAMBIO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CARGOINFO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CARGOINFO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CARGOINFO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'CARGOCARDINFO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CARGOCARDINFO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('CARGOCARDINFO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'VENTA_OFF_LINE' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function VENTAOFFLINE(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('VENTA_OFF_LINE', $alias, $tableAlias);
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
     * Representa la columna 'IDPEDIDOLOC' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IDPEDIDOLOC(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('IDPEDIDOLOC', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'video_clip' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function VideoClip(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('video_clip', $alias, $tableAlias);
    }
}