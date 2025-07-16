<?php

namespace App\Utils;

/**
 * Una clase de utilidad para realizar búsquedas en arreglos de arreglos asociativos.
 * * Ejemplo de uso:
 * $datos = [
 * ['id' => 1, 'nombre' => 'Ana', 'ciudad' => 'Madrid'],
 * ['id' => 2, 'nombre' => 'Luis', 'ciudad' => 'Barcelona'],
 * ['id' => 3, 'nombre' => 'Ana', 'ciudad' => 'Sevilla'],
 * ];
 * * $busqueda = new Search($datos);
 * * // Encontrar el primer elemento donde 'nombre' es 'Ana'
 * $resultadoUnico = $busqueda->first('nombre', 'Ana'); 
 * // Devuelve: ['id' => 1, 'nombre' => 'Ana', 'ciudad' => 'Madrid']
 * * // Encontrar todos los elementos donde 'nombre' es 'Ana'
 * $todosLosResultados = $busqueda->findAll('nombre', 'Ana');
 * // Devuelve: [['id' => 1, ...], ['id' => 3, ...]]
 * * // Búsqueda estática (sin instanciar la clase)
 * $resultadoEstatico = Search::findFirst($datos, 'ciudad', 'Barcelona');
 * // Devuelve: ['id' => 2, 'nombre' => 'Luis', 'ciudad' => 'Barcelona']
 */
class Search
{
    /**
     * El arreglo de datos sobre el cual se realizarán las búsquedas.
     * Es de solo lectura para garantizar la inmutabilidad de los datos después de la construcción.
     * @var array
     */
    private readonly array $data;

    /**
     * Constructor de la clase.
     *
     * @param array $data El arreglo de datos para buscar.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Busca y devuelve el *primer* elemento que coincide con la clave y el valor.
     * Es ideal cuando esperas solo un resultado o no te importan los demás.
     *
     * @param string $key La clave (o "columna") a buscar.
     * @param mixed $value El valor a comparar.
     * @return array|null El primer arreglo coincidente o null si no se encuentra.
     */
    public function first(string $key, mixed $value): ?array
    {
        foreach ($this->data as $item) {
            // Comprueba si la clave existe y si su valor coincide
            if (isset($item[$key]) && $item[$key] === $value) {
                return $item; // Devuelve el primer elemento encontrado
            }
        }

        return null; // Devuelve null si no se encuentra ninguna coincidencia
    }

    /**
     * Busca y devuelve *todos* los elementos que coinciden con la clave y el valor.
     *
     * @param string $key La clave a buscar.
     * @param mixed $value El valor a comparar.
     * @return array Un arreglo con todas las coincidencias. Estará vacío si no hay resultados.
     */
    public function findAll(string $key, mixed $value): array
    {
        // array_filter es una excelente opción aquí
        $results = array_filter($this->data, function ($row) use ($key, $value) {
            return isset($row[$key]) && $row[$key] === $value;
        });

        // Reindexa el arreglo para evitar claves discontinuas (ej: 0, 2, 5)
        return array_values($results);
    }

    /**
     * Método estático para encontrar el primer elemento sin necesidad de instanciar la clase.
     * Útil para búsquedas rápidas y puntuales.
     *
     * @param array $data El arreglo donde buscar.
     * @param string $key La clave a buscar.
     * @param mixed $value El valor a comparar.
     * @return array|null El primer arreglo coincidente o null si no se encuentra.
     */
    public static function findFirst(array $data, string $key, mixed $value): ?array
    {
        foreach ($data as $item) {
            if (isset($item[$key]) && $item[$key] === $value) {
                return $item;
            }
        }

        return null;
    }
}