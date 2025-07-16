<?php
namespace App\Interfaces;


interface IGeneraFolioService
{
    /**
     * Genera un folio basado en los filtros proporcionados.
     *
     * @param array $filtros Filtros para la generación del folio.
     * @return array Resultado de la generación del folio.
     */
    public function getFolio(int $id_servicio, int $id_habitat): array
    ;
}