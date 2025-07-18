<?php
namespace App\Utils;
use App\Config\Configuration;
use App\Utils\ConfigCrypto;

Class ConfigSyncLocaciones
{
    /**
     * Obtiene la configuración cifrada de una locación.
     *
     * @param string $location_code Código de la locación.
     * @return string|null Configuración cifrada en JSON o null si no existe.
     */
    public static function getConfig(string $location_code): ?string
    {
        $config = self::ConfigLocation($location_code);

        if ($config === null) {
            return null;
        }
        

        return ConfigCrypto::cifrar(
            json_encode($config),
            Configuration::getEncryptionKey(),
            Configuration::getEncryptionMethod()
        );
    }

    public static function getConfigNet(string $location_code): ?string
    {
        $config = self::ConfigLocation($location_code);

        if ($config === null) {
            return null;
        }

        // En ConfigSyncLocaciones::getConfig, justo antes del return ConfigCrypto::cifrar(...)
        $jsonToEncrypt = json_encode($config);




        $encryptedResult = CryptoNet::encrypt(
            $jsonToEncrypt,
            'fZCDxn9dqi5gJeAnirB5GHTNAwC7kIQaKfoanqzdZeU=',
            'JPxzHF8x3qQffoPJ9m8N4w=='
        );

        return $encryptedResult;
    }
    private static function ConfigLocation(string $location_code): ?array
    {
        $host = 'http://50.16.241.234:8080/samreportes/slim4';

        $urls = [
            'thumbs' => $host . '/fotosStaging/thumbs',
            'cloud' => $host . '/fotosStaging/clouds_st_procedure',
            'app' => $host . '/fotosStaging/pictures',
            'photoshare' => $host . '/fotosStaging/shares',
            'videoclips' => $host . '/videos/clips',
            'tmd_videos' => $host . '/videos/tmdvideos',
            'compras_en_linea' => $host . '/fotos/compras_en_linea'
        ];

        $ftpConfig = Configuration::getFtpConfig();

        $locationsCode = ['XH'=>'Del-Xelha','XC'=> 'Del-Xcaret','RM'=> 'Del-RM', 'PM'=> 'Del-PM', 'AQ'=> 'Del-AQ', 'HZ'=> 'Del-HZ', 'PY'=>  'Del-PY'];

        $fecha_hasta = date('Y-m-d');
        $fecha_desde = date('Y-m-d', strtotime('-1 day'));

        $location = 
        [
            'Del-Xelha' => 
            [
                 'location_id' => 5,
                 'location_code' => 'Del-Xelha',
                 'FTPConfig' => $ftpConfig,

                    'thumbs' =>
                            [
                                'status' => 1,
                                'url' => "{$urls['thumbs']}/{$locationsCode['XH']}/{$fecha_desde}/{$fecha_hasta}",
                                'local' => ['path' => 'C:\inetpub\EBPhotoXH\server\uploads\thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg'],
                                'remote' => ['path' => '/thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg']

                            ],

                    'cloud' => 
                            [
                                'status' => 1,
                                'url' => "{$urls['cloud']}/{$locationsCode['XH']}/{$fecha_desde}/{$fecha_hasta}",
                                'local' =>
                                    [
                                        ['path' => 'C:\inetpub\EBPhotoXH\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'],
                                        ['path' => '\\\10.10.104.7\homes\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                    ],

                                'remote' => ['path' => '/Del-XH/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                            ],

                    'app' => 
                            [
                                'status' => 1,
                                'url' => "{$urls['app']}/{$locationsCode['XH']}/{$fecha_desde}/{$fecha_hasta}",
                                'local' => ['path' => 'C:\inetpub\EBPhotoXH\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                'remote' => ['path' => '/mypictures/Del-Xelha/original', 'prefijo' => 'img-', 'ext' => '.jpg']
                            ],

                    'photoshare' => 
                            [
                                'status' => 1,
                                'url' => "{$urls['photoshare']}/{$locationsCode['XH']}/{$fecha_desde}/{$fecha_hasta}",
                                'local' => ['path' => 'C:\inetpub\EBPhotoXH\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                'remote'=>['path' => '/photoshare', 'prefijo' => 'img-', 'ext' => '.jpg']
                            ],

                    'videoclips' => 
                            [
                                'status' => 1,
                                'url' => "{$urls['videoclips']}/{$locationsCode['XH']}/{$fecha_desde}/{$fecha_hasta}",
                                'local' => ['path' => '\\\10.10.104.7\homes\JBOD2', 'prefijo' => '', 'ext' => '.mp4'],
                                'remote' =>['path' => '/Del-XH/Videos', 'prefijo' => '', 'ext' => '.mp4']
                            ],
                    'tmd_videos' =>
                            [
                                'status' => 0,
                                'url' => "{$urls['tmd_videos']}/{$locationsCode['XH']}/{$fecha_desde}/{$fecha_hasta}",
                                'local' => ['path' => '\\\10.10.104.7\homes\JBOD2', 'prefijo' => '', 'ext' => '.mp4'],
                                'remote' =>['path' => '/Del-XH/Videos', 'prefijo' => '', 'ext' => '.mp4']
                            ],

                    'compras_en_linea' => 
                            [
                                'status' => 1,
                                'url' => "{$urls['compras_en_linea']}/{$locationsCode['XH']}/{$fecha_desde}/{$fecha_hasta}",
                                'local' => ['path' => '\\\10.10.104.7\homes\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'], 
                                'remote' => ['path' => '/Del-XH/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                            ]

            ],
        
            'Del-Xcaret' => 
                [
                    'location_id' => 4,
                    'location_code' => 'Del-Xcaret',

                    'FTPConfig' => $ftpConfig,

                        'thumbs' =>
                                [
                                    'status' => 1,
                                    'url' => "{$urls['thumbs']}/{$locationsCode['XC']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoXC\server\uploads\thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg']

                                ],

                        'cloud' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['cloud']}/{$locationsCode['XC']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' =>
                                        [
                                            ['path' => 'C:\inetpub\EBPhotoXC\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'],
                                            ['path' => '\\\10.10.103.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                        ],

                                    'remote' => ['path' => '/Del-XC/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'app' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['app']}/{$locationsCode['XC']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoXC\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/mypictures/Del-Xcaret/original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'photoshare' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['photoshare']}/{$locationsCode['XC']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoXC\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote'=>['path' => '/photoshare', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'videoclips' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['videoclips']}/{$locationsCode['XC']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\10.10.103.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-Xcaret/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],
                        'tmd_videos' =>
                                [
                                    'status' => 1,
                                    'url' => "{$urls['tmd_videos']}/{$locationsCode['XC']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\10.10.103.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-XC/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],

                        'compras_en_linea' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['compras_en_linea']}/{$locationsCode['XC']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\10.10.103.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'], 
                                    'remote' => ['path' => '/Del-XC/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ]

                ],

            'Del-RM' => 
                [
                    'location_id' => 3,
                    'location_code' => 'Del-RM',
                    'FTPConfig' => $ftpConfig,

                        'thumbs' =>
                                [
                                    'status' => 1,
                                    'url' => "{$urls['thumbs']}/{$locationsCode['RM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoRM\server\uploads\thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg']

                                ],

                        'cloud' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['cloud']}/{$locationsCode['RM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' =>
                                        [
                                            ['path' => 'C:\inetpub\EBPhotoRM\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'],
                                            ['path' => '\\\10.10.103.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                        ],

                                    'remote' => ['path' => '/Del-RM/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'app' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['app']}/{$locationsCode['RM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoRM\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/mypictures/Del-RM/original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'photoshare' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['photoshare']}/{$locationsCode['RM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoRM\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote'=>['path' => '/photoshare', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'videoclips' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['videoclips']}/{$locationsCode['RM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.89.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-RM/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],
                        'tmd_videos' =>
                                [
                                    'status' => 0,
                                    'url' => "{$urls['tmd_videos']}/{$locationsCode['RM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.89.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-RM/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],

                        'compras_en_linea' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['compras_en_linea']}/{$locationsCode['RM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.89.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'], 
                                    'remote' => ['path' => '/Del-RM/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ]

                ],

            'Del-PM' => 
                [
                    'location_id' => 8,
                    'location_code' => 'Del-PM',
                    'FTPConfig' => $ftpConfig,

                        'thumbs' =>
                                [
                                    'status' => 1,
                                    'url' => "{$urls['thumbs']}/{$locationsCode['PM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoPM\server\uploads\thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg']

                                ],

                        'cloud' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['cloud']}/{$locationsCode['PM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' =>
                                        [
                                            ['path' => 'C:\inetpub\EBPhotoPM\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'],
                                            ['path' => '\\\192.168.90.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                        ],

                                    'remote' => ['path' => '/Del-PM/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'app' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['app']}/{$locationsCode['PM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoPM\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/mypictures/Del-PM/original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'photoshare' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['photoshare']}/{$locationsCode['PM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoPM\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote'=>['path' => '/photoshare', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'videoclips' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['videoclips']}/{$locationsCode['PM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.90.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-PM/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],
                        'tmd_videos' =>
                                [
                                    'status' => 0,
                                    'url' => "{$urls['tmd_videos']}/{$locationsCode['PM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.90.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-PM/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],

                        'compras_en_linea' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['compras_en_linea']}/{$locationsCode['PM']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.90.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'], 
                                    'remote' => ['path' => '/Del-PM/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ]

                ],
            
            'Del-AQ' => 
                [
                    'location_id' => 6,
                    'location_code' => 'Del-AQ',
                    'FTPConfig' => $ftpConfig,

                        'thumbs' =>
                                [
                                    'status' => 1,
                                    'url' => "{$urls['thumbs']}/{$locationsCode['AQ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoAQ\server\uploads\thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg']

                                ],

                        'cloud' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['cloud']}/{$locationsCode['AQ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' =>
                                        [
                                            ['path' => 'C:\inetpub\EBPhotoAQ\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'],
                                            ['path' => '\\\192.168.88.7\Backups\C$\inetpub\EBPhotoAQ\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                        ],

                                    'remote' => ['path' => '/Del-AQ/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'app' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['app']}/{$locationsCode['AQ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoAQ\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/mypictures/Del-AQ/original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'photoshare' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['photoshare']}/{$locationsCode['AQ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoAQ\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote'=>['path' => '/photoshare', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'videoclips' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['videoclips']}/{$locationsCode['AQ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.88.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-AQ/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],
                        'tmd_videos' =>
                                [
                                    'status' => 0,
                                    'url' => "{$urls['tmd_videos']}/{$locationsCode['AQ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.88.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-AQ/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],

                        'compras_en_linea' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['compras_en_linea']}/{$locationsCode['AQ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.88.7\Backups\C$\inetpub\EBPhotoAQ\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/Del-AQ/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ]

                ],

            'Del-HZ' => 
                [
                    'location_id' => 7,
                    'location_code' => 'Del-HZ',
                    'FTPConfig' => $ftpConfig,

                        'thumbs' =>
                                [
                                    'status' => 1,
                                    'url' => "{$urls['thumbs']}/{$locationsCode['HZ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoHZ\server\uploads\thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg']

                                ],

                        'cloud' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['cloud']}/{$locationsCode['HZ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' =>
                                        [
                                            ['path' => 'C:\inetpub\EBPhotoHZ\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'],
                                            ['path' => '\\\192.168.87.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                        ],

                                    'remote' => ['path' => '/Del-HZ/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'app' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['app']}/{$locationsCode['HZ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoHZ\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/mypictures/Del-HZ/original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'photoshare' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['photoshare']}/{$locationsCode['HZ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoHZ\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote'=>['path' => '/photoshare', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'videoclips' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['videoclips']}/{$locationsCode['HZ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.87.7\homes\Jbod', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-HZ/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],
                        'tmd_videos' =>
                                [
                                    'status' => 0,
                                    'url' => "{$urls['tmd_videos']}/{$locationsCode['HZ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.87.7\homes\Jbod', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-HZ/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],

                        'compras_en_linea' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['compras_en_linea']}/{$locationsCode['HZ']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.87.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'], 
                                    'remote' => ['path' => '/Del-HZ/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ]

                ],
            
            'Del-PY' => 
                [
                    'location_id' => 9,
                    'location_code' => 'Del-PY',
                    'FTPConfig' => $ftpConfig,

                        'thumbs' =>
                                [
                                    'status' => 1,
                                    'url' => "{$urls['thumbs']}/{$locationsCode['PY']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoPY\server\uploads\thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/thumbs', 'prefijo' => 'thumb-img-', 'ext' => '.jpg']

                                ],

                        'cloud' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['cloud']}/{$locationsCode['PY']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' =>
                                        [
                                            ['path' => 'C:\inetpub\EBPhotoPY\server\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'],
                                            ['path' => '\\\192.168.91.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                        ],

                                    'remote' => ['path' => '/Del-PY/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'app' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['app']}/{$locationsCode['PY']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoPY\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote' => ['path' => '/mypictures/Del-PY/original', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'photoshare' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['photoshare']}/{$locationsCode['PY']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => 'C:\inetpub\EBPhotoPY\server\uploads\mediana', 'prefijo' => 'normal-img-', 'ext' => '.jpg'],
                                    'remote'=>['path' => '/photoshare', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ],

                        'videoclips' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['videoclips']}/{$locationsCode['PY']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.91.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-PY/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],
                        'tmd_videos' =>
                                [
                                    'status' => 0,
                                    'url' => "{$urls['tmd_videos']}/{$locationsCode['PY']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.91.7\homes\JBOD', 'prefijo' => '', 'ext' => '.mp4'],
                                    'remote' =>['path' => '/Del-PY/Videos', 'prefijo' => '', 'ext' => '.mp4']
                                ],

                        'compras_en_linea' => 
                                [
                                    'status' => 1,
                                    'url' => "{$urls['compras_en_linea']}/{$locationsCode['PY']}/{$fecha_desde}/{$fecha_hasta}",
                                    'local' => ['path' => '\\\192.168.91.7\homes\administrator\uploads\original', 'prefijo' => 'img-', 'ext' => '.jpg'], 
                                    'remote' => ['path' => '/Del-PY/Fotos', 'prefijo' => 'img-', 'ext' => '.jpg']
                                ]

                ],
        ];

    
    
    if (!isset($location[$location_code])) {
        
        return null;
    }
    
        $result = $location[$location_code];

    return $result;


    }

}