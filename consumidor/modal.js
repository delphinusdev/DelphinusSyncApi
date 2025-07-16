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
    let impuestoPagado = 0;
    const idsTiposPagoPermitidos = [1, 2]; // Ejemplo: Efectivo y Tarjeta

    // // --- Funciones Auxiliares de UI ---
    // function showMessage(message, type = 'info', show = true) {
    //     const modalMensaje = document.getElementById('modalReservaMensaje');
    //     if (!modalMensaje) return;
    //     modalMensaje.textContent = message;
    //     modalMensaje.className = `mensaje-inferior ${type}`;
    //     modalMensaje.style.display = show ? 'block' : 'none';
    // }

    // --- Funciones Auxiliares de UI ---
function showMessage(message, type = 'info', show = true) {
    const modalMensaje = document.getElementById('modalReservaMensaje');
    if (!modalMensaje) return;

    modalMensaje.textContent = message;
    
    // Limpia todas las clases de tipo antes de aplicar la nueva
    modalMensaje.classList.remove('info', 'success', 'complete', 'error', 'loading'); 
    
    // Aplica la clase base y el tipo específico
    modalMensaje.classList.add('mensaje-inferior', type); 

    modalMensaje.style.display = show ? 'block' : 'none';
}

    function displayState(state, message = '') {
        const seccionCrear = document.getElementById('seccionCrearImpuesto');
        const seccionGestion = document.getElementById('seccionGestionPago');
        const noAplicaPreview = document.getElementById('noAplicaImpuestoPreview');

        if (!seccionCrear || !seccionGestion || !noAplicaPreview) return;

        seccionCrear.style.display = 'none';
        seccionGestion.style.display = 'none';
        noAplicaPreview.style.display = 'none';
        showMessage('', 'info', false);

        switch (state) {
            case 'loading':
                showMessage('Cargando...', 'loading');
                break;
            case 'crear':
                seccionCrear.style.display = 'block';
                showMessage(message || 'Datos de reserva cargados. Agregue el impuesto.', 'success');
                break;
            case 'gestionar':
                seccionGestion.style.display = 'block';
                showMessage(message || 'La reserva ya tiene un impuesto definido.', 'success');
                break;
            case 'completado':
                seccionGestion.style.display = 'block';
                showMessage(message || 'El impuesto ya está completamente pagado.', 'complete');
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

    // --- Funciones de Obtención de Datos (API) ---
    async function obtenerPrecioIsepaaa(servicio, idHabitat, fecha) {
        try {
            const endpoint = `/Servicios/${servicio}/Precios?fecha=${fecha}&idHabitat=${idHabitat}`;
            const response = await api.get(endpoint);
            if (response && typeof response === 'object' && response.precioAdulto !== undefined) {
                return response;
            }
            throw new Error("La respuesta de precios servicios no es válida.");
        } catch (error) {
            showMessage('No se pudieron cargar los precios del servicio.', 'error');
            throw error;
        }
    }

    async function obtenerTiposDePago() {
        try {
            const response = await api.get('/FormasPago');
            if (!response || !Array.isArray(response)) {
                throw new Error("La respuesta de Tipos de Pago no es válida.");
            }
            return response.map(pago => ({
                ...pago,
                referencia: pago.value === "Tarjeta de Credito" ? 1 : 0
            }));
        } catch (error) {
            showMessage('No se pudieron cargar las formas de pago.', 'error');
            throw error;
        }
    }

    async function obtenerTiposDeCambio() {
    try {
        const response = await api.get('/TiposCambio');

        if (!response || !Array.isArray(response)) {
            throw new Error("La respuesta de Tipos de Cambio no es válida.");
        }

        let pesos = {
            idMoneda: 2,
            moneda: "MXN",
            monto: 1
        };

        // Add the 'pesos' object to the array
        response.push(pesos);

        // Return the modified array
        return response;

    } catch (error) {
        // Assuming showMessage is a defined function to display messages
        showMessage('No se pudieron cargar los tipos de cambio.', 'error');
        throw error; // Re-throw the error for further handling if needed
    }
}

    async function obtenerTiposDeTarjeta() {
        try {
            const response = await api.get('/TiposTarjeta');
            if (response && Array.isArray(response)) {
                return response;
            }
            throw new Error("La respuesta de Tipos de Tarjeta no es válida.");
        } catch (error) {
            showMessage('No se pudieron cargar los tipos de tarjeta.', 'error');
            throw error;
        }
    }

    async function obtenerMonedas() {
        try {
            const response = await api.get('/Monedas');
            if (!response || !Array.isArray(response)) {
                throw new Error("La respuesta de Monedas no es válida.");
            }
            const nombresMonedas = {
                USD: "Dólares Americanos",
                MXN: "Pesos Mexicanos",
                EUR: "Euros",
                CAN: "Dólar Canadiense"
            };
            return response.map(moneda => ({
                ...moneda,
                name: nombresMonedas[moneda.value] || moneda.value
            }));
        } catch (error) {
            showMessage('No se pudieron cargar las monedas.', 'error');
            throw error;
        }
    }

    async function obtenerReserva(uniqIdReserva) {
        try {
            if (!uniqIdReserva || uniqIdReserva === '0') return null;
            const endpoint = `/Reservas/${uniqIdReserva}`;
            return await api.get(endpoint);
        } catch (error) {
            showMessage('No se pudo verificar el impuesto existente.', 'error');
            throw error;
        }
    }

    async function obtenerPagos(idImpuesto) {
        try {
            if (!idImpuesto || idImpuesto === '0') return [];
            const endpoint = `/Pagos?idReservacion=${idImpuesto}`;
            const response = await api.get(endpoint);
            return response || [];
        } catch (error) {
            showMessage('No se pudieron cargar los pagos anteriores.', 'error');
            throw error;
        }
    }

    // --- Lógica Principal de Orquestación ---
    async function cargarDatosCompletosDeReserva(endpointPrincipal) {
        showMessage('Obteniendo datos de la reserva...', 'loading');
        const reserva = await api.get(endpointPrincipal);
        if (!reserva) throw new Error("No se encontraron datos válidos de la reserva.");
        
        currentReservaData = reserva;

        if (!currentReservaData.aplicaISEPAAA) {
            displayState('noAplica', 'Esta reserva no aplica para el impuesto.');
            return false;
        }
        if (currentReservaData.idServicio === IDISEPAAA_) {
            displayState('noAplica', 'La reserva es un impuesto ISEPAAA, no necesita cambios adicionales.');
            return false;
        }
        if (currentReservaData.codigoPais === 'MXQR') {
            displayState('noAplica', 'El visitante es Quintanarroense, no requiere agregar un impuesto.');
            return false;
        }

        showMessage('Cargando catálogos y detalles del impuesto...', 'loading');
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

        currentImpuestoData = impuesto || {};
        currentPagosData = pagos && Array.isArray(pagos) ? pagos : [];
        currentPrecioIsepaaaData = precio || {};
        fullCatalogData = { tiposCambio, tiposPago, tiposTarjeta, monedas };
        currentMaximoPersonas = (parseInt(reserva.adultos) || 0) + (parseInt(reserva.menores) || 0);

        return true;
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
        // Habilitar el botón en caso de que se haya quedado deshabilitado de un intento anterior
        const submitButton = document.getElementById('btnSubmitReserva');
        if (submitButton) submitButton.disabled = false;
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

        document.getElementById('impGenConfirmaImpuesto').textContent = impuestoDefinidoData.confirma_impuesto;
        document.getElementById('impGenAdultos').textContent = impuestoDefinidoData.adultos;
        document.getElementById('impGenMenores').textContent = impuestoDefinidoData.menores;
        document.getElementById('impGenTarifaUSD').textContent = impuestoDefinidoData.tarifaUSD.toFixed(2);
        document.getElementById('impGenParidad').textContent = impuestoDefinidoData.paridadMXN.toFixed(2);
        document.getElementById('saldoTotalUSD').textContent = impuestoDefinidoData.totalUSD.toFixed(2);
        document.getElementById('saldoTotalMXN').textContent = impuestoDefinidoData.totalMXN.toFixed(2);

        const tiposPagoFiltrados = fullCatalogData.tiposPago.filter(tp => idsTiposPagoPermitidos.includes(tp.id));
        poblarSelect(document.getElementById('tipoPago'), tiposPagoFiltrados, 'id', 'value');
        poblarSelect(document.getElementById('monedaPago'), fullCatalogData.monedas, 'id', 'name');
        
        cargarPagosRealizados();
        if(impuestoPagado === 0)
        {
            displayState('gestionar', mensaje);
        }
        else
        {
            displayState('completado', mensaje);
        }
        
    }

    function actualizarImpuestoPreview() {
        const adultos = parseInt(document.getElementById('adultos').value) || 0;
        const menores = parseInt(document.getElementById('menores').value) || 0;
        const previewDiv = document.getElementById('impuestoPreview');
        if (!previewDiv) return;

        const tarifa = parseFloat(currentPrecioIsepaaaData.precioAdulto) || 0;
        const paridad = parseFloat(currentPrecioIsepaaaData.tipoCambio) || 0;
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
        const tablaPagosBody = document.getElementById('tablaPagos').querySelector('tbody');
        const seccionPagos = document.getElementById('seccionPagosRealizados');
        if (!tablaPagosBody || !seccionPagos) return;

        tablaPagosBody.innerHTML = '';
        seccionPagos.style.display = 'block';

        if (currentPagosData.length > 0) {
            currentPagosData.forEach(pago => agregarPagoATabla(pago));
        } else {
            tablaPagosBody.innerHTML = '<tr><td colspan="6">No hay pagos registrados.</td></tr>';
        }
        actualizarSaldos();
    }

    function agregarPagoATabla(pago) {
        const tablaPagosBody = document.getElementById('tablaPagos').querySelector('tbody');
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
            const paridad_pago_a_MXN = parseFloat(pago.tipoCambio) || 1;
            const montoEnMXN = parseFloat(pago.monto) * paridad_pago_a_MXN;
            const montoEnUSD = (paridad_USD_a_MXN > 0) ? (montoEnMXN / paridad_USD_a_MXN) : 0;
            totalPagadoUSD += montoEnUSD;
        });

        const totalPagadoMXN = totalPagadoUSD * paridad_USD_a_MXN;
        const saldoPendienteUSD = impuestoDefinidoData.totalUSD - totalPagadoUSD;
        const saldoPendienteMXN = impuestoDefinidoData.totalMXN - totalPagadoMXN;

        document.getElementById('saldoPagadoUSD').textContent = totalPagadoUSD.toFixed(2);
        document.getElementById('saldoPagadoMXN').textContent = totalPagadoMXN.toFixed(2);
        document.getElementById('saldoPorPagarUSD').textContent = (saldoPendienteUSD > 0 ? saldoPendienteUSD : 0).toFixed(2);
        document.getElementById('saldoPorPagarMXN').textContent = (saldoPendienteMXN > 0 ? saldoPendienteMXN : 0).toFixed(2);

        actualizarMontoPago();
        if (saldoPendienteUSD <= 0) {
            impuestoPagado = 1;
            showMessage('El impuesto ya está completamente pagado.', 'complete', true);
            const seccionPago = document.getElementById('seccionPago');
            if(seccionPago) seccionPago.style.display = 'none';
        }
    }

    function getPaymentContext() {
        const selectedMonedaId = document.getElementById('monedaPago').value;
        const tipoPagoSelectValue = document.getElementById('tipoPago').value;
        const monedaSeleccionada = fullCatalogData.tiposCambio.find(m => m.idMoneda == selectedMonedaId);
        const isEfectivo = tipoPagoSelectValue === '1';
        const monedaCode = monedaSeleccionada ? monedaSeleccionada.moneda : null;
        return { isEfectivo, monedaCode };
    }

    // --- LÓGICA DE VALIDACIÓN DE MONTO RESTAURADA ---
    function handleMontoInput() {
        const montoInput = document.getElementById('monto');
        let value = montoInput.value;
        const { isEfectivo, monedaCode } = getPaymentContext();

        if (isEfectivo && monedaCode === 'USD') {
            // Si es efectivo y USD, solo permite dígitos.
            value = value.replace(/[^0-9]/g, '');
            montoInput.value = value;
        } else {
            // Para otros casos, permite dígitos y un punto decimal.
            value = value.replace(/[^0-9.]/g, ''); 
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            montoInput.value = value;
        }
    }

    function handleMontoBlur() {
        const montoInput = document.getElementById('monto');
        let value = parseFloat(montoInput.value);
        const { isEfectivo, monedaCode } = getPaymentContext();

        if (isNaN(value)) {
            montoInput.value = "0.00"; 
            return;
        }

        if (isEfectivo && monedaCode === 'USD') {
            // Redondea hacia arriba al entero más cercano para efectivo en USD.
            montoInput.value = Math.ceil(value).toFixed(0);
        } else {
            // Para otros tipos de pago, formatea a dos decimales.
            montoInput.value = value.toFixed(2);
        }
    }
    
 function actualizarMontoPago() {
    const montoInput = document.getElementById('monto');
    if (!montoInput) return;

    const selectedMonedaId = document.getElementById('monedaPago').value;
    const saldoPendienteUSD = parseFloat(document.getElementById('saldoPorPagarUSD').textContent) || 0;
    const tipoPagoSelectValue = document.getElementById('tipoPago').value;

    // Reiniciar y validar condiciones iniciales
    if (!selectedMonedaId || saldoPendienteUSD <= 0) {
        montoInput.value = "0.00";
        return;
    }

    let montoCalculadoFinal = 0; // Iniciar en 0
    const monedaInfo = fullCatalogData.monedas.find(m => m.id == selectedMonedaId);
    
    // Si no se encuentra información de la moneda, no se puede continuar.
    if (!monedaInfo) {
        montoInput.value = "0.00";
        return;
    }

    const paridad_USD_a_MXN = impuestoDefinidoData.paridadMXN;

    // REGLA 1: Si la moneda es Dólares (USD)
    if (monedaInfo.value === 'USD') {
        montoCalculadoFinal = saldoPendienteUSD;
    } 
    else {
        // Para cualquier otra moneda (MXN, EUR, etc.)
        const tipoCambioInfo = fullCatalogData.tiposCambio.find(tc => tc.idMoneda == selectedMonedaId);

        // REGLA 2: Si la moneda SÍ tiene tipo de cambio (ej. MXN, o un EUR configurado)
        if (tipoCambioInfo && tipoCambioInfo.monto > 0) {
            const saldoPendienteMXN = saldoPendienteUSD * paridad_USD_a_MXN;
            montoCalculadoFinal = saldoPendienteMXN / tipoCambioInfo.monto;
        }
        // REGLA 3: Si es otra moneda pero NO tiene tipo de cambio, no se puede calcular.
        else {
            montoCalculadoFinal = 0; // Se deja en 0 para indicar que no es posible el cálculo.
            // Opcionalmente, aquí se podría mostrar un mensaje al usuario.
        }
    }

    // --- Lógica de formato y redondeo (sin cambios) ---
    const isEfectivo = tipoPagoSelectValue === '1';

    if (isEfectivo && monedaInfo && monedaInfo.value === 'USD') {
        const montoFinal = Math.ceil(montoCalculadoFinal);
        montoInput.value = (montoFinal > 0 ? montoFinal : 0).toFixed(0);
        montoInput.setAttribute('step', '1');
    } else {
        montoInput.value = (montoCalculadoFinal > 0 ? montoCalculadoFinal : 0).toFixed(2);
        montoInput.setAttribute('step', 'any');
    }
}

    // --- Handlers de Eventos y Formularios ---
    async function handleOpenModal(button) {
        displayState('loading');
        document.getElementById('miModalReserva').style.display = 'flex';
        document.getElementById('modalReservaTitulo').textContent = button.dataset.modalTitle || 'Gestión de Impuesto ISEPAAA';

        const user = button.dataset.sessionUser;
        const pass = button.dataset.sessionPass;
        const apiEndpoint = button.dataset.apiEndpoint;
        
        if (!user || !pass) return showMessage('Error: Credenciales de autenticación no encontradas.', 'error');
        if (!apiEndpoint) return showMessage('Error: No se especificó el endpoint para la reserva.', 'error');
        
        try {
            await api.authenticate(AUTH_ENDPOINT, { username: user, password: pass });
            showMessage('Autenticación exitosa.', 'success');

            const debeContinuar = await cargarDatosCompletosDeReserva(apiEndpoint);
            
            if (!debeContinuar) return;

            poblarDatosBasicosModal();
            showMessage('', 'success');
            const impuestoExiste = currentImpuestoData && currentImpuestoData.idReservacion;
            if (impuestoExiste) {
                prepararGestionDeImpuesto('El impuesto se cargó correctamente.');
            } else {
                prepararFormularioCreacion();
            }
        } catch (error) {
            showMessage(`Error: ${error.message}`, 'error');
        }
    }

    // --- FUNCIÓN CORREGIDA ---
    async function handleCreateImpuestoSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');

        // **CORRECCIÓN: Deshabilitar botón para prevenir envíos múltiples**
        submitButton.disabled = true;
        showMessage('Procesando impuesto...', 'info');

        const adultos = parseInt(form.elements['adultos'].value) || 0;
        const menores = parseInt(form.elements['menores'].value) || 0;

        if (adultos === 0 && menores === 0) {
            showMessage('Debe ingresar al menos un adulto o menor.', 'error');
            // **CORRECCIÓN: Reactivar botón si hay error de validación**
            submitButton.disabled = false;
            return;
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
            idAgencia: 24003, idRepresentante: 0, idClasificacion: 2, idSubclasificacion: 15, idAutorizo: 0, tipoComentario: 1, porcentajeIVA: 0
        };

        try {
            const result = await api.post('/Reservas', payload);
            if (!result || !result.idReservacion) {
                 throw new Error(result.errors ? result.errors.join(", ") : 'La API no devolvió un ID de reservación válido.');
            }
            currentImpuestoData = await obtenerReserva(result.idReservacion);
            currentPagosData = [];
            prepararGestionDeImpuesto('Impuesto guardado exitosamente.');
        } catch (error) {
            showMessage(error.message, 'error');
            // **CORRECCIÓN: Reactivar botón si la API devuelve un error**
            submitButton.disabled = false;
        }
    }

    async function handlePaymentSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');

        submitButton.disabled = true;
        showMessage('Procesando pago...', 'info');

        const monto = parseFloat(form.elements['monto'].value) || 0;
        if (monto <= 0) {
            showMessage('El monto del pago debe ser mayor a cero.', 'error');
            submitButton.disabled = false;
            return;
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

            await api.post(endpoint, pagoPayload);
            showMessage('Pago registrado exitosamente. Actualizando...', 'success');

            const pagosActualizados = await obtenerPagos(idImpuesto);
            currentPagosData = pagosActualizados && Array.isArray(pagosActualizados) ? pagosActualizados : [];

            cargarPagosRealizados();
            form.reset();
            document.getElementById('camposTarjetaCredito').style.display = 'none';
        } catch (error) {
            showMessage(`Error: ${error.message}`, 'error');
        } finally {
            submitButton.disabled = false;
        }
    }

    function initModal() {
        const modal = document.getElementById('miModalReserva');
        if (!modal) return;
        
        const spanCerrar = modal.querySelector('.modal-reserva-cerrar');
        modal.querySelector('#formReserva').onsubmit = handleCreateImpuestoSubmit;
        modal.querySelector('#formPago').onsubmit = handlePaymentSubmit;

        document.getElementById('adultos').addEventListener('input', actualizarImpuestoPreview);
        document.getElementById('menores').addEventListener('input', actualizarImpuestoPreview);
        
        document.getElementById('tipoPago').addEventListener('change', function () {
            const divCamposTarjeta = document.getElementById('camposTarjetaCredito');
            const selectTipoTarjeta = document.getElementById('tipoTarjeta');
            const selectMoneda = document.getElementById('monedaPago');
            const tipoSeleccionado = fullCatalogData.tiposPago.find(tp => tp.id == this.value);
            const requiereTarjeta = tipoSeleccionado && parseInt(tipoSeleccionado.referencia) === 1;

            document.getElementById('monto').value = '0.00';

            if (requiereTarjeta) {
                divCamposTarjeta.style.display = 'grid';
                poblarSelect(selectTipoTarjeta, fullCatalogData.tiposTarjeta, 'id', 'value', 'Seleccione Tarjeta');
                const monedasFiltradas = fullCatalogData.monedas.filter(moneda => moneda.id === 1 || moneda.id === 2);
                poblarSelect(selectMoneda, monedasFiltradas, 'id', 'name', 'Seleccione Moneda');
                selectTipoTarjeta.required = true;
            } else {
                divCamposTarjeta.style.display = 'none';
                selectTipoTarjeta.required = false;
                selectTipoTarjeta.innerHTML = '';
                poblarSelect(selectMoneda, fullCatalogData.monedas, 'id', 'name', 'Seleccione Moneda');
            }
            document.getElementById('monedaPago').disabled = false;
            actualizarMontoPago();
        });

        document.querySelectorAll('.btn-modal-reserva-trigger').forEach(button => {
            button.addEventListener('click', () => handleOpenModal(button));
        });

        spanCerrar.onclick = () => { modal.style.display = 'none'; };
    }

    // --- Punto de Entrada ---
    document.addEventListener('DOMContentLoaded', function () {
       // injectCSS();
       // injectModalHTML();
        initModal();

        const montoInput = document.getElementById('monto');
        const tipoPagoSelect = document.getElementById('tipoPago');
        const monedaPagoSelect = document.getElementById('monedaPago');

        if (montoInput && tipoPagoSelect && monedaPagoSelect) {
            montoInput.addEventListener('input', handleMontoInput);
            montoInput.addEventListener('blur', handleMontoBlur);
            tipoPagoSelect.addEventListener('change', actualizarMontoPago);
            monedaPagoSelect.addEventListener('change', actualizarMontoPago);
            actualizarMontoPago();
        } else {
            console.error("Error de inicialización: No se encontraron los campos del formulario de pago.");
        }
    });
})();