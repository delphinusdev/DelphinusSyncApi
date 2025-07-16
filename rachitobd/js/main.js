// js/main.js

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

function setupPanelResizing(monacoEditorInstance) {
    $("#schema-sidebar").resizable({
        handles: "e",
        minWidth: 150,
        maxWidth: $(window).width() / 2,
        stop: function() {
            if (window.monacoEditor) {
                window.monacoEditor.layout();
            }
            if ($('#results-section').hasClass('expanded') && window.tabulatorInstance) {
                window.tabulatorInstance.redraw(true);
            }
        }
    });

    $("#editor-area").resizable({
        handles: "s",
        minHeight: 100,
        stop: function() {
            if (window.monacoEditor) {
                window.monacoEditor.layout();
            }
            if ($('#results-section').hasClass('expanded') && window.tabulatorInstance) {
                window.tabulatorInstance.redraw(true);
            }
        }
    });

    console.log('Resizable configurado solo en evento stop para máximo rendimiento.');
}

// function setupPanelResizing(monacoEditorInstance) {
//     // Resizable para la barra lateral (esto está bien como estaba)
//     $("#schema-sidebar").resizable({
//         handles: "e",
//         minWidth: 150,
//         maxWidth: $(window).width() / 2,
//         stop: function(event, ui) {
//             // Es bueno llamar a layout() cuando se termina de redimensionar
//             if (window.monacoEditor) {
//                 window.monacoEditor.layout();
//             }
//         }
//     });

//     // Resizable para el editor (AHORA MUCHO MÁS SIMPLE Y EFICIENTE)
//     $("#editor-area").resizable({
//         handles: "s",
//         minHeight: 100, // Mantenemos una altura mínima para el editor
        
//         // ¡ELIMINADO! Ya no necesitamos calcular maxHeight.
//         // Flexbox en el CSS se encarga de esto de forma nativa y fluida.
        
//         stop: function(event, ui) {
//             // El código en 'stop' sigue siendo importante. Se ejecuta solo
//             // una vez al soltar el ratón.
//             if (window.monacoEditor) {
//                 window.monacoEditor.layout();
//             }
//             if ($('#results-section').hasClass('expanded') && window.tabulatorInstance) {
//                 window.tabulatorInstance.redraw();
//             }
//         }
//     });

//     console.log('setupPanelResizing: Funcionalidad resizable simplificada y eficiente inicializada.');
// }


// function setupPanelResizing(monacoEditorInstance) {
//     // ESTE ES EL CONTENIDO DE LA CONFIGURACIÓN DE RESIZABLE
//     $("#schema-sidebar").resizable({
//         handles: "e",
//         minWidth: 150,
//         maxWidth: $(window).width() / 2,
//         stop: function(event, ui) {
//             if (window.monacoEditor) {
//                 window.monacoEditor.layout();
//                 console.log('Resizable: Monaco Editor layout ajustado por sidebar resize.');
//             }
//         }
//     });

//     $("#editor-area").resizable({
//         handles: "s",
//         minHeight: 100,
//         maxHeight: function() {
//             const workPanelHeight = $(this).parent().height();
//             const statusMessageHeight = $("#statusMessage").outerHeight(true) || 0;
//             const exportButtonsHeight = $("#exportButtonsContainer").outerHeight(true) || 0;
//             const resultsSectionMinContentHeight = 100;
//             const resultsSectionPadding = 20;
//             const totalResultsMinRequiredSpace = resultsSectionMinContentHeight + resultsSectionPadding + statusMessageHeight + exportButtonsHeight;
//             const availableHeight = workPanelHeight - totalResultsMinRequiredSpace;
            
//             console.log(`[Resizable MaxHeight] workPanelHeight: ${workPanelHeight}, statusMessageHeight: ${statusMessageHeight}, exportButtonsHeight: ${exportButtonsHeight}, totalResultsMinRequiredSpace: ${totalResultsMinRequiredSpace}, calculated maxHeight for editor: ${availableHeight}`);
//             return availableHeight;
//         },
//         stop: function(event, ui) {
//             if (window.monacoEditor) {
//                 window.monacoEditor.layout();
//                 console.log('Resizable: Monaco Editor layout ajustado por editor-area resize.');
//             }
//             if ($('#results-section').hasClass('expanded') && window.tabulatorInstance) {
//                 window.tabulatorInstance.redraw();
//                 console.log('Resizable: Tabulator redibujado por editor-area resize.');
//             }
//         }
//     });
//     console.log('setupPanelResizing: Funcionalidad resizable inicializada.');
// }


/**
 * Toma los datos crudos del API y los usa para poblar nuestro modelo de datos.
 * Esta función asume que la base de datos ya ha sido eliminada del schemaModel si se trata de un refresh.
 * @param {string} dbName - El nombre de la base de datos a poblar.
 * @param {object} rawSchemaData - El objeto JSON crudo del API get_schema.php.
 */




