
(function() {

    const API_BASE_URL = 'http://localhost:8080/samreportes/slim4/reservas';

    let currentReservaId = null;
    let currentReservaData = {}; // Datos de la reserva principal
    let impuestoDefinidoData = {}; // Datos del impuesto si ya está definido en la carga inicial o después de crearlo
    // let pagosExistentesData = []; // aun no llego a esa parte Para almacenar pagos cargados inicialmente (opcional, si la API los devuelve juntos)
    let currentTiposPagoData = {}; //ahora sale de la base de datos
    let currentTiposTarjetaData = {}; //ahora sale de la base de datos
    let currentMonedasData = {}; // ahora sale de la base de datos


   
    const DEFAULT_USD_IMPUESTO_ISEPAAA = 2.5; // por lo pronto aun lo conservo al final igual y lo pongo en cero
    const DEFAULT_USD_PARIDAD_MXN = 20.0; //aun lo conservo al final igual y lo pongo en cero

    const modalHTML = `
        <div id="miModalReserva" class="modal-reserva-overlay" style="display:none;">
            <div class="modal-reserva-contenido">
                <span class="modal-reserva-cerrar">&times;</span>
                <h4 id="modalReservaTitulo">Gestión de Impuesto ISEPAAA</h4>
                
                <form id="formReserva">
                    <label><strong id="confirmaid"></strong></label>
                    <input type="hidden" id="reservaId" name="reservaId">
                    <input type="hidden" id="uniqid_reserva" name="uniqid_reserva">

                    <label for="nom_pasajero">Nombre Pasajero:</label>
                    <input type="text" id="nom_pasajero" name="nom_pasajero" disabled>

                    <label for="adultos">Adultos (para impuesto):</label>
                    <input type="number" min="0" id="adultos" name="adultos" required>

                    <label for="menores">Menores (para impuesto):</label>
                    <input type="number" min="0" id="menores" name="menores" required>
                    
                    <div id="impuestoPreview" style="margin-top: 10px; padding: 10px; background-color: #e9ecef; border: 1px dashed #ccc; border-radius: 4px; font-size: 0.9em; line-height: 1.4;">
                        Calculando impuesto...
                    </div>

                    <button type="submit" id="btnSubmitReserva">Guardar y Calcular Impuesto</button>
                </form>

                <div id="seccionImpuestoGenerado" class="seccionImpuestoCreado" style="display:none; margin-top: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                    <h4>Detalles del Impuesto ISEPAAA</h4>
                    <p><strong>Reserva Principal Confirma:</strong> <span id="impGenConfirmaOriginal"></span></p>
                    <p><strong>Confirma Impuesto:</strong> <span id="impGenConfirmaImpuesto"></span></p>
                    <p><strong>Adultos: </strong> <span id="impGenAdultos"> </span><strong> Menores: </strong> <span id="impGenMenores"></span> <strong>Paridad (USD/MXN):</strong> $<span id="impGenParidad"></span></p>
                    <p><strong>Tarifa por Persona (USD):</strong> $<span id="impGenTarifaUSD"></span></p>
                    <p style="font-weight: bold;"><strong>Total Impuesto (USD):</strong> $<span id="impGenTotalUSD"></span> <strong>/ (MXN):</strong> $<span id="impGenTotalMXN"></span></p>
                </div>

                <div id="seccionPago" style="display:none; margin-top: 20px;">
                 <h3>Agregar Pago</h3>
                <div class="seccionImpuestoCreado" style="margin-top: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                <h4 style="font-weight: bold;">Total a Pagar (USD): <strong>$<span id="impGenPagarTotalUSD"></span></strong> / (MXN):<strong>$<span id="impGenPagarTotalMXN"></span></strong></h5>
                </div>
                   
                    <form id="formPago">
                        <label for="tipoPago">Forma de Pago:</label>
                        <select id="tipoPago" name="id_tipo_pago" required></select>
                        <div id="camposTarjetaCredito" style="display:none; margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <label for="tipoTarjeta">Tarjeta:</label>
                            <select id="tipoTarjeta" name="id_tipo_tarjeta"></select>
                        </div>
                        <label for="monedaPago">Moneda del Pago:</label>
                        <select id="monedaPago" name="id_moneda" required></select>
                        <label for="monto">Monto a Pagar:</label>
                        <input type="text" inputmode="numeric" id="monto" name="monto" required style="background-color: #e9ecef;">
                        <div id="camposReferenciaTarjeta" style="display:none; margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                        <label for="referencia_pago">Referencia (Opcional):</label>
                            <input type="text" id="referencia_pago" name="referencia_pago">
                        </div>
                        <button type="submit" id="btnSubmitPago">Confirmar Pago</button>
                    </form>
                    <div class="seccionImpuestoCreado" style="margin-top: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                        <h4 style="font-weight: bold;">Por Pagar (USD): <strong>$<span id="impGenPorPagarTotalUSD">0</span></strong> / (MXN):<strong>$<span id="impGenPorPagarTotalMXN">0</span></strong></h4>
                    </div>
                </div>
                
                <div id="seccionPagosRealizados" style="display:none; margin-top: 20px;">
                    <h3>Pagos Realizados</h3>
                    <table id="tablaPagos" class="tabla-pagos-reserva">
                        <thead>
                            <tr><th>ID Pago</th><th>Forma Pago</th><th>Moneda</th><th>Monto</th><th>Referencia</th><th>Fecha</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="modalReservaMensaje" style="margin-top:15px;"></div>
            </div>
        </div>
    `;

    const modalCSS = `
        .modal-reserva-overlay { position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; }
        .modal-reserva-contenido { background-color: #fefefe; margin: auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 550px; border-radius: 8px; position: relative; max-height: 90vh; overflow-y: auto; }
        .modal-reserva-cerrar { color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 10px; right: 20px; }
        .modal-reserva-cerrar:hover, .modal-reserva-cerrar:focus { color: black; text-decoration: none; cursor: pointer; }
        #formReserva,
    #formPago {
        display: flex;
        flex-direction: column !important;
        gap: 10px !important;
    }
    #formReserva label,
    #formPago label {
        width: 100% !important;
        margin-bottom: -5px !important;
    }
    #formReserva input,
    #formPago input,
    #formPago select {
        width: 100% !important;
        padding: 8px !important;
        border: 1px solid #ccc !important;
        border-radius: 4px !important;
        box-sizing: border-box !important;
    }
    #formReserva button,
    #formPago button {
        background-color: #007bff !important;
        color: white !important;
        padding: 10px 15px !important;
        border: none !important;
        border-radius: 4px !important;
        cursor: pointer !important;
        align-self: flex-end !important;
        margin-top: 10px !important;
    }
    #formReserva button:hover,
    #formPago button:hover {
        background-color: #0056b3 !important;
    }
    #modalReservaMensaje {
        font-size: 0.9em !important;
        margin-top: 10px !important;
    }
    #modalReservaMensaje.success {
        color: green !important;
    }
    #modalReservaMensaje.error {
        color: red !important;
    }
    .tabla-pagos-reserva {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 10px !important;
    }
    .tabla-pagos-reserva th,
    .tabla-pagos-reserva td {
        border: 1px solid #ddd !important;
        padding: 8px !important;
        text-align: left !important;
        font-size: 0.9em !important;
    }
    .tabla-pagos-reserva th {
        background-color: #f2f2f2 !important;
    }
    .seccionImpuestoCreado {
        background-color: #f9f9f9 !important;
    }
    .seccionImpuestoCreado p {
        margin: 5px 0 !important;
        font-size: 0.9em !important;
    }
    .seccionImpuestoCreado h4 {
        margin-bottom: 10px !important;
        font-size: 1.1em !important;
        color: #333 !important;
    }
    #impuestoPreview {
        margin-top: 10px !important;
        padding: 10px !important;
        background-color: #e9ecef !important;
        border: 1px dashed #ccc !important;
        border-radius: 4px !important;
        font-size: 1.1em !important;
        line-height: 1.4 !important;
    }
    `;




    function injectCSS() {
        const styleSheet = document.createElement("style");
        styleSheet.type = "text/css";
        styleSheet.innerText = modalCSS; // Usar la variable modalCSS completa
        document.head.appendChild(styleSheet);
    }
    
    function injectModalHTML() {
        if (!document.getElementById('miModalReserva')) {
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
    }
    
    async function fetchDataAndPopulate(endpoint, formReservaElement) {
        const modalMensaje = document.getElementById('modalReservaMensaje');
        modalMensaje.textContent = 'Cargando datos de la reserva...';
        currentReservaData = {};
        impuestoDefinidoData = {};
        // pagosExistentesData = []; // Si decides usarlo

        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const apiResponse = await response.json();

            // Asumimos que la API devuelve un objeto.
            // Si la estructura es {data: {reserva: ..., impuesto: ..., pagos: ...}}, ajusta aquí.
            // Por ahora, asumiré que apiResponse.reserva, apiResponse.impuesto, apiResponse.pagos pueden existir.

            console.log(JSON.stringify(apiResponse.data));

            


            
            currentReservaData = apiResponse.data.reserva || {}
            currentTiposPagoData = apiResponse.data.tiposPagoData || {};
            currentTiposTarjetaData = apiResponse.data.tiposTarjetaData || {};
            currentMonedasData = apiResponse.data.monedasData || {};
            const posibleImpuesto = apiResponse.data.impuesto || {}; // El impuesto, si existe

            const posiblesPagos = apiResponse.pagos

            if (!currentReservaData || !currentReservaData.id_reservacion) {
                throw new Error("No se encontraron datos válidos de la reserva principal.");
            }

            currentReservaId = currentReservaData.id_reservacion;
            formReservaElement.querySelector('#reservaId').value = currentReservaData.id_reservacion;
            document.getElementById('confirmaid').innerHTML = 'Confirma Principal: ' + (currentReservaData.confirma || '');
            
            if (formReservaElement.elements['uniqid_reserva'] && currentReservaData.uniqid_reserva) {
                formReservaElement.elements['uniqid_reserva'].value = currentReservaData.uniqid_reserva;
            }
            if (formReservaElement.elements['nom_pasajero'] && currentReservaData.nom_pasajero) {
                formReservaElement.elements['nom_pasajero'].value = currentReservaData.nom_pasajero;
            }

            // Llenar adultos y jóvenes para el IMPUESTO desde los datos de la RESERVA PRINCIPAL
            // Asume que tu currentReservaData tiene campos como 'adultos_reserva' o 'num_adultos'. Ajusta los nombres.
            formReservaElement.elements['adultos'].value = currentReservaData.adultos || 0;
            formReservaElement.elements['menores'].value = currentReservaData.menores || 0;
            
            actualizarImpuestoPreview(); // Calcular preview con estos datos iniciales

            // Verificar si ya existe un impuesto definido en la respuesta de la API
            // Ajusta esta condición según cómo tu API indique que un impuesto está definido
            // (ej. tiene un ID, o adultos_impuesto > 0, o un objeto impuesto no vacío)
            if (posibleImpuesto && (posibleImpuesto.id_impuesto || (parseInt(posibleImpuesto.adultos) >= 0 && parseInt(posibleImpuesto.menores) >=0 && (parseInt(posibleImpuesto.menores) + parseInt(posibleImpuesto.menores) > 0) ))) {
                impuestoDefinidoData = {
                    adultos: parseInt(posibleImpuesto.adultos) || 0,
                    menores: parseInt(posibleImpuesto.menores) || 0,
                    tarifaUSD: parseFloat(posibleImpuesto.tarifa_usd) || DEFAULT_USD_IMPUESTO_ISEPAAA,
                    paridadMXN: parseFloat(posibleImpuesto.paridad) || DEFAULT_USD_PARIDAD_MXN,
                    confirma_impuesto: posibleImpuesto.confirma_impuesto || null // Nueva confirma del impuesto
                };
                impuestoDefinidoData.totalUSD = (impuestoDefinidoData.adultos + impuestoDefinidoData.menores) * impuestoDefinidoData.tarifaUSD;
                impuestoDefinidoData.totalMXN = impuestoDefinidoData.totalUSD * impuestoDefinidoData.paridadMXN;

                console.log(impuestoDefinidoData);
                
                // pagosExistentesData = posiblesPagos; // Guardar pagos si vienen con la carga inicial
                modalMensaje.textContent = 'Impuesto previamente definido cargado.';
            } else {
                modalMensaje.textContent = 'Datos de reserva cargados. Defina el impuesto.';
            }
            modalMensaje.className = 'success';
            return posiblesPagos; // Devolver pagos para usarlos directamente si se cargaron

        } catch (error) {
            console.error("Error en fetchDataAndPopulate:", error);
            modalMensaje.textContent = `Error al cargar datos: ${error.message}`;
            modalMensaje.className = 'error';
            // Asegurar que los campos de impuesto estén a 0 si falla la carga y no hay datos de impuesto
            formReservaElement.elements['adultos'].value = 0;
            formReservaElement.elements['menores'].value = 0;
            actualizarImpuestoPreview();
            return []; // Devolver array vacío de pagos en caso de error
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

    function actualizarImpuestoPreview() {
        const inputAdultos = document.getElementById('adultos');
        const inputJoven = document.getElementById('');
        const previewDiv = document.getElementById('impuestoPreview');
        if (!inputAdultos || !inputJoven || !previewDiv) return;

        const adultos = parseInt(inputAdultos.value) || 0;
        const jovenes = parseInt(inputJoven.value) || 0;

        // Usar datos de la reserva principal para tarifa y paridad en la PREVIEW
        const impuestoPorPersonaUSD = parseFloat(currentReservaData.tarifa_usd) || DEFAULT_USD_IMPUESTO_ISEPAAA;
        const paridadUSD_MXN = parseFloat(currentReservaData.paridad) || DEFAULT_USD_PARIDAD_MXN;
        
        const totalPersonas = adultos + jovenes;
        if (totalPersonas === 0 && (inputAdultos.value === "0" || inputJoven.value === "0")) { // Si explícitamente son 0
             previewDiv.innerHTML = `Personas para impuesto: 0<br>
                                Tarifa: $${impuestoPorPersonaUSD.toFixed(2)} USD por persona<br>
                                Paridad: $${paridadUSD_MXN.toFixed(2)} MXN por USD<br>
                                <b>Estimado: $0.00 USD / $0.00 MXN</b>`;
            return;
        }

        const montoUSDEstimado = totalPersonas * impuestoPorPersonaUSD;
        const montoMXNEstimado = montoUSDEstimado * paridadUSD_MXN;

        if (totalPersonas > 0 || adultos > 0 || jovenes > 0) {
            previewDiv.innerHTML = `Personas para impuesto: ${totalPersonas}<br>
                                Tarifa: $${impuestoPorPersonaUSD.toFixed(2)} USD por persona<br>
                                Paridad: $${paridadUSD_MXN.toFixed(2)} MXN por USD<br>
                                <b>Estimado: $${montoUSDEstimado.toFixed(2)} USD / $${montoMXNEstimado.toFixed(2)} MXN</b>`;
        } else {
            previewDiv.innerHTML = 'Ingrese número de adultos y/o Menores para calcular el impuesto.';
        }
    }
    
    function mostrarImpuestoGenerado() {
        const seccion = document.getElementById('seccionImpuestoGenerado');
        const seccionTotalCobrar = document.getElementById('seccionImpuestoGenerado');
        if (!seccion || Object.keys(impuestoDefinidoData).length === 0 || (impuestoDefinidoData.adultos === 0 && impuestoDefinidoData.jovenes === 0 && !impuestoDefinidoData.totalUSD > 0) ) {
            if (seccion) seccion.style.display = 'none';
            return;
        }

        document.getElementById('impGenConfirmaOriginal').textContent = currentReservaData.confirma || currentReservaId || 'N/A';
        document.getElementById('impGenConfirmaImpuesto').textContent = impuestoDefinidoData.confirma_impuesto || 'No generada';
        document.getElementById('impGenAdultos').textContent = impuestoDefinidoData.adultos;
        document.getElementById('impGenMenores').textContent = impuestoDefinidoData.menores;
        document.getElementById('impGenTarifaUSD').textContent = impuestoDefinidoData.tarifaUSD.toFixed(2);
        document.getElementById('impGenParidad').textContent = impuestoDefinidoData.paridadMXN.toFixed(2);
        document.getElementById('impGenTotalUSD').textContent = impuestoDefinidoData.totalUSD.toFixed(2);
        document.getElementById('impGenTotalMXN').textContent = impuestoDefinidoData.totalMXN.toFixed(2);

        document.getElementById('impGenPagarTotalUSD').textContent = impuestoDefinidoData.totalUSD.toFixed(2);
        document.getElementById('impGenPagarTotalMXN').textContent = impuestoDefinidoData.totalMXN.toFixed(2);

        document.getElementById('impGenPorPagarTotalUSD').textContent = impuestoDefinidoData.totalUSD.toFixed(2);
        document.getElementById('impGenPorPagarTotalMXN').textContent = impuestoDefinidoData.totalMXN.toFixed(2);
        
        seccion.style.display = 'block';
    }

    function actualizarMontoPago() {
        const selectMonedaPago = document.getElementById('monedaPago');
        const inputMontoPago = document.getElementById('monto');

        if (!selectMonedaPago || !inputMontoPago || !impuestoDefinidoData.totalUSD) {
            if (inputMontoPago) inputMontoPago.value = "0.00";
            return;
        }
        
        let montoCalculadoFinal = impuestoDefinidoData.totalUSD; // Por defecto en USD
        const monedaSeleccionadaId = selectMonedaPago.value;

        if (monedaSeleccionadaId) {
            const monedaInfo = currentMonedasData.find(m => m.id_moneda == parseInt(monedaSeleccionadaId));
            if (monedaInfo && monedaInfo.cod_moneda === 'MXN') {
                montoCalculadoFinal = impuestoDefinidoData.totalMXN;
            }
        }
        inputMontoPago.value = montoCalculadoFinal.toFixed(2);
    }

    async function cargarPagosRealizados(reservaId, pagosIniciales = null) {
        const tablaPagosBody = document.getElementById('tablaPagos').querySelector('tbody');
        const seccionPagosRealizados = document.getElementById('seccionPagosRealizados');
        if (!tablaPagosBody || !seccionPagosRealizados) return;

        tablaPagosBody.innerHTML = ''; // Limpiar tabla

        let pagos = [];
        if (pagosIniciales && pagosIniciales.length > 0) {
            pagos = pagosIniciales;
        } else if (pagosIniciales === null) { 
            try {
                tablaPagosBody.innerHTML = '<tr><td colspan="6">Cargando pagos...</td></tr>';
                const response = await fetch(`${API_BASE_URL}reservas/${reservaId}/pagos`); // Ajusta endpoint
                if (!response.ok) {
                    console.warn(`No se pudieron cargar los pagos existentes (HTTP ${response.status})`);
                    pagos = []; // Continuar con un array vacío
                } else {
                    const resultado = await response.json();
                    pagos = resultado.data || resultado.pagos || [];
                }
            } catch (error) {
                console.error("Error al cargar pagos:", error);
                tablaPagosBody.innerHTML = '<tr><td colspan="6">Error al cargar pagos.</td></tr>';
                seccionPagosRealizados.style.display = 'block'; // Mostrar tabla con error
                return;
            }
        }
        // Si pagosIniciales fue un array vacío, 'pagos' seguirá siendo un array vacío aquí.

        tablaPagosBody.innerHTML = ''; // Limpiar mensajes de carga/error
        if (pagos.length > 0) {
            pagos.forEach(pago => agregarPagoATabla(pago));
            seccionPagosRealizados.style.display = 'block';
        } else {
            tablaPagosBody.innerHTML = '<tr><td colspan="6">No hay pagos registrados para este impuesto.</td></tr>';
            seccionPagosRealizados.style.display = 'block';
        }
    }

    function agregarPagoATabla(pago) {
        const tablaPagosBody = document.getElementById('tablaPagos').querySelector('tbody');
        if (!tablaPagosBody) return;

        // Si la primera fila es "No hay pagos..." o "Cargando...", la removemos
        const primeraFila = tablaPagosBody.querySelector('tr');
        if (primeraFila && primeraFila.cells.length > 1 && primeraFila.cells[0].colSpan === 6) {
            tablaPagosBody.innerHTML = '';
        }
        
        const fila = tablaPagosBody.insertRow();
        fila.insertCell().textContent = pago.id_pago || pago.id || 'N/A'; // Ajusta según el nombre del ID en tu data
        
        const tipoPagoInfo = currentTiposPagoData.find(tp => tp.id_tipo_pago == pago.id_tipo_pago);
        fila.insertCell().textContent = tipoPagoInfo ? tipoPagoInfo.nom_pago : (pago.nom_pago || 'Desconocido');
        
        const monedaInfo = currentMonedasData.find(m => m.id_moneda == pago.id_moneda);
        fila.insertCell().textContent = monedaInfo ? monedaInfo.cod_moneda : (pago.cod_moneda || 'N/A');
        
        fila.insertCell().textContent = parseFloat(pago.monto || 0).toFixed(2);
        fila.insertCell().textContent = pago.referencia_pago || '';
        fila.insertCell().textContent = pago.fecha_creacion ? new Date(pago.fecha_creacion).toLocaleDateString() : (pago.fecha || 'N/A'); // Ajusta el campo de fecha

        document.getElementById('seccionPagosRealizados').style.display = 'block';
    }


    function initModal() {
        const modal = document.getElementById('miModalReserva');
        // ... (obtener todos los elementos del modal: spanCerrar, formReserva, formPago, etc.)
        const spanCerrar = modal.querySelector('.modal-reserva-cerrar');
        const formReserva = modal.querySelector('#formReserva');
        const formPago = modal.querySelector('#formPago');
        const seccionImpuestoGenerado = modal.querySelector('#seccionImpuestoGenerado');
        const SeccionPago = modal.querySelector('#seccionPago');
        const seccionPagosRealizados = modal.querySelector('#seccionPagosRealizados');
        const modalMensaje = modal.querySelector('#modalReservaMensaje');
        const modalTitulo = modal.querySelector('#modalReservaTitulo');
        // ... (resto de elementos como inputAdultosReserva, inputJovenReserva, etc.)
        const inputAdultosReserva = formReserva.querySelector('#adultos');
        const inputJovenReserva = formReserva.querySelector('#menores');
        const selectTipoPago = formPago.querySelector('#tipoPago');
        const divCamposTarjeta = formPago.querySelector('#camposTarjetaCredito');
        const divCamposReferenciaTarjeta = formPago.querySelector('#camposReferenciaTarjeta');
        const selectTipoTarjeta = formPago.querySelector('#tipoTarjeta');
        const selectMonedaPago = formPago.querySelector('#monedaPago');
        const tablaPagosBody = seccionPagosRealizados.querySelector('#tablaPagos tbody');


        if (!modal || !spanCerrar || !formReserva /* ... y todos los demás ... */) {
            console.error("Error CRÍTICO: No se encontraron todos los elementos del modal.");
            return; 
        }
        
        if (inputAdultosReserva) inputAdultosReserva.addEventListener('input', actualizarImpuestoPreview);
        if (inputJovenReserva) inputJovenReserva.addEventListener('input', actualizarImpuestoPreview);
        if (selectMonedaPago) selectMonedaPago.addEventListener('change', actualizarMontoPago);
         if (selectTipoPago) {
            selectTipoPago.addEventListener('change', function() {
                const requiereTarjeta = currentTiposPagoData.find(tp => tp.id_tipo_pago == this.value)?.referencia === 1;
                // El usuario proveyó el dato: "Tarjeta de Credito" tiene id_tipo_pago = 2
                // O podemos usar la columna 'referencia' si `1` significa que necesita detalles de tarjeta
                if (this.value == '2' || requiereTarjeta) { 
                    divCamposTarjeta.style.display = 'block';
                    divCamposReferenciaTarjeta.style.display = 'block';
                    poblarSelect(selectTipoTarjeta, currentTiposTarjetaData, 'id_tipo_tarjeta', 'nom_tarjeta', 'Seleccione Tarjeta');
                    selectTipoTarjeta.required = true;
                } else {
                    divCamposTarjeta.style.display = 'none';
                    divCamposReferenciaTarjeta.style.display = 'none';
                    selectTipoTarjeta.required = false;
                    selectTipoTarjeta.innerHTML = ''; // Limpiar opciones
                }
            });
        }
        
        document.querySelectorAll('.btn-modal-reserva-trigger').forEach(button => {
            button.addEventListener('click', async function() {
                // Resetear estados y UI
                formReserva.reset();
                formPago.reset();
                modalMensaje.textContent = '';
                impuestoDefinidoData = {};
                currentReservaData = {};
                
                formReserva.style.display = 'none';
                seccionImpuestoGenerado.style.display = 'none';
                SeccionPago.style.display = 'none';
                seccionPagosRealizados.style.display = 'none';
                if(tablaPagosBody) tablaPagosBody.innerHTML = '';
                divCamposTarjeta.style.display = 'none';
                divCamposReferenciaTarjeta.style.display = 'none';
                
                modalTitulo.textContent = this.dataset.modalTitle || 'Gestión de Impuesto ISEPAAA';
                const apiEndpoint = this.dataset.apiEndpoint;

                if (!apiEndpoint) {
                    modalMensaje.textContent = 'Error: No se especificó endpoint para la reserva.';
                    modalMensaje.className = 'error';
                    modal.style.display = 'flex';
                    return;
                }

                const pagosIniciales = await fetchDataAndPopulate(apiEndpoint, formReserva);

                // Lógica de visualización basada en si el impuesto ya está definido
                if (impuestoDefinidoData && (impuestoDefinidoData.adultos > 0 || impuestoDefinidoData.jovenes > 0 || impuestoDefinidoData.totalUSD > 0)) {
                    // Impuesto YA existe
                    formReserva.style.display = 'none'; // Ocultar form de creación de impuesto
                    mostrarImpuestoGenerado();
                    
                    // Configurar y mostrar sección de pago
                    poblarSelect(selectTipoPago, currentTiposPagoData, 'id_tipo_pago', 'nom_pago');
                    poblarSelect(selectMonedaPago, currentMonedasData, 'id_moneda', 'nom_moneda');
                    const usdMoneda = currentMonedasData.find(m => m.cod_moneda === 'USD');
                    if (usdMoneda) selectMonedaPago.value = usdMoneda.id_moneda;
                    else if (currentMonedasData.length > 0) selectMonedaPago.value = currentMonedasData[0].id_moneda;
                    
                    actualizarMontoPago();
                    seccionPago.style.display = 'block';
                    
                    // Cargar pagos (ya sea los iniciales o fetchear si no vinieron)
                    if (pagosIniciales && pagosIniciales.length > 0) {
                        cargarPagosRealizados(currentReservaId, pagosIniciales);
                    } else if (pagosIniciales && pagosIniciales.length === 0) { // API devolvió array vacío
                        cargarPagosRealizados(currentReservaId, []);
                    } else { // API no devolvió sección de pagos, fetchear por separado
                        cargarPagosRealizados(currentReservaId, null);
                    }

                } else {
                    // Impuesto NO existe o es inválido, mostrar form para crearlo
                    formReserva.style.display = 'flex';
                    // actualizarImpuestoPreview() ya se llamó dentro de fetchDataAndPopulate
                    seccionImpuestoGenerado.style.display = 'none';
                    SeccionPago.style.display = 'none';
                    seccionPagosRealizados.style.display = 'none';
                }
                modal.style.display = 'flex';
            });
        });

        spanCerrar.onclick = function() { modal.style.display = 'none'; }
        window.onclick = function(event) { if (event.target == modal) { modal.style.display = 'none'; } }

        formReserva.onsubmit = async function(event) {
            event.preventDefault();
            modalMensaje.textContent = 'Procesando impuesto...';

            const adultos = parseInt(formReserva.elements['adultos'].value) || 0;
            const jovenes = parseInt(formReserva.elements['menores'].value) || 0;

            if (adultos === 0 && jovenes === 0) {
                modalMensaje.textContent = 'Debe ingresar al menos un adulto o menores para el impuesto.';
                modalMensaje.className = 'error';
                return;
            }
            
            const tarifa = parseFloat(currentReservaData.usd_impuesto_isepaaa) || DEFAULT_USD_IMPUESTO_ISEPAAA;
            const paridad = parseFloat(currentReservaData.usd_paridad) || DEFAULT_USD_PARIDAD_MXN;
            const totalUSD = (adultos + jovenes) * tarifa;
            const totalMXN = totalUSD * paridad;

            const datosImpuestoParaApi = {
                id_reservacion_principal: currentReservaId, // Referencia a la reserva original
                uniqid_reserva_principal: currentReservaData.uniqid_reserva, // Si lo necesitas
                adultos_impuesto: adultos,
                joven_impuesto: jovenes,
                tarifa_usd_aplicada: tarifa,
                paridad_aplicada: paridad,
                total_impuesto_usd: totalUSD,
                total_impuesto_mxn: totalMXN
                // Aquí tu API podría generar un nuevo ID o una nueva "confirma" para este impuesto.
            };
            
            // Endpoint para CREAR el impuesto. Puede ser un POST a un nuevo recurso o un PUT si actualiza la reserva principal
            // con una sub-entidad de impuesto. Vamos a asumir un POST a un endpoint de impuestos.
            // Ajusta `url` y `method` según tu API.
            let url = `${API_BASE_URL}/${currentReservaId}/crear_impuesto`; // EJEMPLO de endpoint
            let method = 'POST'; // o 'PUT' si actualizas la reserva existente

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datosImpuestoParaApi)
                });
                const result = await response.json(); // Se espera que result.data contenga el impuesto creado/actualizado

                if (!response.ok) throw new Error(result.message || "Error al guardar el impuesto.");

                if (result.status === 'success' || (result.data && (result.data.id_impuesto || result.data.total_impuesto_usd > 0))) {
                    modalMensaje.textContent = result.message || 'Impuesto guardado exitosamente.';
                    modalMensaje.className = 'success';
                    
                    // Actualizar impuestoDefinidoData con la respuesta de la API
                    const impuestoCreado = result.data.impuesto || result.data; // Ajusta según la estructura de tu respuesta
                    impuestoDefinidoData = {
                        adultos: parseInt(impuestoCreado.adultos_impuesto) || adultos,
                        jovenes: parseInt(impuestoCreado.joven_impuesto) || jovenes,
                        tarifaUSD: parseFloat(impuestoCreado.tarifa_usd_aplicada) || tarifa,
                        paridadMXN: parseFloat(impuestoCreado.paridad_aplicada) || paridad,
                        totalUSD: parseFloat(impuestoCreado.total_impuesto_usd) || totalUSD,
                        totalMXN: parseFloat(impuestoCreado.total_impuesto_mxn) || totalMXN,
                        confirma_impuesto: impuestoCreado.confirma_impuesto || 'Generada-' + Date.now() // Nueva confirma
                    };
                    
                    // Si la API devuelve datos de la reserva principal actualizados (ej. un nuevo 'confirma' general)
                    if (result.data.reserva_actualizada) {
                         currentReservaData = { ...currentReservaData, ...result.data.reserva_actualizada };
                         document.getElementById('confirmaid').innerHTML = 'Confirma Principal: ' + (currentReservaData.confirma || currentReservaId || '');
                    }


                    mostrarImpuestoGenerado();
                    formReserva.style.display = 'none';
                    
                    poblarSelect(selectTipoPago, currentTiposPagoData, 'id_tipo_pago', 'nom_pago');
                    poblarSelect(selectMonedaPago, currentMonedasData, 'id_moneda', 'nom_moneda');
                    const usdMoneda = currentMonedasData.find(m => m.cod_moneda === 'USD');
                    if (usdMoneda) selectMonedaPago.value = usdMoneda.id_moneda;
                    
                    actualizarMontoPago();
                    SeccionPago.style.display = 'block';
                    cargarPagosRealizados(currentReservaId, []); // Cargar tabla de pagos (estará vacía para un nuevo impuesto)
                    
                } else {
                    throw new Error(result.message || 'No se pudo procesar la solicitud del impuesto.');
                }
            } catch (error) {
                modalMensaje.textContent = `Error: ${error.message}`;
                modalMensaje.className = 'error';
                console.error(`Error en fetch ${method} impuesto:`, error);
            }
        }

        formPago.onsubmit = async function(event) { /* ... (sin cambios mayores, pero asegurar que usa currentReservaId o un id_impuesto si lo tienes) ... */ }
    }

    document.addEventListener('DOMContentLoaded', function() {
        injectCSS();
        injectModalHTML();
        initModal();
    });
})();