<?php
namespace App\Models\MypicturesModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class CatalogoFotosLocacionesAppModel extends BaseModel
{
    protected static string $tableName = 'catalogo_fotos_locaciones_app';

    public int $id_catalogofotoslocacionesapp;
    public int $id_venta;
    public int $id_venta_foto;
    public int $id_grupo_fotos;
    public int $id_location;
    public string $location_code;
    public \DateTime $fecha;
    public string $swim_code;
    public string $programa;
    public \DateTime $swim_schedule;
    public string $folder;
    public ?int $id_fotografo;
    public string $nombre_fotografo;
    public string $nombrecliente;
    public string $uniqid;
    public ?int $sincronizadofoto;
    public int $STATUSFOTO;
    public ?string $cantidad;
    public ?string $observaciones;
    public ?int $status_pago;
    public ?bool $file_exist;
    public ?bool $folder_exist;

    /**
     * Representa la columna 'id_catalogofotoslocacionesapp' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdCatalogofotoslocacionesapp(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_catalogofotoslocacionesapp', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_venta' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdVenta(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_venta', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_venta_foto' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdVentaFoto(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_venta_foto', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_grupo_fotos' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdGrupoFotos(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_grupo_fotos', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'id_location' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function IdLocation(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id_location', $alias, $tableAlias);
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
     * Representa la columna 'swim_code' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function SwimCode(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('swim_code', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'programa' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Programa(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('programa', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'swim_schedule' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function SwimSchedule(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('swim_schedule', $alias, $tableAlias);
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
     * Representa la columna 'nombrecliente' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Nombrecliente(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('nombrecliente', $alias, $tableAlias);
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
     * Representa la columna 'cantidad' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Cantidad(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('cantidad', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'observaciones' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Observaciones(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('observaciones', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'status_pago' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function StatusPago(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('status_pago', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'file_exist' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FileExist(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('file_exist', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'folder_exist' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function FolderExist(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('folder_exist', $alias, $tableAlias);
    }
}