@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Ingresos</h1>
    </div>

    <!-- Información general de Ingresos -->
    <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Nombre:</span>
            <span class="info-value" style="color: #2c3e50;">Ingresos</span>
        </div>
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Ingreso Total:</span>
            <span class="info-value" id="costo-total" style="color: #2c3e50;">${{ number_format($costoTotal, 2) }}</span>
        </div>
    </div>

    <!-- Tabla de detalles -->
    <div class="table-container" style="margin-top: 20px;">
        <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">Detalles Adicionales</h2>
        <form action="{{ route('ingresos.store', ['obraId' => $obraId]) }}" method="POST">
            @csrf
            <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Concepto</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Importe</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Observaciones</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="detalle-costo-body">
                    @foreach ($ingresos as $index => $ingreso)
                        <tr>
                            <input type="hidden" name="id[]" value="{{ $ingreso->id }}">
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha[]" value="{{ $ingreso->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto[]" value="{{ $ingreso->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="importe[]" value="{{ $ingreso->importe }}" class="form-control importe" style="border: none; background: transparent; text-align: center;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="observaciones[]" value="{{ $ingreso->observaciones }}" class="form-control observaciones" style="border: none; background: transparent; text-align: center;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                <button type="button" class="btn btn-danger" onclick="removeRow(this, {{ $ingreso->id }})">Eliminar</button>
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
        <a href="{{ route('ingresos.index', ['obraId' => $obraId]) }}" class="btn btn-primary" style="display: inline-block; background-color: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; transition: background-color 0.3s ease;">Regresar</a>
    </div>
</div>

<script>
    function addRow() {
        const tableBody = document.getElementById('detalle-costo-body');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="importe[]" class="form-control importe" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="observaciones[]" class="form-control observaciones" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this, 0)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function removeRow(button, detalleId) {
        if (confirm('¿Estás seguro de que quieres eliminar este registro?')) {
            fetch(`/ingresos/${detalleId}`, {
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
        const importe = parseFloat(row.querySelector('.importe').value) || 0;
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.importe').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('costo-total').innerText = `$${total.toFixed(2)}`;

         // Actualizar el costo total en la vista de costos indirectos
        fetch(`{{ route('updateCostoIndirecto', ['obraId' => $obraId, 'costo' => 'ingresos']) }}?costo=${total.toFixed(2)}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }

</script>
@endsection
