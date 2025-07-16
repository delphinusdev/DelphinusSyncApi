<?php
namespace App\Models;

class ApiResponse
{
    public static function success($data = null, string $message = 'Operación exitosa'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    

    public static function error(string $message = 'Ocurrió un error', $data = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data
        ];
    }
}
