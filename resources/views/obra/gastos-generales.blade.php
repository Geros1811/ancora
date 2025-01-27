<!-- Tabla de gastos generales -->
<h2>
    <span class="toggle-button" onclick="toggleSection('gastos-generales')">+</span>
    Resumen
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
                <td>$00.00</td>
            </tr>
            <tr>
                <td>2</td>
                <td>PAGOS CLIENTE</td>
                <td>$00.00</td>
            </tr>
            <tr>
                <td>3</td>
                <td>FALTA POR PAGAR</td>
                <td>$00.00</td>
            </tr>
            <tr>
                <td>4</td>
                <td>EFECTIVO EN CAJA</td>
                <td>$00.00</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        updateTotalPagosCliente();
    });

    function updateTotalPagosCliente() {
        let total = 0;
        document.querySelectorAll("#calendario-pagos-body input[type='number']").forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById("total-pagos-cliente").textContent = total.toFixed(2);
    }

    // Asegurarse de que updateTotalPagosCliente se llame después de cargar los datos del calendario de pagos
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
                updateTotalPagosCliente(); // Actualizar el total de pagos cliente después de cargar los datos
            })
            .catch(error => {
                console.error('Error al cargar el calendario de pagos:', error);
            });
    }
</script>