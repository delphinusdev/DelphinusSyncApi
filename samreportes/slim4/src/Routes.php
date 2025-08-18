<?php
declare(strict_types=1);
namespace App\Routes;

use App\Controllers\HomeController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\OrdenesController;
use App\Controllers\ReservasController;
use App\Controllers\PhotosController;
use App\Controllers\PhotoshareController;
use App\Controllers\ScriptsController;
use App\Controllers\MypicturesController;
use App\Controllers\VideoClipsController;
use App\Controllers\CryptoController;
use App\Controllers\FotosAppStagingController;
use App\Controllers\SamFotoAQController;

// use App\Services\fotos;

/**
 * Registra todas las rutas de la aplicación en el objeto Slim\App.
 */


return function(App $app): void {

    $app->get('/', [HomeController::class, 'index']);



    $app->group('/js',function(RouteCollectorProxy $group) {
        $group->get('/{script}', [ScriptsController::class, 'index']);
    });

    $app->group('/crypto',function(RouteCollectorProxy $group) {
        $group->get('/',[CryptoController::class, 'index']);
        $group->get('/create/{methodName}', [CryptoController::class, 'create']);
    });

    $app->group('/fotos', function (RouteCollectorProxy $group) {
        $group->get('/genera_modelo/{tabla}',[PhotosController::class, 'createModel']);
        $group->get('/config_sync/{location_code}',[PhotosController::class, 'configSync']);
        $group->get('/config_sync_net/{location_code}',[PhotosController::class, 'configSyncNet']);
        $group->get('/clouds/{location}/{fechad}/{fechah}',[PhotosController::class, 'clouds']);
        $group->get('/thumbs/{location}/{fechad}/{fechah}',[PhotosController::class, 'thumbs']);
        $group->get('/compras_en_linea/{location}/{fechad}/{fechah}',[PhotosController::class, 'comprasEnLinea']);
        // $group->get('/{idloc}/photoshare',[PhotosController::class, 'photoshare']);
        // $group->get('/{idloc}/thumbs',[PhotosController::class, 'thumbs']);
        // $group->get('/{idloc}/videos',[PhotosController::class, 'videos']);
    });

    $app->group('/samfotoaq', function (RouteCollectorProxy $group) {
        $group->get('/genera_modelo/{tabla}',[SamFotoAQController::class, 'createModel']);
    });

    $app->group('/fotosStaging', function(RouteCollectorProxy $group) {
        $group->get('/genera_modelo/{tabla}',[FotosAppStagingController::class, 'createModel']);
        $group->get('/config_sync/{location_code}',[FotosAppStagingController::class, 'configSync']);
        $group->get('/config_sync_net/{location_code}',[FotosAppStagingController::class, 'configSyncNet']);
        $group->get('/clouds/{location}/{fechad}/{fechah}',[FotosAppStagingController::class, 'clouds']);
        $group->get('/clouds_st_procedure/{location}/{fechad}/{fechah}',[FotosAppStagingController::class, 'cloudsStoreProcedure']);
        $group->get('/clouds_st_procedure_test/{location}/{fechad}/{fechah}',[FotosAppStagingController::class, 'cloudsStoreProceduretest']);
        $group->get('/compras_st_procedure/{location}/{fechad}/{fechah}',[FotosAppStagingController::class, 'comprasEnLineaStoreProcedure']);
        $group->get('/thumbs/{location}/{fechad}/{fechah}',[FotosAppStagingController::class, 'thumbs']);
        $group->get('/pictures/{location}/{fechad}/{fechah}',[FotosAppStagingController::class, 'mypictures']);
        $group->get('/pictures_st_procedure/{location}/{fechad}/{fechah}',[FotosAppStagingController::class, 'mypicturesStoreProcedure']);
        
        $group->get('/shares/{location}/{fechad}/{fechah}',[FotosAppStagingController::class, 'share']);

    });

    $app->group('/photoshare', function (RouteCollectorProxy $group) {
        $group->get('/shares/{location}/{fechad}/{fechah}',[PhotoshareController::class, 'share']);
        $group->get('/genera_modelo/{tabla}',[PhotoshareController::class, 'createModel']);
        // $group->get('/{idloc}/photoshare',[PhotosController::class, 'photoshare']);
        // $group->get('/{idloc}/thumbs',[PhotosController::class, 'thumbs']);
        // $group->get('/{idloc}/videos',[PhotosController::class, 'videos']);
    });

    $app->group('/mypictures', function (RouteCollectorProxy $group) {
        $group->get('/pictures/{location}/{fechad}/{fechah}',[MypicturesController::class, 'mypictures']);
        $group->get('/genera_modelo/{tabla}',[MypicturesController::class, 'createModel']);
        // $group->get('/{idloc}/photoshare',[PhotosController::class, 'photoshare']);
        // $group->get('/{idloc}/thumbs',[PhotosController::class, 'thumbs']);
        // $group->get('/{idloc}/videos',[PhotosController::class, 'videos']);
    });

    $app->group('/videos', function (RouteCollectorProxy $group) {
        $group->get('/clips/{location}/{fechad}/{fechah}',[VideoClipsController::class, 'clips']);
        $group->get('/tmdvideos/{location}/{fechad}/{fechah}',[VideoClipsController::class, 'tmdVideos']);
        $group->get('/genera_modelo/{tabla}',[VideoClipsController::class, 'createModel']);
        // $group->get('/{idloc}/photoshare',[PhotosController::class, 'photoshare']);
        // $group->get('/{idloc}/thumbs',[PhotosController::class, 'thumbs']);
        // $group->get('/{idloc}/videos',[PhotosController::class, 'videos']);
    });
    

    $app->group('/reservas', function (RouteCollectorProxy $group) {
        $group->get('/datos_base_isepaaa/{id}',[ReservasController::class, 'getDatosBaseIsepaaa']);
        $group->post('/{id}/crear_impuesto',[ReservasController::class, 'CrearImpuesto']);

        $group->get('/cat_tipo_cambio/{id_moneda}', [ReservasController::class, 'getCatTipoCambio']);
        $group->get('/getfolio',   [ReservasController::class, 'getFolio']);
        // $group->get('/modelo', [GenerateModeloController::class, 'createModel']);
    });
    // Grupo de rutas /ordenes
    $app->group('/ordenes', function (RouteCollectorProxy $group) {
        $group->get('/pendientes',   [OrdenesController::class, 'index']);
        $group->get('/terminadas',   [OrdenesController::class, 'index']);
        $group->get('/canceladas',   [OrdenesController::class, 'index']);
    });

    // Otras rutas sueltas:
    // $app->get('/hello', function(...) { ... });
};
