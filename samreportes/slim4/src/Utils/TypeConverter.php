<?php

namespace App\Utils;

class TypeConverter
{
    /**
     * Recorre un array de resultados y convierte los campos especificados a integer.
     * Maneja correctamente los valores NULL.
     *
     * @param array $data El array de datos a procesar.
     * @param array $numericKeys Un array con los nombres de las claves a convertir.
     * @return array El array de datos con los tipos corregidos.
     */
    public static function castNumericFields(array $data, array $numericKeys): array
    {
        foreach ($data as &$row) { // Usar '&' para modificar el array original
            foreach ($numericKeys as $key) {
                // Solo convierte el valor si existe y no es nulo
                if (isset($row[$key])) {
                    $row[$key] = (int)$row[$key];
                }
            }
        }
        unset($row); // Romper la referencia del bucle

        return $data;
    }
}