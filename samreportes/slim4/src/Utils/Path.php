<?php
declare(strict_types=1);

namespace App\Utils;

/**
 * Helper para combinar rutas de archivos/directorios,
 * preservando rutas absolutas.
 */
class Path
{
    /**
     * Combina múltiples segmentos de ruta en una sola ruta.
     * Si el primer segmento es absoluto (comienza con '/'),
     * mantiene esa barra inicial.
     *
     * @param string ...$segments
     * @return string
     */
    public static function combine(string ...$segments): string
    {
        if (empty($segments)) {
            return '';
        }

        // 1) Primer segmento: sólo quitamos barras de final, no las de inicio
        $first = array_shift($segments);
        $path  = rtrim($first, '/\\');

        // 2) Resto de segmentos: quitamos ambas caras y los pegamos
        foreach ($segments as $segment) {
            $segment = trim($segment, '/\\');
            $path    .= DIRECTORY_SEPARATOR . $segment;
        }

        return $path;
    }
}
