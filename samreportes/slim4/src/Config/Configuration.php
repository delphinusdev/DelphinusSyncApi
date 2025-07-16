<?php
namespace App\Config;

// Ejemplo simplificado de App\Config\Configuration.php
namespace App\Config;

class Configuration
{
    private static array $config = [
        'connections' => [
            'RESERVAS' => [
                'dsn' => 'sqlsrv:Server=192.168.9.2;Database=delphinus_etravel_3f;TrustServerCertificate=True',
                'username' => 'sa',
                'password' => 't1c9gvd$',
            ],
            'FOTOS' => [
                'dsn' => 'sqlsrv:Server=photodelphinus.com;Database=FOTOS;TrustServerCertificate=True',
                'username' => 'sa',
                'password' => 't1c9gvd$',
            ],
            'PHOTOSHARE' => [
                'dsn' => 'sqlsrv:Server=photodelphinus.com;Database=photoshare;TrustServerCertificate=True',
                'username' => 'sa',
                'password' => 't1c9gvd$',
            ],
            'MYPICTURES' => [
                'dsn' => 'sqlsrv:Server=photodelphinus.com;Database=MYPICTURES;TrustServerCertificate=True',
                'username' => 'sa',
                'password' => 't1c9gvd$'
            ],
            'FOTOS_APP_STAGING'=>['dsn' => 'sqlsrv:Server=photodelphinus.com;Database=FOTOS_APP_STAGING;TrustServerCertificate=True',
                'username' => 'sa',
                'password' => 't1c9gvd$'],
            'VIDEOCLIPS' => [
                'dsn' => 'sqlsrv:Server=photodelphinus.com;Database=eb_photo_delphinus;TrustServerCertificate=True',
                'username' => 'sa',
                'password' => 't1c9gvd$',
            ],
            'SAMFOTOAQ' => [
                'dsn' => 'sqlsrv:Server=192.168.88.100;Database=FOTOS;TrustServerCertificate=True',
                'username' => 'sa',
                'password' => 't1c9gvd$',
            ],
        ],

        'locaciones' =>[
            'Del-RM' => [3,'Del-RM','RM','Del-RM'],
            'Del-Xcaret' => [4,'Del-XC','XC','Del-Xcaret'],
            'Del-Xelha' => [5,'Del-XH','XH','Del-Xelha'],
            'Del-AQ' => [6,'Del-AQ','AQ','Del-AQ'],
            'Del-HZ' => [7,'Del-HZ','HZ','Del-HZ'],
            'Del-PM' => [8,'Del-PM','PM','Del-PM'],
            'Del-PY' => [9,'Del-PY','PY','Del-PY']
        ],
        
        'llave' => [
            'clave' => 'jN4uVI2nopu0sa8PKUR-NPLeI7WsYFbFVrDuhDk0GLk',
            'metodo' => 'AES-256-CBC'
        
        ],
        'FTPServer' => [
            'host' => 'photodelphinus.com',
            'username' => 'DelphiFTP',
            'password' => 'Axs4LTP+',
            'passive' => true,
        ],
        // ... otras configuraciones
    ];

    public static function getConnectionString(string $name): ?string
    {
        return self::$config['connections'][$name]['dsn'] ?? null;
    }

    public static function getDbCredentials(string $name): array
    {
        return [
            'Username' => self::$config['connections'][$name]['username'] ?? null,
            'Password' => self::$config['connections'][$name]['password'] ?? null,
        ];
    }

    public static function getLocations():array
    {
        return self::$config['locaciones'];
    }

    public static function getLocation(string $name): ? array
    {
        return self::$config['locaciones'][$name] ?? null;
    }
    public static function getEncryptionConfig(): array
    {
        return self::$config['llave'] ?? [];
    }
    
    public static function getEncryptionKey(): string
    {
        return self::$config['llave']['clave'] ?? '';
    }

    public static function getEncryptionMethod(): string
    {
        return self::$config['llave']['metodo'] ?? '';
    }
    public static function getFtpConfig(): array
    {
        return self::$config['FTPServer'] ?? [];
    }
}

// class Configuration
// {
//     private static array $config = [];

//     private static function load(): void
//     {
//         if (empty(self::$config)) {
//             $json = file_get_contents(BASE_PATH . '/config/appsettings.json');
//             self::$config = json_decode($json, true);
//         }
//     }

//     public static function get(string $section, string $key)
//     {
//         self::load();
//         return self::$config[$section][$key] ?? null;
//     }

//     public static function getConnectionString(string $name): ?string
//     {
//         self::load();
//         return self::$config['ConnectionStrings'][$name] ?? null;
//     }

//     public static function getDbCredentials(string $name): ?array
//     {
//         self::load();
//         return self::$config['DbCredentials'][$name] ?? null;
//     }
// }
