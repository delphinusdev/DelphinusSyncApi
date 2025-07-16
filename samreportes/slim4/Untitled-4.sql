

SELECT COUNT(idfoto) as duplicados, idfoto, ID_VENTA, idlocation 
FROM grupos_fotos_locaciones 
WHERE cast(fecha as date) = '2025-07-09' and isnull(ID_VENTA,0) > 0
group by idfoto, ID_VENTA, idlocation 
having COUNT(idfoto) > 1;

select MIN(idgrupofotoslocaciones) from grupos_fotos_locaciones where idfoto = 23116816 and ID_VENTA = 492075 and idlocation = 5;
