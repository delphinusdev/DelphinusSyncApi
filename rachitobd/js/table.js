// js/table.js

let tabulatorInstance = null;

/**
 * Crea o actualiza la tabla de resultados con Tabulator.
 * @param {object} resultData - Objeto con las columnas y filas.
 * @returns {object} - La nueva instancia de Tabulator.
 */
export function createResultsTable(resultData) {
    if (tabulatorInstance) {
        tabulatorInstance.destroy();
    }

    const { columns, rows } = resultData;
    document.getElementById('resultsTableContainer').style.display = 'block';
    document.getElementById('exportButtonsContainer').style.display = 'block'; // Show export buttons

    const tableColumns = columns.map(col => ({
        title: col,
        field: col,
        headerFilter: "input"
    }));

    // No longer adding an 'Exportar' column

    tabulatorInstance = new Tabulator("#resultsTable", {
        height: "100%",
        layout: "fitDataFill",
        placeholder: "No se encontraron resultados",
        data: rows,
        columns: tableColumns,
        clipboard: true,
        clipboardCopyRowRange: "active",
        renderHorizontal: "virtual", // virtual rendering mejora rendimiento horizontal
    renderVertical: "virtual", // virtual rendering mejora rendimiento vertical
    });

    


    // Adjuntamos la instancia a la ventana para que los botones de descarga funcionen
    window.tabulatorInstance = tabulatorInstance;

    // Attach event listeners to the new export buttons
    document.getElementById('exportCsvBtn').onclick = () => {
        tabulatorInstance.download('csv', 'query_results.csv');
    };
    document.getElementById('exportExcelBtn').onclick = () => {
        tabulatorInstance.download('xlsx', 'query_results.xlsx');
    };
    
    return tabulatorInstance;
}



// export function createResultsTable(resultData) {
//     if (tabulatorInstance) {
//         tabulatorInstance.destroy();
//     }

//     const { columns, rows } = resultData;
//     document.getElementById('resultsTableContainer').style.display = 'block';

//     const tableColumns = columns.map(col => ({
//         title: col,
//         field: col,
//         headerFilter: "input"
//     }));

//     // tableColumns.unshift({
//     //     title: "Exportar",
//     //     hozAlign: "center",
//     //     headerSort: false,
//     //     formatter: function(cell, formatterParams, onRendered) {
//     //         return `<button class="tabulator-btn" title="Descargar CSV" onclick="event.stopPropagation(); window.tabulatorInstance.download('csv', 'query_results.csv');"><i class="fas fa-file-csv"></i></button>
//     //                 <button class="tabulator-btn" title="Descargar Excel" onclick="event.stopPropagation(); window.tabulatorInstance.download('xlsx', 'query_results.xlsx');"><i class="fas fa-file-excel"></i></button>`;
//     //     }
//     // });

//     tabulatorInstance = new Tabulator("#resultsTable", {
//         height: "100%",
//         layout: "fitDataFill",
//         placeholder: "No se encontraron resultados",
//         data: rows,
//         columns: tableColumns,
//         clipboard: true,
//         clipboardCopyRowRange: "active",
//     });

//     // Adjuntamos la instancia a la ventana para que los botones de descarga funcionen
//     window.tabulatorInstance = tabulatorInstance;
    
//     return tabulatorInstance;
// }