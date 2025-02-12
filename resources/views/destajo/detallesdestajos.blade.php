@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">Detalles de Destajo: {{ $detalle->frente }}</h1>
        <h2 style="font-size: 20px; color: #34495e; margin-bottom: 10px;">{{ $nombre_nomina }} ({{ $dia_inicio }} - {{ $fecha_inicio }} al {{ $dia_fin }} - {{ $fecha_fin }})</h2>
    </div>

    <form action="{{ route('detalles.destajos.store', ['obraId' => $obraId, 'destajoId' => $detalle->id]) }}" method="POST">
        @csrf
        

        <div class="table-container" style="margin-top: 20px;">
            <table class="obra-table">
                <thead>
                    <tr>
                        <th>Cotización</th>
                        <th>Monto Aprobado</th>
                        <th id="pago-header">
                            Pago 1 
                            <button type="button" class="btn btn-success btn-sm" onclick="agregarColumnaPago(this)">+</button>
                        </th>
                        @php
                            $maxPagos = 1;
                        @endphp
                        @foreach($destajoDetalles as $destajoDetalle)
                            @if($destajoDetalle->pagos)
                                @php
                                    $pagos = json_decode($destajoDetalle->pagos, true);
                                    $maxPagos = max($maxPagos, count($pagos));
                                @endphp
                            @endif
                        @endforeach
                        @for($i = 2; $i <= $maxPagos; $i++)
                            <th>Pago {{ $i }}</th>
                        @endfor
                        <th>Pendiente</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($destajoDetalles as $destajoDetalle)
                    <tr class="{{ $detalle->locked ? 'locked-row' : '' }}">
                        <td><input type="text" name="cotizacion[]" class="form-control" value="{{ $destajoDetalle->cotizacion }}" {{ $detalle->locked ? 'disabled' : '' }}></td>
                        <td><input type="number" name="monto_aprobado[]" class="form-control monto_aprobado" value="{{ $destajoDetalle->monto_aprobado }}" placeholder="$" oninput="calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()" {{ $detalle->locked ? 'disabled' : '' }}></td>

                        @php
                            $pagos = $destajoDetalle->pagos ? json_decode($destajoDetalle->pagos, true) : [];
                        @endphp
                        @for($i = 1; $i <= $maxPagos; $i++)
                            <td>
                                Fecha: <input type="date" name="pago_fecha_{{ $i }}[]" class="form-control" value="{{ $pagos[$i]['fecha'] ?? '' }}" onchange="calcularPendiente(this.closest('tr'))" {{ $detalle->locked ? 'disabled' : '' }}>
                                Pago: <input type="number" name="pago_numero_{{ $i }}[]" class="form-control pago_numero" value="{{ $pagos[$i]['numero'] ?? '' }}" placeholder="$" oninput="calcularPendiente(this.closest('tr'))" {{ $detalle->locked ? 'disabled' : '' }}>
                            </td>
                        @endfor

                        <td><input type="number" name="pendiente[]" class="form-control" value="{{ $destajoDetalle->pendiente }}" placeholder="$" readonly></td>
                        <td>
                            <select name="estado[]" class="form-control" {{ $detalle->locked ? 'disabled' : '' }}>
                                <option value="En Curso" {{ $destajoDetalle->estado == 'En Curso' ? 'selected' : '' }}>En Curso</option>
                                <option value="Finalizado" {{ $destajoDetalle->estado == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 10px; text-align: right;">
                <strong>Monto Total Autorizado:</strong> $<span id="monto_aprobado_total">0.00</span>
            </div>
            <div style="margin-top: 10px; text-align: right;">
                <strong>Cantidad Total Pagada:</strong> $<span id="cantidad_total_pagada">0.00</span>
            </div>
            <button type="button" class="btn btn-primary" onclick="agregarFila()" {{ $detalle->locked ? 'disabled' : '' }}>Agregar Fila</button>
        </div>

        <button type="submit" class="btn btn-success" {{ $detalle->locked ? 'disabled' : '' }}>Guardar Detalles</button>
        <a href="{{ route('destajos.detalles.pdf', $detalle->id) }}" class="btn btn-primary" target="_blank">
            Generar PDF
        </a>
    </form>
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

    .locked-row {
        background-color: #90EE90; /* LightGreen color */
    }
</style>

<script>
    function calcularTotalMontoAprobado() {
        let totalMontoAprobado = 0;
        let totalCantidadPagada = 0;
        document.querySelectorAll('input[name="monto_aprobado[]"]').forEach(function(input) {
            totalMontoAprobado += Number(input.value);
        });

        document.querySelectorAll('tbody tr').forEach(function(row) {
            let totalPagos = 0;
            let pagoInputs = row.querySelectorAll('td > input[name^="pago_numero"]');
            pagoInputs.forEach(function(pago) {
                totalPagos += Number(pago.value) || 0;
            });
            totalCantidadPagada += totalPagos;
        });

        document.getElementById('monto_aprobado_total').innerText = totalMontoAprobado.toFixed(2);
        document.getElementById('cantidad_total_pagada').innerText = totalCantidadPagada.toFixed(2);
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

        // Recalculate total amount paid
        let totalCantidadPagada = 0;
        document.querySelectorAll('tbody tr').forEach(function(row) {
            let totalPagos = 0;
            let pagoInputs = row.querySelectorAll('td > input[name^="pago_numero"]');
            pagoInputs.forEach(function(pago) {
                totalPagos += Number(pago.value) || 0;
            });
            totalCantidadPagada += totalPagos;
        });
        document.getElementById('cantidad_total_pagada').innerText = totalCantidadPagada.toFixed(2);
    }

    function agregarColumnaPago(button) {
        button.disabled = true; // Deshabilitar el botón de la columna anterior

        const table = document.querySelector('.obra-table');
        const headerRow = table.querySelector('thead tr');

        // Contar cuántas columnas de pago hay
        let pagoCount = headerRow.querySelectorAll('th').length - 4 + 1; // +1 porque añadiremos una nueva
        const newHeader = document.createElement('th');
        newHeader.innerHTML = `Pago ${pagoCount} <button type="button" class="btn btn-success btn-sm" onclick="agregarColumnaPago(this)">+</button>`;

        // Insertar el nuevo encabezado ANTES de la columna "Pendiente"
        const pendienteHeader = headerRow.querySelector('th:nth-last-child(2)');
        headerRow.insertBefore(newHeader, pendienteHeader);

        // Agregar la nueva columna en cada fila del cuerpo de la tabla
        document.querySelectorAll('.obra-table tbody tr').forEach(row => {
            const newColumn = document.createElement('td');
            newColumn.innerHTML = `
                Fecha: <input type="date" name="pago_fecha_${pagoCount}[]" class="form-control" onchange="calcularPendiente(this.closest('tr'))">
                Pago: <input type="number" name="pago_numero_${pagoCount}[]" class="form-control pago_numero" value="" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
            `;

            // Insertar antes de la celda "Pendiente"
            const pendienteCell = row.querySelector('td:nth-last-child(2)');
            row.insertBefore(newColumn, pendienteCell);
        });
    }

function agregarFila() {
    const tableBody = document.querySelector('.obra-table tbody');
    const newRow = document.createElement('tr');
    newRow.dataset.pagoCount = document.querySelector('.obra-table thead tr').querySelectorAll('th').length - 4;

    let newRowHTML = `
        <td><input type="text" name="cotizacion[]" class="form-control" value=""></td>
        <td><input type="number" name="monto_aprobado[]" class="form-control monto_aprobado" value="0" placeholder="$" oninput="calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()"></td>
    `;

    let numPagoColumns = parseInt(newRow.dataset.pagoCount);
    for (let i = 1; i <= numPagoColumns; i++) {
        newRowHTML += `
            <td>
                Fecha: <input type="date" name="pago_fecha_${i}[]" class="form-control" value="" onchange="calcularPendiente(this.closest('tr'))">
                Pago: <input type="number" name="pago_numero_${i}[]" class="form-control pago_numero" value="0" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
            </td>
        `;
    }

    newRowHTML += `
        <td><input type="number" name="pendiente[]" class="form-control" value="0" placeholder="$" readonly></td>
        <td>
            <select name="estado[]" class="form-control">
                <option value="En Curso" selected>En Curso</option>
                <option value="Finalizado">Finalizado</option>
            </select>
        </td>
    `;

    newRow.innerHTML = newRowHTML;
    tableBody.appendChild(newRow);
    calcularPendiente(newRow);
    calcularTotalMontoAprobado();
}

    // Call calcularTotalMontoAprobado initially to calculate the total on page load
    calcularTotalMontoAprobado();
</script>
@endsection
