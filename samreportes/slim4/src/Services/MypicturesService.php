<?php

namespace App\Services;

use App\Database\SqlSrvGenericContext;
use App\Services\GenerateModeloService;
use App\Models\MypicturesModel\CatalogoFotosLocacionesAppModel as catalogoFotos;

class MypicturesService
{
    private SqlSrvGenericContext $repo;
    private GenerateModeloService $modelo;

    public function __construct(SqlSrvGenericContext $mypicturesContext, GenerateModeloService $modelo)
    {
        $this->repo = $mypicturesContext;
        $this->modelo = $modelo;
    }

    public function createModel($table)
    {

        $query = $this->modelo->getTableColumns($table)->build();
        $tabla = $this->repo->select($query['sql'], $query['params']);
        $model =  $this->modelo->generateModelClass($table, $tabla, 'App\\Models');
        return $model;
    }

    public function mypictures($fechad, $fechah, $location): array
    {

        $result = [];
        $query = catalogoFotos::from('p')
            ->newQuery()
            ->select([
                "'app' AS tipo",
                sprintf('%s AS subfolder',catalogoFotos::Folder(null, 'p')),
                 catalogoFotos::uniqid(null, 'p'),
                  catalogoFotos::LocationCode(null, 'p')])
                   
            ->from(catalogoFotos::tableName('p'))
            ->where(sprintf('CAST(%s AS DATE)', catalogoFotos::Fecha(null, 'p')), '>=', $fechad)
            ->where(sprintf('CAST(%s AS DATE)', catalogoFotos::Fecha(null, 'p')), '<=', $fechah)
            ->where(sprintf('CAST(%s AS VARCHAR(20))', catalogoFotos::LocationCode(null, 'p')), '=', $location)
            ->build();

            $query_result = $this->repo->select($query['sql'], $query['params']);




        return $query_result;
    }
}
