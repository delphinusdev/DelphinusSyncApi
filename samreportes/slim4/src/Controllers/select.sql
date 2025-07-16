select
    cfav.grupofotos,
    vv.folio,
    count(paquete) AS cantidad,
    paquete,
    CONVERT(VARCHAR(10), fecha_nado, 105) AS fecha,
    cfav.programa,
    hora_nado,
    visitantes,
    fotografo,
    cliente,
    id_maquina,
    vv.observaciones,
    idpaquete,
    case
        when coalesce(nullif(eh.id_grupo, 0), 0) > 0 then vv.folio
        else 0
    end as envio
from
    catalogo_fotos_all_v cfav
    inner join ventas_v vv on cfav.grupofotos = vv.grupofotos
    inner join detalle_venta_paquetes_v dvpv on vv.folio = dvpv.folio
    left outer join envio_hotel eh on cfav.grupofotos = eh.id_grupo
where
    dvpv.status = 1
    and cfav.fecha_nado >= '2025-05-26'
    and cfav.fecha_nado <= '2025-05-26'
    and dvpv.tipos = 'F'
group by
    cfav.grupofotos,
    vv.folio,
    dvpv.paquete,
    cfav.fecha_nado,
    cfav.programa,
    cfav.hora_nado,
    cfav.visitantes,
    cfav.fotografo,
    cfav.cliente,
    cfav.id_maquina,
    vv.observaciones,
    dvpv.idpaquete,
    case
        when coalesce(nullif(eh.id_grupo, 0), 0) > 0 then vv.folio
        else 0
    end
order by
    vv.folio ASC