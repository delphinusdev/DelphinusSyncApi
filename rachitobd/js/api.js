// js/api.js

/**
 * Ejecuta una consulta en el servidor.
 * @param {string} query - La consulta SQL a ejecutar.
 * @returns {Promise<object>} - La respuesta del servidor.
 */
const dir = './backend';

export async function runQuery(query) {
    const response = await fetch(`${dir}/execute_query.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ query: query })
    });
    return response.json();
}

/**
 * Obtiene la lista de bases de datos disponibles.
 * @returns {Promise<object>} - La respuesta del servidor.
 */
export async function fetchDatabases() {
    const response = await fetch(`${dir}/get_databases.php`);
    return response.json();
}

/**
 * Obtiene el esquema de la base de datos actual.
 * @returns {Promise<object>} - La respuesta del servidor.
 */
export async function fetchSchema() {
    const response = await fetch(`${dir}/get_schema.php`);
    return response.json();
}

/**
 * Establece la base de datos actual en la sesión del servidor.
 * @param {string} databaseName - El nombre de la nueva base de datos.
 * @returns {Promise<object>} - La respuesta del servidor.
 */
export async function setCurrentDatabase(databaseName) {
    const response = await fetch(`${dir}/set_current_database.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ databaseName })
    });
    return response.json();
}

/**
 * Cierra la sesión en el servidor.
 * @returns {Promise<object>}
 */
export async function logoutUser() {
    const response = await fetch(`${dir}/logout.php`);
    return response.json();
}