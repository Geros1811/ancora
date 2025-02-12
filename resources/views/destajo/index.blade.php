@extends('layouts.app')

@section('content')
<div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
    <form id="form-destajos" action="{{ route('destajos.store', ['obraId' => $obraId]) }}" method="POST">
        @csrf
        <div class="info-item" style="display: flex; flex-wrap: wrap; justify-content: space-between; margin-bottom: 10px;">
            <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                <span class="info-label" style="font-weight: bold; color: #34495e;">Seleccione Nómina:</span>
                <select name="nomina_id" id="nomina_id" class="form-control" onchange="actualizarFechas()">
                    <option value="" disabled selected>Seleccione una nómina</option>
                    @foreach ($nominas as $nomina)
                        <option value="{{ $nomina->id }}" data-fecha-inicio="{{ $nomina->fecha_inicio }}" data-fecha-fin="{{ $nomina->fecha_fin }}">
                            {{ $nomina->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                <span class="info-label" style="font-weight: bold; color: #34495e;">Semana del:</span>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" readonly>
            </div>
            <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                <span class="info-label" style="font-weight: bold; color: #34495e;">al:</span>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" readonly>
            </div>
        </div>
        <button type="button" class="btn btn-primary" onclick="crearTabla()">Crear Tabla de Destajos</button>
    </form>
</div>

{{-- Se agrupan los destajos por nómina para mostrarlos en una sola tabla por cada nómina --}}
@php
    $destajosAgrupados = $detalles->groupBy('nomina_id');
@endphp

<div id="contenedor-tablas">
    @foreach($destajosAgrupados as $nominaId => $destajos)
        <div class="table-container" style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3>
                    {{ $destajos->first()->nomina->nombre }} - 
                    {{ $destajos->first()->nomina->fecha_inicio }} al {{ $destajos->first()->nomina->fecha_fin }}
                </h3>
                @php
                    $totalCantidad = $destajos->sum('cantidad');
                @endphp
                <div class="total-cantidad" style="font-size: 16px; font-weight: bold;">
                    Total Cantidad: ${{ number_format($totalCantidad, 2) }}
                </div>
            </div>
            <form class="destajo-form" action="{{ route('destajos.store', ['obraId' => $obraId]) }}" method="POST">
                @csrf
                <input type="hidden" name="nomina_id" value="{{ $nominaId }}">
                <table class="obra-table">
                    <thead>
                        <tr>
                            <th>Frente</th>
                            <th>Monto Aprobado</th>
                            <th>Cantidad</th>
                            <th style="width: 170px; text-align: center">Acciones</th>
                            <th>Bloquear</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-{{ $nominaId }}">
                        @foreach($destajos as $detalle)
                        <tr class="fila-destajo {{ $detalle->locked ? 'locked-row' : '' }}" data-id="{{ $detalle->id }}">
                            <td>
                                <select name="frente[]" class="form-control frente" disabled>
                                    <option value="{{ $detalle->frente }}" selected>{{ $detalle->frente }}</option>
                                </select>
                            </td>
                            <td><input type="number" name="monto_aprobado[]" class="form-control monto_aprobado" value="{{ $detalle->monto_aprobado }}" readonly></td>
                            <td><input type="number" name="cantidad[]" class="form-control cantidad" value="{{ $detalle->cantidad }}" readonly></td>
                            <td style="text-align: center">
                                 <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                                    <a href="{{ route('detalles.destajos', ['id' => $detalle->id]) }}" class="btn btn-sm btn-info">
                                        Ir a detalles
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Eliminar</button>
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('destajos.toggleLock', $detalle->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        {{ $detalle->locked ? 'Desbloquear' : 'Bloquear' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" onclick="agregarFila({{ $nominaId }})">Agregar Fila</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    @endforeach
</div>
@endsection

{{-- Tus estilos y funciones JavaScript se mantienen igual --}}
<style>
    .obra-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        color: black;
        border-radius: 8px;
    }

    .obra-table th, .obra-table td {
        padding: 5px;
        text-align: left;
        border: 1px solid #ddd;
        font-size: 14px;
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

    .obra-table td input {
        width: 100%;
        padding: 3px;
        border-radius: 4px;
        border: 1px solid #ddd;
        box-sizing: border-box;
        background-color: white;
        color: black;
        font-size: 14px;
    }

    .table-container button {
        margin-top: 15px;
    }

    .custom-input {
        display: none;
    }
    .total-cantidad {
        font-size: 16px;
        font-weight: bold;
        display: inline;
        margin-left: 10px; /* Adjust as needed */
    }

    .locked-row {
        background-color: #ADD8E6; /* LightBlue color */
    }
</style>

<script>
    function actualizarFechas() {
        var select = document.getElementById("nomina_id");
        var fechaInicio = select.options[select.selectedIndex].getAttribute("data-fecha-inicio");
        var fechaFin = select.options[select.selectedIndex].getAttribute("data-fecha-fin");
        document.getElementById("fecha_inicio").value = fechaInicio;
        document.getElementById("fecha_fin").value = fechaFin;
    }
    
    function crearTabla() {
        var nominaSelect = document.getElementById("nomina_id");
        var nominaId = nominaSelect.value;
        var nominaTexto = nominaSelect.options[nominaSelect.selectedIndex].text;
        var fechaInicio = document.getElementById("fecha_inicio").value;
        var fechaFin = document.getElementById("fecha_fin").value;
    
        if (!nominaId) {
            alert("Seleccione una nómina antes de crear la tabla.");
            return;
        }
        var tablaHtml = `
            <div class="table-container" style="margin-top: 20px;">
                <h3>${nominaTexto} - ${fechaInicio} al ${fechaFin}</h3>
                <form class="destajo-form" action="{{ route('destajos.store', ['obraId' => $obraId]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="nomina_id" value="${nominaId}">
                    <table class="obra-table">
                        <thead>
                            <tr>
                                <th>Frente</th>
                                <th>Monto Aprobado</th>
                                <th>Cantidad</th>
                                <th style="width: 170px; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-${nominaId}">
                            <tr>
                                <td>
                                    <select name="frente[]" class="form-control" onchange="toggleCustomInput(this)">
                                        <option value="Plomeria">Plomeria</option>
                                        <option value="Electricidad">Electricidad</option>
                                        <option value="Colocador de Pisos">Colocador de Pisos</option>
                                        <option value="Pintor">Pintor</option>
                                        <option value="Herreria">Herreria</option>
                                        <option value="Carpintero">Carpintero</option>
                                        <option value="Aluminio">Aluminio</option>
                                        <option value="Aire Acondicionado">Aire Acondicionado</option>
                                        <option value="Tabla Roca">Tabla Roca</option>
                                        <option value="Otros">Otros</option>
                                    </select>
                                    <input type="text" name="frente_custom[]" class="form-control custom-input" placeholder="Especifique" onblur="this.value = this.value.trim();">
                                </td>
                                <td><input type="number" name="monto_aprobado[]" class="form-control" value="0" readonly></td>
                                <td><input type="number" name="cantidad[]" class="form-control" value="0" readonly></td>
                                <td><button type="button" class="btn btn-danger" onclick="eliminarFila(this)">Eliminar</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success" onclick="agregarFila(${nominaId})">Agregar Fila</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        `;
        document.getElementById("contenedor-tablas").insertAdjacentHTML("beforeend", tablaHtml);
    }
    
    function toggleCustomInput(select) {
        var customInput = select.parentElement.querySelector('.custom-input');
        if (select.value === "Otros") {
            customInput.style.display = "block";
            customInput.value = "";
        } else {
            customInput.style.display = "none";
            customInput.value = "";
        }
    }
    
    function agregarFila(nominaId) {
        var filaHtml = `
            <tr>
                <td>
                    <select name="frente[]" class="form-control" onchange="toggleCustomInput(this)">
                        <option value="Plomeria">Plomeria</option>
                        <option value="Electricidad">Electricidad</option>
                        <option value="Colocador de Pisos">Colocador de Pisos</option>
                        <option value="Pintor">Pintor</option>
                        <option value="Herreria">Herreria</option>
                        <option value="Carpintero">Carpintero</option>
                        <option value="Aluminio">Aluminio</option>
                        <option value="Aire Acondicionado">Aire Acondicionado</option>
                        <option value="Tabla Roca">Tabla Roca</option>
                        <option value="Otros">Otros</option>
                    </select>
                    <input type="text" name="frente_custom[]" class="form-control custom-input" placeholder="Especifique" onblur="this.value = this.value.trim();">
                </td>
                <td><input type="number" name="monto_aprobado[]" class="form-control" value="0" readonly></td>
                <td><input type="number" name="cantidad[]" class="form-control" value="0" readonly></td>
                <td><button type="button" class="btn btn-danger" onclick="eliminarFila(this)">Eliminar</button></td>
            </tr>
        `;
        document.getElementById("tabla-" + nominaId).insertAdjacentHTML("beforeend", filaHtml);
    }
    
    function eliminarFila(button) {
        var row = button.closest("tr");
        var destajoId = row.dataset.id;

        if (confirm('¿Estás seguro de que quieres eliminar este destajo?')) {
            fetch('/destajos/' + destajoId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => {
                if (response.ok) {
                    row.remove();
                } else {
                    alert('Error al eliminar el destajo.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el destajo.');
            });
        }
    }

    
 </script>
