@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Destajos</h1>
    </div>

    <form action="{{ route('destajos.store', ['obraId' => $obraId]) }}" method="POST">

        @csrf
        <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
            @foreach ($detalles->groupBy('frente') as $frente => $destajos)
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h2 style="font-size: 24px; color: #34495e; margin-bottom: 0;">{{ $frente }}</h2>
                    <span style="font-size: 18px; font-weight: bold; color: #2c3e50;">Total: <span class="total-monto" data-frente="{{ $frente }}">$0.00</span></span>
                </div>

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
                        <!-- No filas precargadas -->
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRowDestajos(this, '{{ $frente }}')">Añadir Fila</button>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

<script>
function addPago(button) {
    const table = button.closest('table');
    const theadRow = table.querySelector('thead tr');
    const tbodyRows = table.querySelectorAll('tbody tr');

    const pagosCount = table.querySelectorAll('thead th').length - 5;

    const th = document.createElement('th');
    th.style.backgroundColor = '#2980b9';
    th.style.color = 'white';
    th.style.textAlign = 'center';
    th.style.padding = '10px';
    th.style.width = '160px';
    th.innerHTML = `Pago ${pagosCount + 1} <button type="button" class="btn btn-sm btn-light" onclick="addPago(this)">+</button>`;

    theadRow.insertBefore(th, theadRow.children[2 + pagosCount]);

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
        row.insertBefore(td, row.children[2 + pagosCount]);
    });
}

function removeRow(button) {
    const table = button.closest('table');
    button.closest('tr').remove();
    updateTotal(table);
}

function updateTotal(table) {
    let totalMonto = 0;
    table.querySelectorAll('input[name="monto_aprobado[]"]').forEach(input => {
        totalMonto += parseFloat(input.value.replace(/,/g, '')) || 0;
    });

    let frente = table.dataset.frente;
    let totalElement = document.querySelector(`.total-monto[data-frente="${frente}"]`);
    if (totalElement) {
        totalElement.innerText = `$${totalMonto.toFixed(2)}`;
    }
}

function addRowDestajos(button, frente) {
    const table = document.querySelector(`table[data-frente="${frente}"]`);
    const tableBody = table.querySelector('tbody');
    const pagosCount = table.querySelectorAll('thead th').length - 5;

    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" name="cotizacion[]" class="form-control"></td>
        <td><input type="text" name="monto_aprobado[]" class="form-control money-input" onblur="formatMoney(this)" oninput="updateTotal(this.closest('table'))"></td>
        ${Array.from({ length: pagosCount }).map(() => `
            <td class="pagos-column" style="padding: 10px; text-align: center; width: 160px;">
                <div class="pagos-container">
                    <input type="date" name="pago_fecha[]" class="form-control pago-fecha">
                    <input type="number" step="0.01" name="pago[]" class="form-control pago-fecha">
                </div>
            </td>
        `).join('')}
        <td><input type="number" step="0.01" name="pendiente[]" class="form-control" oninput="updateTotal(this.closest('table'))"></td>
        <td><input type="text" name="estado[]" class="form-control"></td>
        <td class="acciones-column"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
    `;

    tableBody.appendChild(newRow);
    updateTotal(table);
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
    updateTotal(input.closest('table'));
});
</script>
@endsection
