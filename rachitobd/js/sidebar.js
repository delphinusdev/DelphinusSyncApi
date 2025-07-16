export function populateSchemaSidebar(databaseInstance) {
    if (!databaseInstance) return;
    const container = document.getElementById('schema-tree-container');
    container.innerHTML = '';

    const categories = {
        'tables': { name: 'Tablas', icon: 'fa-table', color: 'var(--vscode-accent-blue)' },
        'views': { name: 'Vistas', icon: 'fa-eye', color: '#3d9a54' },
        'procedures': { name: 'Procedimientos', icon: 'fa-database', color: '#b58e3a' },
        'functions': { name: 'Funciones', icon: 'fa-cogs', color: '#c586c0' }
    };

    const tree = document.createElement('ul');
    tree.className = 'tree-view';

    for (const categoryKey in categories) {
        const objectsMap = databaseInstance[categoryKey];

        if (objectsMap.size === 0) continue;

        const categoryLi = document.createElement('li');
        categoryLi.className = 'category-container'; // ¡Añadido: Clase para la lista de categoría!

        const categoryHeader = document.createElement('div');
        categoryHeader.className = 'table-item expanded'; // Inicia expandido
        categoryHeader.innerHTML = `<i class="fas fa-chevron-down"></i><i class="fas ${categories[categoryKey].icon}" style="color:${categories[categoryKey].color};"></i><span>${categories[categoryKey].name}</span>`;

        const objectList = document.createElement('ul');
        objectList.className = 'column-list show'; // Inicia mostrado

        // Event Listener para los encabezados de categoría
        categoryHeader.addEventListener('click', () => {
            categoryHeader.classList.toggle('expanded');
            objectList.classList.toggle('show');
            const icon = categoryHeader.querySelector('i.fa-chevron-down, i.fa-chevron-right');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-right');
            }
        });

        const sortedObjectNames = Array.from(objectsMap.keys()).sort();

        sortedObjectNames.forEach(objectName => {
            const objectData = objectsMap.get(objectName);
            const hasColumns = objectData.columns && objectData.columns.length > 0;

            const objectLi = document.createElement('li');
            objectLi.className = 'table-item-container';
            let itemHTML = `<div class="table-item" data-name="${objectName}">`;
            itemHTML += hasColumns ? `<i class="fas fa-chevron-right"></i>` : `<span style="width:14px; display:inline-block;"></span>`;
            itemHTML += `<i class="fas ${categoryKey === 'tables' || categoryKey === 'views' ? 'fa-table' : 'fa-gear'}" style="color:var(--vscode-text-dark);"></i>`;
            itemHTML += `<span>${objectName}</span></div>`;
            objectLi.innerHTML = itemHTML;

            if (hasColumns) {
                const columnList = document.createElement('ul');
                columnList.className = 'column-list';
                [...objectData.columns].sort((a, b) => a.name.localeCompare(b.name)).forEach(column => {
                    const colLi = document.createElement('li');
                    colLi.innerHTML = `<div class="column-item" data-name="${column.name}"><i class="fas fa-columns" style="color:var(--vscode-success-green);"></i><span>${column.name}</span></div>`;
                    columnList.appendChild(colLi);
                });
                objectLi.appendChild(columnList);
            }
            objectList.appendChild(objectLi);
        });

        categoryLi.appendChild(categoryHeader);
        categoryLi.appendChild(objectList);
        tree.appendChild(categoryLi);
    }
    container.appendChild(tree);
}




export function setupSidebarInteractions(monacoEditorInstance) {
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

export function setupSchemaSearch() {
    const searchInput = document.getElementById('schema-search-input');
    searchInput.addEventListener('input', function () {
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