<div class="liston">
    <div id="liston-derecha"><span class="titulo_internas">VENTAS RECIENTES</span></div>
    <div id="liston-izquierda">
        <a href="ventas-xls.php" target="_blank" style="width:40px; float:left; margin-top:13px; margin-right:10px"><img src="images/excel.png" width="36" height="36" /></a>
        <form name"form_buscar" id="form_buscar" method="get" action="" style="width:450px; float:right;">
            <table width="100%" border="0" cellspacing="5">
                <tr>
                    <td><span class="titulo-buscador">Desde:</span><br />
                        <input name="fecha1" id="fecha1" type="text" class="buscador"/></td>
                    <td><span class="titulo-buscador">Hasta:</span><br />
                        <input name="fecha2" id="fecha2" type="text" class="buscador"/></td>
                    <td><span class="titulo-buscador">Locación:</span><br />
                        <select name="locacion" id="locacion" class="combo-form">
                            <option value="0">Selecciona...</option>
                            <option value="6">Delphinus Aquarium Cancun</option>
                            <option value="2">Delphinus Dreams Cancún</option>
                            <option value="3">Delphinus Riviera Maya</option>
                            <option value="4">Delphinus Xcaret</option>
                            <option value="5">Delphinus Xel-ha</option>
                            <option value="7">Delphinus Hyatt Ziva</option>
                            <option value="8">Delphinus Puerto Morelos</option>
                        </select>
                    </td>
                    <td><span class="titulo-buscador">Status Pagos:</span><br />
                        <select name="status_pago" id="status_pago" class="combo-form">
                            <option value="">Selecciona...</option>
                            <option value="1">Pagados</option>
                            <option value="0">No Pagados</option>
                           
                        </select>
                    </td>
                    <td><span class="titulo-buscador">No. Pedido:</span><br />
                        <input name="texto" type="text" class="buscador"/></td>
                    <td align="center" valign="middle"><input name="" type="submit" value="Buscar" class="boton_c"/></td>
                </tr>
            </table>
        </form>
        <div class="limpiador"></div>
    </div>
    <div class="limpiador"></div>
</div>
<?php
include("lib/conexion.php"); //

$texto_aux= ''; //
$locacion= 0; //
$status_pago= ''; //
$fecha1 =''; //
$fecha2 = ''; //
if(isset($_GET['fecha1']) and isset($_GET['fecha2'])) //
{
    $fecha1 =$_GET['fecha1']; //
    $fecha2 = $_GET['fecha2']; //
}

if(isset($_GET['texto']) and isset($_GET['locacion']) and isset($_GET['status_pago'])) //
{
    $texto_aux= trim($_GET['texto']); //
    $locacion= $_GET['locacion']; //
    $status_pago= $_GET['status_pago']; //
}

