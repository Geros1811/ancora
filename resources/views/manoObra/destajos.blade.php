@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Destajos</h1>
    </div>

    <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
        @foreach ($detalles->groupBy('frente') as $frente => $destajos)
            <h2 style="font-size: 24px; color: #34495e; margin-bottom: 10px;">{{ $frente }}</h2>
            <table class="table table-bordered" style="width: 100%; border-collapse: collapse; margin-top: 10px;" data-frente="{{ $frente }}">
                <thead>
                    <tr>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Cotización</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Monto Aprobado</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px; width: 160px;">Pago 1 <button type="button" class="btn btn-sm btn-light" onclick="addPago(this)">+</button></th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Pendiente</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Estado</th>
                        <th style="background-color: #2980b9; color: white; text-align: center; padding: 10px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($destajos as $destajo)
                        @if (!$loop->first)
                            <tr>
                                <td><input type="text" name="cotizacion[]" class="form-control" value="{{ $destajo->cotizacion }}"></td>
                                <td><input type="text" name="monto_aprobado[]" class="form-control money-input" value="{{ number_format($destajo->monto_aprobado, 2) }}" onblur="formatMoney(this)" oninput="updateTotal(this)"></td>
                                <td class="pagos-column" style="padding: 10px; text-align: center; width: 160px;">
                                    <div class="pagos-container">
                                        <input type="date" name="pago_fecha[]" class="form-control pago-fecha" value="{{ $destajo->pago_fecha }}">
                                        <input type="number" step="0.01" name="pago[]" class="form-control pago-fecha" value="{{ $destajo->pago }}">
                                    </div>
                                </td>
                                <td><input type="number" step="0.01" name="pendiente[]" class="form-control" value="{{ $destajo->pendiente }}" oninput="updateTotal(this)"></td>
                                <td><input type="text" name="estado[]" class="form-control" value="{{ $destajo->estado }}" style="margin-bottom: 10px;"><input type="text" name="estado_otro[]" class="form-control" style="margin-top: 5px;"></td>
                                <td class="acciones-column" style="text-align: center;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRowDestajos(this, '{{ $frente }}')">Añadir Fila</button>
            <div class="total-monto-aprobado" style="text-align: right; margin-top: 20px;">
                <span style="font-size: 18px; font-weight: bold; color: #34495e;">Monto total aprobado:</span>
                <span class="total-monto" style="font-size: 18px; color: #2c3e50;">$0.00</span>
            </div>
        @endforeach
    </div>
</div>

<style>
    .form-control {
        border: 1px solid #ddd;
        background: #fff;
        text-align: center;
        width: 100%;
    }

    .table-container {
        margin-top: 40px;
    }

    .table-title {
        font-size: 20px;
        color: #34495e;
        margin-bottom: 10px;
    }

    .obra-table th, .obra-table td {
        border: 1px solid #ddd;
        text-align: center;
        padding: 10px;
    }

    .obra-table th {
        background-color: #2980b9;
        color: white;
        font-weight: bold;
    }

    .obra-table td {
        background-color: #f9f9f9;
    }

    .btn {
        margin-top: 10px;
    }

    .acciones-column {
        text-align: center;
    }
</style>

<script>
function addPago(button) {
    const table = button.closest('table');
    const theadRow = table.querySelector('thead tr');
    const tbodyRows = table.querySelectorAll('tbody tr');

    // Contamos solo las columnas de pago
    const pagosCount = table.querySelectorAll('thead th').length - 5; // Restar las columnas fijas

    // Crear nueva columna en el encabezado
    const th = document.createElement('th');
    th.style.backgroundColor = '#2980b9';
    th.style.color = 'white';
    th.style.textAlign = 'center';
    th.style.padding = '10px';
    th.style.width = '160px';
    th.innerHTML = `Pago ${pagosCount + 1} <button type="button" class="btn btn-sm btn-light" onclick="addPago(this)">+</button>`;
    
    // Insertar después de la última columna de pago
    theadRow.insertBefore(th, theadRow.children[2 + pagosCount]);

    // Añadir nueva columna en cada fila del cuerpo de la tabla
    tbodyRows.forEach(row => {
        const td = document.createElement('td');
        td.className = 'pagos-column';
        td.style.padding = '10px';
        td.style.textAlign = 'center';
        td.style.width = '160px';
        td.innerHTML = `
            <div class="pagos-container">
                <input type="date" name="pago_fecha[]" class="form-control pago-fecha">
                <input type="number" step="0.01" name="pago[]" class="form-control pago-fecha">
            </div>
        `;

        // Insertar después de la última columna de pago existente
        row.insertBefore(td, row.children[2 + pagosCount]);
    });
}

    function removeRow(button) {
        button.closest('tr').remove();
        updateTotal(button);
    }

    function updateTotal(input) {
        const table = input.closest('table');
        let totalMonto = 0;
        table.querySelectorAll('input[name="monto_aprobado[]"]').forEach(input => {
            totalMonto += parseFloat(input.value.replace(/,/g, '')) || 0;
        });
        table.nextElementSibling.querySelector('.total-monto').innerText = `$${totalMonto.toFixed(2)}`;
    }

    function addRowDestajos(button, frente) {
    const table = document.querySelector(`table[data-frente="${frente}"]`);
    const tableBody = table.querySelector('tbody');
    const pagosCount = table.querySelectorAll('thead th').length - 5; // Restar columnas fijas

    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" name="cotizacion[]" class="form-control"></td>
        <td><input type="text" name="monto_aprobado[]" class="form-control money-input" onblur="formatMoney(this)" oninput="updateTotal(this)"></td>
        ${Array.from({ length: pagosCount }).map(() => `
            <td class="pagos-column" style="padding: 10px; text-align: center; width: 160px;">
                <div class="pagos-container">
                    <input type="date" name="pago_fecha[]" class="form-control pago-fecha">
                    <input type="number" step="0.01" name="pago[]" class="form-control pago-fecha">
                </div>
            </td>
        `).join('')}
        <td><input type="number" step="0.01" name="pendiente[]" class="form-control" oninput="updateTotal(this)"></td>
        <td><input type="text" name="estado[]" class="form-control"></td>
        <td class="acciones-column"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
    `;

    tableBody.appendChild(newRow);
}


    function formatMoney(input) {
        if (input.value.trim() === '') return;
        let value = input.value.replace(/,/g, '');
        value = parseFloat(value).toFixed(2);
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    document.querySelectorAll('.money-input').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/,/g, '');
            value = parseFloat(value).toFixed(2);
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        });
    });
</script>
@endsection
