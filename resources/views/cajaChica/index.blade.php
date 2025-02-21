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
                            TOTAL: ${{ number_format($cajaChica->total, 2) }}
                        </span>
                    </h2>
                </div>

                <div id="table-container-{{ $cajaChica->id }}" style="display: none;">
                    <form action="{{ route('cajaChica.addDetail') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="caja_chica_id" value="{{ $cajaChica->id }}">
                        <input type="hidden" name="obra_id" value="{{ $obraId }}">
                        <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Descripción</th>
                                    <th>Vista</th>
                                    <th>Gasto</th>
                                    <th>Foto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="detalle-costo-body">
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
                                        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow(this)">Añadir Fila</button>
                        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
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

    function addRow(button) {
        const tableBody = button.closest('form').querySelector('.detalle-costo-body');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>#</td>
            <td><input type="text" class="form-control" name="descripcion"></td>
            <td>
                <select class="form-control" name="vista">
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
            <td><input type="number" class="form-control gasto-input" name="gasto" onchange="updateSubtotal()"></td>
            <td><input type="file" class="form-control" name="foto"></td>
            <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function removeRow(button) {
        const row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
        updateTotal(row.closest('.table-container'));
    }

    function updateSubtotal() {
        let subtotal = Array.from(document.querySelectorAll('.gasto-input')).reduce((acc, input) => acc + parseFloat(input.value || 0), 0);
        let cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
        document.getElementById('subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('cambio').innerText = (cantidad - subtotal).toFixed(2);
    }
</script>
@endsection
