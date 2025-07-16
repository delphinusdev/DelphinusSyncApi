<?php
namespace App\Controllers;
use Slim\Routing\RouteContext;
// use App\Interfaces\IGeneraFolioService;
use App\Services\ReservasService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ApiResponse;
use App\Services\fotos;
use App\Utils\Path;
use Throwable;

class ReservasController
{
    private ReservasService $reservasService;


    public function __construct(ReservasService $ReservasService)
    {
        $this->reservasService = $ReservasService;
    }



    public function getDatosBaseIsepaaa(Request $request, Response $response, string $id): Response
    {
        try {

            if (!$id) {
                throw new \InvalidArgumentException('El parámetro id es obligatorio');
            }
            
            $data = $this->reservasService->getDatosBaseIsepaaa($id);
            if (empty($data)) {
                throw new \RuntimeException('No se encontraron datos para la reserva con ID: ' . $id);
            }

            $payload = ApiResponse::success($data, 'Información de la reserva obtenida correctamente');
        } catch (\InvalidArgumentException $ex) {
            $payload = ApiResponse::error('Parámetro inválido', $ex->getMessage());
        } catch (\RuntimeException $ex) {
            $payload = ApiResponse::error('Error al obtener la reserva', $ex->getMessage());
        } catch (Throwable $ex) {
            $payload = ApiResponse::error('Error al obtener órdenes', $ex->getMessage());
        }
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CrearImpuesto(Request $request, Response $response, string $id): Response
    {
        try {
                $req = $request->getParsedBody();

            if (!$req) {
                throw new \InvalidArgumentException('no se recibieron datos');
            }


            if (!$id) {
                throw new \InvalidArgumentException('El parámetro id es obligatorio');
            }
            if ($req['cod_pais'] === 'MXQR') {
                throw new \RuntimeException('Esta reserva no requiere impuesto: ' . $id);
            }
            $data = $this->reservasService->CrearImpuesto(['id' => $id,'request' => $req]);
            if (empty($data)) {
                throw new \RuntimeException('No fue posible agregar la reserva: ' . $id);
            }

            $payload = ApiResponse::success($data, 'Reserva Creada');
        } catch (\InvalidArgumentException $ex) {
            $payload = ApiResponse::error('Parámetro inválido', $ex->getMessage());
        } catch (\RuntimeException $ex) {
            $payload = ApiResponse::error('Error al obtener la reserva', $ex->getMessage());
        } catch (Throwable $ex) {
            $payload = ApiResponse::error('Error al obtener órdenes', $ex->getMessage());
        }
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getCatTipoCambio(Request $request, Response $response, int $id_moneda): Response
    {
        
        try {
            echo $id_moneda ;
            $data = $this->reservasService->getCatTipoCambio($id_moneda);
            if (empty($data)) {
                throw new \RuntimeException('No se encontraron datos de tipo de cambio');
            }
            $payload = ApiResponse::success($data, 'Tipo de cambio obtenido correctamente');
        } catch (Throwable $ex) {
            $payload = ApiResponse::error('Error al obtener tipo de cambio', $ex->getMessage());
        }

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // public function getDatosBaseIsepaaa(Request $request, Response $response, array $args = []): Response
    // {
    //     try {
    //         $params = $request->getQueryParams();

    //        $idResevaBase = $params['id_reserva_base'] ?? null;
    //         if (!$idResevaBase) {
    //             throw new \InvalidArgumentException('El parámetro id_reserva_base es obligatorio');
    //         }
    //         $data = $this->generaFolioService->getFolio(['id_reserva_base' => $idResevaBase]);

    //         $payload = ApiResponse::success($data, 'folio generado correctamente');
    //     } catch (Throwable $ex) {
    //         $payload = ApiResponse::error('Error al obtener órdenes', $ex->getMessage());
    //     }

    //     $response->getBody()->write(json_encode($payload));
    //     return $response->withHeader('Content-Type', 'application/json');
    // }

    public function ScriptsJs(Request $request, Response $response, array $args = [])
    {
        $params = $request->getQueryParams();
        
        $scriptPath = Path::combine(BASE_PATH_SLIM4,'src','js',$params['script']); // Ajusta la ruta según tu estructura de directorios
        $fileStream = null;
        // $response->getBody()->write("Producción scripts: " . htmlspecialchars($scriptPath));
        // return $response->withHeader('Content-Type', 'application/javascript');
                        
                        
    if (file_exists($scriptPath)) {
        $fileStream = new \Slim\Psr7\Stream(fopen($scriptPath, 'r'));
        return $response->withHeader('Content-Type', 'application/javascript; charset=UTF-8')
                        ->withBody($fileStream);
    }
    return $response->withStatus(404, 'Script not found');
    }

}
