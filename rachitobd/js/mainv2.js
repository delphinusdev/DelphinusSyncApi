import * as api from './api.js';
import * as ui from './ui.js';
import * as sidebar from './sidebar.js';
import * as editor from './editor.js';
import * as table from './table.js';
import { SchemaModel, Database, SchemaObject, Column, RoutableObject } from './model.js';

// --- Estado de la Aplicación ---
let monacoEditorInstance;
const schemaModel = new SchemaModel();

// --- Lógica Principal de la Aplicación ---

/**
 * Toma los datos crudos del API y los usa para poblar nuestro modelo de datos.
 * Esta función asume que la base de datos ya ha sido eliminada del schemaModel si se trata de un refresh.
 * @param {string} dbName - El nombre de la base de datos a poblar.
 * @param {object} rawSchemaData - El objeto JSON crudo del API get_schema.php.
 */
function populateModelFromRawData(dbName, rawSchemaData) {
    console.log('populateModelFromRawData: Populating model for DB:', dbName, 'with data:', rawSchemaData);
    const db = new Database(dbName);

    for (const objectType in rawSchemaData) {
        const objects = rawSchemaData[objectType];
        for (const objectName in objects) {
            
            if (objectType === 'Tables' || objectType === 'Views') {
                const schemaObject = new SchemaObject(objectName);
                const columns = objects[objectName].columns || [];
                columns.forEach(colName => {
                    schemaObject.addColumn(new Column(colName));
                });
                db.addObject(objectType, schemaObject);

            } else if (objectType === 'Procedures' || objectType === 'Functions') {
                db.addObject(objectType, new RoutableObject(objectName));
            }
        }
    }
    // Añade la base de datos al modelo. Si ya existe y no fue borrada, no se sobrescribe.
    // Pero como loadSchemaAndUpdateUI ahora borra antes, esto siempre añadirá la nueva data.
    schemaModel.addDatabase(db); 
    console.log('populateModelFromRawData: Database instance added to schemaModel:', db);
}

/**
 * Carga el esquema de la base de datos actual desde el servidor
 * y actualiza la UI (sidebar y autocompletado).
 * Esta función ahora siempre fuerza una recarga del esquema desde el servidor.
 */
async function loadSchemaAndUpdateUI() {
    ui.showStatusMessage('Cargando esquema...', 'info', 0);

    const currentDbName = schemaModel.currentDatabaseName;
    console.log('loadSchemaAndUpdateUI: currentDbName =', currentDbName);

    if (!currentDbName) {
        ui.showStatusMessage('No se ha seleccionado una base de datos.', 'error');
        sidebar.populateSchemaSidebar(null); // Limpiar la sidebar
        editor.registerMonacoCompletionProvider(monacoEditorInstance, null); // Desactivar autocompletado
        return;
    }

    // IMPLICIT FIX: Siempre eliminar la entrada existente para la base de datos actual del modelo.
    // Esto asegura que populateModelFromRawData añadirá la información fresca obtenida del servidor.
    if (schemaModel.databases.has(currentDbName)) {
        schemaModel.databases.delete(currentDbName);
        console.log(`loadSchemaAndUpdateUI: Deleted old schema for ${currentDbName} to prepare for fresh load.`);
    }

    try {
        const result = await api.fetchSchema(); // Siempre intenta obtener el esquema más reciente del servidor
        console.log('loadSchemaAndUpdateUI: fetchSchema result =', result);

        if (result.success) {
            populateModelFromRawData(currentDbName, result.schema);
            const currentDatabaseInstance = schemaModel.getDatabase(currentDbName); // Obtener la instancia recién populada
            console.log('loadSchemaAndUpdateUI: currentDatabaseInstance (after populate) =', currentDatabaseInstance);

            if (currentDatabaseInstance) {
                console.log('Passing database instance to sidebar and editor:', currentDatabaseInstance);
                sidebar.populateSchemaSidebar(currentDatabaseInstance);
                editor.registerMonacoCompletionProvider(monacoEditorInstance, currentDatabaseInstance);
                ui.showStatusMessage(`Esquema de '${currentDbName}' cargado.`, 'success');
            } else {
                ui.showStatusMessage('Error interno: No se pudo obtener la instancia de la base de datos después de cargar el esquema.', 'error');
                console.error('currentDatabaseInstance is null after populateModelFromRawData.');
                sidebar.populateSchemaSidebar(null); // Limpiar sidebar en caso de error
                editor.registerMonacoCompletionProvider(monacoEditorInstance, null); // Desactivar autocompletado
            }
        } else {
            ui.showStatusMessage(`Error al cargar el esquema: ${result.message}`, 'error');
            console.error('Error fetching schema:', result.message);
            sidebar.populateSchemaSidebar(null); // Limpiar sidebar en caso de error
            editor.registerMonacoCompletionProvider(monacoEditorInstance, null); // Desactivar autocompletado
        }
    } catch (error) {
        console.error('Error de red al cargar el esquema:', error);
        ui.showStatusMessage(`Error de red al cargar el esquema: ${error.message}`, 'error');
        sidebar.populateSchemaSidebar(null); // Limpiar sidebar en caso de error
        editor.registerMonacoCompletionProvider(monacoEditorInstance, null); // Desactivar autocompletado
    }
}

