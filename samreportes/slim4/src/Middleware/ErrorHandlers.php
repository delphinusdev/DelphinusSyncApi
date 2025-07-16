<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\ErrorMiddleware;
use Slim\App;

return function(App $app): void {
    // 1) Añade el ErrorMiddleware
    $errorMiddleware = $app->addErrorMiddleware(
        displayErrorDetails: false,
        logErrors:           true,
        logErrorDetails:     false
    );

    // 2) Manejador genérico para excepciones
    $genericHandler = function (
        Request   $request,
        \Throwable $exception,
        bool       $displayErrorDetails,
        bool       $logErrors,
        bool       $logErrorDetails
    ) use ($app): Response {
        $payload = [
            'success' => false,
            'message' => $exception->getMessage(),
        ];
        $response = $app->getResponseFactory()
                        ->createResponse(500)
                        ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($payload));
        return $response;
    };
    $errorMiddleware->setDefaultErrorHandler($genericHandler);

    // 3) Manejador para 404
    $errorMiddleware->setErrorHandler(
        HttpNotFoundException::class,
        function (Request $request, \Throwable $exception) use ($app): Response {
            $payload = [
                'success' => false,
                'message' => 'Ruta no encontrada: ' . $request->getUri()->getPath(),
            ];
            $response = $app->getResponseFactory()
                            ->createResponse(404)
                            ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($payload));
            return $response;
        }
    );
};
