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

    <div id="tablas-detalles-container" style="margin-top: 40px;">
        @foreach ($cajaChicas as $cajaChica)
            <div class="table-container" style="margin-top: 40px;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <button type="button" class="btn btn-info btn-sm" onclick="toggleTableVisibility({{ $cajaChica->id }})" style="margin-right: 10px;">
                        <span class="toggle-button">+</span>
                    </button>
                    <h2 class="table-title" style="font-size: 20px; color: #34495e; margin: 0;">
                        Caja Chica: {{ $cajaChica->fecha }} - {{ $cajaChica->maestroObra->name }}
                        <span id="total-cajaChica-{{ $cajaChica->id }}" style="font-size: 16px; color: #e74c3c;" data-cajaChica-id="{{ $cajaChica->id }}">
                            Cantidad: ${{ number_format($cajaChica->cantidad, 2) }}
                        </span>
                    </h2>
                </div>

                <div id="table-container-{{ $cajaChica->id }}" style="display: none;">
                    <form action="{{ route('cajaChica.addDetail') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="caja_chica_id" value="{{ $cajaChica->id }}">
                        <input type="hidden" name="obra_id" value="{{ $obraId }}">
                        <input type="hidden" id="cantidad-{{ $cajaChica->id }}" value="{{ $cajaChica->cantidad }}">
                        <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Descripción</th>
                                    <th>Vista</th>
                                    <th>Gasto</th>
                                    <th>Foto</th>
                                </tr>
                            </thead>
                            <tbody class="detalle-costo-body">
                                @foreach ($cajaChica->detallesCajaChica as $index => $detalle)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><input type="text" class="form-control" name="detalles[{{ $index }}][descripcion]" value="{{ $detalle->descripcion }}"></td>
                                        <td>
                                            <select class="form-control" name="detalles[{{ $index }}][vista]">
                                                @foreach(['papeleria', 'gasolina', 'rentas', 'utilidades', 'acarreos', 'comidas', 'tramites', 'cimbras', 'maquinariaMayor', 'rentaMaquinaria', 'maquinariaMenor', 'limpieza', 'herramientaMenor', 'equipoSeguridad', 'materiales'] as $vista)
                                                    <option value="{{ $vista }}" {{ $detalle->vista == $vista ? 'selected' : '' }}>{{ ucfirst($vista) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control gasto-input" name="detalles[{{ $index }}][gasto]" value="{{ $detalle->gasto }}" onchange="updateSubtotal({{ $cajaChica->id }})"></td>
                                        <td>
                                            @if (isset($detalle->foto))
                                                <a href="{{ asset('storage/' . $detalle->foto) }}" target="_blank">Ver Foto</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 10px;">
                            <button type="button" class="btn btn-success" onclick="addRow(this, {{ $cajaChica->id }})">Añadir Fila</button>
                            <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Guardar</button>
                            <div style="margin-left: 20px; text-align: right;">
                                <div>Subtotal: $<span id="subtotal-{{ $cajaChica->id }}">{{ number_format($cajaChica->subtotal, 2) }}</span></div>
                                <div>Cambio: $<span id="cambio-{{ $cajaChica->id }}">{{ number_format($cajaChica->cambio, 2) }}</span></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function toggleTableVisibility(cajaChicaId) {
        const container = document.getElementById("table-container-" + cajaChicaId);
        const button = event.target;
        const toggleButton = button.querySelector('.toggle-button');
        if (container.style.display === "none") {
            container.style.display = "block";
            toggleButton.textContent = "-";
        } else {
            container.style.display = "none";
            toggleButton.textContent = "+";
        }
    }

    function addRow(button, cajaChicaId) {
        const tableBody = button.closest('form').querySelector('.detalle-costo-body');
        const newRow = document.createElement('tr');
        const rowCount = tableBody.querySelectorAll('tr').length;
        newRow.innerHTML = `
            <td>${rowCount + 1}</td>
            <td><input type="text" class="form-control" name="detalles[${rowCount}][descripcion]"></td>
            <td>
                <select class="form-control" name="detalles[${rowCount}][vista]">
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
            <td><input type="number" class="form-control gasto-input" name="detalles[${rowCount}][gasto]" onchange="updateSubtotal(cajaChicaId)"></td>
            <td><input type="file" class="form-control" name="detalles[${rowCount}][foto]"></td>
        `;
        tableBody.appendChild(newRow);
        updateSubtotal(cajaChicaId);
    }

    function removeRow(button) {
        const row = button.closest('tr');
        const cajaChicaId = row.closest('form').querySelector('input[name="caja_chica_id"]').value;
        row.remove();
        updateSubtotal(cajaChicaId);
    }

    function updateSubtotal(cajaChicaId) {
        let subtotal = 0;
        document.querySelectorAll(`#table-container-${cajaChicaId} .gasto-input`).forEach(input => {
            subtotal += parseFloat(input.value || 0);
        });
        let cantidad = parseFloat(document.getElementById(`cantidad-${cajaChicaId}`).value) || 0;
        document.getElementById(`subtotal-${cajaChicaId}`).innerText = subtotal.toFixed(2);
        document.getElementById(`cambio-${cajaChicaId}`).innerText = (cantidad - subtotal).toFixed(2);
    }
</script>
@endsection
