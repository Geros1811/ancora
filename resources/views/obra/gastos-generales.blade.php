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
                <td>${{ number_format($obra->presupuesto, 2) }}</td>
            </tr>
            <tr>
                <td>2</td>
                <td>PAGOS CLIENTE</td>
                <td>$<span id="total-pagos-cliente">{{ number_format($totalPagosCliente, 2) }}</span></td>
            </tr>
            <tr>
                <td>3</td>
                <td>FALTA POR PAGAR</td>
                <td>$<span id="falta-por-pagar">{{ number_format($obra->presupuesto - $totalPagosCliente, 2) }}</span></td>
            </tr>
            <tr>
                <td>4</td>
                <td>EFECTIVO EN CAJA</td>
                <td>$<span id="efectivo-en-caja">{{ number_format($totalPagosCliente - ($costosDirectos->sum('costo') + $costosIndirectos->sum('costo')), 2) }}</span></td>
            </tr>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ...existing code...

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
        document.getElementById("total-pagos-cliente").textContent = formatCurrencyValue(total); // Actualizar en la tabla de resumen

        // Actualizar el valor de "FALTA POR PAGAR" en la tabla de resumen
        let presupuesto = parseFloat("{{ $obra->presupuesto }}");
        let faltaPorPagar = presupuesto - total;
        document.getElementById("falta-por-pagar").textContent = formatCurrencyValue(faltaPorPagar);

        // Actualizar el valor de "EFECTIVO EN CAJA" en la tabla de resumen
        let costosDirectos = parseFloat("{{ $costosDirectos->sum('costo') }}");
        let costosIndirectos = parseFloat("{{ $costosIndirectos->sum('costo') }}");
        let efectivoEnCaja = total - (costosDirectos + costosIndirectos);
        document.getElementById("efectivo-en-caja").textContent = formatCurrencyValue(efectivoEnCaja);
    }
    // ...existing code...
</script>
