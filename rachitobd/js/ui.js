// js/ui.js
import { logoutUser } from './api.js';

/**
 * Muestra un mensaje de estado en la interfaz.
 * @param {string} message - El mensaje a mostrar.
 * @param {string} type - 'info', 'success', o 'error'.
 * @param {number} duration - Duración en milisegundos.
 */
export function showStatusMessage(message, type = 'info', duration = 6000) {
    const statusDiv = document.getElementById('statusMessage');
    statusDiv.innerHTML = `<b>${message}</b>`;
    statusDiv.className = `status-message ${type} show`;
    setTimeout(() => statusDiv.classList.remove('show'), duration);
}

/**
 * Maneja el cierre de sesión del usuario.
 */
export async function logout() {
    localStorage.clear(); // Limpia todos los esquemas en caché
    try {
        const result = await logoutUser();
        if (result.success) {
            window.location.href = 'index.html';
        }
    } catch (error) {
        console.error("Error al cerrar sesión:", error);
    }
}

/**
 * Permite al usuario abrir un archivo .sql local y cargarlo en el editor.
 * @param {object} monacoEditorInstance - La instancia del editor Monaco.
 */
export async function openSqlFile(monacoEditorInstance) {
    if (!window.showOpenFilePicker) {
        showStatusMessage("Tu navegador no soporta la API para abrir archivos locales.", "error");
        return;
    }
    try {
        const [fileHandle] = await window.showOpenFilePicker({
            types: [{ description: 'Archivos SQL', accept: { 'text/sql': ['.sql'] } }],
            excludeAcceptAllOption: true,
            multiple: false
        });
        const file = await fileHandle.getFile();
        const fileContent = await file.text();
        if (monacoEditorInstance) {
            monacoEditorInstance.setValue(fileContent);
            showStatusMessage(`Archivo '${file.name}' cargado con éxito.`, "success");
        }
    } catch (err) {
        if (err.name !== 'AbortError') {
            console.error(err);
            showStatusMessage("Error al leer el archivo.", "error");
        }
    }
}