<?php
namespace App\Models;
final class ReservaFilter
{
    public int|string $id;
    public string      $uniqid_reserva;

    public function __construct(int|string $id, string $uniqid_reserva)
    {
        if (! is_int($id) && ! is_string($id)) {
            throw new \InvalidArgumentException("El campo 'id' debe ser int o string.");
        }
        if ($uniqid_reserva === '') {
            throw new \InvalidArgumentException("El campo 'uniqid_reserva' no puede estar vacío.");
        }
        
        

        $this->id             = $id;
        $this->uniqid_reserva = $uniqid_reserva;
    }
}