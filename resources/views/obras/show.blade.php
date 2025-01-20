@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="dashboard-container">
        <h1>Detalles de la Obra: {{ $obra->nombre }}</h1>

        @section('head')
        <link rel="stylesheet" href="{{ asset('css/obra-details.css') }}">
    @endsection
    

        <!-- Sección de información general -->
        <div class="obra-info">
            <p><strong>Presupuesto:</strong> ${{ number_format($obra->presupuesto, 2) }}</p>
            <p><strong>Cliente:</strong> {{ $obra->cliente }}</p>
            <p><strong>Fecha de Inicio:</strong> {{ $obra->fecha_inicio }}</p>
            <p><strong>Fecha de Término:</strong> {{ $obra->fecha_termino }}</p>
            <p><strong>Residente de Obra:</strong> {{ $obra->residente }}</p>
            <p><strong>Ubicación:</strong> {{ $obra->ubicacion }}</p>
            <p><strong>Descripción:</strong> {{ $obra->descripcion }}</p>
        </div>

<!-- Tabla de calendario de pagos -->
<h2>
    <span class="toggle-button" onclick="toggleSection('calendario-pagos')">+</span>
    Calendario de Pagos (Total: $<span id="total-pago">0.00 MXN</span>)
</h2>
<div id="calendario-pagos" class="hidden-section">
    <button onclick="addRow('calendario-pagos-body')">Agregar Fila</button>
    <table class="obra-table">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Fecha de Pago</th>
                <th>Pago</th>
                <th>Acumulado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="calendario-pagos-body">
            <tr>
                <td><input type="text" value="Anticipo" class="editable"></td>
                <td><input type="date" class="editable"></td>
                <td><input type="number" value="0" class="editable" oninput="updateAcumulado()" onblur="formatCurrency(this)"></td>
                <td><input type="text" value="$0.00" class="editable" disabled></td>
                <td><span onclick="toggleLock(this)" class="lock-icon" style="color: green;">🔓</span></td>
            </tr>
        </tbody>
    </table>
