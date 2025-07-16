<?php
namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utils\Path;

class ScriptsController
{
    
    public function __construct()
    {
        // Aquí podrías inyectar servicios si es necesario
    }

    public function index(Request $request, Response $response, string $script): Response
    {
        if (empty($script)) {
            return $response->withStatus(400, 'Script parameter is required');
        }

        $scriptPath = Path::combine(BASE_PATH_SLIM4, 'src', 'js', $script);

        // Validate that the file exists and is readable
        if (!file_exists($scriptPath) || !is_readable($scriptPath)) {
            return $response->withStatus(404, 'Script not found or not readable');
        }

        $handle = fopen($scriptPath, 'r');
        if ($handle === false) {
            return $response->withStatus(500, 'Error opening the script file');
        }

        try {
            $fileStream = new \Slim\Psr7\Stream($handle);
        } catch (\Exception $e) {
            fclose($handle);
            return $response->withStatus(500, 'Error creating stream: ' . $e->getMessage());
        }

        return $response->withHeader('Content-Type', 'application/javascript; charset=UTF-8')
            ->withBody($fileStream);
    }
}
