<?php
$pageTitle = 'Administración'; //
$content = './pages/usuarios_content.php'; // Path to the actual content for the users page
$headExtra = '
<style>
.correo{
    text-decoration: none;
}
.correo:hover{
    text-decoration: underline;
}
#liston-izquierda {
    float: right !important;
    margin-top: -8px !important;
    padding-right: 52px !important;
    width: 240px !important;
}
</style>
'; //

include("./componentes/template.php");
?>