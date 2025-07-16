<?php
namespace App\Services;

use App\Interfaces\IOrdenesService;
use App\Utils\SqlTemplateLoader;
USe App\Database\EbPhotoDelphinusContext;

class OrdenesService implements IOrdenesService
{
    /**
     * @var EbPhotoDelphinusContext
     */
    private EbPhotoDelphinusContext $repo;
    /**
     * OrdenesService constructor.
     *
     * @param EbPhotoDelphinusContext $context
     */

    public function __construct(EbPhotoDelphinusContext $context)
    {
        $this->repo = $context;
    }


    public function index(array $filtros): array
    {
        // Implementación de la lógica para obtener órdenes pendientes
        $sql = SqlTemplateLoader::load('ordenes', [
    'SELECT_CLAUSE'   => '',
    'CONDITIONS'      => "
    dvpv.status = :status
    and cfav.fecha_nado >= :desde
    and cfav.fecha_nado <= :hasta
    and dvpv.tipos = 'F'
    ",
    'GROUP_BY_CLAUSE' => '',
    'ORDER_BY_CLAUSE' => 'ORDER BY cfav.fecha_nado DESC',
]);
       return $this->repo->select($sql, $filtros );


    }

}
