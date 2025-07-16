<?php
namespace App\Controllers;

use App\Interfaces\IOrdenesService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ApiResponse;
use Throwable;

class OrdenesController
{
    private IOrdenesService $service;

    public function __construct(IOrdenesService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, Response $response, array $args = []): Response
    {
        try {
            $params = $request->getQueryParams();

            $defaultParams = [
                'status' => 1, // Estado por defecto
                'desde'  => FECHA_HOY, // Fecha de inicio por defecto (hoy)
                'hasta'  => FECHA_HOY // Fecha de fin por defecto (hoy)
            ];



            $defaultParams = array_merge($defaultParams, $params);

            $data = $this->service->index($defaultParams);

            $payload = ApiResponse::success($data, 'Órdenes recuperadas correctamente');
        } catch (Throwable $ex) {
            $payload = ApiResponse::error('Error al obtener órdenes', $ex->getMessage());
        }

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function show(Request $request, Response $response, array $args = []): Response
    {
        $id = $args['id'];
        // Aquí podrías llamar a $this->service->getOrdenById($id)
        $response->getBody()->write("Producción show: " . htmlspecialchars($id));
        return $response;
    }
}
