SELECT
    p.confirma_id AS id_grupo_fotos,
    p.image_url,
    p.location_id
FROM
    tb_shared p
WHERE
    CAST(p.created_at AS DATE) >= '2025-04-01'
    AND CAST(p.created_at AS DATE) <= '2025-06-13'
    AND CAST(p.location_id AS VARCHAR(20)) = 'Del-RM'
    AND isnull(p.status_pago,0) = 1