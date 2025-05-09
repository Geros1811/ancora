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

.hidden-section {
    display: none;
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
                        <span onclick="toggleLock(this)" class="lock-icon" style="color: ${pago.bloqueado ? 'green' : 'black'};">${pago.bloqueado ? '🔒' : '🔓'}</span>
                    `;
    
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
        const inputs = row.querySelectorAll("input:not(.pago-id)");
        inputs.forEach(input => input.disabled = true);
        row.style.backgroundColor = "#d4edda";
    }
    
    function updateAcumulado() {
        let rows = document.getElementById("calendario-pagos-body").rows;
        let total = 0;
    
        for (let i = 0; i < rows.length; i++) {
            let paymentCell = rows[i].cells[2].getElementsByTagName("input")[0];
            let acumuladoCell = rows[i].cells[3].getElementsByTagName("input")[0];
    
            let paymentValue = parseFloat(paymentCell.value.replace(/,/g, "")) || 0;
            total += paymentValue;
            acumuladoCell.value = formatCurrencyValue(total);
        }
    
        document.getElementById("total-pago").textContent = formatCurrencyValue(total) + " MXN";
    }
    
    function formatCurrency(input) {
        let value = parseFloat(input.value.replace(/,/g, "")) || 0;
        if (input.value !== "") {
            input.value = value.toFixed(2);
        }
        updateAcumulado();
    }
    
    function formatCurrencyValue(value) {
        return new Intl.NumberFormat("es-MX", { 
            minimumFractionDigits: 2,
            maximumFractionDigits: 2 
        }).format(value);
    }
    
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
                cell.innerHTML = '<input type="number" value="0.00" class="editable" oninput="updateAcumulado()" onblur="formatCurrency(this)">';
            } else if (i === 3) {
                cell.innerHTML = '<input type="text" value="0.00" class="editable" disabled>';
            } else if (i === 4) {
                cell.innerHTML = '<input type="hidden" class="pago-id" value=""><span onclick="toggleLock(this)" class="lock-icon" style="color: green;">🔓</span>';
            }
        }
        updateAcumulado();
    }
    
    function toggleLock(lockIcon) {
        @if(Auth::user()->role == 'cliente')
            alert('No tienes permiso para desbloquear este elemento.');
            return;
        @endif
        
        const row = lockIcon.closest("tr");
        const inputs = row.querySelectorAll("input:not(.pago-id)");
        const pagoId = row.querySelector(".pago-id").value;

        if (lockIcon.innerHTML === "🔓") {
            inputs.forEach(input => input.disabled = true);
            row.style.backgroundColor = "#d4edda";
            lockIcon.style.color = "green";
            lockIcon.innerHTML = "🔒";
            
            // Solo guardar si es un registro existente (tiene ID)
            if (pagoId) {
                guardarCambios();
            }
        } else if (lockIcon.innerHTML === "🔒") {
            const password = prompt("Introduce la contraseña para desbloquear:");
            
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
                    inputs.forEach(input => input.disabled = false);
                    row.style.backgroundColor = "";
                    lockIcon.style.color = "";
                    lockIcon.innerHTML = "🔓";
                } else {
                    alert("Contraseña incorrecta.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Hubo un problema al validar la contraseña.");
            });
        }
    }

    function guardarCambios() {
        let rows = document.getElementById("calendario-pagos-body").rows;
        let data = [];
        let obraId = {{ $obra->id }};

        for (let i = 0; i < rows.length; i++) {
            let idInput = rows[i].querySelector(".pago-id");
            let concepto = rows[i].cells[0].getElementsByTagName("input")[0].value;
            let fecha_pago = rows[i].cells[1].getElementsByTagName("input")[0].value;
            let pago = parseFloat(rows[i].cells[2].getElementsByTagName("input")[0].value.replace(/,/g, "")) || 0;
            let bloqueado = rows[i].cells[4].querySelector("span").innerHTML === "🔒";

            data.push({
                id: idInput ? idInput.value : null,
                concepto: concepto,
                fecha_pago: fecha_pago,
                pago: pago,
                bloqueado: bloqueado
            });
        }

        fetch('/guardar-cambios', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ 
                pagos: data, 
                obra_id: obraId 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar IDs de los nuevos registros creados
                if (data.ids) {
                    data.ids.forEach((newId, index) => {
                        if (rows[index]) {
                            let idInput = rows[index].querySelector(".pago-id");
                            if (idInput && !idInput.value) {
                                idInput.value = newId;
                            }
                        }
                    });
                }
                alert("Cambios guardados exitosamente.");
            } else {
                alert("Error: " + (data.message || "Hubo un problema al guardar"));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error de conexión al guardar");
        });
    }
</script>