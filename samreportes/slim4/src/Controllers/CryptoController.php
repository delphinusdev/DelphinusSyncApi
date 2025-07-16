<?php
namespace App\Controllers;

use App\Utils\ConfigCrypto;
use App\Utils\CryptoNet;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CryptoController
{
    /**
     * Endpoint de ejemplo para demostrar el cifrado y descifrado usando una clave generada.
     * La respuesta se devuelve en formato JSON para una mejor organización.
     *
     * @param Request $request La solicitud HTTP.
     * @param Response $response La respuesta HTTP.
     * @param array $data Datos adicionales (no usados directamente en este ejemplo).
     * @return Response La respuesta HTTP con los datos cifrados/descifrados o un mensaje de error en JSON.
     */
    public function index(Request $request, Response $response, array $data = []): Response
    {

        // Obtiene el método de cifrado predeterminado configurado en ConfigCrypto.
        $metodo = ConfigCrypto::getMetodo();

        // Genera una clave aleatoria segura que será usada para cifrar y descifrar.
        $clave = ConfigCrypto::generarClave($metodo);

        // Crea un array de ejemplo con datos a cifrar y lo codifica a JSON.
        $dataToEncrypt = [
            'id_transaccion' => uniqid(),
            'mensaje' => 'Hola, mundo con clave generada!',
            'timestamp' => date('Y-m-d H:i:s'),
            'cryptonet' => CryptoNet::generateKey(),
            'cryptonetiv' => CryptoNet::generateIV()
        ];
        $json = json_encode($dataToEncrypt);

        // Cifra los datos JSON usando la clave y el método definidos.
        // Los logs de error se manejan internamente por ConfigCrypto.
        $cifrado = ConfigCrypto::cifrar($json, $clave, $metodo);

        if ($cifrado === null) {
            // Prepara una respuesta de error en JSON
            $responseData = [
                'status' => 'error',
                'message' => 'Error al cifrar los datos.',
                'details' => 'Verifique los logs del servidor para más información si es necesario.'
            ];
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        // Intenta descifrar los datos cifrados para verificar la funcionalidad.
        $descifrado = ConfigCrypto::descifrar($cifrado, $clave, $metodo);

        if ($descifrado === null) {
            // Prepara una respuesta de error en JSON
            $responseData = [
                'status' => 'error',
                'message' => 'Error al descifrar los datos cifrados.',
                'details' => 'La clave o el método podrían ser incorrectos, o los datos dañados. Verifique los logs del servidor.'
            ];
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        // Prepara los resultados en un array asociativo para su serialización a JSON.
        $responseData = [
            'status' => 'success',
            'message' => 'Proceso de cifrado y descifrado exitoso.',
            'encryption_details' => [
                'method_used' => $metodo,
                // 'generated_key' => $clave, // No exponer la clave en un entorno real
                'original_data_json' => $json,
                'encrypted_data_base64' => $cifrado,
                'decrypted_data_json' => $descifrado,
            ]
        ];

        // Establece la cabecera Content-Type para indicar que la respuesta es JSON.
        $response = $response->withHeader('Content-Type', 'application/json');
        // Escribe el contenido JSON en el cuerpo de la respuesta.
        $response->getBody()->write(json_encode($responseData, JSON_PRETTY_PRINT));
        return $response;
    }

    /**
     * Endpoint para generar una nueva clave de cifrado.
     * La respuesta se devuelve en formato JSON.
     *
     * @param Request $request La solicitud HTTP.
     * @param Response $response La respuesta HTTP.
     * @param string $methodName El nombre del método de cifrado (ej. 'AES-256-CBC').
     * Si está vacío, se usará el método predeterminado de ConfigCrypto.
     * @return Response La respuesta HTTP con la clave generada en JSON.
     */
    public function create(Request $request, Response $response, string $methodName = ''): Response
    {
        // Genera una clave aleatoria. Esta clave puede ser usada luego por 'cifrar'/'descifrar'.
        $methodName = ConfigCrypto::toLowerCase($methodName);
        $clave = ConfigCrypto::generarClave($methodName);
        
        $responseData = [
            'status' => 'success',
            'message' => 'Clave generada exitosamente.',
            'generated_key' => $clave
        ];

        if (!empty($methodName)) {
             $responseData['method_used'] = $methodName;
        } else {
             $responseData['method_used'] = ConfigCrypto::getMetodo() . ' (predeterminado)';
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($responseData, JSON_PRETTY_PRINT));
        return $response;
    }
}
