<?php
$pageTitle = 'Ventas'; //
$content = './pages/ventas_content.php'; // Path to the actual content for the sales page

// Specific head content for ventas.php (jQuery UI for datepickers)
$headExtra = '
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script>
        $(function() {
            $( "#fecha1" ).datepicker();
            $( "#fecha2" ).datepicker();
        });
    </script>
'; //

include("./componentes/template.php");
?>