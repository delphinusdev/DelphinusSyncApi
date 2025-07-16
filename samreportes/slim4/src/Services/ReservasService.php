<?php

namespace App\Services;

use App\Database\SqlSrvGenericContext;
use App\Models\ReservasModel\RsrvPasajerosModel as pasajeros;
use App\Models\ReservasModel\VentasPreciosServiciosModel as precios;
use App\Models\ReservasModel\VentasCatTipoCambioModel as tipoCambio;
use App\Models\ReservasModel\VentasCatMonedasModel as monedas;
use App\Models\ReservasModel\VentasTipoTarjetaModel as tarjetas;
use App\Models\ReservasModel\VentasTipoPagoModel as tipoPago;
use App\Models\ReservasModel\VentasLocacionesModel as locaciones;
use App\Utils\AddOperations as operations;
use App\Utils\Search;

use App\Utils\QueryBuilder;
use App\Models\ReservasModel\RsrvReservasModel as reservas;
use Exception;

class ReservasService
{
    private SqlSrvGenericContext $repo;



    public function __construct(SqlSrvGenericContext $reservasContext)
    {
        $this->repo = $reservasContext;
    }

    private function exec(string $sql, array $query): array
    {

        return $this->repo->$sql($query['sql'], $query['params']);
    }

    public function getMaxCodeReserva(int $id_habitat): QueryBuilder
    {
        return reservas::from('r')->select('MAX(' . reservas::CodReserva(null, 'r') . ') + 1 AS folio_maximo')
            ->from(reservas::tableName('r'))
            ->where(reservas::IdLocacion(null, 'r'));
    }

    public function getTiposPago(): array
    {
        $query = tipoPago::from('tp')
            ->newQuery()
            ->select([
                tipoPago::IdTipoPago(null, 'tp'),
                tipoPago::NomPago(null, 'tp'),
                sprintf('CASE WHEN %s = %s THEN %d ELSE %d END AS referencia', tipoPago::CodPago(null, 'tp'), "'TC'", 1, 0),
                tipoPago::CodPago(null, 'tp')
            ])
            ->from(tipoPago::tableName('tp'))
            ->where(tipoPago::Status(null, 'tp'), '=', 1)
            ->build();

        return $this->exec('select', $query);
    }

    public function getTarjetas(): array
    {
        $query = tarjetas::from('t')
            ->newQuery()
            ->select([tarjetas::IdTipoTarjeta(null, 't'), tarjetas::NomTarjeta(null, 't')])
            ->from(tarjetas::tableName('t'))
            ->where(tarjetas::Status(null, 't'), '=', 1)
            ->build();

        return $this->exec('select', $query);
    }

    public function getCatTipoCambio(): array
    {
        $param = ['status' => 1];

        $sqlFecha = 'cast(getdate() AS date)';



        $joinAnd = sprintf(
            ' AND %s <=  %s AND %s >= %s AND %s = %d ',
            tipoCambio::FechaInicial(null, 't'),
            $sqlFecha,
            tipoCambio::FechaFinal(null, 't'),
            $sqlFecha,
            tipoCambio::Status(null, 't'),
            1

        );

        $query = monedas::from('m')
            ->newQuery()
            ->select([
                monedas::IdMoneda(null, 'm'),
                monedas::NomMoneda(null, 'm'),
                monedas::CodMoneda(null, 'm'),
                sprintf('CAST( ISNULL(%s,1) AS DECIMAL(18,2) ) AS paridad', tipoCambio::Paridad(null, 't'))

            ])
            ->from(monedas::tableName('m'))
            ->leftJoin(tipoCambio::tableName('t'), monedas::IdMoneda(null, 'm'), '=', tipoCambio::IdMoneda(null, 't') . $joinAnd)
            ->where(monedas::Status(null, 'm'), '=', $param['status'])
            ->build();



        return $this->exec('select', $query);
    }

    public function getQueryPrecios(): QueryBuilder
    {
        $queryBuilder = precios::from('p')
            ->newQuery()
            ->select([
                precios::IdServicio(null, 'p'),
                sprintf(
                    'CAST(%s AS DECIMAL(18,11)) AS precio',
                    precios::Precio(null, 'p')
                )
            ])
            ->from(precios::tableName('p'));
        return $queryBuilder;
    }

