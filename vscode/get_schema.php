<?php
// get_schema.php
session_start(); 

header('Content-Type: application/json');

if (!isset($_SESSION['db_connection']) || empty($_SESSION['db_connection'])) {
    echo json_encode(['success' => false, 'message' => 'No hay una sesión de base de datos activa. Por favor, inicie sesión.']);
    exit;
}

$serverName = $_SESSION['db_connection']['serverName'];
$database   = $_SESSION['db_connection']['database'];
$uid        = $_SESSION['db_connection']['uid'];
$pwd        = $_SESSION['db_connection']['pwd'];

$connectionInfo = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pwd,
    "CharacterSet" => "UTF-8"
];

// Estructura de esquema categorizada
$schema = [
    'Tables' => [],
    'Views' => [],
    'Procedures' => [],
    'Functions' => []
];

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn) {
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
    LEFT JOIN INFORMATION_SCHEMA.COLUMNS AS c ON t.TABLE_CATALOG = c.TABLE_CATALOG AND t.TABLE_SCHEMA = c.TABLE_SCHEMA AND t.TABLE_NAME = c.TABLE_NAME
    WHERE t.TABLE_TYPE = 'BASE TABLE'
      AND t.TABLE_SCHEMA NOT IN ('sys', 'INFORMATION_SCHEMA', 'guest') -- Filtro de esquemas del sistema

    UNION ALL

    -- 2. Obtener Vistas y sus Columnas
    SELECT 
        t.TABLE_SCHEMA, 
        t.TABLE_NAME, 
        c.COLUMN_NAME, 
        'Views' AS ObjectType
    FROM INFORMATION_SCHEMA.TABLES AS t
    LEFT JOIN INFORMATION_SCHEMA.COLUMNS AS c ON t.TABLE_CATALOG = c.TABLE_CATALOG AND t.TABLE_SCHEMA = c.TABLE_SCHEMA AND t.TABLE_NAME = c.TABLE_NAME
    WHERE t.TABLE_TYPE = 'VIEW'
      AND t.TABLE_SCHEMA NOT IN ('sys', 'INFORMATION_SCHEMA', 'guest') -- Filtro de esquemas del sistema

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

    $params = []; // La consulta ya no necesita parámetros de DB
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $objectType = $row['ObjectType']; // 'Tables', 'Views', 'Procedures', 'Functions'
            $objectName = $row['TABLE_NAME']; // Nombre del objeto
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
    } else {
        // Manejar error de consulta si es necesario
    }
    sqlsrv_close($conn);
}

// Devolvemos el esquema completo como JSON
echo json_encode($schema);
?>
