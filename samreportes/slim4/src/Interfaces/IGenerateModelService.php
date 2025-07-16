<?php
namespace App\Interfaces;
interface IGenerateModelService
{
    /**
     * Genera un folio basado en los filtros proporcionados.
     *
     * @param array $filtros Filtros para la generación del folio.
     * @return array Resultado de la generación del folio.
     */
    public static function CreateModel(array $filtros): array;
}