function populateModelFromRawData(dbName, rawSchemaData) {
    console.log('populateModelFromRawData: Iniciando población del modelo para DB:', dbName, 'con data:', rawSchemaData);
    const db = new Database(dbName);

    for (const objectType in rawSchemaData) {
        const objects = rawSchemaData[objectType];
        if (objects && typeof objects === 'object') { // Asegurarse de que 'objects' es un objeto iterable
            for (const objectName in objects) {
                if (objects.hasOwnProperty(objectName)) { // Solo procesar propiedades propias
                    if (objectType === 'Tables' || objectType === 'Views') {
                        const schemaObject = new SchemaObject(objectName);
                        const columns = objects[objectName].columns || [];
                        columns.forEach(colName => {
                            schemaObject.addColumn(new Column(colName));
                        });
                        db.addObject(objectType, schemaObject);
                    } else if (objectType === 'Procedures' || objectType === 'Functions') {
                        const routableObject = new RoutableObject(objectName);
                        db.addObject(objectType, routableObject);
                    }
                }
            }
        } else {
            console.warn(`populateModelFromRawData: Tipo de objeto inesperado para ${objectType}:`, objects);
        }
    }
    schemaModel.addDatabase(db);
    console.log('populateModelFromRawData: Modelo poblado. Base de datos añadida al schemaModel:', db);
}

async function handleQueryExecution() {
    const query = monacoEditorInstance.getValue();
    if (!query.trim()) {
        ui.showStatusMessage("El editor está vacío. Ingresa una consulta.", "info");
        return;
    }

    ui.showStatusMessage("Ejecutando consulta...", "info");
    document.getElementById('results-section').classList.remove('expanded'); // Ocultar resultados previos
    document.getElementById('exportButtonsContainer').style.display = 'none'; // Ocultar botones de exportación
    if (window.tabulatorInstance) {
        window.tabulatorInstance.destroy(); // Destruir instancia anterior de Tabulator
    }


    try {
        const result = await api.runQuery(query);
        if (result.success) {
            if (result.rows && result.columns) {
                table.createResultsTable(result);
                document.getElementById('results-section').classList.add('expanded'); // <--- AÑADIDO: Mostrar la sección de resultados
                
                // Ajustar la altura del editor para acomodar los resultados
                const workPanelHeight = document.getElementById('work-panel').clientHeight;
                const resultsSectionHeight = document.getElementById('results-section').clientHeight;
                // Asegúrate de que resultsSectionHeight sea un valor válido y no 0 si la sección está oculta
                // Si la sección de resultados está oculta al principio, su clientHeight puede ser 0.
                // Es mejor confiar en que el CSS la expandirá y luego el resizable ajustará.
                // Por ahora, solo asegurémonos de que el editor se redibuje.
                if (window.monacoEditor) {
                    window.monacoEditor.layout();
                }
                
                ui.showStatusMessage("Consulta ejecutada con éxito.", "success");
            } else if (result.message) {
                ui.showStatusMessage(result.message, "info"); // Mensajes de éxito sin resultados (ej: UPDATE, INSERT)
            } else {
                ui.showStatusMessage("Consulta ejecutada, pero sin datos de resultados.", "info");
            }
        } else {
            ui.showStatusMessage(result.message || "Error desconocido al ejecutar la consulta.", "error");
            document.getElementById('results-section').classList.remove('expanded'); // Esconde la sección si hay un error
        }
    } catch (error) {
        console.error("Error en la ejecución de la consulta:", error);
        ui.showStatusMessage(`Error en la ejecución de la consulta: ${error.message}`, "error");
        document.getElementById('results-section').classList.remove('expanded'); // Esconde la sección si hay un error
    }
}

async function loadSchemaAndUpdateUI() {
    const dbName = schemaModel.currentDatabaseName;
    console.log('loadSchemaAndUpdateUI: Intentando cargar esquema para:', dbName);
    if (!dbName) {
        ui.showStatusMessage("No hay base de datos seleccionada para cargar el esquema.", "info");
        console.warn('loadSchemaAndUpdateUI: No se pudo cargar el esquema porque no hay base de datos seleccionada.');
        return;
    }

    ui.showStatusMessage(`Cargando esquema para ${dbName}...`, "info");
    try {
        const result = await api.fetchSchema();
        console.log('loadSchemaAndUpdateUI: Resultado de api.fetchSchema():', result);
        if (result.success && result.schema) {
            schemaModel.removeDatabase(dbName); // Limpiar antes de poblar
            populateModelFromRawData(dbName, result.schema);
            const currentDb = schemaModel.getCurrentDatabase();
            if (currentDb) {
                sidebar.populateSchemaSidebar(currentDb);
                editor.registerMonacoCompletionProvider(monacoEditorInstance, currentDb);
                ui.showStatusMessage(`Esquema de '${dbName}' cargado con éxito.`, "success");
                console.log('loadSchemaAndUpdateUI: Esquema cargado y UI actualizada para:', dbName);
            } else {
                ui.showStatusMessage(`Esquema de '${dbName}' no se pudo cargar correctamente.`, "error");
                console.error('loadSchemaAndUpdateUI: currentDb es null después de populateModelFromRawData.');
            }
        } else {
            ui.showStatusMessage(result.message || 'Error al cargar el esquema: Datos no válidos.', 'error');
            console.error('loadSchemaAndUpdateUI: Error en la respuesta del API o datos de esquema faltantes:', result);
        }
    } catch (error) {
        ui.showStatusMessage(`Error cargando esquema: ${error.message}`, 'error');
        console.error('loadSchemaAndUpdateUI: Excepción al cargar esquema:', error);
    }
}

