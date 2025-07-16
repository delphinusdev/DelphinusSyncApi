<?php

namespace App\Services;

use App\Database\SqlSrvGenericContext;
use App\Services\GenerateModeloService;
use App\Models\PhotoshareModel\TbSharedModel as tbshared;
use App\Utils\FilterExtracUrl;

class PhotoshareService
{
    private SqlSrvGenericContext $repo;
    private GenerateModeloService $modelo;

    public function __construct(SqlSrvGenericContext $photoshareContext, GenerateModeloService $modelo)
    {
        $this->repo = $photoshareContext; 
        $this->modelo = $modelo;
    }

    public function createModel($table)
    {
        
        $query = $this->modelo->getTableColumns($table)->build();
        $tabla = $this->repo->select($query['sql'],$query['params']);
        $model =  $this->modelo->generateModelClass($table, $tabla, 'App\\Models');
        return $model;
    }

    public function share($fechad,$fechah,$location):array
    {
        
        $result = [];
        $query = tbshared::from('t')
        ->newQuery()
        ->select([tbshared::ConfirmaId('id_grupo_fotos','p'),tbshared::ImageUrl(null,'p'),tbshared::LocationId(null,'p')])
        ->from(tbshared::tableName('p'))
        ->where(sprintf('CAST(%s AS DATE)',tbshared::CreatedAt(null,'p')),'>=',$fechad)
        ->where(sprintf('CAST(%s AS DATE)',tbshared::CreatedAt(null,'p')),'<=',$fechah)
        ->where(sprintf('CAST(%s AS VARCHAR(20))',tbshared::LocationId(null,'p')),'=',$location)
        ->where(tbshared::StatusPago(null,'p'),'=',1)
        
        ->build();

        if (!empty($this->repo->select($query['sql'], $query['params']))); {
            $query_result = $this->repo->select($query['sql'], $query['params']);
            // echo count($query_result);
            // exit;
            foreach ($query_result as $key => $val) {
                $url = FilterExtracUrl::extractUrl($val['image_url']);
                if ($url != null) {
                    $result[] = array('tipo' => 'photoshare', 'subfolder' => $url['subfolder'], 'uniqid' => $url['basename'], 'location_code' => $val['location_id']);
                }
            }
        }



        
        return $result ;
    }
}