    public function getUltimoFolioReserva(int $id_habitat): QueryBuilder
    {
        $$queryBuilder = reservas::from('r')
            ->newQuery()
            ->select('MAX(' . reservas::CodReserva(null, 'r') . ') + 1 AS folio_maximo')
            ->from(reservas::tableName('r'))

            ->where(reservas::IdLocacion(null, 'r'), '=', $id_habitat)
            ->limit(1);

        return $queryBuilder;
    }

    public function getReserva(): QueryBuilder
    {

        $param = [1, 3];

        $select = [
            reservas::IdReservacion(null, 'r'),
            reservas::IdLocacion(null, 'r'),
            reservas::FechaServicio(null, 'r'),
            reservas::UniqidReserva(null, 'r'),
            reservas::Confirma(null, 'r'),
            '3 as adultos',
            '2 as menores',
            // sprintf("CASE WHEN %s = 'MXQR' THEN 0 ELSE ISNULL(%s,0) END AS adultos", pasajeros::CodPais(null,'p'), reservas::PaxFinales(null, 'r')),
            // sprintf("CASE WHEN %s = 'MXQR' THEN 0 ELSE ISNULL(%s,0) END AS menores", pasajeros::CodPais(null,'p'), reservas::PaxnFinales(null, 'r')),
            pasajeros::IdPasajero(null, 'p'),
            pasajeros::CodPais(null, 'p'),
            pasajeros::NomPasajero(null, 'p'),
            locaciones::CodLocacion(null, 'l'),
            locaciones::NomLocacion(null, 'l')
        ];

        $query = reservas::from('r')
            ->newQuery()
            ->select($select)
            ->from(reservas::tableName('r'))
            ->leftJoin(pasajeros::tableName('p'), reservas::IdPasajero(null, 'r'), '=', pasajeros::IdPasajero(null, 'p'))
            ->innerJoin(locaciones::tableName('l'), reservas::IdLocacion(null, 'r'), '=', locaciones::IdLocacion(null, 'l'))
            ->whereIn(reservas::Status(null, 'r'),$param);

        return $query;
    }


    public function getDatosBaseIsepaaa(int $id): array
    {
        $getQueryReservaBase = [];
        $getQueryReservaImpuesto = [];
        $getQueryPrecios = [];

        $getDataReservaBase = [];
        $getDataReservaImpuesto = [];
        $getDataTiposPagos = [];
        $getDataTarjetas = [];
        $getDataCatTipoCambio = [];

        $getTarifaPorPersonaUSD = [];

        $getTipoCambioUSD = 0.00;

        $uniqReserva = 'xxxxxxxxx';

        $getQueryPrecios = $this->getQueryPrecios()
            ->where(precios::IdServicio(null, 'p'), '=', ID_RESERVA)
            ->limit(1)
            ->build();


        $getDataTiposPagos = $this->getTiposPago();
        $getDataTarjetas = $this->getTarjetas();
        $getDataCatTipoCambio = $this->getCatTipoCambio();
        $getTarifaPorPersonaUSD = $this->repo->selectOne($getQueryPrecios['sql'], $getQueryPrecios['params']);


        $getTipoCambioUSD = Search::findFirst($getDataCatTipoCambio, 'cod_moneda', 'USD')['paridad'];

        $getQueryReservaBase = $this->getReserva()
            ->where(reservas::IdReservacion(null, 'r'), '=', $id)
            ->limit(1)->build();


        $getDataReservaBase = $this->repo->selectOne($getQueryReservaBase['sql'], $getQueryReservaBase['params']);
        $getDataReservaBase = ($getDataReservaBase === false) ? [] : $getDataReservaBase;


        if (!empty($getDataReservaBase)) {
            $getDataReservaBase['id_servicio_tarifa_usd'] = $getTarifaPorPersonaUSD['id_servicio'];
            $getDataReservaBase['tarifa_usd'] = $getTarifaPorPersonaUSD['precio'];
            $getDataReservaBase['paridad'] = $getTipoCambioUSD;
            $getDataReservaBase['tarifa_usd_mn_default'] = 50.00;


            $uniqReserva = $getDataReservaBase['uniqid_reserva'];


            $getQueryReservaImpuesto = $this->getReserva()
                ->where(reservas::IdServicio(null, 'r'), '=', ID_RESERVA)
                ->where(reservas::UniqidReserva(null, 'r'), '=', $uniqReserva)
                ->limit(1)
                ->build();

            $getDataReservaImpuesto = $this->repo->selectOne($getQueryReservaImpuesto['sql'], $getQueryReservaImpuesto['params']);
            $getDataReservaImpuesto = ($getDataReservaImpuesto === false) ? [] : $getDataReservaImpuesto;

            
          

            if (!empty($getDataReservaImpuesto)) {
                $getDataReservaImpuesto['tarifa_usd'] = $getTarifaPorPersonaUSD['precio'];
                $getDataReservaImpuesto['paridad'] = $getTipoCambioUSD;
            }
        }

        
        $info = [
            'reserva' => $getDataReservaBase ,
            'tiposPagoData' => $getDataTiposPagos,
            'tiposTarjetaData' => $getDataTarjetas,
            'monedasData' => $getDataCatTipoCambio,
            'impuesto' => $getDataReservaImpuesto ,
            'pagos' => []
        ];


        return $info;
    }

