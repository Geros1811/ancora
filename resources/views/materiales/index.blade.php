@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Materiales</h1>
    </div>

    <!-- Información general de materiales -->
    <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Nombre:</span>
            <span class="info-value" style="color: #2c3e50;">Materiales</span>
        </div>
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Costo Total:</span>
            <span class="info-value" id="costo-total" style="color: #2c3e50;">${{ number_format($costoTotal, 2) }}</span>
        </div>
    </div>

    @include('materiales.generales', ['obraId' => $obraId, 'generales' => $generales])
    @include('materiales.agregados', ['obraId' => $obraId, 'agregados' => $agregados])
    @include('materiales.aceros', ['obraId' => $obraId, 'aceros' => $aceros])
    @include('materiales.cemento', ['obraId' => $obraId, 'cemento' => $cemento])
    @include('materiales.losas', ['obraId' => $obraId, 'losas' => $losas])

    <!-- Botón para regresar -->
    <div class="actions" style="margin-top: 20px; text-align: center;">
        @if(Auth::check() && Auth::user()->role != 'maestro_obra' && Auth::user()->role != 'residente' && Auth::user()->hasRole('arquitecto'))
            <a href="{{ route('materiales.pdf', ['obraId' => $obraId]) }}" class="btn btn-primary" style="display: inline-block; background-color: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; transition: background-color 0.3s ease;">
                PDF <i class="fas fa-file-pdf" style="margin-left: 5px;"></i>
            </a>
        @endif
    </div>
</div>

<script>
    function addRow(type) {
        const tableBody = document.getElementById(`detalle-${type}-body`);
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_${type}[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_${type}[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                <input type="text" name="unidad[]" class="form-control" style="border: none; background: transparent; text-align: center;">
            </td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_${type}[]" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, type)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_${type}[]" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, type)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_${type}[]" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                <input type="file" name="fotos_${type}[]" class="form-control" style="border: none; background: transparent; text-align: center;">
            </td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this, type)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function removeRow(button, id, type) {
        if (confirm('¿Estás seguro de que deseas eliminar este elemento?')) {
            fetch(`/materiales/${type}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    const row = button.parentNode.parentNode;
                    row.parentNode.removeChild(row);
                    updateTotal();
                } else {
                    alert('Error al eliminar el registro.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el registro.');
            });
        }
    }

    function updateSubtotal(input, type) {
        const row = input.parentNode.parentNode;
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
        const subtotal = cantidad * precioUnitario;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('costo-total').innerText = `$${total.toFixed(2)}`;
    }

    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const toggleButton = section.previousElementSibling.querySelector('.toggle-button');
        if (section.style.display === 'none') {
            section.style.display = 'block';
            toggleButton.innerText = '-';
        } else {
            section.style.display = 'none';
            toggleButton.innerText = '+';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.hidden-section').forEach(section => {
            section.style.display = 'none';
        });
    });
</script>
@endsection
