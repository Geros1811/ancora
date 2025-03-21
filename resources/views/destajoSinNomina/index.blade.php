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
            <span class="toggle-button" onclick="toggleTable('partida-{{ $partida->id }}')">+</span>
            <h3>{{ $partida->title }}</h3>

            <div class="table-wrapper">
                <table id="partida-{{ $partida->id }}" class="partida-table hidden">
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
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <h4>Total de la Partida: $<span class="total-partida">0.00</span></h4>
            <h4>Pagos de la Partida: $<span class="total-pagos-partida">0.00</span></h4>
            <button class="hidden add-row-button" onclick="addRow('partida-{{ $partida->id }}')">Añadir Fila</button>
        </div>
    @endforeach

    <script>
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
            newCell.innerHTML = '<span class="subtotal">0.00</span>';
        } else if (header.cells[i].innerHTML.includes('Restante')) {
            newCell.innerHTML = '<span class="restante">0.00</span>';
        } else if (header.cells[i].innerHTML.includes('Cantidad')) {
            newCell.innerHTML = '<input type="number" class="cantidad" value="1" oninput="calcularSubtotal(this)">';
        } else if (header.cells[i].innerHTML.includes('Precio Unitario')) {
            newCell.innerHTML = '<input type="number" class="precioUnitario" value="0" oninput="calcularSubtotal(this)">';
        } else {
            newCell.innerHTML = '<input type="text">';
        }

        newRow.appendChild(newCell);
    }

    tbody.appendChild(newRow);
}

        function addColumn(tableId) {
            let table = document.getElementById(tableId);
            let header = table.querySelector('thead tr');
            let paymentCounter = header.cells.length - 7;
            let newHeader = document.createElement('th');
            newHeader.innerHTML = 'Pago ' + (paymentCounter + 1);
            header.appendChild(newHeader);

            let tbody = table.querySelector('tbody');
            tbody.querySelectorAll('tr').forEach(row => {
                let newCell = document.createElement('td');
                newCell.innerHTML = '<input type="number" class="pago" value="0" oninput="actualizarTotales()"><br><input type="date">';
                row.appendChild(newCell);
            });
        }

        function calcularSubtotal(input) {
            let row = input.closest('tr');
            let cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
            let precioUnitario = parseFloat(row.querySelector('.precioUnitario').value) || 0;
            let subtotal = cantidad * precioUnitario;

            row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
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
@endsection
