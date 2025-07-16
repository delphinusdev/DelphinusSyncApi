
update u set u.sincronizado = 0
FROM
    eb_photo_delphinus.dbo.ventas as v inner JOIN USUARIO u ON v.id_venta = u.ID_VENTAS 
WHERE
    cast(fecha as date) >= '2025-07-01'
    and cast(fecha as date) <= '2025-07-11'
    and status_pago = 1;


    SELECT * FROM catalogo_fotos_locaciones_app WHERE ID_VENTA = 747594;

    SELECT * FROM USUARIO WHERE ID_VENTAS = 747594;