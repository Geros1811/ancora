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
            <tr style="background-color: #ADD8E6;">
                <th colspan="3" style="text-align: left; background-color: #ADD8E6;">Cliente</th>
            </tr>
            <tr style="background-color: #ADD8E6;">
                <td style="background-color: #ADD8E6;">1</td>
                <td style="background-color: #ADD8E6;">MONTO DE OBRA</td>
                <td style="background-color: #ADD8E6;">${{ number_format($obra->presupuesto, 2) }}</td>
            </tr>
            <tr style="background-color: #ADD8E6;">
                <td style="background-color: #ADD8E6;">2</td>
                <td style="background-color: #ADD8E6;">PAGOS CLIENTE</td>
                <td style="background-color: #ADD8E6;">$<span id="total-pagos-cliente">{{ number_format($totalPagosCliente, 2) }}</span></td>
            </tr>
            <tr style="background-color: #ADD8E6;">
                <td style="background-color: #ADD8E6;">3</td>
                <td style="background-color: #ADD8E6;">FALTA POR PAGAR</td>
                <td style="background-color: #ADD8E6;">$<span id="falta-por-pagar">{{ number_format($obra->presupuesto - $totalPagosCliente, 2) }}</span></td>
            </tr>
            <tr style="background-color: #90EE90;">
                <th colspan="3" style="text-align: left; background-color: #90EE90;">Estado de Resultados</th>
            </tr>
            <tr style="background-color: #90EE90;">
                <td style="background-color: #90EE90;">4</td>
                <td style="background-color: #90EE90;">TOTAL GASTOS DE OBRA</td>
                <td style="background-color: #90EE90;">${{ number_format($costosDirectos->sum('costo') + $costosIndirectos->sum('costo') + $pagosAdministrativos->sum('costo') - $pagosAdministrativosOcultos, 2) }}</td>
            </tr>
            <tr style="background-color: #90EE90;">
                <td style="background-color: #90EE90;">5</td>
                <td style="background-color: #90EE90;">INGRESOS DE OBRA</td>
                <td style="background-color: #90EE90;">$<span id="total-ingresos">{{ number_format($ingresos->sum('importe'), 2) }}</span></td>
            </tr>
            <tr style="background-color: #90EE90;">
                <td style="background-color: #90EE90;">6</td>
                <td style="background-color: #90EE90;">EFECTIVO EN CAJA</td>
                <td style="background-color: #90EE90;">$<span id="efectivo-en-caja">{{ number_format(($totalPagosCliente + $ingresos->sum('importe')) - ($costosDirectos->sum('costo') + $costosIndirectos->sum('costo') + $pagosAdministrativos->sum('costo') - $pagosAdministrativosOcultos), 2) }}</span></td>
            </tr>
            <tr style="background-color: #90EE90;">
                <td style="background-color: #90EE90;">7</td>
                <td style="background-color: #90EE90;">PRECIO POR M2</td>
                <td style="background-color: #90EE90;">$<span id="precio-por-m2">{{ number_format(($costosDirectos->sum('costo') + $costosIndirectos->sum('costo') + $pagosAdministrativos->sum('costo') - $pagosAdministrativosOcultos) / $obra->metros_cuadrados, 2) }}</span></td>
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
        let pagosAdministrativos = parseFloat("{{ $pagosAdministrativos->sum('costo') }}");
        let ingresos = parseFloat("{{ $ingresos->sum('importe') }}");
        let efectivoEnCaja = (total + ingresos) - (costosDirectos + costosIndirectos + pagosAdministrativos);
        document.getElementById("efectivo-en-caja").textContent = formatCurrencyValue(efectivoEnCaja);

        // Actualizar el valor de "Precio por M2" en la tabla de resumen
        let metrosCuadrados = parseFloat("{{ $obra->metros_cuadrados }}");
        let precioPorM2 = (costosDirectos + costosIndirectos + pagosAdministrativos) / metrosCuadrados;
        document.getElementById("precio-por-m2").textContent = formatCurrencyValue(precioPorM2);
    }
    // ...existing code...
</script>
