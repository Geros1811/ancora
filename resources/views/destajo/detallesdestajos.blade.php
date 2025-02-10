@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">Detalles de Destajo: {{ $detalle->frente }}</h1>
        <h2 style="font-size: 20px; color: #34495e; margin-bottom: 10px;">{{ $nombre_nomina }} ({{ $dia_inicio }} - {{ $fecha_inicio }} al {{ $dia_fin }} - {{ $fecha_fin }})</h2>
    </div>

    <div class="table-container" style="margin-top: 20px;">
        <table class="obra-table">
            <thead>
                <tr>
                    <th>Cotización</th>
                    <th>Monto Aprobado</th>
                    <th id="pago-header">Pago 1 <button type="button" class="btn btn-success btn-sm" onclick="agregarColumnaPago(this)">+</button></th>
                    <th>Pendiente</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>

        </table>
        <div style="margin-top: 10px; text-align: right;">
            <strong>Monto Total Autorizado:</strong> $<span id="monto_aprobado_total">0.00</span>
        </div>
        <button type="button" class="btn btn-primary" onclick="agregarFila()">Agregar Fila</button>
    </div>

<style>
    .obra-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        color: black;
        border-radius: 8px;
    }

    .obra-table th, .obra-table td {
        padding: 3px;
        text-align: left;
        border: 1px solid #ddd;
        font-size: 12px;
    }

    .obra-table th {
        background-color: #0056b3;
        color: white;
    }

    .obra-table tr:nth-child(even) {
        background-color: #f9f9f9;
        }

    .obra-table tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .obra-table td input, .obra-table td select {
        width: 100%;
        padding: 3px;
        border-radius: 4px;
        border: 1px solid #ddd;
        box-sizing: border-box;
        background-color: white;
        color: black;
        font-size: 12px;
    }

    .table-container button {
        margin-top: 15px;
    }
</style>

<script>
    function calcularTotalMontoAprobado() {
        let total = 0;
        document.querySelectorAll('input[name="monto_aprobado[]"]').forEach(function(input) {
            total += Number(input.value);
        });
        document.getElementById('monto_aprobado_total').innerText = total.toFixed(2);
    }

    function calcularPendiente(row) {
        let montoAprobado = Number(row.querySelector('input[name="monto_aprobado[]"]').value) || 0;
        let totalPagos = 0;
        let pagoInputs = row.querySelectorAll('td > input[name^="pago_numero"]');
        pagoInputs.forEach(function(pago) {
            totalPagos += Number(pago.value) || 0;
        });
        let pendiente = montoAprobado - totalPagos;
        row.querySelector('input[name="pendiente[]"]').value = pendiente.toFixed(2);
		if (pendiente <= 0) {
			row.querySelector('select[name="estado[]"]').value = 'Finalizado';
		} else {
			row.querySelector('select[name="estado[]"]').value = 'En Curso';
		}
    }

    function agregarColumnaPago(button) {
        button.disabled = true; // Disable the button that was clicked

        const row = button.closest('tr');
        let pagoCount = row.dataset.pagoCount || 1;
        pagoCount++;
        row.dataset.pagoCount = pagoCount;

        const headerRow = button.closest('thead').querySelector('tr');
        const paymentHeader = button.closest('th');
        const newHeader = document.createElement('th');
        newHeader.innerHTML = `Pago ${pagoCount} <button type="button" class="btn btn-success btn-sm" onclick="agregarColumnaPago(this)">+</button>`;
        headerRow.insertBefore(newHeader, paymentHeader.nextSibling);

        const tableBody = document.querySelector('.obra-table tbody');
        const allRows = tableBody.querySelectorAll('tr');
        allRows.forEach(row => {
            let currentPagoCount = row.dataset.pagoCount || 1;
            const newColumn = document.createElement('td');
            newColumn.innerHTML = `
                Fecha: <input type="date" name="pago_fecha[${currentPagoCount}][]" class="form-control" onchange="calcularPendiente(this.closest('tr'))">
                Pago: <input type="number" name="pago_numero[${currentPagoCount}][]" class="form-control pago_numero" value="" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
            `;
            const pendienteCell = row.cells[row.cells.length - 1]; // Inserts before Estado
            row.insertBefore(newColumn, pendienteCell);
        });
    }

    function agregarFila() {
        const tableBody = document.querySelector('.obra-table tbody');
        const newRow = document.createElement('tr');
        newRow.dataset.pagoCount = 1; // Initialize pagoCount for the new row
        let newRowHTML = `
            <td><input type="text" name="cotizacion[]" class="form-control" value=""></td>
            <td><input type="number" name="monto_aprobado[]" class="form-control monto_aprobado" value="" placeholder="$" oninput="calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()"></td>
        `;

        let headerRow = document.querySelector('.obra-table thead tr');
        let numPagoColumns = headerRow.querySelectorAll('th').length - 4;
        for (let i = 0; i < numPagoColumns; i++) {
            let pagoNumber = i + 1;
            newRowHTML += `
                <td>
                    Fecha: <input type="date" name="pago_fecha_${pagoNumber}[]" class="form-control" onchange="calcularPendiente(this.closest('tr'))">
                    Pago: <input type="number" name="pago_numero_${pagoNumber}[]" class="form-control pago_numero" value="" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
                </td>
            `;
        }

        newRowHTML += `
            <td><input type="number" name="pendiente[]" class="form-control" value="" placeholder="$" readonly></td>
            <td>
                <select name="estado[]" class="form-control" disabled>
                    <option value="En Curso" selected>En Curso</option>
                    <option value="Finalizado">Finalizado</option>
                </select>
            </td>
        `;
        newRow.innerHTML = newRowHTML;
        tableBody.appendChild(newRow);
        calcularPendiente(newRow.querySelector('input[name="monto_aprobado[]"]').closest('tr'));
        calcularTotalMontoAprobado();
    }

    // Call calcularTotalMontoAprobado initially to calculate the total on page load
    calcularTotalMontoAprobado();
</script>
@endsection