if ($texto_aux != "") //
{
    echo "1"; //
    $qrys="SELECT * FROM PEDIDOS WHERE ESTADO = 1 AND ISNULL(ID_VENTA,0) = 0 AND PEDIDO = '$texto_aux'  ORDER BY IDPEDIDO DESC"; //
}
else if($fecha1== "" && $fecha2 == "" && $locacion != 0 ) //
{
    echo "2"; //
    $qrys="SELECT * FROM PEDIDOS WHERE ESTADO = 1 AND ISNULL(ID_VENTA,0) = 0 AND IDLOCACION = '$locacion'  ORDER BY IDPEDIDO DESC"; //
}
else if($fecha1 != "" && $fecha2 != "" && $locacion != 0) //
{
    echo "3"; //
    $partes1 = explode("/", $_GET['fecha1']); //
    $partes2 = explode("/", $_GET['fecha2']); //
    $fecha1=$partes1[2]."-".$partes1[0]."-".$partes1[1]." 00:00"; //
    $fecha2=$partes2[2]."-".$partes2[0]."-".$partes2[1]." 23:59:59.999"; //
    $qrys="SELECT * FROM PEDIDOS WHERE ESTADO = 1 AND ISNULL(ID_VENTA,0) = 0 AND FECHA_PAGO BETWEEN '$fecha1' AND '$fecha2' AND IDLOCACION = '$locacion'  ORDER BY IDPEDIDO DESC"; //
}
else if($fecha1 != "" && $fecha2 != "" && $status_pago=='') //
{
    echo "4"; //
    $partes1 = explode("/", $fecha1); //
    $partes2 = explode("/", $fecha2); //
    $fecha1=$partes1[2]."-".$partes1[0]."-".$partes1[1]." 00:00"; //
    $fecha2=$partes2[2]."-".$partes2[0]."-".$partes2[1]." 23:59:59.999"; //
    $qrys="SELECT * FROM PEDIDOS WHERE ESTADO = 1 AND ISNULL(ID_VENTA,0) = 0 AND FECHA_PAGO BETWEEN '$fecha1' AND '$fecha2'  ORDER BY IDPEDIDO DESC"; //
}
else if($status_pago!='' && $_GET['fecha1'] == "" && $_GET['fecha2'] == "") //
{
    echo "6"; //
    $qrys="SELECT * FROM PEDIDOS WHERE ESTADO = '$status_pago' AND ISNULL(ID_VENTA,0) = 0  ORDER BY IDPEDIDO DESC"; //
}
else if($status_pago!='' && $_GET['fecha1'] != "" && $_GET['fecha2'] != "") //
{
    echo "7"; //
    $partes1 = explode("/", $_GET['fecha1']); //
    $partes2 = explode("/", $_GET['fecha2']); //
    $fecha1=$partes1[2]."-".$partes1[0]."-".$partes1[1]." 00:00"; //
    $fecha2=$partes2[2]."-".$partes2[0]."-".$partes2[1]." 23:59:59.999"; //
    $qrys="SELECT * FROM PEDIDOS WHERE ESTADO = '$status_pago' AND ISNULL(ID_VENTA,0) = 0 AND FECHA_PAGO BETWEEN '$fecha1' AND '$fecha2'  ORDER BY IDPEDIDO DESC"; //
}
else //
{
    echo "5"; //
    $qrys="SELECT * FROM PEDIDOS WHERE ESTADO = 1 AND ISNULL(ID_VENTA,0) = 0  ORDER BY IDPEDIDO DESC"; //
}
$sql = $qrys; //
$stmt = sqlsrv_query( $conn, $sql ); //
$totalcompra=0; //
echo '<table width="100%" border=0 cellpadding="0" cellspacing="0" class="tabla-captura" align="center">'; //
    echo '<tr>'; //
        echo '<td></td>'; //
        echo '<td><div class="titulo-captura">FECHA</td>'; //
        echo '<td><div class="titulo-captura">FECHA PAGO</td>'; //
        echo '<td><div class="titulo-captura">PEDIDO</td>'; //
        echo '<td><div class="titulo-captura">FOTOS</td>'; //
        echo '<td><div class="titulo-captura">CLIENTE</td>'; //
        echo '<td><div class="titulo-captura">USUARIO</td>'; //
        echo '<td><div class="titulo-captura">PASSWORD</td>'; //
        echo '<td><div class="titulo-captura">COSTO(USD)</td>'; //
        echo '<td><div class="titulo-captura">LOCACION</td>'; //
        echo '<td><div class="titulo-captura">PAGO</td>'; //
        echo '<td><div class="titulo-captura">MONEDA</td>'; //
        echo '<td><div class="titulo-captura">OPCIONES</td>'; //
    echo '</tr>'; //
    $x=1; //
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) //
    {
        
    echo '<tr>'; //
        echo '<td class="input-master-2">'.$x.'</td>'; //
        $fecha_reg = explode("-", $row[1]); //
        $anno = substr($fecha_reg[0], 0,-4); //
        $mes = substr($fecha_reg[0], 4,-2); //
        $dia = substr($fecha_reg[0], -2); //
        $fecha_aux_final=$dia."/".$mes."/".$anno; //

        echo '<td class="input-master-1">'.$fecha_aux_final.'</td>'; //
        echo '<td class="input-master-1">'.$row[26]->format('d/m/Y').'</td>'; //
        echo '<td class="input-master-1">'.htmlentities($row[1]).'</td>'; //
        $cantidad_fotos=substr_count( $row[3], "|" ); //
        echo '<td class="input-master-1">'.$cantidad_fotos.'</td>'; //
        $aux_cliente=$row[4]; //
        $sql_aux1 = "SELECT NOMBRE, PATERNO, EMAIL, CONTRASENA FROM CLIENTES WHERE IDCLIENTE = '$aux_cliente' AND ISNULL(ID_VENTA,0) = 0 "; //
        $stmt_aux1 = sqlsrv_query( $conn, $sql_aux1 ); //
        $row_aux1 = sqlsrv_fetch_array($stmt_aux1, SQLSRV_FETCH_NUMERIC); //
        $cliente_aux=$row_aux1[0]." ".$row_aux1[1]; //
        echo '<td class="input-master-1">'.strtoupper($cliente_aux).'</td>'; //
        echo '<td class="input-master-1">'.htmlentities(strtoupper($row_aux1[2])).'</td>'; //
        echo '<td class="input-master-1">'.htmlentities(strtoupper($row_aux1[3])).'</td>'; //
        echo '<td class="input-master-1"> $'.number_format($row[5],1).'</td>'; //
        $totalcompra=$totalcompra+ number_format($row[5],1); //
        $aux_locacion=$row[8]; //
        $sql_aux3 = "SELECT location_name FROM LOCACIONES WHERE idlocacion = '$aux_locacion' "; //
        $stmt_aux3 = sqlsrv_query( $conn, $sql_aux3 ); //
        $row_aux3 = sqlsrv_fetch_array($stmt_aux3, SQLSRV_FETCH_NUMERIC); //
        $locacion_aux=$row_aux3[0]; //
        echo '<td class="input-master-1">'.htmlentities($locacion_aux).'</td>'; //
        if($row[8]==2) //
        {
            $locacion_code='Del-DCun'; //
        }
        else if($row[8]==3) //
        {
            $locacion_code='Del-RM'; //
        }
        else if($row[8]==4) //
        {
            $locacion_code='Del-Xcaret'; //
        }
        else if($row[8]==5) //
        {
            $locacion_code='Del-Xelha'; //
        }
        else if($row[8]==7) //
        {
            $locacion_code='Del-HZ'; //
        }
        else if($row[8]==8) //
        {
            $locacion_code='Del-PM'; //
        }

        if($row[13]==2){ //
            $denomiacion = 'Bancomer'; //
        }elseif($row[13]==1){ //
            $denomiacion = 'PayPal'; //
        }elseif($row[13]==''){ //
            $denomiacion = '-'; //
        }

        if($row[14]==2){ //
            $moneda = 'USD'; //
        }elseif($row[14]==1) { //
            $moneda = 'MX'; //
        }elseif($row[14]=='') { //
            $moneda = '-'; //
        }
    
        echo '<td class="input-master-1">'.htmlentities($denomiacion).'</td>'; //
        echo '<td class="input-master-1">'.htmlentities($moneda).'</td>'; //

        $id_foto = explode("|",$row[3]); //
        echo '<td class="input-master-1" style="text-align:center !important"><a href="detalle-compra.php?id='.$row[0].'"><img src="images/detalle.png" width="24" height="24" title="Detalle de la Compra" /></a></td>'; //
    echo '</tr>'; //
    $x++; //
    }
echo '</table>'; //

echo '<div align="right" style="margin-top:15px;">[ <span style="color:#666;">TOTAL: </span> <span style="font-size:18px; font-weight:bold"> $ '.number_format($totalcompra,1).' USD</span> ]</div>'; //
?>
<br />
<br />