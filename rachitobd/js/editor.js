let completionProviderDisposable;

/**
 * Inicializa el editor Monaco.
 * @returns {Promise<object>} - Una promesa que se resuelve con la instancia del editor.
 */
export function initializeEditor() {
    return new Promise((resolve) => {
        require(['vs/editor/editor.main'], () => {
            const editorInstance = monaco.editor.create(document.getElementById('editorContainer'), {
                value: "",
                language: 'sql',
                theme: 'vs-dark',
                automaticLayout: true,
                minimap: { enabled: false },
                wordWrap: 'on'
            });
            resolve(editorInstance);
        });
    });
}

/**
 * Registra el proveedor de autocompletado para Monaco.
 * @param {object} monacoEditorInstance - La instancia del editor.
 * @param {object} database - La instancia de la clase Database del modelo.
 */
// <-- CAMBIO: La función ahora acepta los parámetros que necesita
export function registerMonacoCompletionProvider(monacoEditorInstance, database) {
    if (typeof monaco === 'undefined' || !monacoEditorInstance) return;
    if (completionProviderDisposable) completionProviderDisposable.dispose();

    completionProviderDisposable = monaco.languages.registerCompletionItemProvider('sql', {
        triggerCharacters: [' ', '.', '\t', '\n'],
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
    if (database) {
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
        const lastWordMatch = textUntilPosition.match(/([a-zA-Z0-9_]+)\.$/);

        // --- NEW LOGIC FOR TABLE SUGGESTIONS AFTER FROM/JOIN ---
        // Check if the current context is right after "FROM " or "JOIN "
        const textBeforeCursor = textUntilPosition.slice(0, position.column - 1);
        const isAfterFromOrJoin = /(?:FROM|JOIN)\s+([a-zA-Z0-9_."\[\]]*)$/i.test(textBeforeCursor);

        // Get the current word being typed
        const currentWord = word.word.toLowerCase();
        

if (lastWordMatch) { // Si el usuario escribe "tabla." o "alias."
                    const prefix = lastWordMatch[1].toLowerCase();
                    // <-- CAMBIO: Lógica para buscar la tabla en el modelo
                    const aliasTableName = aliases[prefix];
                    const tableOrView = database.getTableOrViewByName(aliasTableName || prefix);

                    if (tableOrView) {
                        tableOrView.columns.forEach(col => {
                            suggestions.push({
                                label: col.name, // <-- CAMBIO: usar col.name
                                kind: monaco.languages.CompletionItemKind.Field,
                                insertText: col.name,
                                range: range,
                                detail: `${tableOrView.name} Column`,
                                sortText: 'c_' + col.name
                            });
                        });
                    }
                } else { // Sugerir nombres de tablas y vistas
                    const currentWord = word.word.toLowerCase();
                    
                    database.tables.forEach((table, tableName) => {
                        if (tableName.toLowerCase().startsWith(currentWord)) {
                            suggestions.push({ label: tableName, kind: monaco.languages.CompletionItemKind.Struct, insertText: tableName, range: range, detail: 'Table' });
                        }
                    });
                    database.views.forEach((view, viewName) => {
                        if (viewName.toLowerCase().startsWith(currentWord)) {
                            suggestions.push({ label: viewName, kind: monaco.languages.CompletionItemKind.Struct, insertText: viewName, range: range, detail: 'View' });
                        }
                    });
                }
            }
            return { suggestions: suggestions };
        }
    });
}