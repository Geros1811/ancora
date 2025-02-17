@extends('layouts.app')

@section('content')
@php
    // Si no existe $nombre_nomina, asumimos que estamos en modo pendiente,
    // por lo que forzamos que los campos sean editables.
    $isPendiente = !isset($nombre_nomina);
    if ($isPendiente) {
        $editable = true;
    }
@endphp

<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">
            Detalles de Destajo: {{ $detalle->frente }}
        </h1>
        @if(isset($nombre_nomina))
            <h2 style="font-size: 20px; color: #34495e; margin-bottom: 10px;">
                {{ $nombre_nomina }} ({{ $dia_inicio }} - {{ $fecha_inicio }} al {{ $dia_fin }} - {{ $fecha_fin }})
            </h2>
        @else
            <h2 style="font-size: 20px; color: #34495e; margin-bottom: 10px;">
                Modo Pendiente (En Curso)
            </h2>
        @endif
    </div>

    {{-- Seleccionar la acción del formulario según el modo --}}
    @if(isset($nombre_nomina))
        <form action="{{ route('detalles.destajos.store', ['obraId' => $obraId, 'destajoId' => $detalle->id]) }}" method="POST" enctype="multipart/form-data">
    @else
        <form action="{{ route('detalles.destajos.storePendiente', ['obraId' => $obraId, 'destajoId' => $detalle->id]) }}" method="POST" enctype="multipart/form-data">
    @endif
        @csrf
        <input type="hidden" name="fecha_inicio" value="{{ $fecha_inicio }}">
        <input type="hidden" name="fecha_fin" value="{{ $fecha_fin }}">

        <div class="table-container" style="margin-top: 20px;">
            <table class="obra-table">
                <thead>
                    <tr>
                        <th>Cotización</th>
                        <th>Monto Aprobado</th>
                        <th id="pago-header">
                            Pago 1 
                            <button type="button" class="btn btn-success btn-sm" onclick="agregarColumnaPago(this, '{{ $fecha_inicio }}', '{{ $fecha_fin }}')" {{ $editable ? '' : 'disabled' }}>+</button>
                        </th>
                        @php
                            $maxPagos = 1;
                        @endphp
                        @foreach($destajoDetalles as $destajoDetalle)
                            @if($destajoDetalle->pagos)
                                @php
                                    $pagosTemp = json_decode($destajoDetalle->pagos, true);
                                    $maxPagos = max($maxPagos, count($pagosTemp));
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
                    {{-- Filas principales --}}
                    @foreach($destajoDetalles as $index => $destajoDetalle)
                        @php
                            $pagos = $destajoDetalle->pagos ? json_decode($destajoDetalle->pagos, true) : [];
                            $estado = $destajoDetalle->estado;
                        @endphp
                        <tr class="{{ $detalle->locked ? 'locked-row' : '' }} {{ $destajoDetalle->estado == 'En Curso' ? 'en-curso-row' : '' }}">
                            <td>
                                <input type="text" name="cotizacion[]" class="form-control" value="{{ $destajoDetalle->cotizacion }}" {{ (!$detalle->locked) ? '' : 'readonly' }}>
                            </td>
                            <td>
                                <input type="number" name="monto_aprobado[]" class="form-control monto_aprobado" value="{{ $destajoDetalle->monto_aprobado }}" placeholder="$" 
                                    oninput="calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()" {{ (!$detalle->locked) ? '' : 'readonly' }}>
                            </td>
                            
                            @for($i = 1; $i <= $maxPagos; $i++)
                                <td>
                                    Fecha: <input type="date" name="pago_fecha_{{ $i }}[]" class="form-control" value="{{ $pagos[$i]['fecha'] ?? '' }}" onchange="calcularPendiente(this.closest('tr'))" {{ (!$detalle->locked) ? '' : 'readonly' }}>
                                    Pago: <input type="number" name="pago_numero_{{ $i }}[]" class="form-control pago_numero" value="{{ $pagos[$i]['numero'] ?? '' }}" placeholder="$" oninput="calcularPendiente(this.closest('tr'))" {{ (!$detalle->locked) ? '' : 'readonly' }}>
                                </td>
                            @endfor

                            <td>
                                <input type="number" name="pendiente[]" class="form-control" value="{{ $destajoDetalle->pendiente }}" placeholder="$" readonly>
                            </td>
                            <td>
                                <select name="estado[]" class="form-control" {{ (!$detalle->locked) ? '' : 'disabled' }}>
                                    <option value="En Curso" {{ $estado == 'En Curso' ? 'selected' : '' }}>En Curso</option>
                                    <option value="Finalizado" {{ $estado == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach

                    {{-- Filas anteriores: se muestran ahora como editables --}}
                    @if(isset($previousDestajoDetalles) && count($previousDestajoDetalles) > 0)
                        @foreach($previousDestajoDetalles as $destajoDetalle)
                            @php
                                $pagos = $destajoDetalle->pagos ? json_decode($destajoDetalle->pagos, true) : [];
                                $estado = $destajoDetalle->estado;
                            @endphp
                            <tr class="previous-destajo-detail">
                                <td>
                                    <input type="text" name="cotizacion[]" class="form-control" value="{{ $destajoDetalle->cotizacion }}">
                                </td>
                                <td>
                                    <input type="number" name="monto_aprobado[]" class="form-control monto_aprobado" value="{{ $destajoDetalle->monto_aprobado }}" placeholder="$">
                                </td>
                                
                                @for($i = 1; $i <= $maxPagos; $i++)
                                    <td>
                                        Fecha: <input type="date" name="pago_fecha_{{ $i }}[]" class="form-control" value="{{ $pagos[$i]['fecha'] ?? '' }}" onchange="calcularPendiente(this.closest('tr'))">
                                        Pago: <input type="number" name="pago_numero_{{ $i }}[]" class="form-control pago_numero" value="{{ $pagos[$i]['numero'] ?? '' }}" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
                                    </td>
                                @endfor

                                <td>
                                    <input type="number" name="pendiente[]" class="form-control" value="{{ $destajoDetalle->pendiente }}" placeholder="$" readonly>
                                </td>
                                <td>
                                    <select name="estado[]" class="form-control">
                                        <option value="En Curso" {{ $estado == 'En Curso' ? 'selected' : '' }}>En Curso</option>
                                        <option value="Finalizado" {{ $estado == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            <div style="margin-top: 10px; text-align: right;">
                <strong>Monto Total Autorizado:</strong> $<span id="monto_aprobado_total">0.00</span>
            </div>
            <div style="margin-top: 10px; text-align: right;">
                <strong>Cantidad Total Pagada:</strong> $<span id="cantidad_total_pagada">0.00</span>
            </div>
            <button type="button" class="btn btn-primary" onclick="agregarFila('{{ $fecha_inicio }}', '{{ $fecha_fin }}')" {{ (!$detalle->locked) ? '' : 'disabled' }}>Agregar Fila</button>
        </div>

        <button type="submit" class="btn btn-success" {{ (!$detalle->locked) ? '' : 'disabled' }}>Guardar Detalles</button>
        
        @if(isset($nombre_nomina))
            <a href="{{ route('destajos.detalles.pdf', $detalle->id) }}" class="btn btn-primary" target="_blank">
                Generar PDF
            </a>
        @else
            <a href="{{ route('destajos.detalles.pdfPendiente', $detalle->id) }}" class="btn btn-primary" target="_blank">
                Generar PDF
            </a>
        @endif
        <button type="button" class="btn btn-secondary" onclick="exportarDetalles()" {{ (!$detalle->locked) ? '' : 'disabled' }}>Exportar</button>
    </form>

    {{-- Formulario para subir imágenes --}}
    <form action="{{ route('detalles.destajos.uploadImage', ['obraId' => $obraId, 'destajoId' => $detalle->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="image">Subir Imagen:</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir Imagen</button>
    </form>

    {{-- Mostrar imágenes subidas --}}
    <div class="image-gallery" style="margin-top: 20px;">
        <h3>Imágenes Subidas:</h3>
        <div class="row">
            @if(isset($imagenes) && count($imagenes) > 0)
                @foreach($imagenes as $imagen)
                    <div class="col-md-3">
                        <div class="thumbnail">
                            <img src="{{ asset('storage/' . $imagen->path) }}" alt="Imagen" style="width:100%">
                            <div class="caption">
                                <p>{{ $imagen->created_at }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
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

    .en-curso-row {
        background-color: #FFFFE0;
    }

    .image-gallery .thumbnail {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        margin-bottom: 20px;
    }

    .image-gallery .thumbnail img {
        border-radius: 4px;
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
            let fechaInputs = row.querySelectorAll('td > input[name^="pago_fecha"]');

            for (let i = 0; i < pagoInputs.length; i++) {
                let pago = Number(pagoInputs[i].value) || 0;
                let fecha = fechaInputs[i].value;

                if (fecha >= '{{ $fecha_inicio }}' && fecha <= '{{ $fecha_fin }}') {
                    totalPagos += pago;
                }
            }
            totalCantidadPagada += totalPagos;
        });

        document.getElementById('monto_aprobado_total').innerText = totalMontoAprobado.toFixed(2);
        document.getElementById('cantidad_total_pagada').innerText = totalCantidadPagada.toFixed(2);
    }

    function calcularPendiente(row) {
    let montoInput = row.querySelector('input[name="monto_aprobado[]"]');
    if (!montoInput) return;
    let montoAprobado = Number(montoInput.value) || 0;
    let totalPagos = 0;
    let pagoInputs = row.querySelectorAll('input[name^="pago_numero"]');

    for (let i = 0; i < pagoInputs.length; i++) {
        let pago = Number(pagoInputs[i].value) || 0;
        totalPagos += pago; // Se suman todos los pagos sin importar la fecha
    }

    let pendiente = montoAprobado - totalPagos;
    let pendienteInput = row.querySelector('input[name="pendiente[]"]');
    if (pendienteInput) {
        pendienteInput.value = pendiente.toFixed(2);
    }
    
    let estadoSelect = row.querySelector('select[name="estado[]"]');
    if (estadoSelect) {
        estadoSelect.value = pendiente <= 0 ? 'Finalizado' : 'En Curso';
    }
    }

    function agregarColumnaPago(button, fechaInicio, fechaFin) {
        button.disabled = true;
        const table = document.querySelector('.obra-table');
        const headerRow = table.querySelector('thead tr');
        let pagoCount = headerRow.querySelectorAll('th').length - 4 + 1;
        const newHeader = document.createElement('th');
        newHeader.innerHTML = `Pago ${pagoCount} <button type="button" class="btn btn-success btn-sm" onclick="agregarColumnaPago(this, '${fechaInicio}', '${fechaFin}')">+</button>`;
        const pendienteHeader = headerRow.querySelector('th:nth-last-child(2)');
        headerRow.insertBefore(newHeader, pendienteHeader);

        document.querySelectorAll('.obra-table tbody tr').forEach(row => {
            const newColumn = document.createElement('td');
            newColumn.innerHTML = `
                Fecha: <input type="date" name="pago_fecha_${pagoCount}[]" class="form-control" min="${fechaInicio}" max="${fechaFin}" onchange="calcularPendiente(this.closest('tr'))">
                Pago: <input type="number" name="pago_numero_${pagoCount}[]" class="form-control pago_numero" placeholder="$" oninput="calcularPendiente(this.closest('tr'))">
            `;
            const pendienteCell = row.querySelector('td:nth-last-child(2)');
            row.insertBefore(newColumn, pendienteCell);
        });
    }

    function agregarFila(fechaInicio, fechaFin) {
        const tableBody = document.querySelector('.obra-table tbody');
        const newRow = document.createElement('tr');
        let numPagoColumns = document.querySelector('.obra-table thead tr').querySelectorAll('th').length - 4;
        let newRowHTML = `
            <td><input type="text" name="cotizacion[]" class="form-control" value=""></td>
            <td><input type="number" name="monto_aprobado[]" class="form-control monto_aprobado" value="0" placeholder="$" oninput="calcularPendiente(this.closest('tr')); calcularTotalMontoAprobado()"></td>
        `;

        for (let i = 1; i <= numPagoColumns; i++) {
            newRowHTML += `
                <td>
                    Fecha: <input type="date" name="pago_fecha_${i}[]" class="form-control" min="${fechaInicio}" max="${fechaFin}" onchange="calcularPendiente(this.closest('tr'))">
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

    function exportarDetalles() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('detalles.destajos.exportar', ['obraId' => $obraId, 'destajoId' => $detalle->id]) }}";
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }

    // Inicializar totales al cargar la página
    calcularTotalMontoAprobado();
</script>
@endsection
