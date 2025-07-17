<div class="liston">
    <div id="liston-derecha"><span class="titulo_internas">LISTA DE USUARIOS</span></div>
    <div id="liston-izquierda">
        <a href="usuarios-xls.php" target="_blank" style="width:40px; float:left; margin-top:13px; margin-right:10px"><img src="images/excel.png" width="36" height="36" /></a>
        <form name"form_buscar" id="form_buscar" method="get" action="" style="width:185px; float:right;">
            <table width="40%" border="0" cellspacing="5">
                <tr>
                    <td><span class="titulo-buscador">Nombre:</span><br />
                        <input name="nombre" id="nombre" type="text" class="buscador"/>
                    </td>
                    <td><span class="titulo-buscador">Email:</span><br />
                        <input name="email" id="email" type="text" class="buscador"/>
                    </td>
                    <td align="center" valign="middle"><input name="" type="submit" value="Buscar" class="boton_c"/></td>
                </tr>
            </table>
        </form>
    </div>
    <div class="limpiador"></div>
</div>
<?php
include("lib/conexion.php"); //
$nombre = ''; //
$email = ''; //
if(isset($_GET['nombre'])) //
{
    $nombre= trim($_GET['nombre']); //
}
if(isset($_GET['email'])) //
{
    $email= $_GET['email']; //
}
if ($nombre != "" && $email=="") //
{
    $qrys="SELECT * FROM CLIENTES WHERE NOMBRE = '$nombre' ORDER BY IDCLIENTE DESC"; //
}
else if ($email != "" && $nombre == "") //
{
    $qrys="SELECT * FROM CLIENTES WHERE EMAIL = '$email' ORDER BY IDCLIENTE DESC"; //
}
else if ($email != "" && $nombre != "") //
{
    $qrys="SELECT * FROM CLIENTES WHERE NOMBRE = '$nombre' AND EMAIL = '$email' ORDER BY IDCLIENTE DESC"; //
}
else //
{
    $qrys="SELECT * FROM CLIENTES ORDER BY IDCLIENTE DESC"; //
}

$sql = $qrys; //
$stmt = sqlsrv_query( $conn, $sql ); //

echo '<table width="100%" border=0 cellpadding="0" cellspacing="0" class="tabla-captura" align="center">'; //
    echo '<tr>'; //
        echo '<td></td>'; //
        echo '<td><div class="titulo-captura">NOMBRE</td>'; //
        echo '<td><div class="titulo-captura-1">APELLIDO</td>'; //
        echo '<td><div class="titulo-captura">E-MAIL</td>'; //
        echo '<td><div class="titulo-captura-1">TELEFONO</td>'; //
        echo '<td><div class="titulo-captura">PAIS</td>'; //
        echo '<td><div class="titulo-captura-1">CONTRASEÑA</td>'; //
        echo '<td><div class="titulo-captura">OPCIONES</td>'; //
    echo '</tr>'; //
    $x=1; //
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) //
    {
    echo '<tr>'; //
        echo '<td class="input-master-2">'.$x.'</td>'; //
        echo '<td class="input-master">'.htmlentities($row[1]).'</td>'; //
        echo '<td class="input-master-1">'.htmlentities($row[2]).'</td>'; //
        echo '<td class="input-master"><a href="mailto:'.htmlentities($row[4]).'" class="correo">'.htmlentities($row[4]).'</a></td>'; //
        echo '<td class="input-master-1">'.htmlentities($row[5]).'</td>'; //
        echo '<td class="input-master">'.htmlentities($row[6]).'</td>'; //
        echo '<td class="input-master-1"><b>'.htmlentities($row[10]).'</b></td>'; //
        echo '<td class="input-master"><a href="editar-usuarios.php?id='.$row[0].'"><img src="images/clave.png" width="24" height="24" /></a></td>'; //
    echo '</tr>'; //
    $x++; //
    }
echo '</table>'; //
?>
<br />
<br />