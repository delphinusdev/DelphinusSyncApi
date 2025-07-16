class ApiService {
    constructor(baseURL) {
        this.baseURL = baseURL;
        this.token = null;
    }

    /**
     * Establece el token de autenticación para futuras solicitudes.
     * @param {string} token - El token JWT.
     */
    setToken(token) {
        this.token = token;
    }

    /**
     * Realiza el proceso de autenticación.
     * @param {string} authEndpoint - El endpoint para la autenticación.
     * @param {object} credentials - Las credenciales del usuario.
     * @returns {Promise<object>} - La respuesta de la API.
     */
    async authenticate(authEndpoint, credentials) {
        const response = await this.post(authEndpoint, credentials, false); // No requiere token para autenticarse
        if (response && response.token) {
            this.setToken(response.token);
        }
        return response;
    }

    /**
     * Realiza una solicitud a la API.
     * @param {string} endpoint - El endpoint.
     * @param {string} method - El método HTTP.
     * @param {object|null} body - El cuerpo de la solicitud.
     * @param {boolean} requiresAuth - Indica si la solicitud necesita autenticación.
     * @returns {Promise<object>} - Los datos de la respuesta en formato JSON.
     * @private
     */
    async _request(endpoint, method, body = null, requiresAuth = true) {
        const url = `${this.baseURL}${endpoint}`;
        const headers = { 'Content-Type': 'application/json' };

        if (requiresAuth) {
            if (!this.token) {
                throw new Error('Error de autenticación: No se ha proporcionado un token.');
            }
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        const config = { method, headers };
        if (body) {
            config.body = JSON.stringify(body);
        }

        try {
            const response = await fetch(url, config);
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: 'Error sin detalles.' }));
                throw new Error(`Error HTTP ${response.status}: ${errorData.errors || response.statusText}`);
            }
            const responseText = await response.text();
            return responseText ? JSON.parse(responseText) : {};

        } catch (error) {
            throw error;
        }
    }

    get(endpoint, requiresAuth = true) { return this._request(endpoint, 'GET', null, requiresAuth); }
    post(endpoint, body, requiresAuth = true) { return this._request(endpoint, 'POST', body, requiresAuth); }
}