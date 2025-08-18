<?php
/**
 * Cliente mínimo para autenticación y creación de reservas.
 * Requisitos: PHP 7.4+ (probado en 8.x). Sin librerías externas (usa cURL).
 */
class ReservaApiClient
{
    private string $baseUrl;
    private ?string $bearerToken = null;
    private int $timeout = 30;          // segundos
    private int $connectTimeout = 10;   // segundos
    private bool $verifySSL = true;     // cámbialo a false SOLO en entornos de prueba

    public function __construct(string $baseUrl, ?int $timeout = null, ?int $connectTimeout = null, ?bool $verifySSL = null)
    {
        $this->baseUrl = rtrim($baseUrl, "/");
        if ($timeout !== null)        $this->timeout = $timeout;
        if ($connectTimeout !== null) $this->connectTimeout = $connectTimeout;
        if ($verifySSL !== null)      $this->verifySSL = $verifySSL;
    }

    /**
     * Autentica y guarda el token Bearer internamente.
     * Devuelve el payload de autenticación (por si necesitas más datos).
     */
    public function authenticate(string $username, string $password): array
    {
        $endpoint = "/api/v2/Usuarios/Authenticate";
        $payload  = [
            'username' => $username,
            'password' => $password,
        ];

        $resp = $this->request('POST', $endpoint, $payload, false);

        // Ajusta la ruta del token según devuelva tu API, por ejemplo:
        // - $resp['token']
        // - $resp['data']['token']
        // A continuación se contemplan opciones comunes:
        $token = $resp['token'] ?? ($resp['data']['token'] ?? null);

        if (!is_string($token) || $token === '') {
            throw new RuntimeException('No se recibió un token de autenticación válido.');
        }

        $this->bearerToken = $token;
        return $resp;
    }

    /**
     * Crea una reserva. $data debe contener los campos del body requeridos.
     * Devuelve la respuesta del API como array.
     */
    public function crearReserva(array $data): array
    {
        if (!$this->bearerToken) {
            throw new RuntimeException('No autenticado. Llama primero a authenticate().');
        }

        // Fuerza tipos numéricos donde aplique para evitar que viajen como strings
        $data = $this->coerceReservaTypes($data);

        $endpoint = "/api/v2/Reservas";
        return $this->request('POST', $endpoint, $data, true);
    }

    /**
     * Método genérico de request con cURL (JSON).
     */
    private function request(string $method, string $endpoint, ?array $body, bool $withAuth): array
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('No se pudo inicializar cURL.');
        }

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        if ($withAuth) {
            $headers[] = 'Authorization: Bearer ' . $this->bearerToken;
        }

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST   => strtoupper($method),
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => $this->timeout,
            CURLOPT_CONNECTTIMEOUT  => $this->connectTimeout,
            CURLOPT_FOLLOWLOCATION  => false,
        ]);

        // SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySSL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->verifySSL ? 2 : 0);

        if (!empty($body)) {
            $json = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                throw new RuntimeException('Error codificando JSON: ' . json_last_error_msg());
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $err   = curl_error($ch);
        $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($errno) {
            throw new RuntimeException("Error de cURL ({$errno}): {$err}");
        }

        // Acepta respuestas vacías (204, etc.)
        if ($raw === '' || $raw === false) {
            if ($http >= 200 && $http < 300) {
                return ['status' => $http, 'data' => null];
            }
            throw new RuntimeException("HTTP {$http}: Respuesta vacía del servidor.");
        }

        $decoded = json_decode($raw, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            // Si devuelve HTML o texto, lo regresamos como string para depuración
            if ($http >= 200 && $http < 300) {
                return ['status' => $http, 'raw' => $raw];
            }
            throw new RuntimeException("HTTP {$http}: Respuesta no-JSON: {$raw}");
        }

        if ($http < 200 || $http >= 300) {
            // Intenta extraer mensaje de error de la API
            $msg = $decoded['message'] ?? $decoded['error'] ?? 'Error no especificado.';
            throw new RuntimeException("HTTP {$http}: {$msg}");
        }

        return $decoded;
    }

    /**
     * Fuerza el tipado de campos de la reserva para que json_encode mande números como números.
     */
    private function coerceReservaTypes(array $d): array
    {
        // Campos que deben ser enteros
        $intFields = [
            'idHabitat','idServicio','adultos','menores','incentivos','idIdioma','idPromocion',
            'descuento','idHotel','idAgencia','idRepresentante','idClasificacion','idSubclasificacion',
            'idMedioVenta','idAutorizo','tipoComentario'
        ];

        foreach ($intFields as $f) {
            if (array_key_exists($f, $d) && $d[$f] !== null && $d[$f] !== '') {
                $d[$f] = (int)$d[$f];
            }
        }

        // Asegurar strings en campos que podrían llegar numéricos
        $stringFields = ['fecha','hora','nombre','correo','telefono','codigoPais','habitacion','pickup','cupon','comentarios'];
        foreach ($stringFields as $f) {
            if (array_key_exists($f, $d) && $d[$f] !== null) {
                $d[$f] = (string)$d[$f];
            }
        }

        return $d;
    }
}