</div>

        <!-- Tabla de gastos generales -->
        <h2>
            <span class="toggle-button" onclick="toggleSection('gastos-generales')">+</span>
            Resumen (Total: $963,872.97)
        </h2>
        <div id="gastos-generales" class="hidden-section">
            <table class="obra-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nombre</th>
                        <th>Costo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>MONTO DE OBRA</td>
                        <td>$1,307,872.97</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>PAGOS CLIENTE</td>
                        <td>$344,000.00</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>FALTA POR PAGAR</td>
                        <td>$963,872.97</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>EFECTIVO EN CAJA</td>
                        <td>$963,872.97</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabla de costos directos -->
        <h2>
            <span class="toggle-button" onclick="toggleSection('costos-directos')">+</span>
            Costos Directos (Total: $161,318.50)
        </h2>
        <div id="costos-directos" class="hidden-section">
            <table class="obra-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nombre</th>
                        <th>Costo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Materiales</td>
                        <td>$155,258.50</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Mano de Obra</td>
                        <td>#REF!</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Equipo de Seguridad</td>
                        <td>$6,000.00</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Herramienta Menor</td>
                        <td>$60.00</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Maquinaria Menor</td>
                        <td>$60.00</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Limpieza</td>
                        <td>#REF!</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Maquinaria Mayor</td>
                        <td>#REF!</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Cimbras</td>
                        <td>#REF!</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Acarreos</td>
                        <td>#REF!</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Comidas</td>
                        <td>$2,500.00</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>Trámites</td>
                        <td>#REF!</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><strong>TOTAL</strong></td>
                        <td><strong>#REF!</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabla de costos indirectos -->
        <h2>
            <span class="toggle-button" onclick="toggleSection('costos-indirectos')">+</span>
            Costos Indirectos (Total: $10,000.00)
        </h2>
        <div id="costos-indirectos" class="hidden-section">
            <table class="obra-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nombre</th>
                        <th>Costo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Papeleria</td>
                        <td>$2,500.00</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Gasolina</td>
                        <td>$2,500.00</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Rentas</td>
                        <td>$5,000.00</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Utilidades</td>
                        <td>$2,500.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            cargarCalendarioPagos();
        });

        function cargarCalendarioPagos() {
            fetch(`/obras/{{ $obra->id }}/calendario-pagos`)
                .then(response => response.json())
                .then(data => {
                    let tableBody = document.getElementById("calendario-pagos-body");
                    tableBody.innerHTML = ""; // Limpiar la tabla antes de agregar los datos

                    data.forEach(pago => {
                        let newRow = tableBody.insertRow();

                        let cellConcepto = newRow.insertCell(0);
                        cellConcepto.innerHTML = `<input type="text" value="${pago.concepto}" class="editable">`;

                        let cellFechaPago = newRow.insertCell(1);
                        cellFechaPago.innerHTML = `<input type="date" value="${pago.fecha_pago}" class="editable">`;

                        let cellPago = newRow.insertCell(2);
                        cellPago.innerHTML = `<input type="number" value="${pago.pago}" class="editable" oninput="updateAcumulado()" onblur="formatCurrency(this)">`;

                        let cellAcumulado = newRow.insertCell(3);
                        cellAcumulado.innerHTML = `<input type="text" value="${formatCurrencyValue(pago.acumulado)}" class="editable" disabled>`;

                        let cellAccion = newRow.insertCell(4);
                        cellAccion.innerHTML = `<span onclick="toggleLock(this)" class="lock-icon" style="color: ${pago.bloqueado ? 'green' : 'black'};">${pago.bloqueado ? '🔒' : '🔓'}</span>`;

                        if (pago.bloqueado) {
                            bloquearFila(newRow);
                        }
                    });

                    updateAcumulado(); // Actualizar el acumulado después de cargar los datos
                })
                .catch(error => {
                    console.error('Error al cargar el calendario de pagos:', error);
                });
        }

        function bloquearFila(row) {
            const inputs = row.querySelectorAll("input");
            inputs.forEach(input => input.disabled = true);
            row.style.backgroundColor = "#d4edda"; // Cambiar color de fondo a verde claro
        }

        // Función para actualizar el acumulado
        function updateAcumulado() {
            let rows = document.getElementById("calendario-pagos-body").rows;
            let total = 0;

            for (let i = 0; i < rows.length; i++) {
                let paymentCell = rows[i].cells[2].getElementsByTagName("input")[0]; // Pago
                let acumuladoCell = rows[i].cells[3].getElementsByTagName("input")[0]; // Acumulado

                // Convertir el valor de pago a número (sin formato)
                let paymentValue = parseFloat(paymentCell.value.replace(/,/g, "")) || 0;

                // Sumar al acumulado total
                total += paymentValue;

                // Actualizar el acumulado formateado (sin permitir edición)
                acumuladoCell.value = formatCurrencyValue(total);
            }

            // Actualizar el total formateado en el título
            document.getElementById("total-pago").textContent = formatCurrencyValue(total) + " MXN";
        }

        // Función para formatear a moneda MXN solo al perder el foco
        function formatCurrency(input) {
            // Formatear solo cuando el usuario termine de escribir
            let value = parseFloat(input.value.replace(/,/g, "")) || 0;
            if (input.value !== "") {
                input.value = value.toFixed(2); // Dejar sin el símbolo de moneda para celdas editables
            }
            updateAcumulado(); // Recalcular el acumulado
        }

        // Función para formatear un número como moneda para celdas no editables
        function formatCurrencyValue(value) {
            return new Intl.NumberFormat("es-MX", { style: "currency", currency: "MXN" }).format(value).replace("$", "");
        }

        // Función para agregar una nueva fila
        function addRow() {
            var table = document.getElementById("calendario-pagos-body");
            var newRow = table.insertRow();

            for (let i = 0; i < 5; i++) {
                var cell = newRow.insertCell(i);
                if (i === 0) {
                    cell.innerHTML = '<input type="text" value="Nuevo Concepto" class="editable">';
                } else if (i === 1) {
                    cell.innerHTML = '<input type="date" class="editable">';
                } else if (i === 2) {
                    cell.innerHTML = '<input type="number" value="0.00" class="editable" onblur="formatCurrency(this)">';
                } else if (i === 3) {
                    cell.innerHTML = '<input type="text" value="$0.00" class="editable" disabled>';
                } else if (i === 4) {
                    cell.innerHTML = '<span onclick="toggleLock(this)" class="lock-icon" style="color: green;">🔓</span>';
                }
            }

            // Actualizar el acumulado al agregar una fila
            updateAcumulado();
        }

        // Función para activar/desactivar el bloqueo
        function toggleLock(lockIcon) {
            const row = lockIcon.closest("tr"); // Fila correspondiente al candado
            const inputs = row.querySelectorAll("input"); // Todos los campos de entrada dentro de la fila

            if (lockIcon.innerHTML === "🔓") {
                // Bloquear todos los campos de la fila y ponerla verde
                inputs.forEach(input => input.disabled = true);
                row.style.backgroundColor = "#d4edda"; // Cambiar color de fondo a verde claro
                lockIcon.style.color = "green";
                lockIcon.innerHTML = "🔒"; // Cambiar icono a "bloqueado"
                
                // Guardar cambios automáticamente
                guardarCambios();
            } else if (lockIcon.innerHTML === "🔒") {
                // Solicitar contraseña
                const password = prompt("Introduce la contraseña para desbloquear:");

                // Validar la contraseña con AJAX
                fetch('/validar-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ password: password })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Desbloquear todos los campos de la fila y restaurar color
                        inputs.forEach(input => input.disabled = false);
                        row.style.backgroundColor = ""; // Restaurar color de fondo original
                        lockIcon.style.color = ""; // Restaurar color original del icono
                        lockIcon.innerHTML = "🔓"; // Cambiar icono a "desbloqueado"
                    } else {
                        alert("Contraseña incorrecta."); // Mostrar mensaje de error
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Hubo un problema al validar la contraseña.");
                });
            }
        }

        // Función para mostrar/ocultar secciones
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            const button = section.previousElementSibling.querySelector('.toggle-button');
            if (section.classList.contains('hidden-section')) {
                section.classList.remove('hidden-section');
                button.textContent = '-';
            } else {
                section.classList.add('hidden-section');
                button.textContent = '+';
            }
        }

        // Función para guardar los cambios
        function guardarCambios() {
            let rows = document.getElementById("calendario-pagos-body").rows;
            let data = [];
            let obraId = {{ $obra->id }}; // Obtener el ID de la obra

            for (let i = 0; i < rows.length; i++) {
                let concepto = rows[i].cells[0].getElementsByTagName("input")[0].value;
                let fecha_pago = rows[i].cells[1].getElementsByTagName("input")[0].value; // Cambiar a fecha_pago
                let pago = parseFloat(rows[i].cells[2].getElementsByTagName("input")[0].value.replace(/,/g, "")) || 0;
                let acumulado = parseFloat(rows[i].cells[3].getElementsByTagName("input")[0].value.replace(/,/g, "")) || 0;
                let bloqueado = rows[i].cells[4].getElementsByTagName("span")[0].innerHTML === "🔒";

                data.push({
                    concepto: concepto,
                    fecha_pago: fecha_pago, // Cambiar a fecha_pago
                    pago: pago,
                    acumulado: acumulado,
                    bloqueado: bloqueado
                });
            }

            fetch('/guardar-cambios', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ pagos: data, obra_id: obraId }) // Enviar el ID de la obra
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Cambios guardados exitosamente.");
                } else {
                    alert("Hubo un problema al guardar los cambios.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Hubo un problema al guardar los cambios.");
            });
        }
    </script>
@endsection
