// js/model.js

/**
 * Representa una única columna en una tabla.
 */
export class Column {
    constructor(name) {
        this.name = name;
        // Futuro: se podrían añadir dataType, isNullable, etc.
    }
}

/**
 * Representa un objeto de base de datos que puede contener columnas (Tabla o Vista).
 */
export class SchemaObject {
    constructor(name) {
        this.name = name;
        this.columns = []; // Un array de instancias de Column
    }

    /**
     * Añade una columna a este objeto.
     * @param {Column} column - La instancia de la columna a añadir.
     */
    addColumn(column) {
        this.columns.push(column);
    }
}

/**
 * Representa un objeto de base de datos que no tiene columnas (Procedimiento o Función).
 */
export class RoutableObject {
    constructor(name) {
        this.name = name;
    }
}


/**
 * Representa una base de datos individual y contiene todos sus objetos.
 */
export class Database {
    constructor(name) {
        this.name = name;
        this.tables = new Map();
        this.views = new Map();
        this.procedures = new Map();
        this.functions = new Map();
    }

    /**
     * Añade un objeto (tabla, vista, etc.) a la base de datos.
     * @param {string} type - 'Tables', 'Views', 'Procedures', 'Functions'
     * @param {SchemaObject | RoutableObject} object - La instancia del objeto.
     */
    addObject(type, object) {
        const collection = this[type.toLowerCase()];
        if (collection && !collection.has(object.name)) {
            collection.set(object.name, object);
        }
    }
    
    /**
     * Obtiene una tabla o vista por su nombre.
     * @param {string} name - El nombre de la tabla o vista.
     * @returns {SchemaObject | undefined}
     */
    getTableOrViewByName(name) {
        return this.tables.get(name) || this.views.get(name);
    }
}



export class SchemaModel {
    constructor() {
        if (SchemaModel.instance) {
            return SchemaModel.instance;
        }
        this.databases = new Map(); // Un mapa de instancias de Database
        this.currentDatabaseName = null;
        SchemaModel.instance = this;
    }

    /**
     * Añade una base de datos al modelo.
     * @param {Database} db - La instancia de la base de datos.
     */
    addDatabase(db) {
        // Ensure to overwrite if a database with the same name already exists
        // This is important for refreshing schema or correcting a partially loaded one
        this.databases.set(db.name, db);
    }

    /**
     * Obtiene una base de datos por su nombre.
     * @param {string} name - El nombre de la base de datos.
     * @returns {Database | undefined}
     */
    getDatabase(name) {
        return this.databases.get(name);
    }

    /**
     * Establece la base de datos activa.
     * @param {string} name - El nombre de la base de datos.
     */
    setCurrentDatabase(name) {
        // REMOVE THE CONDITIONAL CHECK HERE
        this.currentDatabaseName = name;
        console.log(`SchemaModel: currentDatabaseName set to '${this.currentDatabaseName}'`); // Added for explicit logging
    }

    /**
     * Obtiene la instancia de la base de datos activa actualmente.
     * @returns {Database | null}
     */
    getCurrentDatabase() {
        if (this.currentDatabaseName) {
            return this.getDatabase(this.currentDatabaseName);
        }
        return null;
    }
    /**
     * Elimina una base de datos del modelo por su nombre.
     * @param {string} name - El nombre de la base de datos a eliminar.
     * @returns {boolean} - true si se eliminó, false si no se encontró.
     */
    removeDatabase(name) {
        if (this.databases.has(name)) {
            console.log(`SchemaModel: Eliminando base de datos '${name}' del modelo.`);
            return this.databases.delete(name);
        }
        console.warn(`SchemaModel: Intento de eliminar base de datos '${name}' que no existe.`);
        return false;
    }
}