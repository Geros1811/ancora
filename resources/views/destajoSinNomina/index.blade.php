@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/destajoSinNomina.css') }}">

    <h1>Destajos Sin Nomina</h1>
    <p>Si deseas usar este destajo Sin nomina</p>

    <div>
        <h2>Monto Total Aprobado: $<span id="montoTotalAprobado">0.00</span></h2>
        <h2>Pagos Totales: $<span id="pagosTotales">0.00</span></h2>
    </div>

    <form action="{{ route('destajosSinNomina.store', ['obraId' => $obraId]) }}" method="POST">
        @csrf
        <label for="partida_title">Título de la Partida:</label>
        <input type="text" id="partida_title" name="partida_title">
        <button type="submit">Crear Partida</button>
    </form>

    @foreach ($partidas as $partida)
        <div class="partida-container">
            <form id="partida-form-{{ $partida->id }}"
                action="{{ route('destajosSinNomina.storeDetalles', ['obraId' => $obraId, 'partidaId' => $partida->id]) }}"
                method="POST">
                @csrf
                <span class="toggle-button" onclick="toggleTable('partida-{{ $partida->id }}')">+</span>
                <h3>{{ $partida->title }}</h3>

                <div class="table-wrapper">
                    <table id="partida-{{ $partida->id }}" class="partida-table">
                        <thead>
                            <tr>
                                <th>Clave</th>
                                <th>Concepto</th>
                                <th>Unidad</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                                <th>Restante
                                    <button type="button" onclick="addColumn('partida-{{ $partida->id }}')">+</button>
                                </th>
                                @php
                                    $pagoNumbers = [];

                                    // Recorrer las partidas y detalles para encontrar los índices de los pagos
                                    foreach ($partida->detalles as $detalle) {
                                        if (is_array($detalle->pagos)) {
                                            $pagoNumbers = array_merge($pagoNumbers, array_keys($detalle->pagos));
                                        }
                                    }

                                    // Ordenar los números de pagos de menor a mayor
                                    $pagoNumbers = array_unique($pagoNumbers);
                                    sort($pagoNumbers);
                                @endphp
                                @foreach ($pagoNumbers as $pagoNumber)
                                    <th>Pago {{ $pagoNumber }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @if ($partida->detalles)
                                @foreach ($partida->detalles as $detalle)
                                    <tr>
                                        <td><input type="text" name="clave[]" value="{{ $detalle->clave }}"></td>
                                        <td><input type="text" name="concepto[]" value="{{ $detalle->concepto }}"></td>
                                        <td><input type="text" name="unidad[]" value="{{ $detalle->unidad }}"></td>
                                        <td><input type="number" class="cantidad" name="cantidad[]"
                                                value="{{ $detalle->cantidad }}" oninput="calcularSubtotal(this)"></td>
                                        <td><input type="number" class="precioUnitario" name="precio_unitario[]"
                                                value="{{ $detalle->precio_unitario }}" oninput="calcularSubtotal(this)"></td>
                                        <td><span class="subtotal">{{ $detalle->subtotal }}</span><input type="hidden"
                                                class="subtotal-hidden" name="subtotal[]" value="{{ $detalle->subtotal }}">
                                        </td>
                                        <td><span class="restante">0.00</span></td>
                                        @foreach ($pagoNumbers as $pagoNumber)
                                            <td>
                                                <input type="number" class="pago" name="pago_{{ $pagoNumber }}[]" 
                                                    value="{{ isset($detalle->pagos[$pagoNumber]['monto']) ? $detalle->pagos[$pagoNumber]['monto'] : '' }}">
                                                <br>
                                                <input type="date" name="pago_fecha_{{ $pagoNumber }}[]" 
                                                    value="{{ isset($detalle->pagos[$pagoNumber]['fecha']) ? $detalle->pagos[$pagoNumber]['fecha'] : '' }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        toggleTable('partida-{{ $partida->id }}');
                    });
                </script>

                <h4>Total de la Partida: $<span class="total-partida">0.00</span></h4>
                <h4>Pagos de la Partida: $<span class="total-pagos-partida">0.00</span></h4>
                <button type="submit" onclick="guardarPartida(event, '{{ $partida->id }}')">Guardar Partida</button>
            </form>
            <button class="add-row-button" onclick="addRow('partida-{{ $partida->id }}')">Añadir Fila</button>
        </div>
    @endforeach

    <script>
       function guardarPartida(event, partidaId) {
    event.preventDefault();

    let form = document.getElementById('partida-form-' + partidaId);
    let formData = new FormData(form);

    // Get all payment inputs
    let paymentInputs = form.querySelectorAll('input[name^="pago_"]');
    paymentInputs.forEach(input => {
        formData.append(input.name, input.value);
    });

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        let tableBody = document.querySelector('#partida-' + partidaId + ' tbody');
        tableBody.innerHTML = '';

       data.forEach(detalle => {
        let newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="clave[]" value="${detalle.clave}"></td>
            <td><input type="text" name="concepto[]" value="${detalle.concepto}"></td>
            <td><input type="text" name="unidad[]" value="${detalle.unidad}"></td>
            <td><input type="number" class="cantidad" name="cantidad[]" value="${detalle.cantidad}" oninput="calcularSubtotal(this)"></td>
            <td><input type="number" class="precioUnitario" name="precio_unitario[]" value="${detalle.precio_unitario}" oninput="calcularSubtotal(this)"></td>
            <td><span class="subtotal">${detalle.subtotal}</span><input type="hidden" class="subtotal-hidden" name="subtotal[]" value="${detalle.subtotal}"></td>
            <td><span class="restante">0.00</span></td>
        `;

        // Add payment inputs
        @php
            $pagoNumbers = [];
            foreach ($partidas as $partida) {
                if ($partida->detalles) {
                    foreach ($partida->detalles as $detalle) {
                        if (is_array($detalle->pagos)) {
                            $pagoNumbers = array_merge($pagoNumbers, array_keys($detalle->pagos));
                        }
                    }
                }
            }
            $pagoNumbers = array_unique($pagoNumbers);
            sort($pagoNumbers);
        @endphp
        @foreach ($pagoNumbers as $pagoNumber)
            let pagoValue = detalle.pagos && detalle.pagos['{{ $pagoNumber }}'] ? detalle.pagos['{{ $pagoNumber }}'].monto : '';
            let pagoFecha = detalle.pagos && detalle.pagos['{{ $pagoNumber }}'] ? detalle.pagos['{{ $pagoNumber }}'].fecha : '';
            newRow.innerHTML += `
                <td>
                    <input type="number" class="pago" name="pago_{{ $pagoNumber }}[]" value="${pagoValue}">
                    <br>
                    <input type="date" name="pago_fecha_{{ $pagoNumber }}[]" value="${pagoFecha}">
                </td>
            `;
        @endforeach

        tableBody.appendChild(newRow);
    });
})
.catch(error => {
    console.error('Error:', error);
});
}

        function toggleTable(tableId) {
            let table = document.getElementById(tableId);
            let addButton = table.parentNode.parentNode.querySelector('.add-row-button');
            table.classList.toggle('hidden');
            addButton.classList.toggle('hidden');
        }

        function addRow(tableId) {
            let table = document.getElementById(tableId);
            let tbody = table.querySelector('tbody');
            let header = table.querySelector('thead tr');
            let numCols = header.cells.length;

            let newRow = document.createElement('tr');
            for (let i = 0; i < numCols; i++) {
                let newCell = document.createElement('td');

                if (header.cells[i].innerHTML.includes('Pagos') || header.cells[i].innerHTML.includes('Pago')) {
                    newCell.innerHTML = '<input type="number" class="pago" value="0" oninput="actualizarTotales()"><br><input type="date">';
                } else if (header.cells[i].innerHTML.includes('Subtotal')) {
                    newCell.innerHTML = '<span class="subtotal">0.00</span><input type="hidden" class="subtotal-hidden" name="subtotal[]" value="0">';
                } else if (header.cells[i].innerHTML.includes('Restante')) {
                    newCell.innerHTML = '<span class="restante">0.00</span>';
                } else if (header.cells[i].innerHTML.includes('Cantidad')) {
                    newCell.innerHTML = '<input type="number" class="cantidad" name="cantidad[]" value="1" oninput="calcularSubtotal(this)">';
                } else if (header.cells[i].innerHTML.includes('Precio Unitario')) {
                    newCell.innerHTML = '<input type="number" class="precioUnitario" name="precio_unitario[]" value="0" oninput="calcularSubtotal(this)">';
                } else {
                    let name = '';
                    if (header.cells[i].innerHTML.includes('Clave')) {
                        name = 'clave[]';
                    } else if (header.cells[i].innerHTML.includes('Concepto')) {
                        name = 'concepto[]';
                    } else if (header.cells[i].innerHTML.includes('Unidad')) {
                        name = 'unidad[]';
                    }
                    newCell.innerHTML = '<input type="text" name="' + name + '">';
                }

                newRow.appendChild(newCell);
            }

            tbody.appendChild(newRow);
        }

        function addColumn(tableId) {
            let table = document.getElementById(tableId);
            let header = table.querySelector('thead tr');

            // Contar cuántas columnas de pago existen
            let existingPayments = header.querySelectorAll('th').length - 7; 
            let newPaymentIndex = existingPayments + 1;

            // Crear nueva columna en el encabezado
            let newHeader = document.createElement('th');
            newHeader.innerHTML = 'Pago ' + newPaymentIndex;
            header.appendChild(newHeader);

            // Agregar nuevas celdas a cada fila
            let tbody = table.querySelector('tbody');
            tbody.querySelectorAll('tr').forEach(row => {
                let newCell = document.createElement('td');
               newCell.innerHTML = `
                    <input type="number" class="pago" name="pago_${newPaymentIndex}[]" value="0">
                    <br>
                    <input type="date" name="pago_fecha_${newPaymentIndex}[]">
                    `;
                row.appendChild(newCell);
            });
        }
        function calcularSubtotal(input) {
            let row = input.closest('tr');
            let cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
            let precioUnitario = parseFloat(row.querySelector('.precioUnitario').value) || 0;
            let subtotal = cantidad * precioUnitario;

            row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
            row.querySelector('.subtotal-hidden').value = subtotal.toFixed(2);
            actualizarTotales();
        }

        function actualizarTotales() {
            let totalMontoAprobado = 0;
            let totalPagosGlobal = 0;

            document.querySelectorAll('.partida-container').forEach(partida => {
                let totalSubtotales = 0;
                let totalPagos = 0;

                partida.querySelectorAll('.subtotal').forEach(sub => {
                    totalSubtotales += parseFloat(sub.textContent) || 0;
                });

                partida.querySelectorAll('.pago').forEach(pago => {
                    totalPagos += parseFloat(pago.value) || 0;
                });

                partida.querySelector('.total-partida').textContent = totalSubtotales.toFixed(2);
                partida.querySelector('.total-pagos-partida').textContent = totalPagos.toFixed(2);

                totalMontoAprobado += totalSubtotales;
                totalPagosGlobal += totalPagos;

                partida.querySelectorAll('.restante').forEach(restanteCell => {
                    let subtotal = parseFloat(restanteCell.closest('tr').querySelector('.subtotal').textContent) || 0;
                    let pagosFila = 0;
                    restanteCell.closest('tr').querySelectorAll('.pago').forEach(pago => {
                        pagosFila += parseFloat(pago.value) || 0;
                    });
                    restanteCell.textContent = (subtotal - pagosFila).toFixed(2);
                });
            });

            document.getElementById('montoTotalAprobado').textContent = totalMontoAprobado.toFixed(2);
            document.getElementById('pagosTotales').textContent = totalPagosGlobal.toFixed(2);
        }
    </script>
