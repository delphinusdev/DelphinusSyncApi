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
        console.log("Token almacenado.");
    }

    /**
     * Realiza el proceso de autenticación.
     * @param {string} authEndpoint - El endpoint para la autenticación (ej: '/api/v2/Usuarios/Authenticate').
     * @param {object} credentials - Las credenciales del usuario (ej: { username, password }).
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
     * @param {string} endpoint - El endpoint al que se llamará (ej: '/reservas/123').
     * @param {string} method - El método HTTP ('GET', 'POST', etc.).
     * @param {object|null} body - El cuerpo de la solicitud para métodos como POST.
     * @param {boolean} requiresAuth - Indica si la solicitud necesita el token de autenticación.
     * @returns {Promise<object>} - Los datos de la respuesta en formato JSON.
     * @private
     */
    async _request(endpoint, method, body = null, requiresAuth = true) {
        const url = `${this.baseURL}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
        };

        if (requiresAuth) {
            if (!this.token) {
                // Si requiere autenticación y no hay token, se detiene la solicitud.
                // Podrías implementar una lógica para re-autenticar o redirigir al login.
                throw new Error('Error de autenticación: No se ha proporcionado un token.');
            }
            // Agrega el encabezado de autorización Bearer
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        const config = {
            method: method,
            headers: headers,
        };

        if (body) {
            config.body = JSON.stringify(body);
        }

        try {
            const response = await fetch(url, config);

            if (!response.ok) {
                // Intenta leer el cuerpo del error para dar más detalles
                const errorData = await response.json().catch(() => ({ message: 'Error sin detalles.' }));
                throw new Error(`Error HTTP ${response.status}: ${errorData.errors || response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error(`Error en la solicitud a ${method} ${endpoint}:`, error);
            // Relanza el error para que el código que llama pueda manejarlo
            throw error;
        }
    }

    /**
     * Realiza una solicitud GET a la API.
     * @param {string} endpoint - El endpoint.
     * @param {boolean} requiresAuth - Si necesita autenticación. Por defecto, true.
     * @returns {Promise<object>}
     */
    get(endpoint, requiresAuth = true) {
        return this._request(endpoint, 'GET', null, requiresAuth);
    }

    /**
     * Realiza una solicitud POST a la API.
     * @param {string} endpoint - El endpoint.
     * @param {object} body - El cuerpo de la solicitud.
     * @param {boolean} requiresAuth - Si necesita autenticación. Por defecto, true.
     * @returns {Promise<object>}
     */
    post(endpoint, body, requiresAuth = true) {
        return this._request(endpoint, 'POST', body, requiresAuth);
    }
}

