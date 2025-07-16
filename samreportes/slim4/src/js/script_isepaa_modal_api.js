// public/js/script-modal.js
(function() {
    //para imprimir que pantalla hay que usar [config_tipo_pantalla] => multi 
    // API endpoint configuration

    // const API_BASE_URL = 'http://localhost:8080/samreportes/slim4/reservas';
    const API_BASE_URL = 'http://192.168.9.2:8084';
    const authUrl = 'http://192.168.9.2:8084/api/v2/Usuarios/Authenticate';

    // State variables to hold data for the active modal
    let currentReservaId = null;
    let currentReservaData = {};
    let impuestoDefinidoData = {};
    let pagosData = []; // Stores the list of payments for the current tax
    let currentTiposPagoData = {};
    let currentTiposTarjetaData = {};
    let currentMonedasData = {};

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

    function showMessage(message, type = 'error',show=1) {
        const modalMensaje = document.getElementById('modalReservaMensaje');
        modalMensaje.textContent = message;
        modalMensaje.className = type;
        if(show===1)
        {
            modalMensaje.style.display='block'; 
        }
        else
        {
            modalMensaje.style.display='none'; 
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

    // --- Core Logic Functions ---
    /**
 * Realiza una solicitud POST a la URL de autenticación.
 * @param {string} url - La URL del endpoint de autenticación.
 * @param {object} data - El objeto con los datos del usuario (e.g., email y contraseña).
 * @returns {Promise<object>} - La respuesta de la API en formato JSON.
 */
async function authenticateUser(url = '', data = {}) {
  try {
    const response = await fetch(url, {
      method: 'POST', // Especifica que el método es POST
      headers: {
        'Content-Type': 'application/json' // Indica que el cuerpo de la solicitud es JSON
      },
      body: JSON.stringify(data) // Convierte el objeto de datos de JavaScript a un string JSON
    });

    if (!response.ok) {
        // Si el servidor responde con un código de error (e.g., 400, 401, 500)
        // se lanza un error para ser capturado por el bloque catch.
        throw new Error(`Error en la solicitud: ${response.status}`);
    }

    return await response.json(); // Parsea la respuesta JSON y la devuelve
  } catch (error) {
    console.error('Hubo un problema con la operación de fetch:', error);
    // Opcionalmente, puedes manejar el error de una forma más específica o mostrar un mensaje al usuario.
    throw error; // Lanza el error para que el código que llama a la función pueda manejarlo.
  }
}


async function fetchDataAndPopulate(endpoint, token = null) {
    showMessage('Cargando datos de la reserva...', 'success');
    try {
        const headers = {
            'Content-Type': 'application/json'
        };

        // If a token is provided, add the Authorization header
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            method: 'GET', // Assuming GET, change if needed
            headers: headers
        });

        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        const apiResponse = await response.json();

        if (!apiResponse.success || !apiResponse.data.reserva) {
            throw new Error(apiResponse.message || "No se encontraron datos válidos de la reserva.");
        }
        
        // Store all data in state variables
        const { reserva, impuesto, tiposPagoData, tiposTarjetaData, monedasData, pagos } = apiResponse.data;
        currentReservaData = reserva;
        currentTiposPagoData = tiposPagoData || [];
        currentTiposTarjetaData = tiposTarjetaData || [];
        currentMonedasData = monedasData || [];
        pagosData = pagos || [];
        
        currentReservaId = currentReservaData.id_reservacion;

        // Populate general info
        document.getElementById('HabitatName').textContent = currentReservaData.nom_locacion || 'N/A';
        document.getElementById('confirmaId').textContent = currentReservaData.confirma || 'N/A';
        document.getElementById('pasajeroNombre').textContent = currentReservaData.nom_pasajero || 'N/A';
        document.getElementById('idLocacion').value = currentReservaData.id_locacion || '';
        
         if (currentReservaData.cod_pais && currentReservaData.cod_pais === 'MXQR') {
                showNoAplicaImpuesto('El visitante es Quintanaroense, no requiere agregar un impuesto', 'error');
                showMessage('','error',0);
                return;
               
         }

        
        // Check if tax is already defined
        else if (impuesto && (impuesto.id_reservacion && (parseInt(impuesto.adultos) > 0 || parseInt(impuesto.menores) > 0))) {
            impuestoDefinidoData = {
                idLocacion: parseInt(impuesto.id_locacion) || 0,
                adultos: parseInt(impuesto.adultos) || 0,
                menores: parseInt(impuesto.menores) || 0,
                tarifaUSD: parseFloat(impuesto.tarifa_usd) || 0,
                paridadMXN: parseFloat(impuesto.paridad) || 0,
                confirma_impuesto: impuesto.confirma_impuesto || 'N/A'
            };
        
            
            impuestoDefinidoData.totalUSD = (impuestoDefinidoData.adultos + impuestoDefinidoData.menores) * impuestoDefinidoData.tarifaUSD;
            impuestoDefinidoData.totalMXN = impuestoDefinidoData.totalUSD * impuestoDefinidoData.paridadMXN;
            showMessage('Impuesto previamente definido cargado.', 'success');
            return true; // Indicates tax exists
        } else {
             // Populate tax creation form with defaults from main reservation
            document.getElementById('adultos').value = currentReservaData.adultos || 0;
            document.getElementById('menores').value = currentReservaData.menores || 0;
            actualizarImpuestoPreview();
            showMessage('Datos de reserva cargados, Agregue el impuesto', 'success');
            return false; // Indicates tax does not exist
        }
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

        const tarifa = parseFloat(currentReservaData.tarifa_usd) || 0;
        const paridad = parseFloat(currentReservaData.paridad) || 0;
        
        const totalPersonas = adultos + menores;
        const montoUSDEstimado = totalPersonas * tarifa;
        const montoMXNEstimado = montoUSDEstimado * paridad;

        if (totalPersonas > 0) {
            previewDiv.innerHTML = `Personas para impuesto: ${totalPersonas}<br>
                                Tarifa: $${tarifa.toFixed(6)} USD por persona<br>
                                Paridad: $${paridad.toFixed(2)} MXN por USD<br>
                                <b>Estimado: $${montoUSDEstimado.toFixed(6)} USD / $${montoMXNEstimado.toFixed(2)} MXN</b>`;
        } else {
            previewDiv.innerHTML = 'Ingrese número de adultos y/o menores para calcular el impuesto.';
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
        
        // Populate payment form dropdowns
        poblarSelect(document.getElementById('tipoPago'), currentTiposPagoData, 'id_tipo_pago', 'nom_pago');
        poblarSelect(document.getElementById('monedaPago'), currentMonedasData, 'id_moneda', 'nom_moneda');
        
        // Default payment currency to USD if available
        const usdMoneda = currentMonedasData.find(m => m.cod_moneda === 'USD');
        if (usdMoneda) document.getElementById('monedaPago').value = usdMoneda.id_moneda;

        document.getElementById('seccionCrearImpuesto').style.display = 'none';
        document.getElementById('seccionGestionPago').style.display = 'block';
    }

    function cargarPagosRealizados() {
        const tablaPagosBody = document.getElementById('tablaPagos').querySelector('tbody');
        tablaPagosBody.innerHTML = '';
        
        if (pagosData.length > 0) {
            pagosData.forEach(pago => agregarPagoATabla(pago));
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
        fila.insertCell().textContent = pago.id_pago || 'N/A';
        const tipoPagoInfo = currentTiposPagoData.find(tp => tp.id_tipo_pago == pago.id_tipo_pago);
        fila.insertCell().textContent = tipoPagoInfo ? tipoPagoInfo.nom_pago : 'Desconocido';
        const monedaInfo = currentMonedasData.find(m => m.id_moneda == pago.id_moneda);
        fila.insertCell().textContent = monedaInfo ? monedaInfo.cod_moneda : 'N/A';
        fila.insertCell().textContent = parseFloat(pago.monto || 0).toFixed(2);
        fila.insertCell().textContent = pago.referencia_pago || '';
        fila.insertCell().textContent = pago.fecha_creacion ? new Date(pago.fecha_creacion).toLocaleDateString() : 'N/A';
    }
    
    function actualizarSaldos() {
        const paridad_USD_a_MXN = impuestoDefinidoData.paridadMXN;
        let totalPagadoUSD = 0;

        pagosData.forEach(pago => {
            const monedaDelPago = currentMonedasData.find(m => m.id_moneda == pago.id_moneda);
            if (!monedaDelPago || !monedaDelPago.paridad) return;

            const paridad_pago_a_MXN = parseFloat(monedaDelPago.paridad);
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
    }
    
    function actualizarMontoPago() {
        const montoInput = document.getElementById('monto');
        const selectedMonedaId = document.getElementById('monedaPago').value;
        const saldoPendienteUSD = parseFloat(document.getElementById('saldoPorPagarUSD').textContent) || 0;
        
        if (!selectedMonedaId || saldoPendienteUSD <= 0) {
            montoInput.value = "0.00";
            return;
        }

        const monedaSeleccionada = currentMonedasData.find(m => m.id_moneda == selectedMonedaId);
        if (!monedaSeleccionada || !monedaSeleccionada.paridad) {
            montoInput.value = "0.00";
            return;
        }

        const paridad_USD_a_MXN = impuestoDefinidoData.paridadMXN;
        const paridad_seleccionada_a_MXN = parseFloat(monedaSeleccionada.paridad);

        let montoCalculadoFinal;
        if (monedaSeleccionada.cod_moneda === 'USD') {
            montoCalculadoFinal = saldoPendienteUSD;
        } else {
            const saldoPendienteMXN = saldoPendienteUSD * paridad_USD_a_MXN;
            montoCalculadoFinal = (paridad_seleccionada_a_MXN > 0) ? (saldoPendienteMXN / paridad_seleccionada_a_MXN) : 0;
        }
        
        montoInput.value = (montoCalculadoFinal > 0 ? montoCalculadoFinal : 0).toFixed(2);
    }
    
    // --- Event Listeners and Initialization ---

    function initModal() {
        const modal = document.getElementById('miModalReserva');
        const spanCerrar = modal.querySelector('.modal-reserva-cerrar');
        const formReserva = modal.querySelector('#formReserva');
        const formPago = modal.querySelector('#formPago');

        // Event listeners for dynamic UI updates
        document.getElementById('adultos').addEventListener('input', actualizarImpuestoPreview);
        document.getElementById('menores').addEventListener('input', actualizarImpuestoPreview);
        document.getElementById('monedaPago').addEventListener('change', actualizarMontoPago);
        document.getElementById('tipoPago').addEventListener('change', function () {
            const divCamposTarjeta = document.getElementById('camposTarjetaCredito');
            const selectTipoTarjeta = document.getElementById('tipoTarjeta');
            const tipoSeleccionado = currentTiposPagoData.find(tp => tp.id_tipo_pago == this.value);
            // Check if 'referencia' is 1 (or other indicator for requiring card details)
            const requiereTarjeta = tipoSeleccionado && parseInt(tipoSeleccionado.referencia) === 1;

            if (requiereTarjeta) {
                divCamposTarjeta.style.display = 'grid';
                poblarSelect(selectTipoTarjeta, currentTiposTarjetaData, 'id_tipo_tarjeta', 'nom_tarjeta', 'Seleccione Tarjeta');
                selectTipoTarjeta.required = true;
            } else {
                divCamposTarjeta.style.display = 'none';
                selectTipoTarjeta.required = false;
                selectTipoTarjeta.innerHTML = '';
            }
        });

        // Main trigger to open the modal
        document.querySelectorAll('.btn-modal-reserva-trigger').forEach(button => {
            button.addEventListener('click', async function () {

                // Reset UI and state
                formReserva.reset();
                formPago.reset();
                document.getElementById('modalReservaMensaje').style.display = 'none';
                document.getElementById('seccionCrearImpuesto').style.display = 'none';
                document.getElementById('seccionGestionPago').style.display = 'none';
                impuestoDefinidoData = {};
                currentReservaData = {};
                pagosData = [];

                modal.style.display = 'flex';
                document.getElementById('modalReservaTitulo').textContent = this.dataset.modalTitle || 'Gestión de Impuesto ISEPAAA';
                document.getElementById('sessionUser').value = this.dataset.sessionUser || 0;
                document.getElementById('altaIdLocacion').value = this.dataset.altaIdLocacion || 0;
                const apiEndpoint = this.dataset.apiEndpoint;
                if (!apiEndpoint) {
                    showMessage('Error: No se especificó el endpoint para la reserva.', 'error');
                    return;
                }

                let userData = {
                    username: 'sistemas',
                    password: 't1c9gvd$$'
                    // Agrega aquí cualquier otro campo que tu API requiera para la autenticación
                };

                // 3. Llama a la función y maneja la respuesta.
                let token = null;
                authenticateUser(authUrl, userData)
                    .then(data => {
                        console.log('Autenticación exitosa:', data);
                        token = data.token; // Asumiendo que la respuesta contiene un token
                    })
                    .catch(error => {
                        console.error('Error en la autenticación:', error);
                        // Aquí puedes manejar el error, por ejemplo, mostrar un mensaje al usuario.
                    });

                const impuestoExiste = await fetchDataAndPopulate(apiEndpoint, token);

                if (impuestoExiste) {
                    mostrarGestionImpuestoUI();
                } else {
                    document.getElementById('seccionCrearImpuesto').style.display = 'none';
                    document.getElementById('seccionGestionPago').style.display = 'none';
                }
            });
        });

        // Close modal events
        spanCerrar.onclick = function () { modal.style.display = 'none'; }
        // window.onclick = function(event) { if (event.target == modal) { modal.style.display = 'none'; } }

        // Form Submission for creating the tax
        formReserva.onsubmit = async function (event) {
            event.preventDefault();
            showMessage('Procesando impuesto...', 'success');

            const adultos = parseInt(formReserva.elements['adultos'].value) || 0;
            const menores = parseInt(formReserva.elements['menores'].value) || 0;
            const idLocacion = parseInt(formReserva.elements['idLocacion'].value) || 0;
            const idSessionUser = parseInt(formReserva.elements['sessionUser'].value) || 0;
            const altaIdLocacion = parseInt(formReserva.elements['altaIdLocacion'].value) || 0;

            if (adultos === 0 && menores === 0) {
                showMessage('Debe ingresar al menos un adulto o menor.', 'error');
                return;
            }
            if (currentReservaData.cod_pais === 'MXQR') {
                showMessage('El visitante es Quintanaroense, "no requiere impuesto"', 'error');
                return;
            }
            if (idSessionUser === 0) {
                showMessage('No fue posible obtener la sesión del usuario, actualice la página.', 'error');
                return;
            }
            if (idLocacion === 0) {
                showMessage('No fue posible obtener el habitat de la reserva, actualice la página.', 'error');
                return;
            }

            const datosImpuestoParaApi = {
                id_reservacion: currentReservaId,
                uniqid_reserva: currentReservaData.uniqid_reserva,
                id_locacion: idLocacion,
                alta_id_locacion: altaIdLocacion,
                fecha_servicio: currentReservaData.fecha_servicio,
                id_pasajero: currentReservaData.id_pasajero,
                pax: currentReservaData.adultos,
                paxn: currentReservaData.menores,
                pax_finales: adultos,
                paxn_finales: menores,
                precio: currentReservaData.tarifa_usd,
                precion: currentReservaData.tarifa_usd,
                id_usuario: idSessionUser,
                paridad: currentReservaData.paridad,
                id_servicio: currentReservaData.id_servicio_tarifa_usd,
                cod_pais: currentReservaData.cod_pais

                // The API should calculate and apply these values based on its own logic
            };

            try {
                const response = await fetch(`${API_BASE_URL}/${currentReservaId}/crear_impuesto`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datosImpuestoParaApi)
                });
                const result = await response.json();

                if (!response.ok || !result.success) throw new Error(result.message || "Error al guardar el impuesto.");

                showMessage(result.message || 'Impuesto guardado exitosamente.', 'success');

                // Refresh all data from the server to ensure consistency
                const impuestoExiste = await fetchDataAndPopulate(document.querySelector('.btn-modal-reserva-trigger[data-api-endpoint*="' + currentReservaId + '"]').dataset.apiEndpoint);
                if (impuestoExiste) {
                    mostrarGestionImpuestoUI();
                }

            } catch (error) {
                showMessage(`Error: ${error.message}`, 'error');
                console.error('Error al crear impuesto:', error);
            }
        }

        // Form Submission for adding a payment (you would complete this part)
        formPago.onsubmit = async function (event) {
            event.preventDefault();
            showMessage('Procesando pago... (funcionalidad no implementada)', 'success');
            // This is where you would add the fetch call to your API endpoint for creating a payment.
            // After a successful payment, you would:
            // 1. Fetch the updated payment list or add the new payment to the `pagosData` array.
            // 2. Call `cargarPagosRealizados()` to refresh the table and balances.
            // Example:
            // const newPayment = await api.createPayment(...);
            // pagosData.push(newPayment);
            // cargarPagosRealizados(); // This will update the table and all balances.
        }
    }

    // --- DOM Ready ---
    document.addEventListener('DOMContentLoaded', function () {
        injectCSS();
        injectModalHTML();
        initModal();
    });
})();