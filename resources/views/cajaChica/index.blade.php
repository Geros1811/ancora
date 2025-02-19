@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/cajaChica.css') }}">
@endsection

@section('content')
<div class="container caja-chica-container">
    <div class="section-header">
        <h1>Caja Chica</h1>
    </div>

    <form id="cajaChicaForm" action="{{ route('cajaChica.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="obra_id" value="{{ $obraId }}">
        
        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" class="form-control" id="fecha" name="fecha">
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad">
        </div>

        <div class="form-group">
            <label for="maestro_obra">Maestro de Obra:</label>
            <select class="form-control" id="maestro_obra" name="maestro_obra_id">
                <option value="">Seleccione un maestro de obra</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Crear</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(isset($cajaChica))
    <form id="detalleCajaChicaForm" action="{{ route('cajaChica.addDetail') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="caja_chica_id" value="{{ $cajaChica->id }}">
        <input type="hidden" name="obra_id" value="{{ $obraId }}">

        <table class="obra-table" id="detalleTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Descripción</th>
                    <th>Vista</th>
                    <th>Gasto</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cajaChica->detallesCajaChica as $index => $detalle)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><input type="text" class="form-control" name="detalles[{{ $index }}][descripcion]" value="{{ $detalle->descripcion }}" disabled></td>
                        <td>
                            <select class="form-control" name="detalles[{{ $index }}][vista]" disabled>
                                @foreach(['papeleria', 'gasolina', 'rentas', 'utilidades', 'acarreos', 'comidas', 'tramites', 'cimbras', 'maquinariaMayor', 'rentaMaquinaria', 'maquinariaMenor', 'limpieza', 'herramientaMenor', 'equipoSeguridad', 'materiales'] as $vista)
                                    <option value="{{ $vista }}" {{ $detalle->vista == $vista ? 'selected' : '' }}>{{ ucfirst($vista) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" class="form-control gasto-input" name="detalles[{{ $index }}][gasto]" value="{{ $detalle->gasto }}" disabled></td>
                        <td>
                            @if (isset($detalle->foto))
                                <a href="{{ asset('storage/' . $detalle->foto) }}" target="_blank">Ver Foto</a>
                            @endif
                        </td>
                    </tr>
                @endforeach

                <!-- Nueva fila para agregar detalles -->
                <tr id="newDetailRow">
                    <td>#</td>
                    <td><input type="text" class="form-control" name="descripcion"></td>
                    <td>
                        <select class="form-control" name="vista">
                            <option value="">Seleccione una vista</option>
                            @foreach(['papeleria', 'gasolina', 'rentas', 'utilidades', 'acarreos', 'comidas', 'tramites', 'cimbras', 'maquinariaMayor', 'rentaMaquinaria', 'maquinariaMenor', 'limpieza', 'herramientaMenor', 'equipoSeguridad', 'materiales'] as $vista)
                                <option value="{{ $vista }}">{{ ucfirst($vista) }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" class="form-control gasto-input" name="gasto" onchange="updateSubtotal()"></td>
                    <td><input type="file" class="form-control" name="foto"></td>
                </tr>
            </tbody>
        </table>

        <div class="button-and-totals">
            <button type="button" class="btn btn-success" onclick="addDetailFields()">Añadir Fila</button>
            <button type="submit" class="btn btn-primary">Guardar Detalles</button>
            <div class="totals-group">
                <div class="subtotal-section">Subtotal: <span id="subtotal">0.00</span></div>
                <div class="cambio-section">Cambio: <span id="cambio">0.00</span></div>
            </div>
        </div>
    </form>
    @endif
</div>

<script>
let detailCount = document.querySelectorAll("#detalleTable tbody tr").length - 1;

function addDetailFields() {
    const tableBody = document.querySelector("#detalleTable tbody");
    const newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td>${detailCount}</td>
        <td><input type="text" class="form-control" name="detalles[${detailCount}][descripcion]"></td>
        <td>
            <select class="form-control" name="detalles[${detailCount}][vista]">
                <option value="">Seleccione una vista</option>
                <option value="papeleria">Papelería</option>
                <option value="gasolina">Gasolina</option>
                <option value="rentas">Rentas</option>
                <option value="utilidades">Utilidades</option>
                <option value="acarreos">Acarreos</option>
                <option value="comidas">Comidas</option>
                <option value="tramites">Trámites</option>
                <option value="cimbras">Cimbras</option>
                <option value="maquinariaMayor">Maquinaria Mayor</option>
                <option value="rentaMaquinaria">Renta Maquinaria</option>
                <option value="maquinariaMenor">Maquinaria Menor</option>
                <option value="limpieza">Limpieza</option>
                <option value="herramientaMenor">Herramienta Menor</option>
                <option value="equipoSeguridad">Equipo Seguridad</option>
                <option value="materiales">Materiales</option>
            </select>
        </td>
        <td><input type="number" class="form-control gasto-input" name="detalles[${detailCount}][gasto]" onchange="updateSubtotal()"></td>
        <td><input type="file" class="form-control" name="detalles[${detailCount}][foto]"></td>
    `;
    tableBody.appendChild(newRow);
    detailCount++;
}

function updateSubtotal() {
    let subtotal = Array.from(document.querySelectorAll('.gasto-input')).reduce((acc, input) => acc + parseFloat(input.value || 0), 0);
    let cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    document.getElementById('subtotal').innerText = subtotal.toFixed(2);
    document.getElementById('cambio').innerText = (cantidad - subtotal).toFixed(2);
}
</script>
@endsection
