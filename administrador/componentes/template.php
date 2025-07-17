<?php
// This file will receive variables for dynamic content like title and specific scripts/styles
// It also ensures the session is started for all pages using this template.
include_once("lib/sesion.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="css/estilo.css" rel="stylesheet" type="text/css" />
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/validar.js" type="text/javascript"></script>
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/menu.js" type="text/javascript"></script>
    <title>Photo Delphinus | <?php echo isset($pageTitle) ? $pageTitle : 'Administrador'; ?></title>
    <link type="image/x-icon" href="favicon.ico" rel="icon" />
    <link type="image/x-icon" href="favicon.ico" rel="shortcut icon" />
    <style>
        #pie{
            border-top: 10px solid #FFF;
            height: 60px;
            margin: 0 auto 5px;
            padding-top: 11px;
        }
        .jqueryslidemenu ul li ul li a {
            background-color: #009288;
            color: #fff;
            margin: 0;
            padding: 5px;
            text-align: left!important;
            width: 138px!important;
        }
    </style>
    <?php
    // Include any page-specific head content (like datepicker for ventas.php)
    if (isset($headExtra)) {
        echo $headExtra;
    }
    ?>
    <script type="text/javascript">
        //<![CDATA[
        function modelesswin(url,mwidth,mheight){if(document.all&&window.print)
        eval('window.showModelessDialog(url,"","help:0;dialogTop:50px;dialogLeft:50px;resizable:0;dialogWidth:'+mwidth+'px;dialogHeight:'+mheight+'px")')
        else
        eval('window.open(url,"","width='+mwidth+'px,height='+mheight+'px,resizable=1,scrollbars=1,left=20,top=20")')}
        //]]>
    </script>
</head>
<body>
<div id="blanco">
    <div id="contenedor">
        <div id="encabezado">
            <?php include("componentes/encabezado.php");?>
        </div>
        <div id="cuerpo">
            <div id="contenido2">
                <?php
                // This is where the main content of each specific page will be included
                if (isset($content)) {
                    include $content;
                }
                ?>
            </div>
            <div class="limpiador"></div>
        </div>
    </div>
</div>
<div id="pie">
    <?php include("componentes/pie.php");?>
</div>
</body>
</html>