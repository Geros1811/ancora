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

    <div id="tablesContainer">
        @foreach ($cajaChicas as $cajaChica)
            <div class="table-container">
                <div class="table-details-header">
                    <h2 class="table-title">Detalles</h2>
                    <p>Fecha: {{ \Carbon\Carbon::parse($cajaChica->fecha)->format('d/m/Y') }}</p>
                    <p>Cantidad: {{ number_format($cajaChica->cantidad, 2) }}</p>
                    <p>Maestro de Obra: {{ $cajaChica->maestroObra->name }}</p>
                </div>
                <table class="obra-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Descripción</th>
                            <th>Vista</th>
                            <th>Gasto</th>
                            <th>Añadir Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cajaChica->detalles as $index => $detalle)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detalle['descripcion'] }}</td>
                                <td>{{ $detalle['vista'] }}</td>
                                <td>{{ number_format($detalle['gasto'], 2) }}</td>
                                <td>
                                    @if (isset($detalle['foto']))
                                        <a href="{{ asset('storage/' . $detalle['foto']) }}" target="_blank">Ver Foto</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.getElementById('cajaChicaForm').addEventListener('submit', function(event) {
        event.preventDefault();

        var fecha = document.getElementById('fecha').value;
        var cantidad = document.getElementById('cantidad').value;
        var maestroObra = document.getElementById('maestro_obra').options[document.getElementById('maestro_obra').selectedIndex].text;

        var fechaFormateada = new Date(fecha).toLocaleDateString('es-ES');
        var cantidadFormateada = parseFloat(cantidad).toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });

        var tableContainer = document.createElement('div');
        tableContainer.classList.add('table-container');

        var tableDetailsHeader = document.createElement('div');
        tableDetailsHeader.classList.add('table-details-header');

        var tableTitle = document.createElement('h2');
        tableTitle.classList.add('table-title');
        tableTitle.innerText = 'Detalles';

        var fechaP = document.createElement('p');
        fechaP.innerText = 'Fecha: ' + fechaFormateada;

        var cantidadP = document.createElement('p');
        cantidadP.innerText = 'Cantidad: ' + cantidadFormateada;

        var maestroP = document.createElement('p');
        maestroP.innerText = 'Maestro de Obra: ' + maestroObra;

        tableDetailsHeader.appendChild(tableTitle);
        tableDetailsHeader.appendChild(fechaP);
        tableDetailsHeader.appendChild(cantidadP);
        tableDetailsHeader.appendChild(maestroP);

        var table = document.createElement('table');
        table.classList.add('obra-table');

        var tableHeader = document.createElement('thead');
        tableHeader.innerHTML = `
            <tr>
                <th>No.</th>
                <th>Descripción</th>
                <th>Vista</th>
                <th>Gasto</th>
                <th>Añadir Foto</th>
            </tr>
        `;

        var tableBody = document.createElement('tbody');
        tableBody.id = 'detalle-caja-chica-body';

        table.appendChild(tableHeader);
        table.appendChild(tableBody);

        var subtotalSection = document.createElement('div');
        subtotalSection.classList.add('subtotal-section');
        subtotalSection.innerHTML = `<p>Subtotal: <span id="subtotal">0.00</span></p>`;

        var cambioSection = document.createElement('div');
        cambioSection.classList.add('cambio-section');
        cambioSection.innerHTML = `<p>Cambio: <span id="cambio">${cantidadFormateada}</span></p>`;

        var addRowButton = document.createElement('button');
        addRowButton.classList.add('btn', 'btn-success');
        addRowButton.innerText = 'Añadir Fila';
        addRowButton.onclick = function() {
            addRow(tableBody, cantidad);
        };

        var saveButton = document.createElement('button');
        saveButton.classList.add('btn', 'btn-primary');
        saveButton.style.marginLeft = '10px';
        saveButton.innerText = 'Guardar';
        saveButton.onclick = function() {
            saveTable(tableContainer);
        };

        var buttonAndTotals = document.createElement('div');
        buttonAndTotals.classList.add('button-and-totals');
        buttonAndTotals.appendChild(addRowButton);
        buttonAndTotals.appendChild(saveButton);
        buttonAndTotals.appendChild(subtotalSection);
        buttonAndTotals.appendChild(cambioSection);

        tableContainer.appendChild(tableDetailsHeader);
        tableContainer.appendChild(table);
        tableContainer.appendChild(buttonAndTotals);

        document.getElementById('tablesContainer').appendChild(tableContainer);

        document.getElementById('cajaChicaForm').reset();
    });

    function addRow(tableBody, initialCantidad) {
        var newRow = document.createElement('tr');
        var rowCount = tableBody.rows.length + 1;

        newRow.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" name="detalles[${rowCount}][descripcion]" class="form-control"></td>
            <td>
                <select name="detalles[${rowCount}][vista]" class="form-control">
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
            <td><input type="number" name="detalles[${rowCount}][gasto]" class="form-control gasto-input" onchange="updateSubtotal(this, ${initialCantidad})"></td>
            <td><input type="file" name="detalles[${rowCount}][foto]" class="form-control"></td>
        `;

        tableBody.appendChild(newRow);
    }

    function updateSubtotal(input, initialCantidad) {
        var tableContainer = input.closest('.table-container');
        var tableBody = tableContainer.querySelector('tbody');
        var gastoInputs = tableBody.querySelectorAll('.gasto-input');
        var subtotal = Array.from(gastoInputs).reduce((sum, input) => sum + parseFloat(input.value || 0), 0);

        tableContainer.querySelector('#subtotal').innerText = subtotal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
        tableContainer.querySelector('#cambio').innerText = (initialCantidad - subtotal).toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
    }

    function saveTable(tableContainer) {
        // Aquí puedes implementar la lógica para guardar la tabla en la vista
        alert('Tabla guardada en la vista.');
    }
</script>
@endsection
