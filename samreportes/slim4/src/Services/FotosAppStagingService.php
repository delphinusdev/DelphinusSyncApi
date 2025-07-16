<?php

namespace App\Services;

use App\Database\SqlSrvGenericContext;
use App\Services\GenerateModeloService;
use App\Models\FotosStagingModel\StgCatalogoFotosLocacionesAppMypicturesModel as catalogoFotos;
use App\Models\FotosStagingModel\StgPedidosFotosModel as pedidos;
use App\Models\FotosStagingModel\StgGruposFotosLocacionesModel as grupos;
use App\Models\FotosStagingModel\StgGruposFotosLocacionesModel as gruposClouds;
use App\Models\FotosStagingModel\StgSharedPhotoshareModel as tbshared;
use App\Utils\FilterExtracUrl;
use App\Utils\ConfigSyncLocaciones;

class FotosAppStagingService
{
    private SqlSrvGenericContext $repo;
    private GenerateModeloService $modelo;

    public function __construct(SqlSrvGenericContext $fotosappstagingContext, GenerateModeloService $modelo)
    {
        $this->repo = $fotosappstagingContext;
        $this->modelo = $modelo;
    }

    public function createModel($table)
    {

        $query = $this->modelo->getTableColumns($table)->build();
        $tabla = $this->repo->select($query['sql'], $query['params']);
        $model =  $this->modelo->generateModelClass($table, $tabla, 'App\\Models');
        return $model;
    }

    public function getConfigLocaciones(string $location_code): ?string
    {
        return ConfigSyncLocaciones::getConfig($location_code);
    }

    public function getConfigLocacionesNet(string $location_code): ?string
    {
        return ConfigSyncLocaciones::getConfigNet($location_code);
    }

    public function clouds(string $fechad, string $fechah, array $location, string $tipo = 'clouds'): array
    {
        // 1. Obtener los datos crudos de los pedidos.
        $pedidosResult = $this->fetchPedidoData($fechad, $fechah, $location, $tipo);
        if (empty($pedidosResult)) {
            return [];
        }

        // 2. Procesar los datos para obtener listas de IDs únicos.
        $preparedIds = $this->prepareUniqueIds($pedidosResult);
        if (empty($preparedIds['photo_ids'])) {
            return [];
        }


        $tempTableName = null;

        try {
            // 3. Crear e insertar los IDs en una tabla temporal.
            $tempTableName = $this->createAndPopulateTempTable($preparedIds['photo_ids']);

            // 4. Ejecutar la consulta final usando la tabla temporal.
            return $this->fetchPhotosWithTempTable(
                $tempTableName,
                $preparedIds['group_ids'],
                $location[0],
                $location[1],
                $tipo
            );
        } finally {
            // 5. Asegurarse de que la tabla temporal se elimine siempre.
            if ($tempTableName) {
                $this->repo->executeStatement(sprintf("DROP TABLE %s;", $tempTableName));
            }
        }
        return $preparedIds;
    }
    private function fetchPedidoData(string $fechad, string $fechah, array $location, string $tipo): array
    {
        $simbolo = ($tipo === 'compras_en_linea') ? '=' : '>';

        $pedidosQuery = pedidos::from('p')
            ->newQuery()
            ->select([pedidos::IDLOCACION(null, 'p'), pedidos::IDGRUPO(null, 'p'), pedidos::FOTOS(null, 'p')])
            ->from(pedidos::tableName('p'))
            ->where(sprintf('CAST(%s AS DATE)', pedidos::FECHAPAGO(null, 'p')), '>=', $fechad)
            ->where(sprintf('CAST(%s AS DATE)', pedidos::FECHAPAGO(null, 'p')), '<=', $fechah)
            ->where(pedidos::ESTADO(null, 'p'), '=', 1)
            ->where(sprintf('ISNULL(%s,0)', pedidos::IDVENTA(null, 'p')), "{$simbolo}", 0)
            ->where(pedidos::IDLOCACION(null, 'p'), '=', $location[0])
            ->where(pedidos::FOTOS(null, 'p'), '!=', '')
            ->orderBy(pedidos::IDPEDIDO())
            ->build();

        return $this->repo->select($pedidosQuery['sql'], $pedidosQuery['params']);
    }

    /**
     * PASO 2: Procesa el resultado de la consulta para extraer y limpiar los IDs.
     * @return array ['photo_ids' => [...], 'group_ids' => [...]]
     */
    private function prepareUniqueIds(array $pedidosResult): array
    {
        $gruposIds = array_unique(array_column($pedidosResult, 'ID_GRUPO'));

        $allFotosIds = [];
        foreach ($pedidosResult as $pedido) {
            $fotosDePedido = explode('|', $pedido['FOTOS']);
            $allFotosIds = array_merge($allFotosIds, $fotosDePedido);
        }

        $uniqueFotosIds = array_filter(array_unique($allFotosIds));

        return [
            'photo_ids' => array_values($uniqueFotosIds), // Re-indexa el array
            'group_ids' => array_values($gruposIds)      // Re-indexa el array
        ];
    }