    public function CrearImpuesto(array $data): array
    {
        $rsrv = new reservas();
        $datos = $data['request'];

        $fecha_servicio = date_format(date_create($datos['fecha_servicio']), 'dmy');
        $reserva = $datos['id_reservacion'];
        $cod_pais = $datos['cod_pais'];
        unset($datos['id_reservacion']);
        unset($datos['cod_pais']);
        $codeReserva = "( {$this->getMaxCodeReserva($datos['id_locacion'])->build()['sql']} = {$datos['id_locacion']} )";
        $folio = " '{$datos['code_locacion']}ISE-V'+{$codeReserva}+'-{$fecha_servicio}' ";
        $pax_finales = $datos['pax_finales'];
        $paxn_finales = $datos['paxn_finales'];
        $totalPaxOriginal = $datos['pax'] + $datos['paxn'];
        $totalPaxActual = $pax_finales + $paxn_finales;
        $paxNoShow = $totalPaxOriginal - $totalPaxActual;

        $totalUSDAdultos = $datos['precio'] * $datos['pax_finales'];
        $totalMXNAdultos = round($totalUSDAdultos * $datos['paridad'], 2);
        $totalUSDMenores = $datos['precion'] * $datos['paxn_finales'];
        $totalMXNMenores = round($totalUSDMenores * $datos['paridad'], 2);
        $MontoTotalUSD = $totalUSDAdultos + $totalUSDMenores;
        $MontoTotalMXN = $totalMXNAdultos + $totalMXNMenores;


        $data_reservas = operations::fromModel(reservas::class, $datos)
            ->with($rsrv->FolioReserva(), $folio)
            ->with($rsrv->IdCliente(), 28044)
            ->with($rsrv->IdRep(), 29483)
            ->with($rsrv->CodPais(), 'MX')
            ->with($rsrv->CodIdioma(), 1)
            ->with($rsrv->IdClasificacion(), 2)
            ->with($rsrv->IdSubclasificacion(), 5)
            ->with($rsrv->IdMedio(), 7)
            ->with($rsrv->IdHotel(), 9484096)
            ->with($rsrv->TotalMonDefault(), $datos['precio'])
            ->with($rsrv->TotalMonBase(), $MontoTotalMXN)
            ->with($rsrv->TotalMonDefault(), $MontoTotalUSD)
            ->with($rsrv->Status(), 1)
            ->with($rsrv->CodReserva(), $codeReserva)
            ->with($rsrv->Confirma(), $folio)
            ->with($rsrv->PrecioNeto(), $totalMXNAdultos)
            ->with($rsrv->PrecioNeton(), $totalMXNMenores)
            ->with($rsrv->TotalMonDefaultNeto(), $MontoTotalUSD)
            ->with($rsrv->TotalMonBaseNeto(), $MontoTotalMXN)
            ->with($rsrv->PaxCobra(), $pax_finales)
            ->with($rsrv->PaxnCobra(), $paxn_finales)
            ->with($rsrv->PaxNoshow(), $paxNoShow)
            ->with($rsrv->IdUsAlta(), $datos['id_usuario'])
            ->with($rsrv->AltaIdLocacion(), $datos['alta_id_locacion'])
            ->get();



        // 3. Realizar la inserción utilizando el modelo
        try {
            $insertQuery = reservas::insert($data)->build();

            // Obtener la SQL y los parámetros generados
            $sql = $insertQuery['sql'];
            $params = $insertQuery['params'];

            echo "SQL de inserción generada:\n";
            echo $sql . "\n";
            echo "Parámetros para la inserción:\n";
            print_r($params);

            // En un entorno real, ejecutarías esto en tu base de datos:
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute($params);
            // echo "Inserción exitosa!";
            return $insertQuery;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return ['error' => $e->getMessage()];
        }
    }

    public function CrearPago(array $data): array
    {
        return $data;
    }
}
