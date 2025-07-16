<?php
// debug_config.php
// Este archivo debe ser incluido en otros scripts PHP.
// No debe haber nada antes de la etiqueta <?php para evitar problemas de salida.

// --- Configuración de Reporte de Errores para Depuración ---
// ¡IMPORTANTE! Desactiva o elimina estas líneas en un entorno de producción
// para evitar exponer información sensible a los usuarios.

ini_set('display_errors', 1);        // Muestra errores en la salida
ini_set('display_startup_errors', 1); // Muestra errores que ocurren durante el inicio de PHP
error_reporting(E_ALL);              // Reporta todos los tipos de errores

// Puedes añadir otras configuraciones PHP aquí si las necesitas globalmente,
// como por ejemplo la zona horaria (recomendado):
// date_default_timezone_set('America/Cancun'); // Ajusta a tu zona horaria
