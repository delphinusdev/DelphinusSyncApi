
let dbSchema = {};
let dataTableInstance = null;
let monacoEditorInstance;
let completionProviderDisposable; // Para manejar el autocompletado
let currentLoadedDatabase = ''; // Variable para almacenar la base de datos actual

async function logout() {
    localStorage.clear(); // Clear all cached schemas
    try {
        const response = await fetch('logout.php');
        const result = await response.json();
        if (result.success) window.location.href = 'index.html';
    } catch (error) { console.error("Error al cerrar sesión:", error); }
}

function forceRefreshSchema() {
    if (currentLoadedDatabase) {
        localStorage.removeItem('dbSchema_' + currentLoadedDatabase); // Remove specific cached schema
    }
    document.getElementById('schemaStatus').textContent = 'Refrescando...';
    loadAndCacheSchema(); // Reload for the current database
}

async function loadDatabasesAndInitialSchema() {
    const databaseSelector = document.getElementById('databaseSelector');
    const currentDatabaseDisplay = document.getElementById('currentDatabaseDisplay');
    currentDatabaseDisplay.textContent = 'Cargando bases de datos...';
    try {
        const response = await fetch('get_databases.php');
        const result = await response.json();

        if (result.success && result.databases.length > 0) {
            databaseSelector.innerHTML = ''; // Clear previous options
            result.databases.forEach(dbName => {
                const option = document.createElement('option');
                option.value = dbName;
                option.textContent = dbName;
                databaseSelector.appendChild(option);
            });

            // Set the current database based on session or first available
            currentLoadedDatabase = result.currentDatabase || result.databases[0];
            databaseSelector.value = currentLoadedDatabase;
            currentDatabaseDisplay.textContent = `Base de datos: ${currentLoadedDatabase}`;
            
            // Load schema for the initial/current database
            loadAndCacheSchema();

        } else {
            currentDatabaseDisplay.textContent = 'No hay bases de datos disponibles.';
            showStatusMessage('No se pudieron cargar las bases de datos o no hay ninguna disponible.', 'error');
        }
    } catch (error) {
        currentDatabaseDisplay.textContent = 'Error al cargar bases de datos.';
        showStatusMessage(`Error al cargar bases de datos: ${error.message}`, 'error');
    }
}

async function switchDatabase() {
    const newDatabase = document.getElementById('databaseSelector').value;
    if (!newDatabase || newDatabase === currentLoadedDatabase) return;

    const currentDatabaseDisplay = document.getElementById('currentDatabaseDisplay');
    currentDatabaseDisplay.textContent = `Cambiando a: ${newDatabase}...`;
    document.getElementById('schemaStatus').textContent = 'Cargando esquema...';

    try {
        const response = await fetch('set_current_database.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ databaseName: newDatabase })
        });
        const result = await response.json();

        if (result.success) {
            currentLoadedDatabase = newDatabase;
            currentDatabaseDisplay.textContent = `Base de datos: ${currentLoadedDatabase}`;
            loadAndCacheSchema(); // Load schema for the newly selected database
        } else {
            showStatusMessage(`Error al cambiar de base de datos: ${result.message}`, 'error');
            currentDatabaseDisplay.textContent = `Error: ${result.message}`;
            // Revert selector if change failed
            document.getElementById('databaseSelector').value = currentLoadedDatabase; 
        }
    } catch (error) {
        showStatusMessage(`Error de conexión al cambiar de base de datos: ${error.message}`, 'error');
        currentDatabaseDisplay.textContent = `Error: ${error.message}`;
        // Revert selector if change failed
        document.getElementById('databaseSelector').value = currentLoadedDatabase;
    }
}

