@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Destajos</h1>
    </div>

    <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
        @foreach ($detalles->groupBy('frente') as $frente => $destajos)
            <h2 style="font-size: 24px; color: #34495e; margin-bottom: 10px;">{{ $frente }}</h2>
            <table class="table table-bordered" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Cotización</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Monto Aprobado</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Pasos</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Pendiente</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Estado</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="cotizacion[]" class="form-control"></td>
                        <td><input type="number" step="0.01" name="monto_aprobado[]" class="form-control" oninput="updateTotal()"></td>
                        <td class="pasos-column" style="background-color: #2980b9; padding: 10px; text-align: center;">
                            <div class="pasos-container">
                                <input type="date" name="paso_fecha[]" class="form-control paso-fecha">
                            </div>
                            <button type="button" class="btn btn-sm btn-light" onclick="addPaso(this)">+</button>
                        </td>
                        <td><input type="number" step="0.01" name="pendiente[]" class="form-control" oninput="updateTotal()"></td>
                        <td><input type="text" name="estado[]" class="form-control"></td>
                        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</div>

<script>
    function addPaso(button) {
        let pasosContainer = button.previousElementSibling;
        let newPaso = document.createElement('input');
        newPaso.type = 'date';
        newPaso.name = 'paso_fecha[]';
        newPaso.className = 'form-control paso-fecha';
        pasosContainer.appendChild(newPaso);
    }

    function removeRow(button) {
        button.closest('tr').remove();
        updateTotal();
    }

    function updateTotal() {
        let totalMonto = 0;
        document.querySelectorAll('input[name="monto_aprobado[]"]').forEach(input => {
            totalMonto += parseFloat(input.value) || 0;
        });
        document.getElementById('total-monto-aprobado').innerText = `$${totalMonto.toFixed(2)}`;
    }
</script>
@endsection
