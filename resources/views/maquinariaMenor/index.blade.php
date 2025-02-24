@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Maquinaria Menor</h1>
    </div>

    <!-- Información general de maquinariaMenor -->
    <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Nombre:</span>
            <span class="info-value" style="color: #2c3e50;">Maquinaria Menor</span>
        </div>
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Costo Total:</span>
            <span class="info-value" id="costo-total" style="color: #2c3e50;">${{ number_format($costoTotal, 2) }}</span>
        </div>
    </div>

    <!-- Tabla de detalles -->
    <div class="table-container" style="margin-top: 20px;">
        <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">Detalles Adicionales</h2>
        <form action="{{ route('maquinariaMenor.store', ['obraId' => $obraId]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Concepto</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Unidad</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Cantidad</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Precio Unitario</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Subtotal</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fotos</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="detalle-costo-body">
                    @foreach ($detalles as $index => $detalle)
                        <tr>
                            <input type="hidden" name="id[]" value="{{ $detalle->id }}">
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha[]" value="{{ $detalle->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto[]" value="{{ $detalle->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                <select name="unidad[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                    <option value="KG" {{ $detalle->unidad == 'KG' ? 'selected' : '' }}>KG</option>
                                    <option value="LTS" {{ $detalle->unidad == 'LTS' ? 'selected' : '' }}>LTS</option>
                                    <option value="PZ" {{ $detalle->unidad == 'PZ' ? 'selected' : '' }}>PZ</option>
                                    <option value="LOTE" {{ $detalle->unidad == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                </select>
                            </td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad[]" value="{{ $detalle->cantidad }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario[]" value="{{ $detalle->precio_unitario }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal[]" value="{{ $detalle->subtotal }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                 <input type="file" name="fotos[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                @if($detalle->foto)
                                    <a href="{{ asset('storage/tickets/' . basename($detalle->foto)) }}" target="_blank">Ver foto</a>
                                @else
                                    <span>Imagen no Subida</span>
                                @endif
                            </td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                <button type="button" class="btn btn-danger" onclick="removeRow(this, {{ $detalle->id }})">Eliminar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow()">Añadir Fila</button>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
        </form>
    </div>

    <!-- Botón para regresar -->
    <div class="actions" style="margin-top: 20px; text-align: center;">
        <a href="{{ route('maquinariaMenor.index', ['obraId' => $obraId]) }}" class="btn btn-primary" style="display: inline-block; background-color: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; transition: background-color 0.3s ease;">Regresar</a>
    </div>
</div>

<script>
    function addRow() {
        const tableBody = document.getElementById('detalle-costo-body');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                <select name="unidad[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                    <option value="KG">KG</option>
                    <option value="LTS">LTS</option>
                    <option value="PZ">PZ</option>
                    <option value="LOTE">LOTE</option>
                </select>
            </td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad[]" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario[]" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal[]" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
             <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                <input type="file" name="fotos[]" class="form-control" style="border: none; background: transparent; text-align: center;">
            </td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this, 0)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function removeRow(button, detalleId) {
        if (confirm('¿Estás seguro de que quieres eliminar este registro?')) {
            fetch(`/maquinariaMenor/${detalleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (response.ok) {
                    const row = button.parentNode.parentNode;
                    row.parentNode.removeChild(row);
                    updateTotal();
                } else {
                    alert('Error al eliminar el registro.');
                }
            });
        }
    }

    function updateSubtotal(input) {
        const row = input.parentNode.parentNode;
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
        const subtotal = cantidad * precioUnitario;
        row.querySelector('.subtotal').value = `$${subtotal.toFixed(2)}`;
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(input => {
            total += parseFloat(input.value.replace('$', '')) || 0;
        });
        document.getElementById('costo-total').innerText = `$${total.toFixed(2)}`;
        
        // Actualizar el costo total en la vista de costos indirectos
        fetch(`{{ route('updateCostoIndirecto', ['obraId' => $obraId, 'costo' => 'maquinariaMenor']) }}?costo=${total.toFixed(2)}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
</script>
@endsection
