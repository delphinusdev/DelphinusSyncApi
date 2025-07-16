<?php
// get_schema.php
session_start();
require_once 'db_connection.php'; // Incluir el nuevo archivo de conexión

header('Content-Type: application/json');

if (!isset($_SESSION['db_connection']) || empty($_SESSION['db_connection'])) {
    echo json_encode(['success' => false, 'message' => 'No hay una sesión de base de datos activa. Por favor, inicie sesión.']);
    exit;
}

$serverName = $_SESSION['db_connection']['serverName'];
$database = $_SESSION['db_connection']['database'];
$uid = $_SESSION['db_connection']['uid'];
$pwd = $_SESSION['db_connection']['pwd'];
$trustServerCertificate = $_SESSION['db_connection']['trustServerCertificate'] ?? false; // Recuperar de la sesión

// Estructura de esquema categorizada
$schema = [
    'Tables' => [],
    'Views' => [],
    'Procedures' => [],
    'Functions' => []
];

$pdo = null; // Inicializar PDO a null
try {
    // Usar la función getPdoConnection para establecer la conexión
    $pdo = getPdoConnection($serverName, $database, $uid, $pwd, $trustServerCertificate);

    // Consulta mejorada para obtener tablas, vistas, columnas, procedimientos y funciones
    // y filtrar mejor los objetos del sistema.
    $query = "
    -- 1. Obtener Tablas y sus Columnas
    SELECT
        t.TABLE_SCHEMA,
        t.TABLE_NAME,
        c.COLUMN_NAME,
        'Tables' AS ObjectType
    FROM INFORMATION_SCHEMA.TABLES AS t
    LEFT JOIN INFORMATION_SCHEMA.COLUMNS AS c ON t.TABLE_CATALOG = c.TABLE_CATALOG
        AND t.TABLE_SCHEMA = c.TABLE_SCHEMA
        AND t.TABLE_NAME = c.TABLE_NAME
    WHERE t.TABLE_TYPE = 'BASE TABLE'
       AND t.TABLE_SCHEMA NOT IN ('sys', 'INFORMATION_SCHEMA', 'guest') -- Filtro de esquemas del sistema

    UNION ALL

    -- 2. Obtener Vistas y sus Columnas
    SELECT
        v.TABLE_SCHEMA,
        v.TABLE_NAME,
        c.COLUMN_NAME,
        'Views' AS ObjectType
    FROM INFORMATION_SCHEMA.VIEWS AS v
    LEFT JOIN INFORMATION_SCHEMA.COLUMNS AS c ON v.TABLE_CATALOG = c.TABLE_CATALOG
        AND v.TABLE_SCHEMA = c.TABLE_SCHEMA
        AND v.TABLE_NAME = c.TABLE_NAME
    WHERE v.TABLE_SCHEMA NOT IN ('sys', 'INFORMATION_SCHEMA', 'guest') -- Filtro de esquemas del sistema

    UNION ALL

    -- 3. Obtener Procedimientos Almacenados
    SELECT
        ROUTINE_SCHEMA,
        ROUTINE_NAME,
        NULL AS COLUMN_NAME, -- Los procedimientos no tienen columnas en esta vista
        'Procedures' AS ObjectType
    FROM INFORMATION_SCHEMA.ROUTINES
    WHERE ROUTINE_TYPE = 'PROCEDURE'
       AND ROUTINE_SCHEMA NOT IN ('sys', 'INFORMATION_SCHEMA', 'guest') -- Filtro de esquemas del sistema

    UNION ALL

    -- 4. Obtener Funciones
    SELECT
        ROUTINE_SCHEMA,
        ROUTINE_NAME,
        NULL AS COLUMN_NAME, -- Las funciones no tienen columnas en esta vista
        'Functions' AS ObjectType
    FROM INFORMATION_SCHEMA.ROUTINES
    WHERE ROUTINE_TYPE = 'FUNCTION'
       AND ROUTINE_SCHEMA NOT IN ('sys', 'INFORMATION_SCHEMA', 'guest') -- Filtro de esquemas del sistema

    ORDER BY ObjectType, TABLE_NAME, COLUMN_NAME;
    ";

    $stmt = $pdo->query($query);

    while ($row = $stmt->fetch()) {
        $objectType = $row['ObjectType']; // 'Tables', 'Views', 'Procedures', 'Functions'
        $objectName = $row['TABLE_NAME'] ?? $row['ROUTINE_NAME']; // Nombre del objeto
        $columnName = $row['COLUMN_NAME'];

        // Si es la primera vez que vemos este objeto, lo inicializamos
        if (!isset($schema[$objectType][$objectName])) {
            $schema[$objectType][$objectName] = ['columns' => []];
        }

        // Si tiene una columna, la agregamos
        if ($columnName) {
            $schema[$objectType][$objectName]['columns'][] = $columnName;
        }
    }

    echo json_encode(['success' => true, 'schema' => $schema]);

} catch (Exception $e) { // Capturar tanto PDOException como la excepción de la extensión no cargada
    error_log("Error en get_schema.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener el esquema: ' . $e->getMessage()]);
    exit;
} finally {
    $pdo = null; // Cerrar la conexión PDO
}
?>