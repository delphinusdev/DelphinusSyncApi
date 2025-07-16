

SELECT * FROM esquema_bonos_coordinadores_editores;

SELECT * FROM empleados WHERE nombre like '%juan%';

408;

select * FROM esquema_bonos_coordinadores_editores where id_empleado = 400 and aplica_hasta is null;


INSERT INTO esquema_bonos_coordinadores_editores(
    location_code,
    id_empleado,
    id_tipo_usuario,
    meta_rango_inicial,
    meta_rango_final,
    monto_porciento,
    monto_fijo,
    es_monto_fijo,
    aplica_desde,
    created,
    updated)
select
    location_code,
    408 AS id_empleado,
    id_tipo_usuario,
    meta_rango_inicial,
    meta_rango_final,
    monto_porciento,
    monto_fijo,
    es_monto_fijo,
    '2025-06-01' as aplica_desde,
    GETDATE() AS created,
    GETDATE() AS updated
from
    esquema_bonos_coordinadores_editores
where id_empleado = 400 and aplica_hasta is null;

UPDATE esquema_bonos_coordinadores_editores
set aplica_hasta = '2025-06-01'
where id_empleado = 400 and aplica_hasta is null;

;

SELECT e.nombre,t.tipo_usuario, ee.* FROM esquema_bonos_coordinadores_editores as ee 
INNER JOIN empleados as e on ee.id_empleado = e.id_empleado
inner join tipo_usuario t on ee.id_tipo_usuario = t.id_tipo_usuario
;

select * FROM tipo_usuario;