async function handleSwitchDatabase(event) {
    const newDbName = event.target.value;
    ui.showStatusMessage(`Cambiando a la base de datos: ${newDbName}...`, "info");
    console.log('handleSwitchDatabase: Intentando cambiar a DB:', newDbName);
    try {
        const result = await api.setCurrentDatabase(newDbName);
        console.log('handleSwitchDatabase: Resultado de api.setCurrentDatabase():', result);
        if (result.success) {
            schemaModel.setCurrentDatabase(newDbName);
            document.getElementById('currentDatabaseDisplay').textContent = `Base de datos: ${newDbName}`;
            await loadSchemaAndUpdateUI();
        } else {
            ui.showStatusMessage(result.message || 'Error al cambiar de base de datos.', 'error');
            // Revertir la selección si hubo un error
            document.getElementById('databaseSelector').value = schemaModel.currentDatabaseName;
            console.error('handleSwitchDatabase: Error al cambiar DB en el servidor:', result);
        }
    } catch (error) {
        ui.showStatusMessage(`Error al cambiar de base de datos: ${error.message}`, 'error');
        // Revertir la selección si hubo un error
        document.getElementById('databaseSelector').value = schemaModel.currentDatabaseName;
        console.error('handleSwitchDatabase: Excepción al cambiar DB:', error);
    }
}

async function init() {
    monacoEditorInstance = await editor.initializeEditor();
    // 1. Exponer monacoEditorInstance globalmente
    window.monacoEditor = monacoEditorInstance;
    console.log('init: Monaco Editor inicializado y expuesto globalmente.');

    document.getElementById('executeButton').addEventListener('click', handleQueryExecution);
    document.getElementById('openFileButton').addEventListener('click', () => ui.openSqlFile(monacoEditorInstance));
    document.getElementById('refreshSchemaButton').addEventListener('click', async () => {
        ui.showStatusMessage("Refrescando esquema...", "info");
        // Forzar un refresh del esquema
        await loadSchemaAndUpdateUI(); 
    });
    document.getElementById('logoutButton').addEventListener('click', ui.logout);
    document.getElementById('databaseSelector').addEventListener('change', handleSwitchDatabase);

    sidebar.setupSidebarInteractions(monacoEditorInstance); // Asegúrate de que esta función usa window.monacoEditor para drag & drop
    sidebar.setupSchemaSearch();
    console.log('init: Listeners y sidebar configurados.');

    // 2. ¡EL CAMBIO CLAVE AQUÍ!
    // ELIMINA TODO EL BLOQUE DE CÓDIGO $("#schema-sidebar").resizable({...}) y $("#editor-area").resizable({...})
    // Y REEMPLAZALO CON LA LLAMADA A TU FUNCIÓN:
    $(function() { // Esto asegura que el DOM esté listo
        console.log('init: Llamando a setupPanelResizing.');
        setupPanelResizing(monacoEditorInstance); // <--- ¡AQUÍ ESTÁ EL CAMBIO!
    });


    const dbSelector = document.getElementById('databaseSelector');
    console.log('init: Cargando bases de datos iniciales.');
    try {
        const result = await api.fetchDatabases();
        console.log('init: Resultado de api.fetchDatabases():', result);
        if (result.success && result.databases && result.databases.length > 0) {
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
            
            await loadSchemaAndUpdateUI(); // <--- Aquí se llama por primera vez para cargar el esquema
            
        } else {
            ui.showStatusMessage(result.message || 'No se encontraron bases de datos.', 'error');
            console.error('init: No se encontraron bases de datos o la respuesta no fue exitosa.');
        }
    } catch (error) {
        ui.showStatusMessage(`Error cargando bases de datos: ${error.message}`, 'error');
        console.error('init: Excepción al cargar bases de datos:', error);
    }
}

// Lógica para alternar la visibilidad de la sección de resultados
document.getElementById('toggleResultsButton').addEventListener('click', function() {
    const resultsSection = document.getElementById('results-section');
    resultsSection.classList.toggle('expanded');
    // Re-layout editor if results panel state changes
    if (window.monacoEditor) {
        window.monacoEditor.layout();
    }
    // Re-layout Tabulator if it's visible
    if (resultsSection.classList.contains('expanded') && window.tabulatorInstance) {
        window.tabulatorInstance.redraw();
    }
});


document.addEventListener('DOMContentLoaded', init);