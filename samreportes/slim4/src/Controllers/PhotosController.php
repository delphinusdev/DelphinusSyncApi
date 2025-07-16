<?php
namespace App\Controllers;

use App\Config\Configuration;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ApiResponse;
use App\Services\PhotosService;
use App\Utils\Path;
use Throwable;

class PhotosController
{
    private PhotosService $foto;

    public function __construct(PhotosService $foto)
    {
        $this->foto = $foto;
    }

    public function configSync(Request $request, Response $response, string $location_code): Response
    {
        $config = $this->foto->getConfigLocaciones($location_code);
        if (empty($config)) {
            $response->getBody()->write(json_encode(ApiResponse::error('Configuración no encontrada')));
            return $response->withStatus(404, 'Configuración no encontrada')->withHeader('Content-Type', 'application/json');
        }


        $response->getBody()->write(json_encode(ApiResponse::success($config,'Configuración de sincronización obtenida')));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function configSyncNet(Request $request, Response $response, string $location_code): Response
    {
        $config = $this->foto->getConfigLocacionesNet($location_code);
        if (empty($config)) {
            $response->getBody()->write(json_encode(ApiResponse::error('Configuración no encontrada')));
            return $response->withStatus(404, 'Configuración no encontrada')->withHeader('Content-Type', 'application/json');
        }


        $response->getBody()->write(json_encode(ApiResponse::success($config, 'Configuración de sincronización obtenida')));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function clouds(Request $request, Response $response, string $fechad, string $fechah, string $location): Response
    {
        try {
            if (empty($fechad) || empty($fechah) || empty($location)) {
                throw new \InvalidArgumentException('Parámetros requeridos: fechad, fechah, location');
            }

            $locacion = Configuration::getLocation($location);

            if (empty($locacion)) {
                throw new \RuntimeException('Locación no encontrada');
            }

            $result = $this->foto->clouds($fechad, $fechah, $locacion, 'cloud');
            $response->getBody()->write(json_encode(ApiResponse::success($result, 'Órdenes obtenidas')));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $ex) {
            $response->getBody()->write(json_encode(ApiResponse::error($ex->getMessage())));
            return $response->withStatus(400, 'Error')->withHeader('Content-Type', 'application/json');
        }
    }

    public function comprasEnLinea(Request $request, Response $response,string $location, string $fechad, string $fechah): Response
    {
        try {
            if (empty($fechad) || empty($fechah) || empty($location)) {
                throw new \InvalidArgumentException('Parámetros requeridos: fechad, fechah, location');
            }

            $locacion = Configuration::getLocation($location);

            if (empty($locacion)) {
                throw new \RuntimeException('Locación no encontrada');
            }

            $result = $this->foto->clouds($fechad, $fechah, $locacion, 'compras_en_linea');
            $response->getBody()->write(json_encode(ApiResponse::success($result, 'Órdenes obtenidas')));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $ex) {
            $response->getBody()->write(json_encode(ApiResponse::error($ex->getMessage())));
            return $response->withStatus(400, 'Error')->withHeader('Content-Type', 'application/json');
        }
    }

    public function thumbs(Request $request, Response $response, string $location, string $fechad, string $fechah): Response
    {
        try {
            if (empty($fechad) || empty($fechah) || empty($location)) {
                throw new \InvalidArgumentException('Parámetros requeridos: fechad, fechah, location');
            }

            $locacion = Configuration::getLocation($location);

            if (empty($locacion)) {
                throw new \RuntimeException('Locación no encontrada');
            }

            $result = $this->foto->thumbs($fechad, $fechah, $locacion);
            $response->getBody()->write(json_encode(ApiResponse::success($result, 'thumbs obtenidas')));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $ex) {
            $response->getBody()->write(json_encode(ApiResponse::error($ex->getMessage())));
            return $response->withStatus(400, 'Error')->withHeader('Content-Type', 'application/json');
        }
    }

    public function createModel(Request $request, Response $response, string $tabla): Response
    {
        $table = $tabla ?? null;

        try {
            if (!$table) {
                throw new \InvalidArgumentException('El parámetro tabla es obligatorio');
            }



            if (!$table) {
                throw new \InvalidArgumentException('El parámetro id_reserva_base es obligatorio');
            }
            $model = $this->foto->createModel($table);
        } catch (Throwable $ex) {
            $model = 'Error al obtener órdenes' . $ex->getMessage();
        }

        $response->getBody()->write($model);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
