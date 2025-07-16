<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use DI\Bridge\Slim\Bridge;
use function DI\autowire;
use function DI\get;
use App\Utils\Path;
use App\Database\PDOConnection;
use App\Database\SqlSrvGenericContext;
use App\Services\OrdenesService;
use App\Interfaces\IOrdenesService;
use App\Interfaces\IPDOConnection;
use App\Utils\QueryBuilder;
use App\Services\PhotosService;
use App\Models\BaseModel;
use App\Services\FotosAppStagingService;
use App\Services\PhotoshareService;
use App\Services\ReservasService;
use App\Services\MypicturesService;
use App\Services\SamFotoAQService;
use App\Services\VideoClipsService;
use Psr\Container\ContainerInterface;

// 2) Constantes globals
define('BASE_PATH', Path::combine(__DIR__, '..', '..', '..'));
define('BASE_PATH_SLIM4', __DIR__);
define('FECHA_HOY', date('Y-m-d'));
define('ID_RESERVA', 10762040);

// preparamos las clases que vamos a inyectar en un arreglo
// Estas clases deben ser las interfaces y sus implementaciones
// que se encuentran en el namespace App\Interfaces y App\Services respectivamente
// Puedes agregar más clases según sea necesario
// Si no tienes una interfaz, puedes usar la clase directamente
// Asegúrate de que las clases estén correctamente autoloaded por Composer


$classNames = [
    IPDOConnection::class => get('db.connection.photos'),

    'db.connection.reservas' => autowire(PDOConnection::class)
        ->constructorParameter('connectionName', 'RESERVAS'),

    'db.connection.samfotoaq' => autowire(PDOConnection::class)
        ->constructorParameter('connectionName', 'SAMFOTOAQ'),

    'db.connection.photos' => autowire(PDOConnection::class)
        ->constructorParameter('connectionName', 'FOTOS'),

    // 'db.connection.photos' => \DI\factory(function (ContainerInterface $c) {
    //     return new PDOConnection('FOTOS'); // Or your actual connection logic
    // })->share(), // This explicitly makes it a singleton

    'db.connection.photoshare' => autowire(PDOConnection::class)
        ->constructorParameter('connectionName', 'PHOTOSHARE'),

    'db.connection.mypictures' => autowire(PDOConnection::class)
        ->constructorParameter('connectionName', 'MYPICTURES'),
    'db.connection.fotosappstaging' => autowire(PDOConnection::class)
        ->constructorParameter('connectionName', 'FOTOS_APP_STAGING'),

    'db.connection.videoclips' => autowire(PDOConnection::class)
        ->constructorParameter('connectionName', 'VIDEOCLIPS'),

    'sql_srv_context.reservas' => autowire(SqlSrvGenericContext::class)
        ->constructorParameter('connectionProvider', get('db.connection.reservas')),

    'sql_srv_context.samfotoaq' => autowire(SqlSrvGenericContext::class)
        ->constructorParameter('connectionProvider', get('db.connection.samfotoaq')),

    'sql_srv_context.photos' => autowire(SqlSrvGenericContext::class)
        ->constructorParameter('connectionProvider', get('db.connection.photos')),

    'sql_srv_context.photoshare' => autowire(SqlSrvGenericContext::class)
        ->constructorParameter('connectionProvider', get('db.connection.photoshare')),

    'sql_srv_context.mypictures' => autowire(SqlSrvGenericContext::class)
        ->constructorParameter('connectionProvider', get('db.connection.mypictures')),
    'sql_srv_context.fotosappstaging' => autowire(SqlSrvGenericContext::class)
        ->constructorParameter('connectionProvider', get('db.connection.fotosappstaging')),

    'sql_srv_context.videoclips' => autowire(SqlSrvGenericContext::class)
        ->constructorParameter('connectionProvider', get('db.connection.videoclips')),



    PhotosService::class => autowire()
        ->constructorParameter('photosContext', get('sql_srv_context.photos')),

    PhotoshareService::class => autowire()
        ->constructorParameter('photoshareContext', get('sql_srv_context.photoshare')),

    MypicturesService::class => autowire()
        ->constructorParameter('mypicturesContext', get('sql_srv_context.mypictures')),
    FotosAppStagingService::class => autowire()
        ->constructorParameter('fotosappstagingContext', get('sql_srv_context.fotosappstaging')),

    ReservasService::class => autowire()
        ->constructorParameter('reservasContext', get('sql_srv_context.reservas')),

    SamFotoAQService::class => autowire()
        ->constructorParameter('samfotoaqContext', get('sql_srv_context.samfotoaq')),

    VideoClipsService::class => autowire()
        ->constructorParameter('videoclipsContext', get('sql_srv_context.videoclips')),

    QueryBuilder::class => autowire()->constructorParameter('dialect', 'sqlserver'),
    IOrdenesService::class => autowire(OrdenesService::class),

];

// 3) Construimos el ContainerBuilder
$builder = new ContainerBuilder();



// 4) Añadimos las definiciones de todos los servicios y contextos
$builder->addDefinitions($classNames);

// 5) Build e inyectamos en Slim
$container = $builder->build();
$app   = Bridge::create($container);

BaseModel::setQueryBuilder($container->get(QueryBuilder::class));

// Permitir CORS (¡Importante para desarrollo y producción si son dominios diferentes!)
// En producción, sé más específico con los orígenes permitidos.


// 6) BasePath en subdirectorio
$app->setBasePath('/samreportes/slim4');

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();



$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// 1) CORS PRE-FLIGHT
$app->options('/{routes:.+}', function ($req, $res) {
    return $res;
});

// 2) CORS GLOBAL
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader(
            'Access-Control-Allow-Headers',
            'Content-Type, Accept, Origin, Authorization, X-Requested-With'
        );
});

// (luego viene addRoutingMiddleware(), errorMiddleware, etc.)





$errorSetup = require Path::combine(__DIR__, 'src', 'Middleware', 'ErrorHandlers.php');
$errorSetup($app);

// 7) Carga y registra tus rutas
$routesFile = Path::combine(__DIR__, 'src', 'Routes.php');
if (! file_exists($routesFile)) {
    throw new \RuntimeException("No existe el archivo de rutas: $routesFile");
}
$registerRoutes = require $routesFile;
$registerRoutes($app);

// 8) Devolvemos la App ya configurada
return $app;
