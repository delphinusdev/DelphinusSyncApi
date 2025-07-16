<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Prueba del Modal (Modo Flexible)</title>
    <style>
        /* Estilos opcionales para tus botones personalizados */
        .mi-boton-personalizado {
            background-color: #28a745;
            /* Verde */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }

        .mi-boton-personalizado:hover {
            background-color: #218838;
        }

        .un-enlace-activador {
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
            margin: 10px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <h1>Bienvenido a mi Página</h1>
    <p>Aquí puedes integrar el modal de reservas usando tus propios botones.</p>

    <button class="js-abrir-modal-reserva" id="btnAbrirModalReserva">Haz tu Reserva Aquí</button>

    <hr>

    <button class="js-abrir-modal-reserva mi-boton-personalizado">Reservar Mesa (Estilo Propio)</button>
    <button type="button" class="btn-modal-reserva-trigger btn btn-default '.$hide.'" 
        data-action="view"
        data-alta-id-locacion="1"
        data-session-user="sistemas"
        data-session-pass="t1c9gvd$$"
        data-api-endpoint="/Reservas/37663196"
        data-otrareserva="37504255,37382210,37504335"
        data-populate-form="true"
        data-modal-title="ASIGNAR IMPUESTO ISEPAAA AL FOLIO: 37498683">
        <i class="icon-ok"></i>AGREGAR ISEPAAA
    </button>

    <hr>

    <p>
        También puedes <a href="#" class="js-abrir-modal-reserva">hacer clic en este enlace para reservar</a>.
    </p>

    <hr>

    <div class="js-abrir-modal-reserva" style="background-color: orange; color: white; padding: 15px; display: inline-block; cursor: pointer; border-radius: 8px;">
        ¡Oferta! Reserva Ya
    </div>

    <button class="btn-modal-reserva-trigger"
        data-action="view"
        data-api-endpoint="reservas/info/80"
        data-populate-form="true" data-modal-title="Detalles de Reserva ID: 80">
        Ver Reserva 80
    </button>

    <p>Más contenido de la página...</p>

    <script src="http://localhost:8080/samreportes/slim4/js/script_isepaaa_api.js" defer></script>
</body>

</html>