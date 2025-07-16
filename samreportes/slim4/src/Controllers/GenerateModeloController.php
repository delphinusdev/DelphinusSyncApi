<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ApiResponse;
use Throwable;
use App\Services\GenerateModeloService;

class GenerateModeloController
{

    private GenerateModeloService $generateModeloService;

    public function __construct(GenerateModeloService $generateModeloService)
    {
        $this->generateModeloService = $generateModeloService;
    }


    public function createModel(Request $request, Response $response, string $tabla): Response
    {
        $table = $tabla ?? null;
        try {
            $params = $request->getQueryParams();

            if (empty($params) || !isset($params['tabla'])) {
                if (!$table) {
                    throw new \InvalidArgumentException('El parámetro tabla es obligatorio');
                }
            } else {
                $table = $params['tabla'];
            }



            if (!$table) {
                throw new \InvalidArgumentException('El parámetro id_reserva_base es obligatorio');
            }
            $data = $this->generateModeloService->getTableColumns($table);

            $model =  $this->generateModeloService->generateModelClass($table, $data, 'App\\Models');
        } catch (Throwable $ex) {
            $payload = ApiResponse::error('Error al obtener órdenes', $ex->getMessage());
        }

        $response->getBody()->write($model);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
