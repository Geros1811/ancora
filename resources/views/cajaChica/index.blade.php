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
            <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
        </div>

        <div class="form-group">
            <label for="maestro_obra">Maestro de Obra:</label>
            <select class="form-control" id="maestro_obra" name="maestro_obra_id" required>
                <option value="">Seleccione un maestro de obra</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Crear</button>
    </form>

    <div id="tablas-detalles-container" style="margin-top: 40px;">
        @foreach ($cajaChicas as $cajaChica)
            <div class="table-container" style="margin-top: 40px;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <button type="button" class="btn btn-info btn-sm" onclick="toggleTableVisibility({{ $cajaChica->id }})" style="margin-right: 10px;">
                        <span class="toggle-button">+</span>
                    </button>
                    <h2 class="table-title" style="font-size: 20px; color: #34495e; margin: 0;">
                        Caja Chica: {{ $cajaChica->formatted_created_at }} - {{ $cajaChica->maestroObra->name }}
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
                                     <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                                     <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Concepto</th>
                                     <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Unidad</th>
                                     <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Cantidad</th>
                                     <th class="gastos-rapidos-th">Precio Unitario</th>
                                      <th class="gastos-rapidos-th">Subtotal</th>
                                      <th class="gastos-rapidos-th">Vista</th>
                                      <th class="gastos-rapidos-th">Foto</th>
                                      <th class="gastos-rapidos-th">Enviar</th>
                                  </tr>
                              </thead>
                              <tbody class="detalle-costo-body">
                                  @foreach ($cajaChica->detallesCajaChica as $detalle)
                                      <tr>
                                          <td class="gastos-rapidos-td">
                                              <input type="date" name="fecha[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" value="{{ $cajaChica->formatted_created_at }}" readonly>
                                          </td>
                                          <td class="gastos-rapidos-td"><input type="text" name="concepto[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" value="{{ $detalle->concepto }}"></td>
                                          <td class="gastos-rapidos-td">
                                              <select name="unidad[]" class="form-control gastos-rapidos-select" style="border: none; background: transparent; text-align: center;">
                                                  <option value="KG" {{ $detalle->unidad == 'KG' ? 'selected' : '' }}>KG</option>
                                                  <option value="LTS" {{ $detalle->unidad == 'LTS' ? 'selected' : '' }}>LTS</option>
                                                  <option value="PZ" {{ $detalle->unidad == 'PZ' ? 'selected' : '' }}>PZ</option>
                                                  <option value="LOTE" {{ $detalle->unidad == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                              </select>
                                          </td>
                                          <td class="gastos-rapidos-td"><input type="number" name="cantidad[]" class="form-control cantidad gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" value="{{ $detalle->cantidad }}" oninput="updateSubtotal(this)"></td>
                                          <td class="gastos-rapidos-td"><input type="number" name="precio_unitario[]" class="form-control precio-unitario gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" value="{{ $detalle->precio_unitario }}" oninput="updateSubtotal(this)"></td>
                                          <td class="gastos-rapidos-td">
                                              <input type="text" class="form-control subtotal display-subtotal gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" readonly value="{{ $detalle->subtotal }}">
                                              <input type="hidden" name="subtotal[]" class="subtotal-hidden" value="{{ $detalle->subtotal }}">
                                          </td>
                                          <td class="gastos-rapidos-td">
                                              <select name="vista[]" class="form-control gastos-rapidos-select" style="border: none; background: transparent; text-align: center;">
                                                  <option value="papeleria" {{ $detalle->vista == 'papeleria' ? 'selected' : '' }}>Papelería</option>
                                                  <option value="gasolina" {{ $detalle->vista == 'gasolina' ? 'selected' : '' }}>Gasolina</option>
                                                  <option value="rentas" {{ $detalle->vista == 'rentas' ? 'selected' : '' }}>Rentas</option>
                                                  <option value="utilidades" {{ $detalle->vista == 'utilidades' ? 'selected' : '' }}>Utilidades</option>
                                                  <option value="acarreos" {{ $detalle->vista == 'acarreos' ? 'selected' : '' }}>Acarreos</option>
                                                  <option value="comida" {{ $detalle->vista == 'comida' ? 'selected' : '' }}>Comida</option>
                                                  <option value="tramites" {{ $detalle->vista == 'tramites' ? 'selected' : '' }}>Trámites</option>
                                                  <option value="cimbras" {{ $detalle->vista == 'cimbras' ? 'selected' : '' }}>Cimbras</option>
                                                  <option value="maquinariaMayor" {{ $detalle->vista == 'maquinariaMayor' ? 'selected' : '' }}>Maquinaria Mayor</option>
                                                  <option value="maquinariaMenor" {{ $detalle->vista == 'maquinariaMenor' ? 'selected' : '' }}>Maquinaria Menor</option>
                                                  <option value="herramientaMenor" {{ $detalle->vista == 'herramientaMenor' ? 'selected' : '' }}>Herramienta Menor</option>
                                                  <option value="equipoSeguridad" {{ $detalle->vista == 'equipoSeguridad' ? 'selected' : '' }}>Equipo de Seguridad</option>
                                                  <option value="limpieza" {{ $detalle->vista == 'limpieza' ? 'selected' : '' }}>Limpieza</option>
                                                  <optgroup label="Materiales" class="gastos-rapidos-optgroup-label">
                                                      <option value="generales" {{ $detalle->vista == 'generales' ? 'selected' : '' }}>Generales</option>
                                                      <option value="agregados" {{ $detalle->vista == 'agregados' ? 'selected' : '' }}>Agregados</option>
                                                      <option value="aceros" {{ $detalle->vista == 'aceros' ? 'selected' : '' }}>Aceros</option>
                                                      <option value="cemento" {{ $detalle->vista == 'cemento' ? 'selected' : '' }}>Cemento</option>
                                                      <option value="losas" {{ $detalle->vista == 'losas' ? 'selected' : '' }}>Losas</option>
                                                  </optgroup>
                                                  <option value="rentaMaquinaria" {{ $detalle->vista == 'rentaMaquinaria' ? 'selected' : '' }}>Renta de Maquinaria</option>
                                              </select>
                                          </td>
                                          <td class="gastos-rapidos-td">
                                              <input type="file" name="foto[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;">
                                              @if($detalle->foto)
                                                  <a href="{{ asset($detalle->foto) }}" target="_blank">Ver Foto</a>
                                              @endif
                                          </td>
                                          <td class="gastos-rapidos-td">
                                              <button type="button" class="btn btn-primary" onclick="submitForm(this)">Enviar</button>
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
        const toggleButton = container.previousElementSibling.querySelector('.toggle-button');
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
        const tableTitle = button.closest('.table-container').querySelector('.table-title').textContent;
        const dateRegex = /(\d{4}-\d{2}-\d{2})/;
        const match = tableTitle.match(dateRegex);
        const tableDate = match ? match[0] : '';

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="gastos-rapidos-td"><input type="date" name="fecha[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" value="${tableDate}"></td>
            <td class="gastos-rapidos-td"><input type="text" name="concepto[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;"></td>
            <td class="gastos-rapidos-td">
                <select name="unidad[]" class="form-control gastos-rapidos-select" style="border: none; background: transparent; text-align: center;">
                    <option value="KG">KG</option>
                    <option value="LTS">LTS</option>
                    <option value="PZ">PZ</option>
                    <option value="LOTE">LOTE</option>
                </select>
            </td>
            <td class="gastos-rapidos-td"><input type="number" name="cantidad[]" class="form-control cantidad gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
            <td class="gastos-rapidos-td"><input type="number" name="precio_unitario[]" class="form-control precio-unitario gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
            <td class="gastos-rapidos-td">
                <input type="text" class="form-control subtotal display-subtotal gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" readonly>
                <input type="hidden" name="subtotal[]" class="subtotal-hidden">
            </td>
            <td class="gastos-rapidos-td">
                <select name="vista[]" class="form-control gastos-rapidos-select" style="border: none; background: transparent; text-align: center;">
                    <option value="papeleria">Papelería</option>
                    <option value="gasolina">Gasolina</option>
                    <option value="rentas">Rentas</option>
                    <option value="utilidades">Utilidades</option>
                    <option value="acarreos">Acarreos</option>
                    <option value="comida">Comida</option>
                    <option value="tramites">Trámites</option>
                    <option value="cimbras">Cimbras</option>
                    <option value="maquinariaMayor">Maquinaria Mayor</option>
                    <option value="maquinariaMenor">Maquinaria Menor</option>
                    <option value="herramientaMenor">Herramienta Menor</option>
                    <option value="equipoSeguridad">Equipo de Seguridad</option>
                    <option value="limpieza">Limpieza</option>
                    <optgroup label="Materiales" class="gastos-rapidos-optgroup-label">
                        <option value="generales">Generales</option>
                        <option value="agregados">Agregados</option>
                        <option value="aceros">Aceros</option>
                        <option value="cemento">Cemento</option>
                        <option value="losas">Losas</option>
                    </optgroup>
                    <option value="rentaMaquinaria">Renta de Maquinaria</option>
                </select>
            </td>
            <td class="gastos-rapidos-td">
                <input type="file" name="foto[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;">
            </td>
            <td class="gastos-rapidos-td">
                <button type="button" class="btn btn-primary" onclick="submitForm(this)">Enviar</button>
            </td>
        `;
        tableBody.appendChild(newRow);
        setTimeout(() => updateSubtotal(cajaChicaId), 0);
    }

    function removeRow(button) {
        const row = button.closest('tr');
        const cajaChicaId = row.closest('form').querySelector('input[name="caja_chica_id"]').value;
        row.remove();
        updateSubtotal(cajaChicaId);
    }

    function updateSubtotal(input) {
        const row = input.parentNode.parentNode;
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
        let subtotal = cantidad * precioUnitario;
        row.querySelector('.display-subtotal').value = subtotal.toFixed(2);
        row.querySelector('.subtotal-hidden').value = subtotal.toFixed(2);

        // Get cajaChicaId from the row
        let cajaChicaId = row.closest('form').querySelector('input[name="caja_chica_id"]').value;

        // Calculate total subtotal for the table
        let totalSubtotal = 0;
        let subtotalInputs = document.querySelectorAll(`#table-container-${cajaChicaId} .subtotal-hidden`);
        subtotalInputs.forEach(input => {
            totalSubtotal += parseFloat(input.value) || 0;
        });

         // Get initial amount for the table
        let initialAmount = parseFloat(document.getElementById(`cantidad-${cajaChicaId}`).value) || 0;

        // Calculate cambio
        let cambio = initialAmount - totalSubtotal;

        // Update subtotal and cambio display
        document.querySelector(`#subtotal-${cajaChicaId}`).textContent = totalSubtotal.toFixed(2);
        document.querySelector(`#cambio-${cajaChicaId}`).textContent = cambio.toFixed(2);
    }

    function submitForm(button) {
        const row = button.closest('tr');
        const formData = new FormData();
        const cajaChicaId = row.closest('form').querySelector('input[name="caja_chica_id"]').value;
        const obraId = document.querySelector('input[name="obra_id"]').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        formData.append('_token', csrfToken);
        formData.append('caja_chica_id', cajaChicaId);
        formData.append('obra_id', obraId);
        formData.append('fecha[]', row.querySelector('input[name="fecha[]"]').value);
        formData.append('concepto[]', row.querySelector('input[name="concepto[]"]').value);
        formData.append('unidad[]', row.querySelector('select[name="unidad[]"]').value);
        formData.append('cantidad[]', row.querySelector('input[name="cantidad[]"]').value);
        formData.append('precio_unitario[]', row.querySelector('input[name="precio_unitario[]"]').value);
        formData.append('subtotal[]', row.querySelector('input[name="subtotal[]"]').value);
        formData.append('vista[]', row.querySelector('select[name="vista[]"]').value);

        const fotoInput = row.querySelector('input[name="foto[]"]');
        if (fotoInput.files.length > 0) {
            formData.append('foto[]', fotoInput.files[0]);
        }

        fetch('{{ route('cajaChica.storeDetail') }}', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Detalle de caja chica enviado correctamente.');
            } else {
                alert('Error al enviar el detalle de caja chica: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al enviar el detalle de caja chica.');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.table-container').forEach(container => {
            let cajaChicaId = container.querySelector('input[name="caja_chica_id"]').value;
            let subtotalInputs = container.querySelectorAll(`#table-container-${cajaChicaId} .subtotal-hidden`);
            subtotalInputs.forEach(input => {
                updateSubtotal(input);
            });
        });
    });
</script>
@endsection
