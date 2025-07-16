<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ApiResponse;
use App\Services\PhotoshareService;
use App\Services\PhotosService;
use App\Utils\Path;
use Throwable;

class PhotoshareController
{
    private PhotoshareService $photoshare;

    public function __construct(PhotoshareService $photoshare)
    {
        $this->photoshare = $photoshare;
    }

    public function share(Request $request, Response $response, string $location , string $fechad, string $fechah): Response
    {
        // $req = $request->getParsedBody();
        $req = $fechad;

        try {
            if ($req === null || empty($req)) { // Mejorar la verificación de parámetros
                throw new \InvalidArgumentException('Faltan parámetros en la solicitud.');
            }

            $data = $this->photoshare->share($fechad,$fechah,$location);

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
            $model = $this->photoshare->createModel($table);
        } catch (Throwable $ex) {
            $model = 'Error al obtener órdenes' . $ex->getMessage();
        }

        $response->getBody()->write($model);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
