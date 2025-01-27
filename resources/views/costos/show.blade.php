@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <!-- Título -->
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles del Costo: {{ $costo['nombre'] }}</h1>
        <p class="section-subtitle" style="font-size: 16px; color: #7f8c8d; margin-bottom: 20px;">{{ $costo['descripcion'] }}</p>
    </div>

    <!-- Información general del costo -->
    <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Nombre:</span>
            <span class="info-value" style="color: #2c3e50;">{{ $costo['nombre'] }}</span>
        </div>
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Descripción:</span>
            <span class="info-value" style="color: #2c3e50;">{{ $costo['descripcion'] }}</span>
        </div>
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Costo Total:</span>
            <span class="info-value" id="costo-total" style="color: #2c3e50;">${{ number_format($costo['costo'], 2) }}</span>
        </div>
    </div>

    <!-- Tabla de detalles -->
    <div class="table-container" style="margin-top: 20px;">
        <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">Detalles Adicionales</h2>
        <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Concepto</th>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Unidad</th>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Subtotal</th>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="detalle-costo-body">
                @foreach ($detalleCosto as $detalle)
                    <tr style="background-color: {{ $loop->even ? '#f9f9f9' : 'transparent' }};">
                        <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" value="{{ $detalle['fecha'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" value="{{ $detalle['concepto'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" value="{{ $detalle['lote'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" value="{{ $detalle['subtotal'] }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" oninput="updateTotal()"></td>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow()">Añadir Fila</button>
    </div>

    <!-- Botón para regresar -->
    <div class="actions" style="margin-top: 20px; text-align: center;">
        <a href="{{ url()->previous() }}" class="btn btn-primary" style="display: inline-block; background-color: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; transition: background-color 0.3s ease;">Regresar</a>
    </div>
</div>

<script>
    function addRow() {
        const tableBody = document.getElementById('detalle-costo-body');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" oninput="updateTotal()"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function removeRow(button) {
        const row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('costo-total').innerText = `$${total.toFixed(2)}`;
    }
</script>
@endsection
