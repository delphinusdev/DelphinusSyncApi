// public/js/script-modal.js
(function() {
    // URL base de tu API (ajusta si es necesario)
    const API_BASE_URL = 'http://192.168.8.107:8080/samreportes/slim4/reservas'; // Ajusta la ruta base de tu API

    let currentReservaId = null; // Para almacenar el ID de la reserva actual (para edición o pagos)
    let currentAction = 'create'; // Para saber si estamos creando o editando

    // 1. HTML del Modal
    const modalHTML = `
        <div id="miModalReserva" class="modal-reserva-overlay" style="display:none;">
            <div class="modal-reserva-contenido">
                <span class="modal-reserva-cerrar">&times;</span>
                <h4 id="modalReservaTitulo">Agregar Impuesto</h4>
                <form id="formReserva">
                    <label><strong id="confirmaid"></strong></label>
                    <input type="hidden" id="reservaId" name="reservaId">
                    <input type="hidden" id="uniqid_reserva" name="uniqid_reserva">

                    <label for="nom_pasajero">Nombre:</label>
                    <input type="text" id="nom_pasajero" name="nom_pasajero" disabled>

                    <label for="adultos">Adultos:</label>
                    <input type="number" min="0" id="adultos" name="adultos" required>

                     <label for="joven">Niños:</label>
                    <input type="number" min="0" id="joven" name="joven" required>

                    <button type="submit" id="btnSubmitReserva">Agregar Impuesto</button>
                </form>


                <div id="seccionPago" style="display:none; margin-top: 20px;">
                    <h3>Agregar Pago</h3>
                    <form id="formPago">
                        <label for="monto">Monto:</label>
                        <input type="number" id="monto" name="monto" step="0.01" required><br><br>
                        <label for="metodo">Método de Pago:</label>
                        <input type="text" id="metodo" name="metodo" placeholder="Ej: Tarjeta, Efectivo" required><br><br>
                        <button type="submit" id="btnSubmitPago">Agregar Pago</button>
                    </form>
                </div>
                <div id="modalReservaMensaje" style="margin-top:15px;"></div>
            </div>
        </div>
    `;

    // 2. CSS del Modal (sin cambios respecto al original)
    const modalCSS = `
        .modal-reserva-overlay {
            position: fixed; z-index: 1000; left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto; background-color: rgba(0,0,0,0.5);
            display: flex; align-items: center; justify-content: center;
        }
        .modal-reserva-contenido {
            background-color: #fefefe; margin: auto; padding: 20px;
            border: 1px solid #888; width: 80%; max-width: 500px;
            border-radius: 8px; position: relative;
        }
        .modal-reserva-cerrar {
            color: #aaa; float: right; font-size: 28px; font-weight: bold;
            position: absolute; top: 10px; right: 20px;
        }
        .modal-reserva-cerrar:hover, .modal-reserva-cerrar:focus {
            color: black; text-decoration: none; cursor: pointer;
        }
        /* 1) Convertimos el form en un flex de columna */
#formReserva,
#formPago {
    display: flex;
    flex-direction: column;
    gap: 10px; /* separa inputs/botón */
}

/* 2) Ajustamos inputs/labels para que sigan ocupando ancho completo */
#formReserva label,
#formPago label {
    width: 100%;
    margin-bottom: 5px;
}

#formReserva input,
#formPago input {
    width: 100%; /* calc(100% - 130px);*/
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* 3) Alineamos el botón a la derecha */
#formReserva button,
#formPago button {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    align-self: flex-end;
    margin-top: 10px; /* separación opcional del último input */
}

#formReserva button:hover,
#formPago button:hover {
    background-color: #0056b3;
}
        
        #modalReservaMensaje { font-size: 0.9em; }
        #modalReservaMensaje.success { color: green; }
        #modalReservaMensaje.error { color: red; }
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

    // Nueva función para cargar datos y poblar el formulario
    async function fetchDataAndPopulateForm(endpoint, form, populate) {
        const modalMensaje = document.getElementById('modalReservaMensaje');
        if (!endpoint) return;

        modalMensaje.textContent = 'Cargando datos...';
        modalMensaje.className = '';

        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`); // Asume que endpoint es relativo a API_BASE_URL
            if (!response.ok) {
                const errorResult = await response.json().catch(() => ({ message: 'Error al obtener datos.' }));
                throw new Error(errorResult.message || `Error HTTP: ${response.status}`);
            }
            const data = await response.json();

            if (data && populate) { // 'data' podría ser el objeto de la reserva directamente
                let reserva = data.data[0];
                if (reserva) { // Si tu API devuelve { reserva: {...} }
                     // Asignar ID para la edición
                    if (reserva.id_reservacion) { // Ajusta 'id' al nombre real del campo ID en tu respuesta
                        currentReservaId = reserva.id_reservacion;
                        form.querySelector('#reservaId').value = reserva.id_reservacion;
                        document.getElementById('confirmaid').innerHTML = 'Confirma: ' + reserva.confirma || ''; // Asegúrate de que 'confirma' sea un campo válido
                    }
                    for (const key in reserva) {
                        if (form.elements[key]) {
                            if (form.elements[key].type === 'date' && reserva[key]) {
                                // Asegurarse que el formato de fecha sea YYYY-MM-DD
                                form.elements[key].value = reserva[key].split('T')[0];
                            } else {
                                form.elements[key].value = reserva[key];
                            }
                        }
                    }
                } else { // Si tu API devuelve el objeto de la reserva directamente
                    if (data.id) { // Ajusta 'id' al nombre real del campo ID
                        currentReservaId = data.id;
                        form.querySelector('#reservaId').value = data.id;
                    }
                    for (const key in data) {
                        if (form.elements[key]) {
                             if (form.elements[key].type === 'date' && data[key]) {
                                form.elements[key].value = data[key].split('T')[0];
                            } else {
                                form.elements[key].value = data[key];
                            }
                        }
                    }
                }
                modalMensaje.textContent = 'Datos cargados. Puede Agregar Impuesto ISEPAAA.';
                modalMensaje.className = 'success';
            } else if (!populate) {
                 modalMensaje.textContent = 'Visualizando datos (solo lectura).'; // Opcional
                 modalMensaje.className = 'success';
                 // Aquí podrías deshabilitar los campos del formulario si es 'view'
                //  Array.from(form.elements).forEach(el => {
                //    el.disabled = false; // Deshabilitar todos los campos
                //  });

                 form.querySelector('#btnSubmitReserva').style.display = 'inline-block'; // Ocultar botón de envío en modo vista
            }

        } catch (error) {
            console.error("Error en fetchDataAndPopulateForm:", error);
            modalMensaje.textContent = `Error al cargar datos: ${error.message}`;
            modalMensaje.className = 'error';
        }
    }


    function initModal() {
        const modal = document.getElementById('miModalReserva');
        const spanCerrar = modal.querySelector('.modal-reserva-cerrar');
        const formReserva = modal.querySelector('#formReserva');
        const formPago = modal.querySelector('#formPago');
        const seccionPago = modal.querySelector('#seccionPago');
        const modalMensaje = modal.querySelector('#modalReservaMensaje');
        const modalTitulo = modal.querySelector('#modalReservaTitulo');
        const btnSubmitReserva = modal.querySelector('#btnSubmitReserva');
        const hiddenReservaIdField = modal.querySelector('#reservaId');

        if (!modal || !spanCerrar || !formReserva || !formPago) {
            console.error("Error: No se encontraron todos los elementos del modal.");
            return;
        }

        // Listener para todos los botones que abren el modal
        document.querySelectorAll('.btn-modal-reserva-trigger').forEach(button => {
            button.addEventListener('click', async function() {
                // Resetear estado
                formReserva.reset();
                formPago.reset();
                modalMensaje.textContent = '';
                modalMensaje.className = '';
                hiddenReservaIdField.value = ''; // Limpiar ID oculto
                currentReservaId = null;
                seccionPago.style.display = 'none';
                formReserva.style.display = 'flex';
                // Habilitar campos y botón de envío (por si estaban deshabilitados en modo 'view')
                Array.from(formReserva.elements).forEach(el => {
                    
                }
                );
                btnSubmitReserva.style.display = 'inline-block';


                // Configurar desde data attributes
                currentAction = this.dataset.action || 'create';
                const apiEndpoint = this.dataset.apiEndpoint; // ej: "reservas/123"
                const populateForm = (this.dataset.populateForm === 'true');

                modalTitulo.textContent = this.dataset.modalTitle || (currentAction === 'edit' ? 'Editar Reserva' : 'Agregar Impuesto ISEPAAA');
                btnSubmitReserva.textContent = this.dataset.submitButtonText || (currentAction === 'edit' ? 'Actualizar Reserva' : 'Agregar Impuesto ISEPAAA');

                if ((currentAction === 'edit' || currentAction === 'view') && apiEndpoint) {
                    // Si es 'edit' o 'view', currentReservaId se establecerá dentro de fetchDataAndPopulateForm
                    await fetchDataAndPopulateForm(apiEndpoint, formReserva, populateForm);
                    if(currentAction === 'view'){
                         Array.from(formReserva.elements).forEach(el => {
                            // No deshabilitar botones de tipo 'button' o 'submit' dentro del form si no son el principal
                            if(el.type !== 'submit' && el.type !== 'button' && el.id !== 'btnSubmitReserva'){
                                // el.disabled = true;
                            }
                        });
                        btnSubmitReserva.style.display = 'inline-block'; // Ocultar botón principal de submit
                    } else { // 'edit'
                        btnSubmitReserva.style.display = 'inline-block';
                    }
                } else { // 'create'
                    currentReservaId = null; // Asegurar que no hay ID
                    hiddenReservaIdField.value = '';
                }

                modal.style.display = 'flex';
            });
        });

        spanCerrar.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        formReserva.onsubmit = async function(event) {
            event.preventDefault();
            modalMensaje.textContent = 'Procesando...';
            modalMensaje.className = '';

            const formData = new FormData(formReserva);
            const data = Object.fromEntries(formData.entries());
            
            // El ID de la reserva para actualizar vendrá del campo oculto
            const idParaActualizar = formData.get('reservaId'); // o currentReservaId si lo mantienes sincronizado

            let url = `${API_BASE_URL}reservas`; // URL base para crear y actualizar (tu API debe manejarlo)
            let method = 'POST';

            if (idParaActualizar) { // Si hay un ID, es una actualización
                url = `${API_BASE_URL}reservas/${idParaActualizar}`; // Endpoint para actualizar
                method = 'PUT'; // o 'PATCH'
                data.id = idParaActualizar; // Asegúrate que el ID se envíe si tu API lo espera en el cuerpo para PUT
            } else {
                 url = `${API_BASE_URL}reservas/crear`; // Endpoint para crear
                 method = 'POST';
            }
            // Remover reservaId del payload si no es necesario o tu API no lo espera para crear
            // delete data.reservaId; (si 'reservaId' es solo para el cliente y no para el payload de creación)


            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    modalMensaje.textContent = result.message;
                    modalMensaje.className = 'success';
                    currentReservaId = result.reservaId || idParaActualizar; // Guardar ID de reserva

                    if (method === 'POST' && result.reservaId) { // Si fue creación y se retornó un ID
                        modalTitulo.textContent = "Agregar Pago para Reserva: " + currentReservaId;
                        formReserva.style.display = 'none';
                        seccionPago.style.display = 'block';
                    } else if (method === 'PUT') {
                        // Decidir qué hacer después de actualizar, quizás solo cerrar o mostrar pago
                        // setTimeout(() => { modal.style.display = 'none'; }, 2000);
                         modalTitulo.textContent = "Agregar Pago para Reserva: " + currentReservaId;
                         formReserva.style.display = 'none';
                         seccionPago.style.display = 'block';
                    }
                } else {
                    modalMensaje.textContent = 'Error: ' + (result.message || (method === 'PUT' ? 'No se pudo actualizar la reserva.' : 'No se pudo crear la reserva.'));
                    modalMensaje.className = 'error';
                }
            } catch (error) {
                modalMensaje.textContent = `Error de conexión al ${method === 'PUT' ? 'actualizar' : 'crear'} reserva.`;
                modalMensaje.className = 'error';
                console.error(`Error en fetch ${method} reserva:`, error);
            }
        }

        // Manejar envío del formulario de pago (sin cambios mayores, pero usa currentReservaId)
        formPago.onsubmit = async function(event) {
            event.preventDefault();
            if (!currentReservaId) {
                modalMensaje.textContent = 'Error: No hay una reserva seleccionada para el pago.';
                modalMensaje.className = 'error';
                return;
            }
            modalMensaje.textContent = 'Procesando pago...';
            modalMensaje.className = '';

            const formData = new FormData(formPago);
            const data = Object.fromEntries(formData.entries());

            try {
                // Ajusta el endpoint si es necesario, ej. API_BASE_URL + `pagos` y pasar reserva_id en el cuerpo
                const response = await fetch(`${API_BASE_URL}reservas/${currentReservaId}/pagos`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    modalMensaje.textContent = result.message;
                    modalMensaje.className = 'success';
                    formPago.reset();
                    // Opcional: Cerrar modal o permitir más acciones
                    // setTimeout(() => { modal.style.display = 'none'; }, 2000);
                } else {
                    modalMensaje.textContent = 'Error: ' + (result.message || 'No se pudo agregar el pago.');
                    modalMensaje.className = 'error';
                }
            } catch (error) {
                modalMensaje.textContent = 'Error de conexión al agregar pago.';
                modalMensaje.className = 'error';
                console.error("Error en fetch pago:", error);
            }
        }
    }

    // Eliminar crearBotonActivador si los botones se definen en HTML
    // function crearBotonActivador() { /* ... ya no es necesario si usas .btn-modal-reserva-trigger ... */ }

    document.addEventListener('DOMContentLoaded', function() {
        injectCSS();
        injectModalHTML();
        // crearBotonActivador(); // Ya no se llama si los botones están en el HTML
        initModal(); // Inicializa la lógica del modal y los listeners para .btn-modal-reserva-trigger
    });

})();