    /**
     * PASO 3: Crea una tabla temporal y la llena con los IDs de las fotos.
     * @return string El nombre de la tabla temporal creada.
     */
    private function createAndPopulateTempTable(array $photoIds): string
    {
        // Nombre único para la tabla temporal para evitar colisiones.
        // Se usa '##' para una tabla temporal global, accesible entre sesiones.
        $tempTableName = '##TempPhotoIDs_' . uniqid();

        // Asumiendo que `Idfoto` es de tipo INT. Si es BIGINT, usa BIGINT.
        $createTempTableSql = sprintf("CREATE TABLE %s (Id INT PRIMARY KEY);", $tempTableName);
        $this->repo->executeStatement($createTempTableSql);

        // Define el tamaño del lote de inserción para evitar el límite de 1000 filas de SQL Server.
        // Se usa un valor ligeramente menor a 1000 para mayor seguridad.
        $batchSize = 500;

        // Divide el array de IDs en lotes.
        $photoIdBatches = array_chunk($photoIds, $batchSize);

        foreach ($photoIdBatches as $batch) {
            if (empty($batch)) {
                continue; // Skip empty batches
            }

            // Prepara los placeholders para el INSERT batch.
            $valuesPlaceholders = implode(',', array_fill(0, count($batch), '(?)'));

            // Construye la consulta INSERT para el lote actual.
            $insertSql = sprintf("INSERT INTO %s (Id) VALUES %s;", $tempTableName, $valuesPlaceholders);

            // Ejecuta la inserción para el lote.
            $this->repo->executeStatement($insertSql, $batch);
        }

        return $tempTableName;
    }

    public function thumbs(string $fechad, string $fechah, array $location): array
    {
        $query = gruposClouds::from('c')
            ->newQuery()
            ->select(["'thumbs' AS tipo", gruposClouds::Folder('subfolder', 'c'), gruposClouds::Uniqid(null, 'c'), gruposClouds::LocationCode(null, 'c')])
            ->from(sprintf('FOTOS.dbo.%s', gruposClouds::tableName('c')))
            ->where(sprintf('CAST(%s AS DATE)', gruposClouds::Fecha(null, 'c')), '>=', $fechad)
            ->where(sprintf('CAST(%s AS DATE)', gruposClouds::Fecha(null, 'c')), '<=', $fechah)
            ->where(gruposClouds::IdLocation(null, 'c'), '=', $location[0])
            ->build();

        return $this->repo->select($query['sql'], $query['params']);
    }

    /**
     * PASO 4: Realiza la consulta final uniendo la tabla de grupos con la tabla temporal.
     */
    private function fetchPhotosWithTempTable(string $tempTableName, array $groupIds, string $locationId, string $location_code, string $tipo): array
    {
        // Genera los placeholders (?) para la cláusula IN de los grupos.
        $groupPlaceholders = implode(',', array_fill(0, count($groupIds), '?'));

        $finalQuery = sprintf(
            "SELECT '%s' as 'tipo',
              g.Folder as 'subfolder', g.Uniqid as 'uniqid', '%s' as 'location_code', %s
             FROM %s
             INNER JOIN %s tmp ON %s = tmp.Id
             WHERE %s IN (%s) AND %s = ?",
            $tipo,
            $location_code,                        //select location_code
            grupos::IdGruposFotos(null, 'g'), // select from grupos
            grupos::tableName('g'),                 // from
            $tempTableName,                      // join
            grupos::Idfoto(null, 'g'),           // on
            grupos::IdGruposFotos(null, 'g'),    // where in (columna)
            $groupPlaceholders,                  // where in (placeholders)
            grupos::Idlocation(null, 'g')        // where
        );



        $finalParams = array_merge($groupIds, [$locationId]);

        return $this->repo->select($finalQuery, $finalParams);
    }

    public function mypictures($fechad, $fechah, $location): array
    {

        $result = [];
        $query = catalogoFotos::from('p')
            ->newQuery()
            ->select([
                "'app' AS tipo",
                sprintf('%s AS subfolder', catalogoFotos::Folder(null, 'p')),
                catalogoFotos::uniqid(null, 'p'),
                catalogoFotos::LocationCode(null, 'p')
            ])

            ->from(catalogoFotos::tableName('p'))
            ->where(sprintf('CAST(%s AS DATE)', catalogoFotos::Fecha(null, 'p')), '>=', $fechad)
            ->where(sprintf('CAST(%s AS DATE)', catalogoFotos::Fecha(null, 'p')), '<=', $fechah)
            ->where(sprintf('CAST(%s AS VARCHAR(20))', catalogoFotos::LocationCode(null, 'p')), '=', $location)
            ->build();

        $query_result = $this->repo->select($query['sql'], $query['params']);




        return $query_result;
    }

    public function share($fechad, $fechah, $location): array
    {

        $result = [];
        $query = tbshared::from('t')
            ->newQuery()
            ->select([tbshared::ConfirmaId('id_grupo_fotos', 'p'), tbshared::ImageUrl(null, 'p'), tbshared::LocationId(null, 'p')])
            ->from(tbshared::tableName('p'))
            ->where(sprintf('CAST(%s AS DATE)', tbshared::CreatedAt(null, 'p')), '>=', $fechad)
            ->where(sprintf('CAST(%s AS DATE)', tbshared::CreatedAt(null, 'p')), '<=', $fechah)
            ->where(sprintf('CAST(%s AS VARCHAR(20))', tbshared::LocationId(null, 'p')), '=', $location)
            ->where(tbshared::StatusPago(null, 'p'), '=', 1)

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




        return $result;
    }
}
