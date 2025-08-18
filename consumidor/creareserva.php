<?php
include_once './apikiosko.php';
try {
    $api = new ReservaApiClient('http://kiosko.delphinus.com.mx'); // sin slash final

    // 1) Autenticación
    $login = $api->authenticate('sistemas', 't1c9gvd$$');
    // echo "Token: " . ($login['token'] ?? $login['data']['token'] ?? 'sin token') . PHP_EOL;

    // 2) Crear la reserva (ejemplo exacto que compartiste)
    $reserva = [
        "fecha"             => "2025-08-18",
        "idHabitat"         => 5,
        "idServicio"        => 2,
        "hora"              => "16:00",
        "adultos"           => 1,
        "menores"           => 0,
        "incentivos"        => 0,
        "idIdioma"          => 1,
        "idPromocion"       => 0,
        "descuento"         => 0,
        "nombre"            => "ISMAEL GARCIA 15",
        "correo"            => "",
        "telefono"          => "",
        "codigoPais"        => "US",
        "idHotel"           => 0,
        "habitacion"        => "",
        "pickup"            => "",
        "idAgencia"         => 9462079,
        "idRepresentante"   => 25065,
        "cupon"             => "KGRD-301830",
        "idClasificacion"   => 3,
        "idSubclasificacion"=> 7,
        "idMedioVenta"      => 8,
        "idAutorizo"        => 0,
        "tipoComentario"    => 1,
        "comentarios"       => ""
    ];

    $respuesta = $api->crearReserva($reserva);
    var_dump($respuesta);

} catch (Throwable $e) {
    // Manejo de error centralizado
    error_log($e->getMessage());
    // En producción puedes mapear mensajes de error a algo más amigable
}