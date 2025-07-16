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
            // CAMBIO: La validación de estatus 200 (response.ok) es la fuente principal de verdad.
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: 'Error sin detalles.' }));
                throw new Error(`Error HTTP ${response.status}: ${errorData.errors || response.statusText}`);
            }
            // Si la respuesta es exitosa pero no tiene cuerpo (ej. un 204 No Content), evitamos un error de parseo.
            const responseText = await response.text();
            return responseText ? JSON.parse(responseText) : {};

        } catch (error) {
            console.error(`Error en la solicitud a ${method} ${endpoint}:`, error);
            throw error;
        }
    }

    get(endpoint, requiresAuth = true) { return this._request(endpoint, 'GET', null, requiresAuth); }
    post(endpoint, body, requiresAuth = true) { return this._request(endpoint, 'POST', body, requiresAuth); }
}

// Bloque autoejecutable para la lógica del modal
(function () {
    // --- Configuración y Estado Global ---
    const API_BASE_URL = 'http://192.168.9.2:8084/api/v2';
    const AUTH_ENDPOINT = '/Usuarios/Authenticate';
    const IDISEPAAA_ = 10762040; // ID de servicio para el impuesto

    const api = new ApiService(API_BASE_URL);

    // Variables de estado
    let currentReservaData = {};
    let currentImpuestoData = {};
    let currentPagosData = [];
    let currentPrecioIsepaaaData = {};
    let fullCatalogData = {
        tiposPago: [],
        tiposTarjeta: [],
        monedas: [],
        tiposCambio: []
    };
    let impuestoDefinidoData = {};
    let currentMaximoPersonas = 0;
    let currentSelectedTipoPagoTarjeta = 0;
    const idsTiposPagoPermitidos = [1, 2]; // Ejemplo: Efectivo y Tarjeta

    // --- Inyección de HTML y CSS (sin cambios) ---
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

    // --- Funciones Auxiliares de UI ---
    function showMessage(message, type = 'info', show = true) {
        const modalMensaje = document.getElementById('modalReservaMensaje');
        modalMensaje.textContent = message;
        modalMensaje.className = `modal-mensaje ${type}`;
        modalMensaje.style.display = show ? 'block' : 'none';
    }

    /**
     * REFACTORIZADO: Controla qué secciones del modal se muestran.
     * @param {'loading' | 'crear' | 'gestionar' | 'noAplica'} state - El estado a mostrar.
     * @param {string} [message] - Un mensaje opcional para el estado 'noAplica'.
     */
    function displayState(state, message = '') {
        const seccionCrear = document.getElementById('seccionCrearImpuesto');
        const seccionGestion = document.getElementById('seccionGestionPago');
        const noAplicaPreview = document.getElementById('noAplicaImpuestoPreview');

        seccionCrear.style.display = 'none';
        seccionGestion.style.display = 'none';
        noAplicaPreview.style.display = 'none';
        showMessage('', 'info', false); // Ocultar mensajes anteriores

        switch (state) {
            case 'loading':
                showMessage('Cargando...', 'info');
                break;
            case 'crear':
                seccionCrear.style.display = 'block';
                showMessage('Datos de reserva cargados. Agregue el impuesto.', 'success');
                break;
            case 'gestionar':
                seccionGestion.style.display = 'block';
                showMessage(message || 'La reserva ya tiene un impuesto definido.', 'success');
                break;
            case 'noAplica':
                noAplicaPreview.textContent = message;
                noAplicaPreview.style.display = 'block';
                break;
        }
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
     */
    async function cargarDatosCompletosDeReserva(endpointPrincipal) {
        showMessage('Obteniendo datos de la reserva...', 'info');
        const reserva = await api.get(endpointPrincipal);
        if (!reserva) {
            throw new Error("No se encontraron datos válidos de la reserva.");
        }
        console.log("Datos de reserva principal:", reserva);
        currentReservaData = reserva;

        if (!currentReservaData.aplicaISEPAAA) {
            displayState('noAplica', 'Esta reserva no aplica para el impuesto.');
            return false;
        }

        // CAMBIO: Validaciones tempranas para detener el flujo si no aplica el impuesto.
        if (currentReservaData.idServicio === IDISEPAAA_) {
            displayState('noAplica', 'La reserva es un impuesto ISEPAAA no necesita realizar cambios adicionales.');
            return false;
        }
        if (currentReservaData.codigoPais === 'MXQR') {
            displayState('noAplica', 'El visitante es Quintanarroense, no requiere agregar un impuesto.');
            return false;
        }

        // Si aplica, obtenemos el resto de la información.
        showMessage('Cargando catálogos y detalles del impuesto...', 'info');
        const idReservaImpuesto = parseInt(reserva.idISEPAAA) || 0;
        const fecha = new Date().toISOString().split('T')[0];

        const [impuesto, precio, pagos, tiposCambio, tiposPago, tiposTarjeta, monedas] = await Promise.all([
            obtenerReserva(idReservaImpuesto),
            obtenerPrecioIsepaaa(IDISEPAAA_, reserva.idHabitat, fecha),
            obtenerPagos(idReservaImpuesto),
            obtenerTiposDeCambio(),
            obtenerTiposDePago(),
            obtenerTiposDeTarjeta(),
            obtenerMonedas()
        ]);

        // Guardar datos en el estado global
        currentImpuestoData = impuesto || {};
        currentPagosData = pagos && Array.isArray(pagos) ? pagos : [];
        currentPrecioIsepaaaData = precio || {};
        fullCatalogData = { tiposCambio, tiposPago, tiposTarjeta, monedas };
        currentMaximoPersonas = (parseInt(reserva.adultos) || 0) + (parseInt(reserva.menores) || 0);

        return true; // Indica que la carga fue exitosa y se debe continuar.
    }

    // --- Lógica de UI y Manipulación de Datos ---

    function poblarDatosBasicosModal() {
        document.getElementById('HabitatName').textContent = currentReservaData.habitat || 'N/A';
        document.getElementById('confirmaId').textContent = currentReservaData.confirma || 'N/A';
        document.getElementById('pasajeroNombre').textContent = currentReservaData.nombre || 'N/A';
    }

    function prepararFormularioCreacion() {
        const form = document.getElementById('formReserva');
        form.elements['adultos'].value = currentReservaData.adultos || 0;
        form.elements['menores'].value = currentReservaData.menores || 0;
        actualizarImpuestoPreview();
        displayState('crear');
    }

    function prepararGestionDeImpuesto(mensaje) {
        const usdTipoCambio = fullCatalogData.tiposCambio.find(tc => tc.moneda === 'USD');
        const paridadMXN = usdTipoCambio ? parseFloat(usdTipoCambio.monto) : 0;

        impuestoDefinidoData = {
            adultos: parseInt(currentImpuestoData.adultos) || 0,
            menores: parseInt(currentImpuestoData.menores) || 0,
            tarifaUSD: parseFloat(currentPrecioIsepaaaData.precioAdulto) || 0,
            paridadMXN: paridadMXN,
            confirma_impuesto: currentImpuestoData.confirma || 'N/A',
            idReserva: currentImpuestoData.idReservacion || 0,
        };
        impuestoDefinidoData.totalUSD = (impuestoDefinidoData.adultos + impuestoDefinidoData.menores) * impuestoDefinidoData.tarifaUSD;
        impuestoDefinidoData.totalMXN = impuestoDefinidoData.totalUSD * impuestoDefinidoData.paridadMXN;

        // Poblar UI
        document.getElementById('impGenConfirmaImpuesto').textContent = impuestoDefinidoData.confirma_impuesto;
        document.getElementById('impGenAdultos').textContent = impuestoDefinidoData.adultos;
        document.getElementById('impGenMenores').textContent = impuestoDefinidoData.menores;
        document.getElementById('impGenTarifaUSD').textContent = impuestoDefinidoData.tarifaUSD.toFixed(2);
        document.getElementById('impGenParidad').textContent = impuestoDefinidoData.paridadMXN.toFixed(2);
        document.getElementById('saldoTotalUSD').textContent = impuestoDefinidoData.totalUSD.toFixed(2);
        document.getElementById('saldoTotalMXN').textContent = impuestoDefinidoData.totalMXN.toFixed(2);

        // Poblar selects del formulario de pago
        const tiposPagoFiltrados = fullCatalogData.tiposPago.filter(tp => idsTiposPagoPermitidos.includes(tp.id));
        poblarSelect(document.getElementById('tipoPago'), tiposPagoFiltrados, 'id', 'value');
        poblarSelect(document.getElementById('monedaPago'), fullCatalogData.monedas, 'id', 'name');
        
        cargarPagosRealizados(); // Carga pagos y actualiza saldos
        displayState('gestionar', mensaje);
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

    function cargarPagosRealizados() {
        console.log("Cargando pagos realizados...", currentPagosData);
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
        const tipoPagoInfo = fullCatalogData.tiposPago.find(tp => tp.id == pago.idTipoPago);
        fila.insertCell().textContent = tipoPagoInfo ? tipoPagoInfo.value : 'Desconocido';
        const monedaInfo = fullCatalogData.monedas.find(m => m.id == pago.idMoneda);
        fila.insertCell().textContent = monedaInfo ? monedaInfo.value : 'N/A';
        fila.insertCell().textContent = parseFloat(pago.monto || 0).toFixed(2);
        fila.insertCell().textContent = pago.referencia || '';
        fila.insertCell().textContent = pago.fecha ? new Date(pago.fecha).toLocaleString() : 'N/A';
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

    function getPaymentContext() {
    const selectedMonedaId = document.getElementById('monedaPago').value;
    const tipoPagoSelectValue = document.getElementById('tipoPago').value;

    const monedaSeleccionada = fullCatalogData.tiposCambio.find(m => m.idMoneda == selectedMonedaId);

    const isEfectivo = tipoPagoSelectValue === '1'; // Assuming '1' is the value for 'Efectivo'
    const monedaCode = monedaSeleccionada ? monedaSeleccionada.moneda : null;

    return { isEfectivo, monedaCode };
}

    function handleMontoInput() {
    const montoInput = document.getElementById('monto');
    let value = montoInput.value;
    const { isEfectivo, monedaCode } = getPaymentContext();

    if (isEfectivo && monedaCode === 'USD') {
        // If cash (efectivo) and USD, only allow digits. Strip out everything else.
        value = value.replace(/[^0-9]/g, '');
        montoInput.value = value;
    } else {
        // For other cases (card or non-USD cash), allow digits and one decimal point.
        // This regex allows numbers and a single decimal point.
        // It prevents multiple decimal points and other non-numeric characters.
        value = value.replace(/[^0-9.]/g, ''); // Remove non-digit/non-dot characters
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join(''); // Keep only the first decimal point
        }
        montoInput.value = value;
    }
}

/**
 * Handles blur event on the amount field to apply final rounding and formatting.
 */
function handleMontoBlur() {
    const montoInput = document.getElementById('monto');
    let value = parseFloat(montoInput.value);
    const { isEfectivo, monedaCode } = getPaymentContext();

    if (isNaN(value)) {
        montoInput.value = "0.00"; // Default to 0.00 if input is not a valid number
        return;
    }

    if (isEfectivo && monedaCode === 'USD') {
        // Round up to the nearest whole number for cash USD
        montoInput.value = Math.ceil(value).toFixed(0);
    } else {
        // For other payment types, format to two decimal places
        montoInput.value = value.toFixed(2);
    }
}
    
   
    function actualizarMontoPago() {
        const montoInput = document.getElementById('monto');
        const selectedMonedaId = document.getElementById('monedaPago').value;
        const saldoPendienteUSD = parseFloat(document.getElementById('saldoPorPagarUSD').textContent) || 0;
        const tipoPagoSelect = document.getElementById('tipoPago').value;
        console.log("montoInput:", montoInput);
        console.log("Saldo pendiente USD:", saldoPendienteUSD);
        console.log("Selected Moneda ID:", selectedMonedaId);

        if (!selectedMonedaId || saldoPendienteUSD <= 0) {
            montoInput.value = "0.00";
            return;
        }

        
        const monedaSeleccionada = fullCatalogData.tiposCambio.find(m => m.idMoneda == selectedMonedaId) || { monto: 1, moneda: 'MXN' };
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

        console.log("Monto calculado final:", tipoPagoSelect);

        const isEfectivo = tipoPagoSelect === '1'; // This line needs `tipoPago` to exist

    // Check if it's cash, in USD (or the relevant target currency for rounding), and has decimals
    if (isEfectivo && monedaSeleccionada.moneda === 'USD' && montoCalculadoFinal % 1 !== 0) {
        // If it's cash and USD and has decimals, round up to the next whole number
        montoCalculadoFinal = Math.ceil(montoCalculadoFinal);
        montoInput.value = (montoCalculadoFinal > 0 ? montoCalculadoFinal : 0).toFixed(0);
        montoInput.setAttribute('step', '1');
    }
    else{
            montoInput.value = (montoCalculadoFinal > 0 ? montoCalculadoFinal : 0).toFixed(2);
            montoInput.setAttribute('step', 'any');
    }
    handleMontoInput();

       
    }

    // --- Handlers de Eventos y Formularios ---

    /**
     * REFACTORIZADO: Lógica para abrir y poblar el modal.
     */
    async function handleOpenModal(button) {
        displayState('loading');
        document.getElementById('miModalReserva').style.display = 'flex';
        document.getElementById('modalReservaTitulo').textContent = button.dataset.modalTitle || 'Gestión de Impuesto ISEPAAA';

        const user = button.dataset.sessionUser;
        const pass = button.dataset.sessionPass;
        const apiEndpoint = button.dataset.apiEndpoint;
        
        // CAMBIO: Validar credenciales antes de continuar.
        if (!user || !pass) {
            return showMessage('Error: Credenciales de autenticación no encontradas en el botón.', 'error');
        }
        if (!apiEndpoint) {
            return showMessage('Error: No se especificó el endpoint para la reserva.', 'error');
        }
        
        try {
            await api.authenticate(AUTH_ENDPOINT, { username: user, password: pass });
            showMessage('Autenticación exitosa.', 'success');

            const debeContinuar = await cargarDatosCompletosDeReserva(apiEndpoint);
            if (!debeContinuar) return; // Se detiene si la validación temprana falló

            poblarDatosBasicosModal();

            // Decidir qué UI mostrar: crear impuesto o gestionar uno existente.
            const impuestoExiste = currentImpuestoData && currentImpuestoData.idReservacion;
            if (impuestoExiste) {
                prepararGestionDeImpuesto('El impuesto se cargó correctamente.');
            } else {
                prepararFormularioCreacion();
            }

        } catch (error) {
            console.error('Error en el proceso de apertura del modal:', error);
            showMessage(`Error: ${error.message}`, 'error');
        }
    }

    /**
     * REFACTORIZADO: Lógica para crear un nuevo impuesto.
     */
    async function handleCreateImpuestoSubmit(event) {
        event.preventDefault();
        showMessage('Procesando impuesto...', 'info');

        const form = event.target;
        const adultos = parseInt(form.elements['adultos'].value) || 0;
        const menores = parseInt(form.elements['menores'].value) || 0;

        if (adultos === 0 && menores === 0) {
            return showMessage('Debe ingresar al menos un adulto o menor.', 'error');
        }
        
        const payload = {
            fecha: currentReservaData.fecha,
            idHabitat: currentReservaData.idHabitat,
            idServicio: IDISEPAAA_,
            adultos: adultos,
            menores: menores,
            idPasajero: currentReservaData.idPasajero,
            nombre: currentReservaData.nombre,
            uniqidReserva: currentReservaData.uniqIdReserva,
            idMedioVenta: currentReservaData.idMedioVenta,
            // Valores fijos o por defecto para la creación del impuesto
            idAgencia: 24003, idRepresentante: 0, idClasificacion: 2, idSubclasificacion: 15, idAutorizo: 0, tipoComentario: 1, porcentajeIVA: 0
        };

        try {
            const result = await api.post('/Reservas', payload);
            if (!result || !result.idReservacion) {
                 throw new Error(result.errors ? result.errors.join(", ") : 'La API no devolvió un ID de reservación válido.');
            }

            // Refrescar datos después de la creación
            currentImpuestoData = await obtenerReserva(result.idReservacion);
            currentPagosData = []; // No habrá pagos para un impuesto nuevo
            
            prepararGestionDeImpuesto('Impuesto guardado exitosamente.');

        } catch (error) {
            console.error("Error al crear el impuesto:", error);
            showMessage(error.message, 'error');
        }
    }

    /**
     * REFACTORIZADO: Lógica para agregar un nuevo pago.
     */
    async function handlePaymentSubmit(event) {
        event.preventDefault();
        showMessage('Procesando pago...', 'info');

        const form = event.target;
        const monto = parseFloat(form.elements['monto'].value) || 0;
        if (monto <= 0) {
            return showMessage('El monto del pago debe ser mayor a cero.', 'error');
        }

        try {
            const idMoneda = form.elements['monedaPago'].value;
            const tipoCambioInfo = fullCatalogData.tiposCambio.find(tc => tc.idMoneda == idMoneda);
            
            const pagoPayload = [{
                idPago: 0,
                idTipoPago: parseInt(form.elements['tipoPago'].value),
                idTipoTarjeta: form.elements['tipoTarjeta'].value ? parseInt(form.elements['tipoTarjeta'].value) : 0,
                idMoneda: parseInt(idMoneda),
                monto: monto,
                referencia: form.elements['referencia_pago'].value || '',
                tipoCambio: tipoCambioInfo ? tipoCambioInfo.monto : 1.0
            }];

            const idImpuesto = impuestoDefinidoData.idReserva;
            const endpoint = `/Reservas/${idImpuesto}/CheckIn`;

            // CAMBIO: Confiar en que si api.post no falla, la operación fue exitosa (HTTP 200)
            await api.post(endpoint, pagoPayload);
            
            showMessage('Pago registrado exitosamente. Actualizando...', 'success');

            // CAMBIO: Refrescar la lista de pagos de forma fiable
            const pagosActualizados = await obtenerPagos(idImpuesto);
            currentPagosData = pagosActualizados && Array.isArray(pagosActualizados) ? pagosActualizados : [];

            cargarPagosRealizados(); // Esto refresca la tabla y los saldos
            form.reset();
            document.getElementById('camposTarjetaCredito').style.display = 'none';

        } catch (error) {
            console.error("Error en el proceso de pago:", error);
            showMessage(`Error: ${error.message}`, 'error');
        }
    }


    /**
     * REFACTORIZADO: Inicializa todos los listeners y la configuración del modal.
     */
    function initModal() {
        const modal = document.getElementById('miModalReserva');
        const spanCerrar = modal.querySelector('.modal-reserva-cerrar');
        
        // Asignación de handlers a los formularios
        modal.querySelector('#formReserva').onsubmit = handleCreateImpuestoSubmit;
        modal.querySelector('#formPago').onsubmit = handlePaymentSubmit;

        // Listeners de UI
        document.getElementById('adultos').addEventListener('input', actualizarImpuestoPreview);
        document.getElementById('menores').addEventListener('input', actualizarImpuestoPreview);
        document.getElementById('monedaPago').addEventListener('change', actualizarMontoPago);

        document.getElementById('tipoPago').addEventListener('change', function () {
            const divCamposTarjeta = document.getElementById('camposTarjetaCredito');
            const selectTipoTarjeta = document.getElementById('tipoTarjeta');
            const selectMoneda = document.getElementById('monedaPago');
            const montoInput = document.getElementById('monto').value = '0.00';
            const tipoSeleccionado = fullCatalogData.tiposPago.find(tp => tp.id == this.value);
            const requiereTarjeta = tipoSeleccionado && parseInt(tipoSeleccionado.referencia) === 1;
            

            if (requiereTarjeta) {
                divCamposTarjeta.style.display = 'grid';
                currentSelectedTipoPagoTarjeta = 1;



                poblarSelect(selectTipoTarjeta, fullCatalogData.tiposTarjeta, 'id', 'value', 'Seleccione Tarjeta');

                 const monedasFiltradas = fullCatalogData.monedas.filter(moneda => moneda.id === 1 || moneda.id === 2);
        poblarSelect(selectMoneda, monedasFiltradas, 'id', 'name', 'Seleccione Moneda');


                selectTipoTarjeta.required = true;
            } else {
                currentSelectedTipoPagoTarjeta = 0;
                divCamposTarjeta.style.display = 'none';
                selectTipoTarjeta.required = false;
                selectTipoTarjeta.innerHTML = '';

                // **MOSTRAR TODAS LAS MONEDAS CUANDO NO REQUIERE TARJETA**
        poblarSelect(selectMoneda, fullCatalogData.monedas, 'id', 'name', 'Seleccione Moneda');
            }
            // CAMBIO: Se eliminó el bloqueo de la moneda
            document.getElementById('monedaPago').disabled = false;
        });

        // Listener para abrir el modal
        document.querySelectorAll('.btn-modal-reserva-trigger').forEach(button => {
            button.addEventListener('click', () => handleOpenModal(button));
        });

        spanCerrar.onclick = () => { modal.style.display = 'none'; };
    }

    // --- Punto de Entrada ---
    // document.addEventListener('DOMContentLoaded', function () {
    //     injectCSS();       // Descomentar si el CSS no está en otro archivo
    //     injectModalHTML(); // Descomentar si el HTML no está en la página principal
    //     initModal();
    // });


    // --- Punto de Entrada ---
document.addEventListener('DOMContentLoaded', function () {
    // 1. Tareas de inicialización que ya tenías
    injectCSS();
    injectModalHTML(); // <-- Clave: esto crea los campos del formulario
    initModal();

    // 2. Ahora que el HTML del modal existe, asigna los eventos a los campos
    const montoInput = document.getElementById('monto');
    const tipoPagoSelect = document.getElementById('tipoPago');
    const monedaPagoSelect = document.getElementById('monedaPago');

    // Verificamos que los elementos se encontraron antes de asignarles eventos
    if (montoInput && tipoPagoSelect && monedaPagoSelect) {
        // Cuando el usuario ESCRIBE en el campo de monto
        montoInput.addEventListener('input', handleMontoInput);

        // Cuando el usuario HACE CLIC FUERA del campo de monto
        montoInput.addEventListener('blur', handleMontoBlur);

        // Cuando el usuario CAMBIA el tipo de pago o la moneda
        tipoPagoSelect.addEventListener('change', actualizarMontoPago);
        monedaPagoSelect.addEventListener('change', actualizarMontoPago);

        // Ejecutar una vez para establecer el valor inicial correcto
        actualizarMontoPago();
    } else {
        console.error("Error: No se encontraron los campos del formulario. Verifica que los IDs (monto, tipoPago, monedaPago) son correctos y existen en el HTML inyectado.");
    }
});

})();