async function loadAndCacheSchema() {
    const schemaStatusSpan = document.getElementById('schemaStatus');
    const cachedSchemaKey = 'dbSchema_' + currentLoadedDatabase; // Cache per database
    const cachedSchema = localStorage.getItem(cachedSchemaKey);
    
    // Clear current schema tree before loading new one
    document.getElementById('schema-tree-container').innerHTML = '';
    dbSchema = {}; // Clear previous schema data

    if (cachedSchema) {
        try {
            dbSchema = JSON.parse(cachedSchema);
            schemaStatusSpan.textContent = `Esquema (${currentLoadedDatabase} - Caché)`;
            populateSchemaSidebar();
            registerMonacoCompletionProvider();
            return;
        } catch(e) { 
            console.warn("Error parsing cached schema, refreshing...", e);
            localStorage.removeItem(cachedSchemaKey); // Clear corrupted cache
        }
    }
    schemaStatusSpan.textContent = `Cargando esquema (${currentLoadedDatabase})...`;
    try {
        const response = await fetch('get_schema.php'); // get_schema.php uses session
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();

        if (Object.keys(data).length > 0 && data.success !== false) { // Check for success and actual data
            dbSchema = data;
            localStorage.setItem(cachedSchemaKey, JSON.stringify(dbSchema));
            schemaStatusSpan.textContent = `Esquema (${currentLoadedDatabase} - OK)`;
            populateSchemaSidebar();
            registerMonacoCompletionProvider();
        } else {
            dbSchema = {}; // Ensure schema is empty if no data
            schemaStatusSpan.textContent = `Esquema (${currentLoadedDatabase} - Vacío)`;
        }
    } catch (error) {
        schemaStatusSpan.textContent = `Error Esquema (${currentLoadedDatabase})`;
        showStatusMessage(`Error al cargar el esquema de ${currentLoadedDatabase}: ${error.message}`, 'error');
    }
}

