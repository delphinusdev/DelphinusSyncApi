<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController
{
    public function index(Request $request, Response $response, array $args = [])
    {
        // Aquí puedes implementar la lógica para manejar la solicitud de inicio
        $response->getBody()->write("Bienvenido a la página de inicio");
        return $response;
    }

    public function about(Request $request, Response $response, array $args = [])
    {
        // Aquí puedes implementar la lógica para manejar la solicitud de información
        $response->getBody()->write("Acerca de esta aplicación");
        return $response;
    }
}