// Asegúrate de que apiService.js esté cargado antes de este script.
(function () {
    // API endpoint configuration
    const API_BASE_URL = 'http://192.168.9.2:8084/api/v2';
    const AUTH_ENDPOINT = '/Usuarios/Authenticate';
    const IDISEPAAA_ = 10762040; // Cambia esto al endpoint real de la reserva

    // Se crea una única instancia del servicio de API
    const api = new ApiService(API_BASE_URL);

    // State variables (sin cambios)
    let currentIsepaaa = 0;
    let currentReservaData = {};
    let impuestoDefinidoData = {};
    let currentPagosData = [];
    let currentTiposPagoData = {};
    let currentTiposTarjetaData = {};
    let currentMonedasData = {};
    let currentImpuestoData = {};
    let currentPrecioIsepaaaData = {};
    let currentTiposCambioData = [];
    let currentMaximoPersonas = 0;
    let idsTiposPago = [1, 2];

    // --- HTML Structure for the Modal ---
    const modalHTML = `
        <div id="miModalReserva" class="modal-reserva-overlay" style="display:none;">
            <div class="modal-reserva-contenido">
                <span class="modal-reserva-cerrar">&times;</span>
                <h4 id="modalReservaTitulo">Gestión de Impuesto ISEPAAA</h4>
                <p class="reserva-info"><strong>Habitat:</strong> <span id="HabitatName"></span> | <strong>Confirma:</strong> <span id="confirmaId"></span> | <strong>Pasajero:</strong> <span id="pasajeroNombre"></span></p>

                <div id="noAplicaImpuestoPreview" style="display:none;">no aplica impuesto</div>

                <div id="seccionCrearImpuesto" style="display:none;">
                    <h5>Calcular Impuesto</h5>
                    <form id="formReserva">
                    <input type="hidden" id="sessionUser" name="sessionUser" required>
                    <input type="hidden" id="altaIdLocacion" name="altaIdLocacion" required>
                    <input type="hidden" id="idLocacion" name="idLocacion" required>
                    <input type="hidden" id="sessionPass" name="sessionPass" required>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="adultos">Adultos:</label>
                                <input type="number" min="0" id="adultos" name="adultos" required>
                            </div>
                            <div class="form-group">
                                <label for="menores">Menores:</label>
                                <input type="number" min="0" id="menores" name="menores" required>
                            </div>
                        </div>
                        <div id="impuestoPreview">Calculando...</div>
                        <button type="submit" id="btnSubmitReserva">Guardar y Calcular Impuesto</button>
                    </form>
                </div>

                <div id="seccionGestionPago" style="display:none;">
                    <div class="summary-box">
                        <h4>Detalles del Impuesto</h4>
                        <p><strong>Confirma Impuesto:</strong> <span id="impGenConfirmaImpuesto"></span></p>
                        <p><strong>Personas:</strong> <span id="impGenAdultos"></span> Adultos, <span id="impGenMenores"></span> Menores</p>
                        <p><strong>Tarifa/Paridad:</strong> $<span id="impGenTarifaUSD"></span> USD @ $<span id="impGenParidad"></span> MXN</p>
                    </div>

                    <div class="summary-box balance-box">
                        <h4>Saldos</h4>
                        <div class="balance-grid">
                            <span>Total:</span><strong class="text-right">$<span id="saldoTotalUSD">0.00</span> USD</strong>
                            <span>Pagado:</span><strong class="text-right">$<span id="saldoPagadoUSD">0.00</span> USD</strong>
                            <span class="por-pagar-label">Por Pagar:</span><strong class="text-right por-pagar-valor">$<span id="saldoPorPagarUSD">0.00</span> USD</strong>
                        </div>
                         <div class="balance-grid-sub">
                            <span></span><span class="text-right">($<span id="saldoTotalMXN">0.00</span> MXN)</span>
                            <span></span><span class="text-right">($<span id="saldoPagadoMXN">0.00</span> MXN)</span>
                            <span></span><span class="text-right por-pagar-valor">($<span id="saldoPorPagarMXN">0.00</span> MXN)</span>
                        </div>
                    </div>

                    <div id="seccionPago">
                        <h5>Agregar Pago</h5>
                        <form id="formPago">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="tipoPago">Forma de Pago:</label>
                                    <select id="tipoPago" name="id_tipo_pago" required></select>
                                </div>
                                <div class="form-group">
                                    <label for="monedaPago">Moneda:</label>
                                    <select id="monedaPago" name="id_moneda" required></select>
                                </div>
                            </div>
                            <div id="camposTarjetaCredito" style="display:none;" class="form-grid">
                                 <div class="form-group">
                                    <label for="tipoTarjeta">Tarjeta:</label>
                                    <select id="tipoTarjeta" name="id_tipo_tarjeta"></select>
                                </div>
                                <div class="form-group">
                                    <label for="referencia_pago">Referencia (Opcional):</label>
                                    <input type="text" id="referencia_pago" name="referencia_pago">
                                </div>
                            </div>
                            <div class="form-grid">
                            <div class="form-group">
                                     <label for="monto">Monto a Pagar:</label>
                                     <input type="text" inputmode="numeric" id="monto" name="monto" required>
                                </div>
                            </div>
                            <button type="submit" id="btnSubmitPago">Confirmar Pago</button>
                        </form>
                    </div>

                    <div id="seccionPagosRealizados" style="display:none;">
                        <h5>Pagos Realizados</h5>
                        <table id="tablaPagos" class="tabla-pagos-reserva">
                            <thead><tr><th>ID</th><th>Forma Pago</th><th>Moneda</th><th>Monto</th><th>Referencia</th><th>Fecha</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                 <div id="modalReservaMensaje" style="margin-top:15px;"></div>
            </div>
        </div>
    `;

    // --- CSS Styles for the Modal (Minified) ---
    const modalCSS = `
.modal-reserva-overlay{position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center}#miModalReserva .modal-reserva-contenido{background-color:#fefefe;margin:auto;padding:25px;border:1px solid #888;width:90%;max-width:700px;border-radius:8px;position:relative;max-height:90vh;overflow-y:auto;font-family:sans-serif}#miModalReserva .modal-reserva-cerrar{color:#aaa;float:right;font-size:28px;font-weight:700;position:absolute;top:10px;right:20px}#miModalReserva .modal-reserva-cerrar:focus,#miModalReserva .modal-reserva-cerrar:hover{color:#000;text-decoration:none;cursor:pointer}#miModalReserva .reserva-info{font-size:.9em;color:#555;background:#f4f4f4;padding:8px;border-radius:4px;margin-top:-10px}#miModalReserva #formPago,#miModalReserva #formReserva{display:flex;flex-direction:column;gap:15px}#miModalReserva .form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px}#miModalReserva .form-group{display:flex;flex-direction:column}#miModalReserva h5{font-size:1.1em;color:#333;margin-top:20px;margin-bottom:10px;border-bottom:1px solid #eee;padding-bottom:5px}#miModalReserva label{margin-bottom:5px;font-weight:700;font-size:.9em}#miModalReserva input[type=number],#miModalReserva input[type=text],#miModalReserva select{width:100%;padding:10px;height:auto!important;border:1px solid #ccc;border-radius:4px;box-sizing:border-box;background-color:#fff;font-size:1em}#miModalReserva button[type=submit]{background-color:#007bff;color:#fff;padding:10px 15px;border:none;border-radius:4px;cursor:pointer;align-self:flex-end;margin-top:10px}#miModalReserva button[type=submit]:hover{background-color:#0056b3}#miModalReserva #modalReservaMensaje{font-size:.9em;margin-top:15px;padding:10px;border-radius:4px;display:none}#miModalReserva #modalReservaMensaje.success{color:#155724;background-color:#d4edda;border:1px solid #c3e6cb;display:block}#miModalReserva #modalReservaMensaje.error{color:#721c24;background-color:#f8d7da;border:1px solid #f5c6cb;display:block}#miModalReserva #noAplicaImpuestoPreview{margin-top:10px;padding:10px;background-color:#e9ecef;border:1px dashed #ccc;border-radius:4px;font-size:1em;line-height:1.5} #impuestoPreview{margin-top:10px;padding:10px;background-color:#e9ecef;border:1px dashed #ccc;border-radius:4px;font-size:1em;line-height:1.5}#miModalReserva .summary-box{background-color:#f9f9f9;border:1px solid #ddd;border-radius:4px;padding:15px;margin-top:15px}#miModalReserva .summary-box h4{margin-top:0}#miModalReserva .summary-box p{margin:5px 0}#miModalReserva .balance-box .por-pagar-label{font-weight:700}#miModalReserva .balance-box .por-pagar-valor{font-weight:700;color:#c0392b}#miModalReserva .balance-grid,#miModalReserva .balance-grid-sub{display:grid;grid-template-columns:100px 1fr;align-items:center;gap:5px 15px}#miModalReserva .balance-grid-sub{font-size:.9em;color:#666}#miModalReserva .text-right{text-align:right}#miModalReserva .tabla-pagos-reserva{width:100%;border-collapse:collapse;margin-top:10px}#miModalReserva .tabla-pagos-reserva td,#miModalReserva .tabla-pagos-reserva th{border:1px solid #ddd;padding:8px;text-align:left;font-size:.9em}#miModalReserva .tabla-pagos-reserva th{background-color:#f2f2f2}
`;

    // --- Helper Functions ---

    function injectCSS() {
        const styleSheet = document.createElement("style");
        styleSheet.type = "text/css";
        styleSheet.innerText = modalCSS;
        document.head.appendChild(styleSheet);
    }

    function injectModalHTML() {
        if (!document.getElementById('miModalReserva')) {
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
    }

    function showMessage(message, type = 'error', show = 1) {
        const modalMensaje = document.getElementById('modalReservaMensaje');
        modalMensaje.textContent = message;
        modalMensaje.className = type;
        if (show === 1) {
            modalMensaje.style.display = 'block';
        }
        else {
            modalMensaje.style.display = 'none';
        }
        // 'success' or 'error'
    }
    function showNoAplicaImpuesto(message) {
        const modalPreviewImpuesto = document.getElementById('noAplicaImpuestoPreview');
        modalPreviewImpuesto.textContent = message;
        modalPreviewImpuesto.style.display = 'block';
    }

    function poblarSelect(selectElement, data, valueField, textField, defaultOptionText = 'Seleccione...') {
        if (!selectElement) return;
        selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueField];
            option.textContent = item[textField];
            selectElement.appendChild(option);
        });
    }

    /**
   * Obtiene los tipos de pago desde la API.
   * @returns {Promise<Array>} Un arreglo con los tipos de pago.
   */
    async function obtenerPrecioIsepaaa(servicio, idHabitat, fecha) {
        try {
            const endpoint = `/Servicios/${servicio}/Precios?fecha=${fecha}&idHabitat=${idHabitat}`;
            const response = await api.get(endpoint);
            // La API debería devolver un array de precios
            if (response && typeof response === 'object' && response.precioAdulto !== undefined) {
                return response;
            }
            throw new Error("La respuesta de precios servicios no es válida.");
        } catch (error) {
            console.error("Error al cargar precios de ISEPAAA:", error);
            showMessage('No se pudieron cargar los precios del servicio.', 'error');
            throw error;
        }
    }

    /**
   * Obtiene los tipos de pago desde la API.
   * @returns {Promise<Array>} Un arreglo con los tipos de pago.
   */
    async function obtenerTiposDePago() {
        try {
            const response = await api.get('/FormasPago');

            // 1. Validar que la respuesta sea un arreglo no vacío
            if (!response || !Array.isArray(response)) {
                throw new Error("La respuesta de Tipos de Pago no es válida.");
            }

            // 2. Usar .map() para transformar cada elemento del arreglo
            const datosTransformados = response.map(pago => {
                // Creamos un nuevo objeto con las propiedades originales (...pago)
                // y agregamos la propiedad 'referencia' usando un operador ternario.
                return {
                    ...pago,
                    referencia: pago.value === "Tarjeta de Credito" ? 1 : 0
                };
            });

            // 3. Devolver el nuevo arreglo con los datos ya transformados
            return datosTransformados;

        } catch (error) {
            console.error("Error al cargar tipos de pago:", error);
            showMessage('No se pudieron cargar las formas de pago.', 'error');
            throw error; // Lanza el error para que Promise.all lo capture
        }
    }

    async function obtenerTiposDeCambio() {
        try {
            const response = await api.get('/TiposCambio');

            // 1. Validar que la respuesta sea un arreglo no vacío
            if (!response || !Array.isArray(response)) {
                throw new Error("La respuesta de Tipos de Pago no es válida.");
            }

            // // 2. Usar .map() para transformar cada elemento del arreglo
            // const datosTransformados = response.map(pago => {
            //     // Creamos un nuevo objeto con las propiedades originales (...pago)
            //     // y agregamos la propiedad 'referencia' usando un operador ternario.
            //     return {
            //         ...pago,
            //         referencia: pago.value === "Tarjeta de Credito" ? 1 : 0
            //     };
            // });

            // 3. Devolver el nuevo arreglo con los datos ya transformados
            return response;

        } catch (error) {
            console.error("Error al cargar tipos de pago:", error);
            showMessage('No se pudieron cargar las formas de pago.', 'error');
            throw error; // Lanza el error para que Promise.all lo capture
        }
    }

    /**
     * Obtiene los tipos de tarjeta desde la API.
     * @returns {Promise<Array>} Un arreglo con los tipos de tarjeta.
     */
    async function obtenerTiposDeTarjeta() {
        try {
            const response = await api.get('/TiposTarjeta');
            if (response && Array.isArray(response)) {
                return response;
            }
            throw new Error("La respuesta de Tipos de Tarjeta no es válida.");
        } catch (error) {
            console.error("Error al cargar tipos de tarjeta:", error);
            showMessage('No se pudieron cargar los tipos de tarjeta.', 'error');
            throw error;
        }
    }

    /**
     * Obtiene las monedas desde la API.
     * @returns {Promise<Array>} Un arreglo con las monedas.
     */
    async function obtenerMonedas() {
        try {
            const response = await api.get('/Monedas');

            if (!response || !Array.isArray(response)) {
                throw new Error("La respuesta de Monedas no es válida.");
            }

            // 1. Define un "diccionario" para los nombres de las monedas
            const nombresMonedas = {
                USD: "Dólares Americanos",
                MXN: "Pesos Mexicanos",
                EUR: "Euros",
                CAN: "Dólar Canadiense"
                // Puedes agregar más monedas aquí fácilmente
            };

            // 2. Usa .map() para transformar cada moneda en el arreglo
            const datosTransformados = response.map(moneda => {
                return {
                    ...moneda, // Copia las propiedades existentes (id, value)
                    // Busca el nombre en el diccionario usando el código (moneda.value)
                    // Si no lo encuentra, usa el mismo código como nombre por defecto
                    name: nombresMonedas[moneda.value] || moneda.value
                };
            });

            // 3. Devuelve el nuevo arreglo transformado
            return datosTransformados;

        } catch (error) {
            console.error("Error al cargar monedas:", error);
            showMessage('No se pudieron cargar las monedas.', 'error');
            throw error;
        }
    }

    /**
     * Obtiene el impuesto asociado a una reserva.
     * @param {string} uniqIdReserva - El ID único de la reserva.
     * @returns {Promise<Object|null>} El objeto del impuesto o null si no existe.
     */
    async function obtenerReserva(uniqIdReserva) {
        try {
            if (!uniqIdReserva || uniqIdReserva === '0' || uniqIdReserva === 0 || uniqIdReserva === 'undefined') {
                return null; // Si no hay ID, no hacemos nada.
            }
            const endpoint = `/Reservas/${uniqIdReserva}`;
            console.log("Cargando reserva desde:", endpoint);
            const response = await api.get(endpoint);
            // Si la API devuelve un objeto `data` con el impuesto, lo retornamos.
            // Si no hay impuesto, es común que `data` sea null o un objeto vacío.
            return response;
        } catch (error) {
            console.error("Error al cargar el impuesto:", error);
            showMessage('No se pudo verificar el impuesto existente.', 'error');
            throw error;
        }
    }

    /**
     * Obtiene los pagos realizados para una reserva.
     * @param {string} uniqIdReserva - El ID único de la reserva.
     * @returns {Promise<Array>} Un arreglo con los pagos.
     */
    async function obtenerPagos(idImpuesto) {
        try {
            if (!idImpuesto || idImpuesto === '0' || idImpuesto === 0 || idImpuesto === 'undefined') {
                return null; // Si no hay ID, no hacemos nada.
            }
            const endpoint = `/Pagos?idReservacion=${idImpuesto}`;
            const response = await api.get(endpoint);
            if (response) {
                return response;
            }
            // Es normal que no haya pagos, así que devolvemos un array vacío.
            return [];
        } catch (error) {
            console.error("Error al cargar los pagos:", error);
            showMessage('No se pudieron cargar los pagos anteriores.', 'error');
            throw error;
        }
    }

    /**
     * Orquesta la carga de todos los datos necesarios para el modal.
     * @param {string} endpointPrincipal - El endpoint para obtener la reserva principal.
     * @returns {Promise<Object>} Un objeto con toda la información consolidada.
     */
    async function cargarDatosCompletosDeReserva(endpointPrincipal) {
        // 1. Obtener la reserva principal
        showMessage('Obteniendo datos de la reserva...', 'success');
        const reservaResponse = await api.get(endpointPrincipal);

        // Validamos que la respuesta sea exitosa y contenga los datos
        if (!reservaResponse) {
            throw new Error(reservaResponse.message || "No se encontraron datos válidos de la reserva.");
        }
        const reserva = reservaResponse;

        console.log("Datos de reserva obtenidos:", reserva);

        // 2. Condición principal: Si no aplica el impuesto, no hacemos más llamadas.
        if (!reserva.aplicaISEPAAA) {
            showMessage('Esta reserva no aplica para el impuesto.', 'info');
            // Devolvemos solo la reserva y valores vacíos para el resto.
            return {
                reserva,
                precio: null,
                impuesto: null,
                tiposCambio: [],
                pagos: [],
                tiposPago: [],
                tiposTarjeta: [],
                monedas: []
            };
        }

        // 3. Si aplica, obtenemos el resto de la información en paralelo.
        showMessage('Cargando catálogos y detalles del impuesto...', 'success');
        const idReserva = parseInt(reserva.idISEPAAA) || 0;
        const idHabitat = reserva.idHabitat || 0; // Aseguramos que idHabitat tenga un valor por defecto
        const servicio = IDISEPAAA_;
        // Obtener la fecha en formato YYYY-MM-DD (ejemplo: 2025-07-01)
        const fechaObj = new Date();
        const fecha = fechaObj.getFullYear() + '-' +
            String(fechaObj.getMonth() + 1).padStart(2, '0') + '-' +
            String(fechaObj.getDate()).padStart(2, '0');



        const [
            impuesto,
            precio,
            pagos,
            tiposCambio,
            tiposPago,
            tiposTarjeta,
            monedas
        ] = await Promise.all([
            obtenerReserva(idReserva),
            obtenerPrecioIsepaaa(servicio, idHabitat, fecha),
            obtenerPagos(idReserva),
            obtenerTiposDeCambio(),
            obtenerTiposDePago(),
            obtenerTiposDeTarjeta(),
            obtenerMonedas()
        ]);

        // 4. Devolvemos todo el paquete de datos.
        return { reserva, impuesto, precio, pagos, tiposCambio, tiposPago, tiposTarjeta, monedas };
    }

    // --- Lógica Principal Refactorizada ---

    async function fetchDataAndPopulate(endpoint) {
        showMessage('Cargando datos de la reserva...', 'success');
        try {
            // Usa el método get del ApiService. Ya incluye el token si está disponible.
            const datos = await cargarDatosCompletosDeReserva(endpoint);

            if (!datos) {
                throw new Error(datos || "No se encontraron datos válidos de la reserva.");
            }

            currentReservaData = datos.reserva;


            currentPrecioIsepaaaData = datos.precio || {};
            currentImpuestoData = datos.impuesto || {};
            currentTiposPagoData = datos.tiposPago;
            currentTiposDePagoFiltrados = currentTiposPagoData.filter(tipoPago => idsTiposPago.includes(tipoPago.id));
            currentTiposTarjetaData = datos.tiposTarjeta;
            currentMonedasData = datos.monedas;
            currentTiposCambioData = datos.tiposCambio || [];
            currentDefaultPayment = currentTiposCambioData.find(item => item.moneda === 'USD') || { monto: 0 }; // Default to USD if available
            // Si datos.pagos es un array, mapeamos cada elemento al modelo datospagoModel
            if (Array.isArray(datos.pagos) && datos.pagos.length > 0) {
                currentPagosData = datos.pagos.map(pago => ({
                    idPago: pago.idPago || null,
                    idTipoPago: pago.idTipoPago || null,
                    idMoneda: pago.idMoneda || null,
                    monto: pago.monto || null,
                    referencia: pago.referencia || null,
                    fecha: pago.fecha || null,
                    tipoCambio: pago.tipoCambio || null
                }));
            } else {
                currentPagosData = [];
            }
            currentMaximoPersonas = (parseInt(currentReservaData.adultos) || 0) + (parseInt(currentReservaData.menores) || 0);


            console.log("Datos de pagos:", currentPagosData);


            document.getElementById('HabitatName').textContent = currentReservaData.habitat || 'N/A';
            document.getElementById('confirmaId').textContent = currentReservaData.confirma || 'N/A';
            document.getElementById('pasajeroNombre').textContent = currentReservaData.nombre || 'N/A';
            document.getElementById('idLocacion').value = currentReservaData.idHabitat || '';

            if (currentReservaData.codigoPais && currentReservaData.codigoPais === 'MXQR') {
                showNoAplicaImpuesto('El visitante es Quintanaroense, no requiere agregar un impuesto', 'error');
                showMessage('', 'error', 0);
                return;
            }

            if (currentReservaData.idServicio == IDISEPAAA_) {
                showNoAplicaImpuesto('No requiere agregar impuesto', 'error');
                document.getElementById('seccionCrearImpuesto').style.display = 'none';
                showMessage('', 'error', 0);
                return;
            }

            return ImpuestoDefinido('La reserva ya tiene un impuesto definido.');

        } catch (error) {
            console.error("Error en fetchDataAndPopulate:", error);
            showMessage(`Error al cargar datos: ${error.message}`, 'error');
            return false;
        }
    }

    function actualizarImpuestoPreview() {
        const adultos = parseInt(document.getElementById('adultos').value) || 0;
        const menores = parseInt(document.getElementById('menores').value) || 0;
        const previewDiv = document.getElementById('impuestoPreview');

        const tarifa = parseFloat(currentPrecioIsepaaaData.precioAdulto) || 0;
        const paridad = parseFloat(currentPrecioIsepaaaData.tipoCambio) || 0;

        // El máximo de personas permitido es la suma de adultos y menores de la reserva original

        const totalPersonas = adultos + menores;
        const montoUSDEstimado = totalPersonas * tarifa;
        const montoMXNEstimado = montoUSDEstimado * paridad;

        if (totalPersonas > currentMaximoPersonas) {
            previewDiv.innerHTML = `<span style="color:red;">No es posible exceder la cantidad máxima de personas (${currentMaximoPersonas}).</span>`;
        } else if (totalPersonas > 0) {
            previewDiv.innerHTML = `Personas para impuesto: ${totalPersonas}<br>
                                Tarifa: $${tarifa.toFixed(6)} USD por persona<br>
                                Paridad: $${paridad.toFixed(2)} MXN por USD<br>
                                <b>Estimado: $${montoUSDEstimado.toFixed(6)} USD / $${montoMXNEstimado.toFixed(2)} MXN</b>`;
        } else {
            previewDiv.innerHTML = 'Ingrese número de adultos y/o menores para calcular el impuesto.';
        }
    }

    function ImpuestoDefinido(message) {
        console.log("Impuesto definido:", currentImpuestoData);
        if (currentImpuestoData.idReservacion && (parseInt(currentImpuestoData.adultos) > 0 || parseInt(currentImpuestoData.menores) > 0)) {
            impuestoDefinidoData = {
                idLocacion: parseInt(currentImpuestoData.idHabitat) || 0,
                adultos: parseInt(currentImpuestoData.adultos) || 0,
                menores: parseInt(currentImpuestoData.menores) || 0,
                tarifaUSD: parseFloat(currentPrecioIsepaaaData.precioAdulto) || 0,
                paridadMXN: parseFloat(currentDefaultPayment.monto) || 0,
                confirma_impuesto: currentImpuestoData.confirma || 'N/A',
                idReserva: currentImpuestoData.idReservacion || 0
            };
            impuestoDefinidoData.totalUSD = (impuestoDefinidoData.adultos + impuestoDefinidoData.menores) * impuestoDefinidoData.tarifaUSD;
            impuestoDefinidoData.totalMXN = impuestoDefinidoData.totalUSD * impuestoDefinidoData.paridadMXN;
            showMessage(message, 'success');
            console.log("Datos del impuesto definido:", impuestoDefinidoData);
            return true;
        } else {
            document.getElementById('adultos').value = currentReservaData.adultos || 0;
            document.getElementById('menores').value = currentReservaData.menores || 0;
            actualizarImpuestoPreview();
            showMessage('Datos de reserva cargados. Agregue el impuesto.', 'success');
            return false;
        }
    }

    function mostrarGestionImpuestoUI() {
        document.getElementById('impGenConfirmaImpuesto').textContent = impuestoDefinidoData.confirma_impuesto;
        document.getElementById('impGenAdultos').textContent = impuestoDefinidoData.adultos;
        document.getElementById('impGenMenores').textContent = impuestoDefinidoData.menores;
        document.getElementById('impGenTarifaUSD').textContent = impuestoDefinidoData.tarifaUSD.toFixed(2);
        document.getElementById('impGenParidad').textContent = impuestoDefinidoData.paridadMXN.toFixed(2);

        // Populate total fields
        document.getElementById('saldoTotalUSD').textContent = impuestoDefinidoData.totalUSD.toFixed(2);
        document.getElementById('saldoTotalMXN').textContent = impuestoDefinidoData.totalMXN.toFixed(2);

        cargarPagosRealizados(); // This will load payments and then update balances
        console.log("currentTiposPagoData:", currentTiposPagoData);
        console.log("currentMonedasData:", currentMonedasData);
        // Populate payment form dropdowns
        poblarSelect(document.getElementById('tipoPago'), currentTiposDePagoFiltrados, 'id', 'value');
        poblarSelect(document.getElementById('monedaPago'), currentMonedasData, 'id', 'name');

        // Default payment currency to USD if available
        const usdMoneda = currentDefaultPayment;
        if (usdMoneda) document.getElementById('monedaPago').value = usdMoneda.idMoneda;

        document.getElementById('seccionCrearImpuesto').style.display = 'none';
        document.getElementById('seccionGestionPago').style.display = 'block';
        actualizarMontoPago();
    }

    function cargarPagosRealizados() {
        const tablaPagosBody = document.getElementById('tablaPagos').querySelector('tbody');
        tablaPagosBody.innerHTML = '';

        if (currentPagosData.length > 0) {
            currentPagosData.forEach(pago => agregarPagoATabla(pago));
            document.getElementById('seccionPagosRealizados').style.display = 'block';
        } else {
            tablaPagosBody.innerHTML = '<tr><td colspan="6">No hay pagos registrados.</td></tr>';
            document.getElementById('seccionPagosRealizados').style.display = 'block';
        }

        actualizarSaldos(); // Critical: update balances after payments are known
    }

    function agregarPagoATabla(pago) {
        const tablaPagosBody = document.getElementById('tablaPagos').querySelector('tbody');
        // If the first row is "No hay pagos...", remove it
        const primeraFila = tablaPagosBody.querySelector('tr');
        if (primeraFila && primeraFila.cells.length > 1 && primeraFila.cells[0].colSpan === 6) {
            tablaPagosBody.innerHTML = '';
        }

        const fila = tablaPagosBody.insertRow();
        fila.insertCell().textContent = pago.idPago || 'N/A';
        const tipoPagoInfo = currentTiposPagoData.find(tp => tp.id == pago.idTipoPago);
        fila.insertCell().textContent = tipoPagoInfo ? tipoPagoInfo.value : 'Desconocido';
        const monedaInfo = currentMonedasData.find(m => m.id == pago.idMoneda);
        fila.insertCell().textContent = monedaInfo ? monedaInfo.value : 'N/A';
        fila.insertCell().textContent = parseFloat(pago.monto || 0).toFixed(2);
        fila.insertCell().textContent = pago.referencia || '';
        fila.insertCell().textContent = pago.fecha ? new Date(pago.fecha).toLocaleDateString() : 'N/A';
    }


    function actualizarSaldos() {
        const paridad_USD_a_MXN = impuestoDefinidoData.paridadMXN;
        let totalPagadoUSD = 0;

        currentPagosData.forEach(pago => {

            if (!pago || !pago.tipoCambio) return;

            const paridad_pago_a_MXN = parseFloat(pago.tipoCambio) || 1; // Default to 1 if not defined
            const montoEnMXN = parseFloat(pago.monto) * paridad_pago_a_MXN;

            // Convert payment to USD for summation
            const montoEnUSD = (paridad_USD_a_MXN > 0) ? (montoEnMXN / paridad_USD_a_MXN) : 0;
            totalPagadoUSD += montoEnUSD;
        });

        const totalPagadoMXN = totalPagadoUSD * paridad_USD_a_MXN;
        const saldoPendienteUSD = impuestoDefinidoData.totalUSD - totalPagadoUSD;
        const saldoPendienteMXN = impuestoDefinidoData.totalMXN - totalPagadoMXN;

        // Update DOM for Paid and To Pay sections
        document.getElementById('saldoPagadoUSD').textContent = totalPagadoUSD.toFixed(2);
        document.getElementById('saldoPagadoMXN').textContent = totalPagadoMXN.toFixed(2);
        document.getElementById('saldoPorPagarUSD').textContent = (saldoPendienteUSD > 0 ? saldoPendienteUSD : 0).toFixed(2);
        document.getElementById('saldoPorPagarMXN').textContent = (saldoPendienteMXN > 0 ? saldoPendienteMXN : 0).toFixed(2);

        // After updating balances, update the payment amount field
        actualizarMontoPago();
        if (saldoPendienteUSD <= 0) {
            showMessage('El impuesto ya está completamente pagado.', 'success', 1);
            document.getElementById('seccionPago').style.display = 'none';
            // document.getElementById('seccionPagosRealizados').style.display = 'block';
        }
    }

    function actualizarMontoPago() {
        const montoInput = document.getElementById('monto');
        const selectedMonedaId = document.getElementById('monedaPago').value;
        const saldoPendienteUSD = parseFloat(document.getElementById('saldoPorPagarUSD').textContent) || 0;
        console.log("montoInput:", montoInput);
        console.log("Saldo pendiente USD:", saldoPendienteUSD);
        console.log("Selected Moneda ID:", selectedMonedaId);

        if (!selectedMonedaId || saldoPendienteUSD <= 0) {
            montoInput.value = "0.00";
            return;
        }


        const monedaSeleccionada = currentTiposCambioData.find(m => m.idMoneda == selectedMonedaId) || { monto: 1, moneda: 'MXN' };
        console.log("Moneda seleccionada:", monedaSeleccionada);
        if (!monedaSeleccionada || !monedaSeleccionada.monto) {
            montoInput.value = "0.00";
            return;
        }

        const paridad_USD_a_MXN = impuestoDefinidoData.paridadMXN;
        const paridad_seleccionada_a_MXN = parseFloat(monedaSeleccionada.monto);

        let montoCalculadoFinal;
        if (monedaSeleccionada.value === 'USD') {
            montoCalculadoFinal = saldoPendienteUSD;
        } else {
            const saldoPendienteMXN = saldoPendienteUSD * paridad_USD_a_MXN;
            montoCalculadoFinal = (paridad_seleccionada_a_MXN > 0) ? (saldoPendienteMXN / paridad_seleccionada_a_MXN) : 0;
        }

        montoInput.value = (montoCalculadoFinal > 0 ? montoCalculadoFinal : 0).toFixed(2);
    }


    // --- Inicialización y Event Listeners ---
    function initModal() {
        const modal = document.getElementById('miModalReserva');
        const spanCerrar = modal.querySelector('.modal-reserva-cerrar');
        const formReserva = modal.querySelector('#formReserva');
        const formPago = modal.querySelector('#formPago');

        // Listeners dinámicos (sin cambios)
        document.getElementById('adultos').addEventListener('input', actualizarImpuestoPreview);
        document.getElementById('menores').addEventListener('input', actualizarImpuestoPreview);
        document.getElementById('monedaPago').addEventListener('change', actualizarMontoPago);

        document.getElementById('tipoPago').addEventListener('change', function () {


            const divCamposTarjeta = document.getElementById('camposTarjetaCredito');
            const selectTipoTarjeta = document.getElementById('tipoTarjeta');
            const selectMoneda = document.getElementById('monedaPago');


            const tipoSeleccionado = currentTiposPagoData.find(tp => tp.id == this.value);
            // Check if 'referencia' is 1 (or other indicator for requiring card details)
            const requiereTarjeta = tipoSeleccionado && parseInt(tipoSeleccionado.referencia) === 1;

            if (requiereTarjeta) {
                divCamposTarjeta.style.display = 'grid';
                poblarSelect(selectTipoTarjeta, currentTiposTarjetaData, 'id', 'value', 'Seleccione Tarjeta');

                selectTipoTarjeta.required = true;

                selectMoneda.value = '2';
                selectMoneda.disabled = true;

                actualizarMontoPago();

            } else {
                divCamposTarjeta.style.display = 'none';
                selectTipoTarjeta.required = false;
                selectTipoTarjeta.innerHTML = '';
                selectMoneda.disabled = false;
            }
        });

        // ---- Evento Principal para abrir el modal (Refactorizado) ----
        document.querySelectorAll('.btn-modal-reserva-trigger').forEach(button => {
            button.addEventListener('click', async function () {
                // Reset UI and state
                formReserva.reset();
                formPago.reset();
                document.getElementById('modalReservaMensaje').style.display = 'none';
                document.getElementById('seccionCrearImpuesto').style.display = 'none';
                document.getElementById('seccionGestionPago').style.display = 'none';
                currentReservaData = {};
                impuestoDefinidoData = {};
                pagosData = [];
                currentTiposPagoData = {};
                currentTiposTarjetaData = {};
                currentMonedasData = {};
                currentImpuestoData = {};
                currentPrecioIsepaaaData = {};
                currentTiposCambioData = [];
                currentMaximoPersonas = 0;

                modal.style.display = 'flex';
                document.getElementById('modalReservaTitulo').textContent = this.dataset.modalTitle || 'Gestión de Impuesto ISEPAAA';
                let user = this.dataset.sessionUser || '';
                document.getElementById('sessionUser').value = user;

                let pass = this.dataset.sessionPass || '';
                document.getElementById('altaIdLocacion').value = this.dataset.altaIdLocacion || 0;

                const apiEndpoint = this.dataset.apiEndpoint;
                if (!apiEndpoint) {
                    showMessage('Error: No se especificó el endpoint para la reserva.', 'error');
                    return;
                }

                try {
                    // 2. Autenticar (si aún no se ha hecho o el token expiró)
                    // Esta lógica asume que autenticas cada vez. Podrías añadir una
                    // comprobación `if (!api.token)` si quieres reutilizar el token.
                    showMessage('Autenticando...', 'success');
                    const userData = {
                        username: user,
                        password: pass
                    };


                    await api.authenticate(AUTH_ENDPOINT, userData);
                    showMessage('Autenticación exitosa.', 'success');

                    // 3. Obtener los datos de la reserva (el token ya está en la instancia de api)
                    const impuestoExiste = await fetchDataAndPopulate(apiEndpoint);

                    if (impuestoExiste) {
                        mostrarGestionImpuestoUI();
                        document.getElementById('seccionCrearImpuesto').style.display = 'none';
                    } else if (currentReservaData.codigoPais !== 'MXQR') { // Solo mostrar si no es de QR
                        document.getElementById('seccionCrearImpuesto').style.display = 'block';
                        document.getElementById('seccionGestionPago').style.display = 'none';
                    }

                } catch (error) {
                    // El error de autenticación o de fetch será capturado aquí
                    console.error('Error en el proceso de apertura del modal:', error);
                    showMessage(`Error: ${error.message}`, 'error');
                }
            });
        });

        // Eventos de cierre y envío de formularios (refactorizados)
        spanCerrar.onclick = function () { modal.style.display = 'none'; };

        // --- Envío de Formulario para crear impuesto (Refactorizado) ---

        formReserva.onsubmit = async function (event) {
            event.preventDefault();
            showMessage('Procesando impuesto...', 'success');

            // --- 1. Obtener los datos del formulario ---
            const adultosParaImpuesto = parseInt(formReserva.elements['adultos'].value) || 0;
            const menoresParaImpuesto = parseInt(formReserva.elements['menores'].value) || 0;
            const idSessionUser = formReserva.elements['sessionUser'].value || null;

            // --- 2. Validaciones básicas ---
            if (adultosParaImpuesto === 0 && menoresParaImpuesto === 0) {
                showMessage('Debe ingresar al menos un adulto o menor.', 'error');
                return;
            }
            if (idSessionUser === null || idSessionUser === '') {
                showMessage('No fue posible obtener la sesión del usuario, actualice la página.', 'error');
                return;
            }

            // --- 3. Construir el payload para la API ---
            // Se combinan los datos de la reserva principal (`currentReservaData`)
            // con los datos específicos del formulario.
            const datosImpuestoParaApi = {
                // Datos que se mantienen de la reserva original
                fecha: currentReservaData.fecha,
                idHabitat: currentReservaData.idHabitat,
                idServicio: IDISEPAAA_,
                idIdioma: currentReservaData.idIdioma,
                nombre: currentReservaData.nombre,
                correo: currentReservaData.correo,
                telefono: currentReservaData.telefono,
                codigoPais: 'MX',
                idHotel: currentReservaData.idHotel,
                idAgencia: 24003,
                idRepresentante: 0,
                idClasificacion: 2,
                idSubclasificacion: 15,
                idMedioVenta: currentReservaData.idMedioVenta,
                idAutorizo: 0,
                tipoComentario: 1, // O el valor que corresponda
                // Datos calculados o ingresados en el formulario del modal
                adultos: adultosParaImpuesto,
                menores: menoresParaImpuesto,
                porcentajeIVA: 0,
                uniqidReserva: currentReservaData.uniqIdReserva,
            };

            try {
                // --- 4. Enviar los datos a la API ---
                // El endpoint podría ser más específico, ej: '/api/v2/Impuestos/Crear'
                // pero usamos el que ya tenías definido.
                const endpoint = `/Reservas`; // Ajusta si el endpoint es diferente
                const result = await api.post(endpoint, datosImpuestoParaApi);

                // Si la respuesta de la API indica un error (por ejemplo, tiene una propiedad 'errors')
                if (result && result.errors && result.errors.length > 0) {
                    console.log("Errores al crear el impuesto int:", result.errors);
                    // Concatenar los errores en un solo mensaje o mostrarlos individualmente
                    const errorMessage = result.errors.join(", "); // Une los errores con una coma
                    throw new Error(errorMessage); // Lanza un error con los mensajes específicos
                }

                // Si todo salió bien y no hay errores explícitos en la respuesta
                showMessage('Impuesto guardado exitosamente.', 'success');

                const datosImpuesto = await obtenerReserva(result.idReservacion);

                // Asignar los nuevos datos al estado global
                currentImpuestoData = datosImpuesto;
                // pagosData = [];
                // Si el impuesto ya existe, muestra la sección de gestión
                if (datosImpuesto) {
                    ImpuestoDefinido('El impuesto se ha creado exitosamente.');
                    mostrarGestionImpuestoUI();
                }

            } catch (error) {
                // Captura cualquier error que se haya lanzado (ya sea por la red, por la validación, etc.)
                console.error("Error al procesar la reserva:", error);
                showMessage(error.message || 'Error desconocido al guardar el impuesto.', 'error');
            }
        }

        formPago.onsubmit = async function (event) {
            event.preventDefault();
            showMessage('Procesando pago...', 'success');

            try {
                // --- Recolectar y validar datos del formulario ---
                const montoPago = parseFloat(formPago.elements['monto'].value) || 0;
                if (montoPago <= 0) {
                    throw new Error('El monto del pago debe ser mayor a cero.');
                }

                const idMoneda = formPago.elements['monedaPago'].value;
                const monedaSeleccionada = currentTiposCambioData.find(m => m.idMoneda == idMoneda);
                const paridadDelPago = monedaSeleccionada ? (monedaSeleccionada.monto || 1.0) : 1.0;

                // --- 1. Crear el objeto de pago individual ---
                const pagoData = {
                    idPago: 0,
                    idTipoPago: parseInt(formPago.elements['tipoPago'].value),
                    idTipoTarjeta: formPago.elements['tipoTarjeta'].value ? parseInt(formPago.elements['tipoTarjeta'].value) : 0,
                    idMoneda: parseInt(idMoneda),
                    monto: montoPago,
                    referencia: formPago.elements['referencia_pago'].value || '',
                    tipoCambio: paridadDelPago
                };

                // --- 2. Crear el PAYLOAD FINAL: un ARREGLO que contiene el objeto del pago ---
                // Esto coincide exactamente con el schema que proporcionaste: [ { ... } ]
                const pagosPayload = [pagoData];

                console.log("Enviando este payload:", pagosPayload);

                // --- 3. Definir el ENDPOINT que apunta directamente al impuesto ---
                // Usamos el ID del impuesto que ya tenemos guardado en el estado del modal.
                const idImpuesto = impuestoDefinidoData.idReserva || 0; // Asegúrate de que este ID sea correcto
                const endpoint = `/Reservas/${idImpuesto}/CheckIn`; // Endpoint lógico para esta acción

                // --- 4. Enviar la solicitud a la API ---
                const result = await api.post(endpoint, pagosPayload);
                console.log(result);

                if (!result) {
                    throw new Error(result.message || 'El servidor no pudo procesar el pago.');
                }

                // --- 5. Actualizar la interfaz tras el éxito ---
                showMessage('Pago registrado exitosamente.', 'success');

                const nuevoPagoConfirmado = await api.get(`/Pagos?idReservacion=${idImpuesto}`); // El API debería devolver el pago con su nuevo ID

                if (Array.isArray(datos.pagos) && datos.pagos.length > 0) {

                    currentPagosData = datos.pagos.map(pago => ({
                        idPago: pago.idPago || null,
                        idTipoPago: pago.idTipoPago || null,
                        idMoneda: pago.idMoneda || null,
                        monto: pago.monto || null,
                        referencia: pago.referencia || null,
                        fecha: pago.fecha || null,
                        tipoCambio: pago.tipoCambio || null
                    }));

                    cargarPagosRealizados(currentPagosData);
                    actualizarSaldos();
                }

                formPago.reset();
                document.getElementById('camposTarjetaCredito').style.display = 'none';

            } catch (error) {
                console.error("Error en el proceso de pago:", error);
                showMessage(`Error: ${error.message}`, 'error');
            }
        };
    }

    // --- Inicialización final ---
    document.addEventListener('DOMContentLoaded', function () {
        injectCSS();
        injectModalHTML();
        initModal();
    });

})();