// --- FUNCIÓN DE AUTOCOMPLETADO RESTAURADA ---
function registerMonacoCompletionProvider() {
if (typeof monaco === 'undefined' || !monacoEditorInstance) return;
if (completionProviderDisposable) completionProviderDisposable.dispose();

completionProviderDisposable = monaco.languages.registerCompletionItemProvider('sql', {
// Add trigger characters for common SQL syntax
triggerCharacters: [' ', '.', '\t', '\n'], // Trigger on space, dot, tab, newline

provideCompletionItems: (model, position) => {
    const suggestions = [];
    const textUntilPosition = model.getValueInRange({ startLineNumber: 1, startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column });
    const word = model.getWordUntilPosition(position);
    const range = { startLineNumber: position.lineNumber, endLineNumber: position.lineNumber, startColumn: word.startColumn, endColumn: word.endColumn };
    
    // Sugerencias de palabras clave de SQL
    const sqlKeywords = ["SELECT", "FROM", "WHERE", "GROUP BY", "ORDER BY", "INSERT INTO", "VALUES", "UPDATE", "SET", "DELETE FROM", "JOIN", "INNER JOIN", "LEFT JOIN", "RIGHT JOIN", "ON", "AS", "TOP", "COUNT", "SUM", "AVG", "MAX", "MIN", "AND", "OR", "NOT", "NULL", "TRUE", "FALSE", "LIKE", "IN", "BETWEEN", "IS"]; // Added more common keywords
    sqlKeywords.forEach(k => suggestions.push({
        label: k,
        kind: monaco.languages.CompletionItemKind.Keyword,
        insertText: k,
        range: range,
        // Add detail for better context
        detail: 'SQL Keyword',
        sortText: 'k_' + k // Ensure keywords appear at the top
    }));

    // Sugerencias de tablas y columnas del esquema
    if (dbSchema) {
        // Detectar alias (ej: FROM Customers AS c)
        const aliases = {};
        const aliasRegex = /(?:FROM|JOIN)\s+([a-zA-Z0-9_."\[\]]+)\s+(?:AS\s+)?([a-zA-Z0-9_]+)/gi;
        let match;
        // Important: Reset lastIndex for global regex in loops
        aliasRegex.lastIndex = 0; 
        while ((match = aliasRegex.exec(textUntilPosition)) !== null) {
            aliases[match[2].toLowerCase()] = match[1].replace(/[\[\]"]/g, '');
        }

        const lastChar = textUntilPosition.slice(-1);
        const lastWordMatch = textUntilPosition.match(/([a-zA-Z0-9_]+)\.$/); // For 'table.column'

        // --- NEW LOGIC FOR TABLE SUGGESTIONS AFTER FROM/JOIN ---
        // Check if the current context is right after "FROM " or "JOIN "
        const textBeforeCursor = textUntilPosition.slice(0, position.column - 1);
        const isAfterFromOrJoin = /(?:FROM|JOIN)\s+([a-zA-Z0-9_."\[\]]*)$/i.test(textBeforeCursor);

        // Get the current word being typed
        const currentWord = word.word.toLowerCase();

        if (lastWordMatch) { // If user types "alias." or "table."
            const prefix = lastWordMatch[1].toLowerCase();
            const tableName = aliases[prefix] || Object.keys(dbSchema.Tables || {}).find(tn => tn.toLowerCase() === prefix); // Ensure dbSchema.Tables exists
            if (tableName && dbSchema.Tables[tableName] && dbSchema.Tables[tableName].columns) { // Ensure columns exist
                dbSchema.Tables[tableName].columns.forEach(col => {
                    suggestions.push({
                        label: col,
                        kind: monaco.languages.CompletionItemKind.Field,
                        insertText: col,
                        range: range,
                        detail: `${tableName} Column`,
                        sortText: 'c_' + col // Sort columns after keywords
                    });
                });
            }
        } else if (isAfterFromOrJoin || currentWord.length > 0) { // Suggest table names if after FROM/JOIN or if typing
            if (dbSchema.Tables) { // Ensure dbSchema.Tables exists
                Object.keys(dbSchema.Tables).forEach(tableName => {
                    // Filter suggestions based on the current word being typed
                    if (tableName.toLowerCase().startsWith(currentWord)) {
                        suggestions.push({
                            label: tableName,
                            kind: monaco.languages.CompletionItemKind.Struct, // Use Struct for tables
                            insertText: tableName,
                            range: range,
                            detail: 'Database Table',
                            sortText: 'b_' + tableName // Sort tables after keywords
                        });
                    }
                });
            }
        }
    }
    return { suggestions: suggestions };
}
});
}


function populateSchemaSidebar() {
const container = document.getElementById('schema-tree-container');
container.innerHTML = ''; // Limpiar contenido anterior

const categoryNames = {
'Tables': { name: 'Tablas', icon: 'fa-table', color: 'var(--vscode-accent-blue)' },
'Views': { name: 'Vistas', icon: 'fa-eye', color: '#3d9a54' },
'Procedures': { name: 'Procedimientos', icon: 'fa-database', color: '#b58e3a' },
'Functions': { name: 'Funciones', icon: 'fa-cogs', color: '#c586c0' } // Cambiado a un icono más genérico
};

const tree = document.createElement('ul');
tree.className = 'tree-view';

for (const categoryKey in dbSchema) {
const categoryData = categoryNames[categoryKey] || { name: categoryKey, icon: 'fa-folder', color: 'var(--vscode-text-dark)' };
const objects = dbSchema[categoryKey];

if (Object.keys(objects).length === 0) continue; 

const categoryLi = document.createElement('li');
categoryLi.className = 'category-container';

const categoryHeader = document.createElement('div');
categoryHeader.className = 'table-item expanded'; 
// Empezamos con fa-chevron-down porque está expandido por defecto
categoryHeader.innerHTML = `<i class="fas fa-chevron-down"></i><i class="fas ${categoryData.icon}" style="color:${categoryData.color};"></i><span>${categoryData.name}</span>`;

const objectList = document.createElement('ul');
objectList.className = 'column-list show';

const sortedObjectNames = Object.keys(objects).sort();

sortedObjectNames.forEach(objectName => {
    const objectData = objects[objectName];
    const hasColumns = objectData.columns && objectData.columns.length > 0;

    const objectLi = document.createElement('li');
    objectLi.className = 'table-item-container';
    
    let itemHTML = `<div class="table-item" data-name="${objectName}">`;
    itemHTML += hasColumns ? `<i class="fas fa-chevron-right"></i>` : `<span style="width:14px; display:inline-block;"></span>`;
    itemHTML += `<i class="fas ${categoryKey === 'Tables' || categoryKey === 'Views' ? 'fa-table' : 'fa-gear'}" style="color:var(--vscode-text-dark);"></i>`;
    itemHTML += `<span>${objectName}</span></div>`;
    objectLi.innerHTML = itemHTML;

    if (hasColumns) {
        const columnList = document.createElement('ul');
        columnList.className = 'column-list';
        objectData.columns.sort().forEach(columnName => {
            const colLi = document.createElement('li');
            colLi.innerHTML = `<div class="column-item" data-name="${columnName}"><i class="fas fa-columns" style="color:var(--vscode-success-green);"></i><span>${columnName}</span></div>`;
            columnList.appendChild(colLi);
        });
        objectLi.appendChild(columnList);
    }
    objectList.appendChild(objectLi);
});

 categoryHeader.addEventListener('click', (e) => {
    e.stopPropagation();
    
    // 1. Buscar el icono UNA SOLA VEZ y guardarlo en una variable
    const icon = categoryHeader.querySelector('.fa-chevron-down, .fa-chevron-right');
    
    // 2. Comprobar que el icono existe antes de manipularlo
    if (icon) {
        // 3. Alternar ambas clases en la variable guardada
        icon.classList.toggle('fa-chevron-down');
        icon.classList.toggle('fa-chevron-right');
    }
    
    // 4. Mostrar u ocultar la lista de objetos
    objectList.classList.toggle('show');
});

categoryLi.appendChild(categoryHeader);
categoryLi.appendChild(objectList);
tree.appendChild(categoryLi);
}
container.appendChild(tree);
}

function setupSidebarInteractions() {
const sidebar = document.getElementById('schema-sidebar');
sidebar.addEventListener('click', e => {
const tableItem = e.target.closest('.table-item');
// Asegurarse de no interferir con el click del encabezado de categoría que tiene su propio listener
if (tableItem && !tableItem.closest('.category-container > .table-item')) {
    tableItem.classList.toggle('expanded');
    tableItem.nextElementSibling?.classList.toggle('show');
}
});
sidebar.addEventListener('dblclick', e => {
const item = e.target.closest('.table-item, .column-item');
if (item && monacoEditorInstance) {
    monacoEditorInstance.trigger('keyboard', 'type', { text: item.dataset.name });
    monacoEditorInstance.focus();
}
});
}

// --- NUEVA FUNCIÓN: BÚSQUEDA EN EL PANEL LATERAL ---
function setupSchemaSearch() {
const searchInput = document.getElementById('schema-search-input');
searchInput.addEventListener('input', function() {
const filter = this.value.toLowerCase();

// Iterar sobre cada contenedor de objeto (tablas, vistas, etc.)
const objectContainers = document.querySelectorAll('.table-item-container');

objectContainers.forEach(container => {
    const itemDiv = container.querySelector('.table-item');
    const itemName = itemDiv.dataset.name.toLowerCase();
    const columnItems = container.querySelectorAll('.column-item');
    
    let isItemVisible = itemName.includes(filter);
    let hasVisibleColumns = false;

    // Filtrar columnas
    columnItems.forEach(col => {
        const colName = col.dataset.name.toLowerCase();
        if (colName.includes(filter)) {
            col.style.display = 'flex';
            hasVisibleColumns = true;
        } else {
            col.style.display = 'none';
        }
    });

    // Mostrar el contenedor del objeto si el objeto mismo o alguna de sus columnas coincide
    if (isItemVisible || hasVisibleColumns) {
        container.style.display = 'block';
        // Si la búsqueda no está vacía y se encontraron columnas, expandir el padre
        if (filter && hasVisibleColumns) {
            itemDiv.classList.add('expanded');
            itemDiv.nextElementSibling?.classList.add('show');
        }
    } else {
        container.style.display = 'none';
    }
});

// Filtrar las categorías principales
const categoryContainers = document.querySelectorAll('.category-container');
categoryContainers.forEach(catContainer => {
    // Verificar si algún hijo de esta categoría está visible
    const visibleItems = catContainer.querySelectorAll('.table-item-container[style*="display: block"]');
    if (visibleItems.length > 0) {
        catContainer.style.display = 'block';
    } else {
        catContainer.style.display = 'none';
    }
});
});
}

function showStatusMessage(message, type = 'info', duration = 6000) {
    const statusDiv = document.getElementById('statusMessage');
    statusDiv.innerHTML = `<b>${message}</b>`;
    statusDiv.className = `status-message ${type} show`;
    setTimeout(() => statusDiv.classList.remove('show'), duration);
}

async function executeQuery() {
    const selection = monacoEditorInstance.getSelection();
    const query = monacoEditorInstance.getModel().getValueInRange(selection).trim() || monacoEditorInstance.getValue();
    if (!query.trim()) return;
    
    // Destruir instancia anterior de DataTable si existe
    if (dataTableInstance) {
        dataTableInstance.destroy();
        dataTableInstance = null; // Resetear la instancia
        // Remover el contenedor de botones y el buscador si ya se habían movido
        $('#dtButtonsContainer').remove();
        $('#dtFilterContainer').remove(); // Eliminar el contenedor del buscador
    }

    $('#resultsTable').empty();
    document.getElementById('resultsTableContainer').style.display = 'none';
    document.getElementById('executeButton').disabled = true;

    try {
        const response = await fetch('execute_query.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ query: query }) });
        const result = await response.json();
        if (result.success) {
    if (result.columns && result.rows) {
        document.getElementById('resultsTableContainer').style.display = 'block';
        
        // La altura del scroll ahora es manejada por el CSS, por lo que podemos usar 100%
        const scrollYValue = '100px'; // Un valor inicial, se ajustará por CSS.

        dataTableInstance = $('#resultsTable').DataTable({
            data: result.rows,
            columns: result.columns.map(col => ({ title: col })),
            
            // --- CONFIGURACIÓN DE SCROLL Y LAYOUT ---
            scrollX: true,
            scrollY: scrollYValue, // Esta propiedad es necesaria para que se generen los contenedores de scroll
            scrollCollapse: true, // Ayuda a que la tabla se ajuste si hay pocos datos
            paging: false, // El scroll infinito es mejor sin paginación
            fixedHeader: true, // Mantiene el header fijo
            
            // --- NUEVO DOM ESTRUCTURADO ---
            // 'B' - Buttons, 'f' - filter, 'r' - processing, 't' - table, 'i' - info
            dom: "<'results-header'Bf>"+
                 "<'dataTables_scroll'tr>"+
                 "<'results-footer'i>",

            buttons: [
                { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copiar', className: 'dt-button' },
                { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', className: 'dt-button' },
                { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'dt-button' }
            ],
            language: {
                "search": "", // Quitamos el texto "Buscar:"
                "searchPlaceholder": "Buscar en resultados...", // Añadimos un placeholder
                "info": "<b>_TOTAL_</b> registros",
                "infoEmpty": "No hay resultados",
                "zeroRecords": "No se encontraron registros que coincidan con la búsqueda"
            }
        });

        // Ya no es necesario mover los botones y el buscador manualmente.
        // El nuevo 'dom' y el CSS se encargarán de ello.

        showStatusMessage(`Éxito: ${result.rows.length} filas encontradas.`, 'success');
    } else {
                showStatusMessage(`Éxito: ${result.message || ''} ${result.rowsAffected !== undefined ? `${result.rowsAffected} filas afectadas.` : ''}`, 'success');
            }
        } else { showStatusMessage(`Error: ${result.message}`, 'error'); }
    } catch (error) { showStatusMessage(`Error de Conexión: ${error.message}`, 'error'); }
    finally { document.getElementById('executeButton').disabled = false; }
}



$(async function() {
    $("#querySectionResizable").resizable({ handles: 's', resize: () => monacoEditorInstance?.layout() });
    require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.49.0/min/vs' } });
    require(['vs/editor/editor.main'], async () => {
        monacoEditorInstance = monaco.editor.create(document.getElementById('editorContainer'), {
            value: "-- ¡Autocompletado y buscador listos!\n-- Escribe 'SELECT' o el alias de una tabla seguido de un punto.\n-- Utiliza el buscador de la izquierda para filtrar objetos y el selector de DB para cambiar la base de datos.",
            language: 'sql', theme: 'vs-dark', automaticLayout: true, minimap: { enabled: false }, wordWrap: 'on'
        });
        
        // Cargar bases de datos y el esquema inicial
        await loadDatabasesAndInitialSchema();
        setupSidebarInteractions();
        setupSchemaSearch(); // <-- Activar la nueva función de búsqueda

        // Event listener for database selection change
        document.getElementById('databaseSelector').addEventListener('change', switchDatabase);
    });
});