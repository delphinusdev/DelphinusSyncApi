<?php
namespace App\Utils;

Class ConfigCrypto
{
    // Método de cifrado predeterminado
    private static string $defaultMethod = 'AES-256-CBC';

    /**
     * Genera una clave segura aleatoria para el método de cifrado especificado.
     * Esta clave está destinada a ser utilizada como argumento para los métodos 'cifrar' y 'descifrar'.
     *
     * @param string $metodo El método de cifrado para el cual generar la clave (ej. 'AES-256-CBC').
     * Si está vacío, se usará el método predeterminado.
     * @return string La clave generada, codificada en Base64 URL-safe.
     */
    public static function generarClave(string $metodo = ''): string
    {
        if (empty($metodo)) {
            $metodo = self::$defaultMethod;
        }

        $key_sizes = [
            'AES-128-CBC' => 16,
            'AES-192-CBC' => 24,
            'AES-256-CBC' => 32,
        ];
        $key_length = $key_sizes[$metodo] ?? 32;

        return rtrim(strtr(base64_encode(random_bytes($key_length)), '+/', '-_'), '=');
    }

    /**
     * Obtiene el método de cifrado predeterminado que está configurado en la clase.
     *
     * @return string El método de cifrado actual.
     */
    public static function getMetodo(): string
    {
        return self::toLowerCase(self::$defaultMethod);
    }
    public static function toLowerCase(string $string): string
    {
        return strtolower($string);
    }

    /**
     * Establece el método de cifrado predeterminado para la clase.
     *
     * @param string $metodo El nuevo método de cifrado (ej. 'AES-128-CBC').
     * @return void
     */
    public static function setMetodo(string $metodo): void
    {
        self::$defaultMethod = $metodo;
    }

    /**
     * Cifra datos utilizando la clave y el método especificados.
     * El IV y los datos cifrados se concatenan y se devuelven codificados en Base64.
     *
     * @param mixed $data Los datos a cifrar (cadena o array).
     * @param string $clave La clave de cifrado (debe ser la misma utilizada para descifrar).
     * @param string $metodo Opcional. El método de cifrado a usar. Si está vacío, se usará el predeterminado.
     * @return string|null Los datos cifrados y el IV en formato Base64, o null en caso de error.
     */
    public static function cifrar($data, string $clave, string $method = ''): ?string
    {
        if (empty($method)) {
            error_log("ConfigCrypto Error: Datos, clave o método de cifrado están vacíos.");
            return null;
        }

        if (empty($data) || empty($clave) || empty($method)) {
            error_log("ConfigCrypto Error: Datos, clave o método de cifrado están vacíos.");
            return null;
        }

        $metodo = self::toLowerCase($method);
        
        // Verifica si el método de cifrado es soportado por la instalación de OpenSSL en el sistema.
        if (!in_array($metodo, openssl_get_cipher_methods())) {
            error_log("ConfigCrypto Error: Método de cifrado '$metodo' no soportado por OpenSSL en este entorno.");
            return null;
        }
        
        $json = is_string($data) ? $data : json_encode($data);
        if ($json === false) {
            error_log("ConfigCrypto Error: No se pudieron serializar los datos a JSON.");
            return null;
        }
        
        $iv_length = openssl_cipher_iv_length($metodo);
        if ($iv_length === false) { // Validación adicional para openssl_cipher_iv_length
            error_log("ConfigCrypto Error: No se pudo obtener la longitud del IV para el método '$metodo'.");
            return null;
        }
        $iv = openssl_random_pseudo_bytes($iv_length);
        if ($iv === false) { // Validación adicional para openssl_random_pseudo_bytes
            error_log("ConfigCrypto Error: No se pudieron generar bytes aleatorios para el IV.");
            return null;
        }


        // Se usa $clave directamente, sin procesamiento con hash/substr.
        $cifrado = openssl_encrypt($json, $metodo, $clave, 0, $iv);

        if ($cifrado === false) {
            error_log("ConfigCrypto Error: Falló openssl_encrypt. Posiblemente clave o método incorrectos, o datos inválidos.");
            return null;
        }

        // Concatenar IV y cifrado, luego codificar todo en Base64
        return base64_encode($iv . $cifrado);
    }

    /**
     * Descifra una cadena Base64 que contiene el IV y los datos cifrados concatenados.
     * Utiliza la clave y el método especificados.
     *
     * @param string $cifradoBase64 La cadena cifrada en Base64 (IV y datos concatenados).
     * @param string $clave La clave de cifrado (debe ser la misma utilizada para cifrar).
     * @param string $metodo Opcional. El método de cifrado a usar. Si está vacío, se usará el predeterminado.
     * @return array|null Los datos descifrados como un array asociativo, o null en caso de error.
     */
    public static function descifrar($cifradoBase64, string $clave, string $method = ''): ?array
    {
        if (empty($method)) {
            $method = self::$defaultMethod;
        }
        if(is_array($cifradoBase64)) {
            return $cifradoBase64; // Si ya es un array, no hay nada que descifrar.
        }

        if (empty($cifradoBase64) || empty($clave) || empty($method)) {
            error_log("ConfigCrypto Error: Datos cifrados, clave o método de cifrado están vacíos.");
            return null;
        }
        
         $metodo = self::toLowerCase($method);
        
        // Verifica si el método de cifrado es soportado por la instalación de OpenSSL en el sistema.
        if (!in_array($metodo, openssl_get_cipher_methods())) {
            error_log("ConfigCrypto Error: Método de cifrado '$metodo' no soportado por OpenSSL en este entorno.");
            return null;
        }

        // Decodificar la cadena completa Base64
        $data = base64_decode($cifradoBase64, true);
        if ($data === false) {
            error_log("ConfigCrypto Error: Falló la decodificación Base64 de los datos cifrados.");
            return null;
        }

        $iv_length = openssl_cipher_iv_length($metodo);
        if ($iv_length === false) { // Validación adicional para openssl_cipher_iv_length
            error_log("ConfigCrypto Error: No se pudo obtener la longitud del IV para el método '$metodo'.");
            return null;
        }

        // Separar el IV y el texto cifrado (el IV está al principio)
        if (strlen($data) < $iv_length) {
            error_log("ConfigCrypto Error: Los datos decodificados son demasiado cortos para contener el IV completo.");
            return null;
        }
        $iv = substr($data, 0, $iv_length);
        $cifrado = substr($data, $iv_length);

        // Se usa $clave directamente, sin procesamiento con hash/substr.
        $jsonDescifrado = openssl_decrypt($cifrado, $metodo, $clave, 0, $iv);

        if ($jsonDescifrado === false) {
            error_log("ConfigCrypto Error: Falló openssl_decrypt. Posiblemente clave, método o IV incorrectos, o datos cifrados corruptos.");
            return null;
        }

        $config = json_decode($jsonDescifrado, true);
        // Verificar si la decodificación JSON fue exitosa y el resultado es un array.
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("ConfigCrypto Error: Falló la decodificación JSON de los datos descifrados: " . json_last_error_msg());
            return null;
        }

        return is_array($config) ? $config : null;
    }
}
