<?php
namespace App\Models\VideoClips;

use \App\Utils\Column;
use \App\Models\BaseModel;

class VideoClipModel extends BaseModel
{
    protected static string $tableName = 'video_clip';

    public int $id;
    public ?int $id_grupo_fotos;
    public ?string $uniqid;
    public ?int $status;
    public ?\DateTime $create_at;
    public ?int $size_in_bytes;
    public ?int $sync;
    public ?\DateTime $date_sync;
    public ?int $upload;
    public ?\DateTime $date_upload;
    public string $location_code;

    /**
     * Representa la columna 'id' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Id(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('id', $alias, $tableAlias);
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
     * Representa la columna 'create_at' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CreateAt(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('create_at', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'size_in_bytes' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function SizeInBytes(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('size_in_bytes', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'sync' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Sync(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('sync', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'date_sync' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DateSync(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('date_sync', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'upload' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Upload(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('upload', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'date_upload' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function DateUpload(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('date_upload', $alias, $tableAlias);
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
}