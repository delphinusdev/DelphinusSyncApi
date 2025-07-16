<?php
namespace App\Models\CloudsModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class GruposFotosLocacionesModel extends BaseModel
{
    protected static string $tableName = 'grupos_fotos_locaciones';

    public int $idgrupofotoslocaciones;
    public int $idgrupofoto;
    public int $idfoto;
    public int $id_grupos_fotos;
    public string $uniqid;
    public \DateTime $fecha;
    public string $location_code;
    public int $idlocation;
    public ?string $nombres;
    public ?string $apellidos;
    public ?string $correo;
    public ?int $pax;
    public ?int $id_idioma;
    public ?string $noreservacion;
    public string $folder;
    public ?int $sincronizadofoto;
    public int $STATUSFOTO;
    public int $STATUSFOTOTHUMB;
    public int $STATUSCOMPRA;
    public ?string $SWIM_CODE;
    public ?int $id_fotografo;
    public ?string $nombre_fotografo;
    public ?bool $watermark;
    public ?string $video;
    public ?int $id_delfin;
    public ?string $nombre_delfin;
    public ?string $confirma;
    public ?int $id_reservacion;
    public ?int $STATUSVIDEO;
    public ?int $ID_VENTA;

    /**
     * Representa la columna 'idgrupofotoslocaciones' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Idgrupofotoslocaciones(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('idgrupofotoslocaciones', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'idgrupofoto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Idgrupofoto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('idgrupofoto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'idfoto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Idfoto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('idfoto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_grupos_fotos' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdGruposFotos(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_grupos_fotos', $alias, $tableAlias);
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
     * Representa la columna 'fecha' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Fecha(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('fecha', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'location_code' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function LocationCode(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('location_code', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'idlocation' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Idlocation(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('idlocation', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nombres' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Nombres(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nombres', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'apellidos' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Apellidos(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('apellidos', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'correo' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Correo(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('correo', $alias, $tableAlias);
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
     * Representa la columna 'noreservacion' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Noreservacion(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('noreservacion', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folder' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Folder(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folder', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'sincronizadofoto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Sincronizadofoto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('sincronizadofoto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'STATUSFOTO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function STATUSFOTO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('STATUSFOTO', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'STATUSFOTOTHUMB' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function STATUSFOTOTHUMB(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('STATUSFOTOTHUMB', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'STATUSCOMPRA' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function STATUSCOMPRA(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('STATUSCOMPRA', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'SWIM_CODE' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function SWIMCODE(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('SWIM_CODE', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_fotografo' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdFotografo(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_fotografo', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nombre_fotografo' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NombreFotografo(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nombre_fotografo', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'watermark' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Watermark(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('watermark', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'video' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Video(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('video', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_delfin' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdDelfin(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_delfin', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'nombre_delfin' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function NombreDelfin(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nombre_delfin', $alias, $tableAlias);
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
     * Representa la columna 'STATUSVIDEO' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function STATUSVIDEO(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('STATUSVIDEO', $alias, $tableAlias);
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
}