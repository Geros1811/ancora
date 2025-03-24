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
                method="POST" enctype="multipart/form-data">
                @csrf
                <h3>{{ $partida->title }} <span class="toggle-button" onclick="toggleTable('partida-{{ $partida->id }}')">+</span></h3>

                <div class="table-container hidden" style="margin-top: 20px;">
                    <div class="table-wrapper">
                        <table class="obra-table">
                            <thead>
                                <tr>
                                    <th>Clave</th>
                                    <th>Concepto</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th id="pago-header-1">
                                        Pago 1
                                        <button type="button" class="btn btn-success btn-sm" onclick="agregarColumnaPago(this)">+</button>
                                    </th>
                                    @php
                                        $maxPagos = 0;
                                        foreach ($partida->detalles as $detalle) {
                                            if (is_array($detalle->pagos)) {
                                                $maxPagos = max($maxPagos, count($detalle->pagos));
                                            }
                                        }
                                    @endphp
                                    @for ($i = 2; $i <= $maxPagos; $i++)
                                        <th id="pago-header-{{ $i }}">Pago {{ $i }}</th>
                                    @endfor
                                    <th>Pendiente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($partida->detalles)
                                    @foreach ($partida->detalles as $index => $detalle)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="detalle_id[]" value="{{ $detalle->id }}">
                                                <input type="text" name="clave[]" class="form-control"
                                                    value="{{ $detalle->clave }}">
                                            </td>
                                            <td>
                                                <input type="text" name="concepto[]" class="form-control"
                                                    value="{{ $detalle->concepto }}">
                                            </td>
                                            <td>
                                                <input type="text" name="unidad[]" class="form-control"
                                                    value="{{ $detalle->unidad }}">
                                            </td>
                                            <td>
                                                <input type="number" name="cantidad[]" class="form-control"
                                                    value="{{ $detalle->cantidad }}" oninput="calcularSubtotal(this.closest('tr')); calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()">
                                            </td>
                                            <td>
                                                <input type="number" name="precio_unitario[]" class="form-control"
                                                    value="{{ $detalle->precio_unitario }}" oninput="calcularSubtotal(this.closest('tr')); calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()">
                                            </td>
                                            <td>
                                                <input type="number" name="subtotal[]" class="form-control"
                                                    value="{{ $detalle->subtotal }}" placeholder="$" readonly>
                                            </td>
                                            <td>
                                                Fecha: <input type="date" name="pago_fecha_1[]" class="form-control" value="{{ $detalle->pagos[1]['fecha'] ?? '' }}"
                                                    onchange="calcularPendiente(this.closest('tr'))"><br>
                                                Pago: <input type="number" name="pago_numero_1[]" class="form-control pago_numero"
                                                    placeholder="$" value="{{ $detalle->pagos[1]['monto'] ?? '' }}" oninput="calcularPendiente(this.closest('tr'))">
                                            </td>
                                            @for ($i = 2; $i <= $maxPagos; $i++)
                                                <td>
                                                    Fecha: <input type="date" name="pago_fecha_{{ $i }}[]" class="form-control" value="{{ $detalle->pagos[$i]['fecha'] ?? '' }}"
                                                        onchange="calcularPendiente(this.closest('tr'))"><br>
                                                    Pago: <input type="number" name="pago_numero_{{ $i }}[]" class="form-control pago_numero"
                                                        placeholder="$" value="{{ $detalle->pagos[$i]['monto'] ?? '' }}" oninput="calcularPendiente(this.closest('tr'))">
                                                </td>
                                            @endfor
                                            <td>
                                                <input type="number" name="pendiente[]" class="form-control"
                                                    value="{{ $detalle->pendiente ?? 0 }}" placeholder="$" readonly>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                        <div style="margin-top: 10px; text-align: right;">
                            <strong>Monto Total Autorizado:</strong> $<span class="monto_aprobado_total">{{ number_format($partida->monto_total_aprobado, 2) }}</span>
                        </div>
                        <div style="margin-top: 10px; text-align: right;">
                            <strong>Cantidad Total Pagada:</strong> $<span class="cantidad_total_pagada">{{ number_format($partida->cantidad_total_pagada, 2) }}</span>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="agregarFila(this)">Agregar Fila</button>
                    </div>

                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar
                        Detalles</button>
                </div>
            </form>
        </div>
    @endforeach

    <style>
        .btn {
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 14px;
        }
    </style>

    <script>
        function calcularTotalMontoAprobado() {
            let totalMontoAprobado = 0;
            let totalCantidadPagada = 0;
            document.querySelectorAll('input[name="subtotal[]"]').forEach(function(input) {
                totalMontoAprobado += Number(input.value) || 0;
            });

            document.querySelectorAll('tbody tr').forEach(function(row) {
                let totalPagos = 0;
                let pagoInputs = row.querySelectorAll('input[name^="pago_numero"]');

                for (let i = 0; i < pagoInputs.length; i++) {
                    let pago = Number(pagoInputs[i].value) || 0;
                    totalPagos += pago;
                }
                totalCantidadPagada += totalPagos;
            });

            document.getElementById('monto_aprobado_total').innerText = totalMontoAprobado.toFixed(2);
            document.getElementById('cantidad_total_pagada').innerText = totalCantidadPagada.toFixed(2);
        }

        function calcularSubtotal(row) {
            let cantidadInput = row.querySelector('input[name="cantidad[]"]');
            let precioUnitarioInput = row.querySelector('input[name="precio_unitario[]"]');
            let subtotalInput = row.querySelector('input[name="subtotal[]"]');

            if (!cantidadInput || !precioUnitarioInput || !subtotalInput) return;

            let cantidad = Number(cantidadInput.value) || 0;
            let precioUnitario = Number(precioUnitarioInput.value) || 0;
            let subtotal = cantidad * precioUnitario;

            subtotalInput.value = subtotal.toFixed(2);
        }

        function calcularPendiente(row) {
            let subtotalInput = row.querySelector('input[name="subtotal[]"]');
            let montoAprobado = Number(subtotalInput.value) || 0;
            let totalPagos = 0;
            let pagoInputs = row.querySelectorAll('input[name^="pago_numero"]');

            for (let i = 0; i < pagoInputs.length; i++) {
                let pago = Number(pagoInputs[i].value) || 0;
                totalPagos += pago;
            }

            let pendiente = montoAprobado - totalPagos;
            pendiente = pendiente < 0 ? 0 : pendiente;
            let pendienteInput = row.querySelector('input[name="pendiente[]"]');
            if (pendienteInput) {
                pendienteInput.value = pendiente.toFixed(2);
            }
        }

        function agregarColumnaPago(button) {
            const table = button.closest('table');
            const headerRow = table.querySelector('thead tr');
            let pagoCount = headerRow.querySelectorAll('th').length - 7;
            const newHeader = document.createElement('th');
            newHeader.innerHTML = `Pago ${pagoCount + 1} <button type="button" class="btn btn-success btn-sm" onclick="agregarColumnaPago(this)">+</button>`;
            headerRow.insertBefore(newHeader, headerRow.lastElementChild);

            table.querySelectorAll('tbody tr').forEach(row => {
                const newColumn = document.createElement('td');
                newColumn.innerHTML = `
                Fecha: <input type="date" name="pago_fecha_${pagoCount + 1}[]" class="form-control" onchange="calcularPendiente(this.closest('tr'))">
                Pago: <input type="number" name="pago_numero_${pagoCount + 1}[]" class="form-control pago_numero" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
            `;
                row.insertBefore(newColumn, row.lastElementChild);
            });
        }

        function agregarFila(button) {
            const tableBody = button.closest('.table-container').querySelector('.obra-table tbody');
            const newRow = document.createElement('tr');
            let numPagoColumns = button.closest('.table-container').querySelector('.obra-table thead tr').querySelectorAll('th').length - 7;
            let newRowHTML = `
            <td><input type="text" name="clave[]" class="form-control" value=""></td>
            <td><input type="text" name="concepto[]" class="form-control" value=""></td>
            <td><input type="text" name="unidad[]" class="form-control" value=""></td>
            <td><input type="number" name="cantidad[]" class="form-control" value="" oninput="calcularSubtotal(this.closest('tr')); calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()"></td>
            <td><input type="number" name="precio_unitario[]" class="form-control" value="" oninput="calcularSubtotal(this.closest('tr')); calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()"></td>
            <td><input type="number" name="subtotal[]" class="form-control subtotal" value="0" placeholder="$" readonly></td>
        `;

            for (let i = 1; i <= numPagoColumns; i++) {
                newRowHTML += `
                <td>
                    Fecha: <input type="date" name="pago_fecha_${i}[]" class="form-control" onchange="calcularPendiente(this.closest('tr'))">
                    Pago: <input type="number" name="pago_numero_${i}[]" class="form-control pago_numero" value="0" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
                </td>
            `;
            }

            newRowHTML += `
            <td><input type="number" name="pendiente[]" class="form-control" value="0" placeholder="$" readonly></td>
        `;
            newRow.innerHTML = newRowHTML;
            tableBody.appendChild(newRow);
            calcularPendiente(newRow);
            calcularTotalMontoAprobado();
        }

        // Inicializar totales al cargar la página
        calcularTotalMontoAprobado();

        function toggleTable(tableId) {
            let toggleButton = document.querySelector(`.toggle-button[onclick="toggleTable('${tableId}')"]`);
            let tableContainer = toggleButton.closest('.partida-container').querySelector('.table-container');
            tableContainer.classList.toggle('hidden');
        }

        function calcularTotalesPorTabla(tableContainer) {
            let totalMontoAprobado = 0;
            let totalCantidadPagada = 0;

            // Suma los subtotales de la tabla
            tableContainer.querySelectorAll('input[name="subtotal[]"]').forEach(function(input) {
                totalMontoAprobado += Number(input.value) || 0;
            });

            // Suma los pagos realizados en la tabla
            tableContainer.querySelectorAll('tbody tr').forEach(function(row) {
                let totalPagos = 0;
                let pagoInputs = row.querySelectorAll('input[name^="pago_numero"]');

                for (let i = 0; i < pagoInputs.length; i++) {
                    let pago = Number(pagoInputs[i].value) || 0;
                    totalPagos += pago;
                }
                totalCantidadPagada += totalPagos;
            });

            // Actualiza los totales en la tabla correspondiente
            tableContainer.querySelector('.monto_aprobado_total').innerText = totalMontoAprobado.toFixed(2);
            tableContainer.querySelector('.cantidad_total_pagada').innerText = totalCantidadPagada.toFixed(2);
        }

        function calcularTotalesGlobales() {
            document.querySelectorAll('.table-container').forEach(function(tableContainer) {
                calcularTotalesPorTabla(tableContainer);
            });
        }

        // Inicializar totales al cargar la página
        calcularTotalesGlobales();

        // Llama a calcularTotalesGlobales después de cualquier cambio
        document.querySelectorAll('input').forEach(function(input) {
            input.addEventListener('input', function() {
                calcularTotalesGlobales();
            });
        });

        // Vincula eventos de entrada para actualizar los totales en tiempo real
        document.querySelectorAll('input[name="cantidad[]"], input[name="precio_unitario[]"], input[name^="pago_numero"]').forEach(function(input) {
            input.addEventListener('input', function() {
                const tableContainer = input.closest('.table-container');
                calcularTotalesPorTabla(tableContainer);
            });
        });

        document.querySelectorAll('form[id^="partida-form-"]').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Evita el envío tradicional del formulario

                const formData = new FormData(form);
                const url = form.action;

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Actualiza la tabla con los datos devueltos
                    actualizarTabla(form.closest('.partida-container'), data);
                    alert('Datos guardados correctamente.');
                })
                .catch(error => {
                    console.error('Error al guardar los datos:', error);
                    alert('Ocurrió un error al guardar los datos.');
                });
            });
        });

        function actualizarTabla(partidaContainer, detalles) {
            const tbody = partidaContainer.querySelector('tbody');
            tbody.innerHTML = ''; // Limpia las filas existentes

            detalles.forEach(detalle => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <input type="hidden" name="detalle_id[]" value="${detalle.id}">
                        <input type="text" name="clave[]" class="form-control" value="${detalle.clave}">
                    </td>
                    <td>
                        <input type="text" name="concepto[]" class="form-control" value="${detalle.concepto}">
                    </td>
                    <td>
                        <input type="text" name="unidad[]" class="form-control" value="${detalle.unidad}">
                    </td>
                    <td>
                        <input type="number" name="cantidad[]" class="form-control" value="${detalle.cantidad}" oninput="calcularSubtotal(this.closest('tr')); calcularPendiente(this.closest('tr')); calcularTotalesPorTabla(partidaContainer)">
                    </td>
                    <td>
                        <input type="number" name="precio_unitario[]" class="form-control" value="${detalle.precio_unitario}" oninput="calcularSubtotal(this.closest('tr')); calcularPendiente(this.closest('tr')); calcularTotalesPorTabla(partidaContainer)">
                    </td>
                    <td>
                        <input type="number" name="subtotal[]" class="form-control" value="${detalle.subtotal}" placeholder="$" readonly>
                    </td>
                    ${generarColumnasPagos(detalle.pagos)}
                    <td>
                        <input type="number" name="pendiente[]" class="form-control" value="${detalle.pendiente}" placeholder="$" readonly>
                    </td>
                `;
                tbody.appendChild(row);
            });

            calcularTotalesPorTabla(partidaContainer);
        }

        function generarColumnasPagos(pagos) {
            let columnas = '';
            const keys = Object.keys(pagos || {});
            keys.forEach(key => {
                columnas += `
                    <td>
                        Fecha: <input type="date" name="pago_fecha_${key}[]" class="form-control" value="${pagos[key]?.fecha || ''}" onchange="calcularPendiente(this.closest('tr'))"><br>
                        Pago: <input type="number" name="pago_numero_${key}[]" class="form-control pago_numero" value="${pagos[key]?.monto || ''}" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
                    </td>
                `;
            });
            return columnas;
        }
    </script>
@endsection
