<?php

namespace App\Services;

// use App\Database\DelphinusETravelContext;
// use App\Utils\QueryBuilder;
// use App\Database\EbPhotoDelphinusContext;
// use App\Interfaces\IGeneraFolioService;
// use App\Models\RsrvReservasModel as reservas;
// use App\Models\RsrvServiciosModel as servicios;
// use App\Models\VentasLocacionesModel as locaciones;

// class GeneraFolioService implements IGeneraFolioService
// {
//     /**
//      * @var DelphinusETravelContext
//      */
//     private DelphinusETravelContext $repo;
//     /**
//      * OrdenesService constructor.
//      *
//      * @param DelphinusETravelContext $context
//      */
//     private QueryBuilder $queryBuilder;


//     public function __construct(DelphinusETravelContext $context, QueryBuilder $queryBuilder)
//     {
//         $this->repo = $context;
//         $this->queryBuilder = $queryBuilder;
//     }


//     public function getFolio(int $id_servicio, int $id_habitat): array
//     {

//         $subconsulta = '( SELECT ' . locaciones::CodLocacion(null, 'l') . ' FROM ' . locaciones::tableName('l') . ' WHERE ' . locaciones::IdLocacion(null, 'l') . ' = ' . reservas::IdLocacion(null, 'r') . ' ) AS cod_locacion';
        
//         $reservas = reservas::from('r')
//         ->newQuery()
//         ->select([
//             reservas::UniqidReserva(null, 'r'),
//             $subconsulta,
//             reservas::IdLocacion(null, 'r')
//         ])
//             ->where(reservas::IdReservacion(null, 'r'), '=', $id_servicio)
//             ->where(reservas::IdLocacion(null,'r'),'=',$id_habitat);


//         $queryFolio = $reservas->select('MAX(' . reservas::IdReservacion(null, 'r') . ') + 1 AS folio_maximo')
//             ->from(reservas::tableName('r'))
//             ->where(reservas::IdServicio(null, 'r'), '=', $id_servicio)
//             ->where(reservas::IdLocacion(null, 'r'), '=', $id_habitat)
//             ->limit(1);





//         $folio = array(
//             'CodigoHabitat' =>  '0000',
//             'CodigoServicio' => 'ISE',
//             'CodigoMedioVenta' => 'V',
//             'CodigoReserva' => '000',
//             'FechaServicio' => date('dmy'),
//         );

//         $folioGenerado = $folio['CodigoHabitat']
//             . $folio['CodigoServicio'] . '-'
//             . $folio['CodigoMedioVenta'] . $folio['CodigoReserva'] . '-'
//             . $folio['FechaServicio'];





//         $reservas = reservas::from('r');

//         $columns = [
//             reservas::IdReservacion(null, 'r'), // r.id_reservacion AS reserva_id
//             reservas::FolioReserva(null, 'r'),   // r.nombre_cliente AS cliente_nombre
//             reservas::Status(null, 'r'), // r.fecha_llegada AS fecha_llegada_reserva
//             servicios::NomServicio(null, 's'),
//             locaciones::NomLocacion(null, 'l') // s.nom_servicio AS servicio_nombre
//         ];

//         // $queryParts = $reservas
//         // ->select($columns)
//         // ->innerJoin(servicios::tableName('s'), reservas::IdServicio(null,'r'), '=', servicios::IdServicio(null, 's'))
//         // ->innerJoin(locaciones::tableName('l'), reservas::IdLocacion(null,'r'), '=', locaciones::IdLocacion(null,'l')) // Join with RsrvServicios on the service ID
//         // ->where(reservas::IdReservacion(null,'r'), '=', $filtros['id_reserva_base'])
//         // ->build();


//         return []; //$this->repo->select($queryParts['sql'],$queryParts['params']) ; // $this->repo->select($sql, $filtros );
//         // Aquí deberías implementar la lógica para generar el folio basado en los filtros proporcionados.
//         // Por ahora, se devuelve un folio de ejemplo.


//     }
// }
