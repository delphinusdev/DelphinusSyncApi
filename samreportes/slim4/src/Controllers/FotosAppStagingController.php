<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ApiResponse;
use App\Services\FotosAppStagingService;
use App\Config\Configuration;
use Throwable;

class FotosAppStagingController
{
    private FotosAppStagingService $fotosAppStaging;

    public function __construct(FotosAppStagingService $fotosAppStaging)
    {
        $this->fotosAppStaging = $fotosAppStaging;
    }
    public function configSync(Request $request, Response $response, string $location_code): Response
    {
        $config = $this->fotosAppStaging->getConfigLocaciones($location_code);
        if (empty($config)) {
            $response->getBody()->write(json_encode(ApiResponse::error('Configuración no encontrada')));
            return $response->withStatus(404, 'Configuración no encontrada')->withHeader('Content-Type', 'application/json');
        }


        $response->getBody()->write(json_encode(ApiResponse::success($config,'Configuración de sincronización obtenida')));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function configSyncNet(Request $request, Response $response, string $location_code): Response
    {
        $config = $this->fotosAppStaging->getConfigLocacionesNet($location_code);
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

            $result = $this->fotosAppStaging->clouds($fechad, $fechah, $locacion, 'cloud');
            $response->getBody()->write(json_encode(ApiResponse::success($result, 'Órdenes obtenidas')));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $ex) {
            $response->getBody()->write(json_encode(ApiResponse::error($ex->getMessage())));
            return $response->withStatus(400, 'Error')->withHeader('Content-Type', 'application/json');
        }
    }
    public function cloudsStoreProcedure(Request $request, Response $response, string $fechad, string $fechah, string $location): Response
    {
        try {
            if (empty($fechad) || empty($fechah) || empty($location)) {
                throw new \InvalidArgumentException('Parámetros requeridos: fechad, fechah, location');
            }

            $locacion = Configuration::getLocation($location);

            if (empty($locacion)) {
                throw new \RuntimeException('Locación no encontrada');
            }

            $result = $this->fotosAppStaging->cloudsStoreProcedure($fechad, $fechah, $locacion, 'cloud');
            $response->getBody()->write(json_encode(ApiResponse::success($result, 'Órdenes obtenidas')));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $ex) {
            $response->getBody()->write(json_encode(ApiResponse::error($ex->getMessage())));
            return $response->withStatus(400, 'Error')->withHeader('Content-Type', 'application/json');
        }
    }
       public function comprasEnLineaStoreProcedure(Request $request, Response $response, string $fechad, string $fechah, string $location): Response
    {
        try {
            if (empty($fechad) || empty($fechah) || empty($location)) {
                throw new \InvalidArgumentException('Parámetros requeridos: fechad, fechah, location');
            }

            $locacion = Configuration::getLocation($location);

            if (empty($locacion)) {
                throw new \RuntimeException('Locación no encontrada');
            }

            $result = $this->fotosAppStaging->comprasEnLineaStoreProcedure($fechad, $fechah, $locacion, 'cloud');
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

            $result = $this->fotosAppStaging->thumbs($fechad, $fechah, $locacion);
            $response->getBody()->write(json_encode(ApiResponse::success($result, 'thumbs obtenidas')));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $ex) {
            $response->getBody()->write(json_encode(ApiResponse::error($ex->getMessage())));
            return $response->withStatus(400, 'Error')->withHeader('Content-Type', 'application/json');
        }
    }

    public function mypictures(Request $request, Response $response,string $location , string $fechad, string $fechah): Response
    {
        // $req = $request->getParsedBody();
        // $req = $fechad;

        try {
            // if ($req === null || empty($req)) { // Mejorar la verificación de parámetros
            //     throw new \InvalidArgumentException('Faltan parámetros en la solicitud.');
            // }

            $data = $this->fotosAppStaging->mypictures($fechad,$fechah,$location);

            // Respuesta exitosa
            $response->getBody()->write(json_encode(ApiResponse::success($data)));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\InvalidArgumentException $ex) {
            // Error específico de parámetros faltantes o inválidos (cliente)
            $response->getBody()->write(json_encode(ApiResponse::error()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // 400 Bad Request

        } catch (Throwable $ex) {
            // Otros errores inesperados (servidor)
            // En producción, evita exponer detalles sensibles del error como $ex->getMessage()
            // Podrías registrar el error completo y enviar un mensaje genérico.
            $errorPayload = [
                'success' => false,
                'message' => 'Ocurrió un error interno en el servidor.',
                // 'detailed_message' => $ex->getMessage() // Solo para depuración/desarrollo
            ];
            error_log("Error en Photoshares: " . $ex->getMessage() . " en " . $ex->getFile() . " línea " . $ex->getLine()); // Registra el error

            $response->getBody()->write(json_encode($errorPayload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500); // 500 Internal Server Error
        }
    }
    public function share(Request $request, Response $response, string $location , string $fechad, string $fechah): Response
    {
        // $req = $request->getParsedBody();
        $req = $fechad;

        try {
            if ($req === null || empty($req)) { // Mejorar la verificación de parámetros
                throw new \InvalidArgumentException('Faltan parámetros en la solicitud.');
            }

            $data = $this->fotosAppStaging->share($fechad,$fechah,$location);

            // Respuesta exitosa
            $response->getBody()->write(json_encode(ApiResponse::success($data)));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\InvalidArgumentException $ex) {
            // Error específico de parámetros faltantes o inválidos (cliente)
            $response->getBody()->write(json_encode(ApiResponse::error()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // 400 Bad Request

        } catch (Throwable $ex) {
            // Otros errores inesperados (servidor)
            // En producción, evita exponer detalles sensibles del error como $ex->getMessage()
            // Podrías registrar el error completo y enviar un mensaje genérico.
            $errorPayload = [
                'success' => false,
                'message' => 'Ocurrió un error interno en el servidor.',
                // 'detailed_message' => $ex->getMessage() // Solo para depuración/desarrollo
            ];
            error_log("Error en Photoshares: " . $ex->getMessage() . " en " . $ex->getFile() . " línea " . $ex->getLine()); // Registra el error

            $response->getBody()->write(json_encode($errorPayload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500); // 500 Internal Server Error
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
            $model = $this->fotosAppStaging->createModel($table);
        } catch (Throwable $ex) {
            $model = 'Error al obtener órdenes' . $ex->getMessage();
        }

        $response->getBody()->write($model);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
