<?php
namespace App\Utils;

class CryptoNet
{
    const METHOD = 'aes-256-cbc';
    const KEY_SIZE_BYTES = 32; // 256 bits
    const IV_SIZE_BYTES = 16;  // 128 bits

    /**
     * Genera una clave aleatoria segura.
     * @return string Clave codificada en base64.
     */
    public static function generateKey(): string
    {
        return base64_encode(random_bytes(self::KEY_SIZE_BYTES));
    }

    /**
     * Genera un Vector de Inicialización (IV) aleatorio seguro.
     * @return string IV codificado en base64.
     */
    public static function generateIV(): string
    {
        return base64_encode(random_bytes(self::IV_SIZE_BYTES));
    }

    /**
     * Cifra un texto plano usando AES-256-CBC.
     *
     * @param string $plainText El texto a cifrar.
     * @param string $key La clave de cifrado (base64 encoded).
     * @param string $iv El Vector de Inicialización (base64 encoded).
     * @return string El texto cifrado y codificado en base64. Retorna false si falla.
     */
    public static function encrypt(string $plainText, string $key, string $iv): string|false
    {
        // Decodificar la clave y el IV de base64
        $decodedKey = base64_decode($key);
        $decodedIv = base64_decode($iv);

        // Verificar que las longitudes de clave e IV sean correctas
        if (strlen($decodedKey) !== self::KEY_SIZE_BYTES || strlen($decodedIv) !== self::IV_SIZE_BYTES) {
            error_log("Error: Key or IV size is incorrect.");
            return false;
        }

        // Cifrar el texto
        // OPENSSL_RAW_DATA: No aplica base64_encode al resultado.
        // OPENSSL_ZERO_PADDING: No se utiliza aquí porque PKCS7 padding es manejado por OpenSSL.
        $encrypted = openssl_encrypt(
            $plainText,
            self::METHOD,
            $decodedKey,
            OPENSSL_RAW_DATA,
            $decodedIv
        );

        if ($encrypted === false) {
            error_log("Encryption failed: " . openssl_error_string());
            return false;
        }

        // Codificar el resultado cifrado en base64 para transportarlo fácilmente
        return base64_encode($encrypted);
    }
}

// --- Ejemplo de uso en PHP ---
/*
$plainText = "Este es un mensaje secreto que quiero cifrar.";

// Generar una clave y un IV seguros (se deben almacenar de forma segura y compartir con .NET)
$encryptionKey = EncryptionService::generateKey();
$encryptionIv = EncryptionService::generateIV();

echo "Clave (Base64): " . $encryptionKey . "\n";
echo "IV (Base64): " . $encryptionIv . "\n";

$cipherText = EncryptionService::encrypt($plainText, $encryptionKey, $encryptionIv);

if ($cipherText !== false) {
    echo "Texto Plano: " . $plainText . "\n";
    echo "Texto Cifrado (Base64): " . $cipherText . "\n";
} else {
    echo "Fallo el cifrado.\n";
}
*/

?>