/**
 * Maneja la ejecución de una consulta SQL.
 */
async function handleExecuteQuery() {
    const query = monacoEditorInstance.getValue();
    if (!query) {
        ui.showStatusMessage('Por favor, ingresa una consulta SQL para ejecutar.', 'info');
        return;
    }

    ui.showStatusMessage('Ejecutando consulta...', 'info', 0);
    try {
        const result = await api.runQuery(query);
        if (result.success) {
            if (result.columns && result.rows) {
                table.createResultsTable(result);
                ui.showStatusMessage(`Consulta ejecutada. ${result.rows.length} filas obtenidas.`, 'success');
            } else {
                table.createResultsTable({ columns: ['Mensaje', 'Filas Afectadas'], rows: [[result.message, result.rowsAffected || 'N/A']] });
                ui.showStatusMessage(result.message, 'success');
            }
        } else {
            ui.showStatusMessage(`Error en la consulta: ${result.message}`, 'error');
            document.getElementById('resultsTableContainer').style.display = 'none';
        }
    } catch (error) {
        ui.showStatusMessage(`Error de red o inesperado: ${error.message}`, 'error');
        console.error('Error executing query:', error);
        document.getElementById('resultsTableContainer').style.display = 'none';
    }
}

/**
 * Maneja el cambio de base de datos en el selector.
 */
async function handleSwitchDatabase() {
    const dbSelector = document.getElementById('databaseSelector');
    const selectedDbName = dbSelector.value;
    if (selectedDbName) {
        ui.showStatusMessage(`Cambiando a la base de datos: ${selectedDbName}...`, 'info');
        try {
            const result = await api.setCurrentDatabase(selectedDbName);
            if (result.success) {
                schemaModel.setCurrentDatabase(selectedDbName);
                document.getElementById('currentDatabaseDisplay').textContent = `Base de datos: ${selectedDbName}`;
                await loadSchemaAndUpdateUI(); // loadSchemaAndUpdateUI ahora se encarga de borrar/refrescar el esquema
                ui.showStatusMessage(`Base de datos cambiada a '${selectedDbName}'.`, 'success');
            } else {
                ui.showStatusMessage(`Error al cambiar la base de datos: ${result.message}`, 'error');
                dbSelector.value = schemaModel.currentDatabaseName || '';
            }
        } catch (error) {
            ui.showStatusMessage(`Error de red al cambiar la base de datos: ${error.message}`, 'error');
        }
    }
}

// --- Inicialización de la Aplicación ---
async function init() {
    monacoEditorInstance = await editor.initializeEditor();
    
    // Configurar interacciones de la UI
    document.getElementById('executeButton').addEventListener('click', handleExecuteQuery);
    document.getElementById('openFileButton').addEventListener('click', () => ui.openSqlFile(monacoEditorInstance));
    document.getElementById('refreshSchemaButton').addEventListener('click', async () => {
        // loadSchemaAndUpdateUI ahora se encarga de borrar/refrescar el esquema
        await loadSchemaAndUpdateUI(); 
    });
    document.getElementById('logoutButton').addEventListener('click', ui.logout);
    document.getElementById('databaseSelector').addEventListener('change', handleSwitchDatabase);

    sidebar.setupSidebarInteractions(monacoEditorInstance);
    sidebar.setupSchemaSearch();

    const dbSelector = document.getElementById('databaseSelector');
    try {
        const result = await api.fetchDatabases();
        if (result.success && result.databases.length > 0) {
            dbSelector.innerHTML = '';
            result.databases.forEach(dbName => {
                const option = document.createElement('option');
                option.value = dbName;
                option.textContent = dbName;
                dbSelector.appendChild(option);
            });
            const currentDbName = result.currentDatabase || result.databases[0];
            schemaModel.setCurrentDatabase(currentDbName);
            dbSelector.value = currentDbName;
            document.getElementById('currentDatabaseDisplay').textContent = `Base de datos: ${currentDbName}`;
            
            await loadSchemaAndUpdateUI();
            
        } else {
            ui.showStatusMessage(result.message || 'No se encontraron bases de datos.', 'error');
        }
    } catch (error) {
        ui.showStatusMessage(`Error cargando bases de datos: ${error.message}`, 'error');
    }
}

document.addEventListener('DOMContentLoaded', init);