<?php

namespace App\Services;

use App\Config\Configuration;
use App\Database\SqlSrvGenericContext;
use App\Services\GenerateModeloService;
use App\Models\VideoClips\VideoClipModel as clips;
use App\Models\CloudsModel\PedidosModel as pedidos;
use App\Models\CloudsModel\GruposFotosLocacionesModel as grupos;
use App\Utils\TypeConverter;

class VideoClipsService
{
    private SqlSrvGenericContext $repo;
    private GenerateModeloService $modelo;

    public function __construct(SqlSrvGenericContext $videoclipsContext, GenerateModeloService $modelo)
    {
        $this->repo = $videoclipsContext;
        $this->modelo = $modelo;
    }

    public function createModel($table)
    {

        $query = $this->modelo->getTableColumns($table)->build();
        $tabla = $this->repo->select($query['sql'], $query['params']);
        $model =  $this->modelo->generateModelClass($table, $tabla, 'App\\Models');
        return $model;
    }

    public function tmdVideos(string $fechad, string $fechah, array $location): array
    {
        $query = pedidos::from('p')
            ->newQuery()
            // --- INICIO DE CAMBIOS ---
            ->select([
                pedidos::IDVENTA('IdVenta', 'p'),
                pedidos::IDPEDIDO('IdPedido', 'p'),
                "NULL AS ConfirmaId",
                "'tmd_videos' AS tipo",
                "'' AS subfolder",
                // Se renombra la columna 'video' a 'uniqid' para consistencia
                sprintf("ISNULL(%s,'NA')", grupos::Video('uniqid', 'c')),
                grupos::LocationCode(null, 'c')
            ])
            // --- FIN DE CAMBIOS ---
            ->from(sprintf('FOTOS.dbo.%s', pedidos::tableName('p')))
            ->innerJoin(
                sprintf('FOTOS.dbo.%s', grupos::tableName('c')),
                pedidos::IDGRUPO(null, 'p'),
                '=',
                sprintf('%s AND %s = %s', grupos::IdGruposFotos(null, 'c'), $this->caseLoc(), grupos::LocationCode(null, 'c'))
            )
            ->where(sprintf('CAST(%s AS DATE)', pedidos::FECHAPAGO(null, 'p')), '>=', $fechad)
            ->where(sprintf('CAST(%s AS DATE)', pedidos::FECHAPAGO(null, 'p')), '<=', $fechah)
            ->where(pedidos::IDLOCACION(null, 'p'), '=', $location[0])
            ->where(pedidos::ESTADO(null, 'p'), '=', 1)
            ->where(sprintf('ISNULL(%s,0)', pedidos::VIDEO(null, 'p')), '=', 1)
            ->where(sprintf("ISNULL(%s,'NA')", grupos::Video(null, 'c')), '<>', 'NA')
            ->groupBy(
                [
                    pedidos::IDVENTA(null, 'p'),
                    pedidos::IDPEDIDO(null, 'p'),
                    sprintf("ISNULL(%s,'NA')", grupos::Video(null, 'c')),
                    grupos::LocationCode(null, 'c')
                ]
            )
            // --- FIN DE CAMBIOS ---
            ->build();

        $data = $this->repo->select($query['sql'], $query['params']);
        
        // return TypeConverter::castNumericFields($data, ['IdVenta', 'IdPedido']);
        return $data;
    }

    public function clips($fechad, $fechah, $location): array
    {
        $query = pedidos::from('p')
            ->newQuery()
            ->select([
                sprintf("CAST(%s AS INT)", pedidos::IDVENTA(null, 'p')) . " AS IdVenta",
                sprintf("CAST(%s AS INT)", pedidos::IDPEDIDO(null, 'p')) . " AS IdPedido",
                "NULL AS ConfirmaId",
                "'clips' AS tipo",
                "'' as subfolder",
                clips::Uniqid(null, 'c'),
                clips::LocationCode(null, 'c')
            ])
            // --- FIN DEL CAMBIO ---
            ->from(sprintf('FOTOS.dbo.%s', pedidos::tableName('p')))
            ->innerJoin(
                sprintf('eb_photo_delphinus.dbo.%s', clips::tableName('c')),
                pedidos::IDGRUPO(null, 'p'),
                '=',
                sprintf('%s AND %s = %s', clips::IdGrupoFotos(null, 'c'), $this->caseLoc(), clips::LocationCode(null, 'c'))
            )
            ->where(sprintf('CAST(%s AS DATE)', pedidos::FECHAPAGO(null, 'p')), '>=', $fechad)
            ->where(sprintf('CAST(%s AS DATE)', pedidos::FECHAPAGO(null, 'p')), '<=', $fechah)
            ->where(pedidos::IDLOCACION(null, 'p'), '=', $location[0])
            ->where(pedidos::ESTADO(null, 'p'), '=', 1)
            ->where(sprintf('ISNULL(%s,0)', pedidos::VideoClip(null, 'p')), '=', 1)
            ->build();

        $data = $this->repo->select($query['sql'], $query['params']);
        
        // return TypeConverter::castNumericFields($data, ['IdVenta', 'IdPedido']);
        return $data;
    }

    private function caseLoc(): ?string
    {
        $locaciones = Configuration::getLocations();
        if (empty($locaciones)) {
            return null;
        }

        $whenStatements = '';
        foreach ($locaciones as $key => $val) {


            $whenStatements .= sprintf("WHEN %s = %d THEN '%s' ", pedidos::IDLOCACION(null, 'p'), $val[0], $key);
        }

        return sprintf("CASE %s ELSE '' END", $whenStatements);
    }
}
