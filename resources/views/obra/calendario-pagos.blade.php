<!-- Tabla de calendario de pagos -->
<h2>
    <span class="toggle-button" onclick="toggleSection('calendario-pagos')">+</span>
    Calendario de Pagos (Total: $<span id="total-pago">0.00 MXN</span>)
</h2>
<div id="calendario-pagos" class="hidden-section">
    @if(Auth::user()->role != 'cliente')
        <button onclick="addRow()">Agregar Fila</button>
    @endif
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
            <!-- Las filas se cargarán dinámicamente desde la base de datos -->
        </tbody>
    </table>
</div>

@if(Auth::user()->role == 'arquitecto' || Auth::user()->role == 'cliente')
    <a class="upload-images-link" href="{{ route('obras.imagenes', ['obraId' => $obra->id]) }}">Subir Imágenes</a>
@endif

<style>
.upload-images-link {
    margin-top: 10px;
    display: inline-block;
    background-color: #007bff;
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 4px;
}

.upload-images-link:hover {
    background-color: #0056b3;
}
</style>

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
                            cellAccion.innerHTML = `
                            <input type="hidden" class="pago-id" value="${pago.id}">
                            <span onclick="toggleLock(this)" class="lock-icon" style="color: ${pago.bloqueado ? 'green' : 'black'};">${pago.bloqueado ? '🔒' : '🔓'}</span>`;
        
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
                        cell.innerHTML = '<input type="hidden" class="pago-id" value=""><span onclick="toggleLock(this)" class="lock-icon" style="color: green;">🔓</span>';
                    }
                }
        
                // Actualizar el acumulado al agregar una fila
                updateAcumulado();
            }
        
            // Función para activar/desactivar el bloqueo
            function toggleLock(lockIcon) {
                @if(Auth::user()->role == 'cliente')
                    alert('No tienes permiso para desbloquear este elemento.');
                    return;
                @endif
                const row = lockIcon.closest("tr"); // Fila correspondiente al candado
                const inputs = row.querySelectorAll("input"); // Todos los campos de entrada within the row
        
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


            // Función para guardar los cambios
            function guardarCambios() {
    let rows = document.getElementById("calendario-pagos-body").rows;
    let data = [];
    let obraId = {{ $obra->id }}; // Obtener el ID de la obra

    for (let i = 0; i < rows.length; i++) {
        let idInput = rows[i].querySelector(".pago-id");
        let id = idInput ? idInput.value : null;

        let concepto = rows[i].cells[0].getElementsByTagName("input")[0].value;
        let fecha_pago = rows[i].cells[1].getElementsByTagName("input")[0].value;
        let pago = parseFloat(rows[i].cells[2].getElementsByTagName("input")[0].value.replace(/,/g, "")) || 0;
        let acumulado = parseFloat(rows[i].cells[3].getElementsByTagName("input")[0].value.replace(/[^0-9.-]+/g, "")) || 0;
        let bloqueado = rows[i].cells[4].getElementsByTagName("span")[0].innerHTML === "🔒";

        data.push({
            id: id ?? null,
            concepto: concepto,
            fecha_pago: fecha_pago,
            pago: pago,
            acumulado: acumulado,
            bloqueado: bloqueado ? 1 : 0
        });
    }

    fetch('/guardar-cambios', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ pagos: data, obra_id: obraId })
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