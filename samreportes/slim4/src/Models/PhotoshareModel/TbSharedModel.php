<?php
namespace App\Models\PhotoshareModel;

use \App\Utils\Column;
use \App\Models\BaseModel;

class TbSharedModel extends BaseModel
{
    protected static string $tableName = 'tb_shared';

    public int $id;
    public string $name;
    public string $email;
    public string $image_url;
    public string $confirma_id;
    public string $order_id;
    public string $location_id;
    public string $group_id;
    public int $enabled;
    public \DateTime $updated_at;
    public \DateTime $created_at;
    public ?int $status_mail;
    public ?int $status_pago;
    public ?int $mailitem_id;
    public ?string $mail_status;

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
     * Representa la columna 'name' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Name(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('name', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'email' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Email(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('email', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'image_url' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ImageUrl(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('image_url', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'confirma_id' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function ConfirmaId(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('confirma_id', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'order_id' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function OrderId(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('order_id', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'location_id' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function LocationId(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('location_id', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'group_id' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function GroupId(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('group_id', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'enabled' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function Enabled(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('enabled', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'updated_at' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function UpdatedAt(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('updated_at', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'created_at' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function CreatedAt(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('created_at', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'status_mail' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function StatusMail(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('status_mail', $alias, $tableAlias);
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
     * Representa la columna 'mailitem_id' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MailitemId(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('mailitem_id', $alias, $tableAlias);
    }

    /**
     * Representa la columna 'mail_status' en la base de datos.
     * @param string|null $alias Un alias opcional para la columna en la consulta.
     * @param string $tableAlias El alias de la tabla (ej. 'r').
     * @return \App\Utils\Column
     */
    public static function MailStatus(?string $alias = null, string $tableAlias = 'self'): Column
    {
        return new Column('mail_status', $alias, $tableAlias);
    }
}