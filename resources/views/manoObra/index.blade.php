@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Nomina</h1>
    </div>

    <!-- Información general de mano de obra -->
    <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
        <form id="form-nomina" action="{{ route('manoObra.store', ['obraId' => $obraId]) }}" method="POST">
            @csrf
            <div class="info-item" style="display: flex; flex-wrap: wrap; justify-content: space-between; margin-bottom: 10px;">
                <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                    <span class="info-label" style="font-weight: bold; color: #34495e;">Nombre de la Nómina:</span>
                    <input type="text" name="nombre_nomina" id="nombre_nomina" class="form-control" style="border: 1px solid #ddd; background: #fff; text-align: center;">
                </div>
                <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                    <span class="info-label" style="font-weight: bold; color: #34495e;">Semana del:</span>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" style="border: 1px solid #ddd; background: #fff; text-align: center;">
                </div>
                <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                    <span class="info-label" style="font-weight: bold; color: #34495e;">al:</span>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" style="border: 1px solid #ddd; background: #fff; text-align: center;">
                </div>
            </div>
            <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span class="info-label" style="font-weight: bold; color: #34495e;">Costo Total:</span>
                <span class="info-value" id="costo-total" style="color: #2c3e50;">${{ number_format($costoTotal, 2) }}</span>
            </div>
            <button type="button" class="btn btn-primary" onclick="crearTabla()">Crear</button>
        </form>
    </div>
    <div class="text-right">
        <!-- Botón para ir al resumen -->
        <form action="{{ route('resumen', ['obraId' => $obraId]) }}" method="get">
            <button type="submit" class="btn btn-info">
                Ver Resumen de Nómina
            </button>
        </form>
    </div>
 
    <!-- Contenedor de tablas de detalles -->
    <div id="tablas-detalles-container" style="margin-top: 40px;">
        @foreach ($nominas as $nomina)
            <div class="table-container" style="margin-top: 40px;">
                <!-- Botón para mostrar u ocultar la tabla antes del título de la nómina -->
                <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">
                    <button type="button" class="btn btn-info btn-sm" onclick="toggleTableVisibility({{ $nomina->id }})" style="margin-bottom: 10px;">+</button>
                    Detalles de Nómina: {{ $nomina->nombre }} : {{ $nomina->dia_inicio }} : {{ $nomina->fecha_inicio }} - {{ $nomina->dia_fin }} : {{ $nomina->fecha_fin }}  
                    <span id="total-nomina-{{ $nomina->id }}" style="font-size: 16px; color: #e74c3c;" data-nomina-id="{{ $nomina->id }}">
                        TOTAL Nómina: ${{ number_format($nomina->total, 2) }}
                    </span>
                </h2>
            <form action="{{ route('manoObra.store', ['obraId' => $obraId]) }}" method="POST">
                @csrf
                <input type="hidden" name="nombre_nomina" value="{{ $nomina->nombre }}">
                <input type="hidden" name="fecha_inicio" value="{{ $nomina->fecha_inicio }}">
                <input type="hidden" name="fecha_fin" value="{{ $nomina->fecha_fin }}">
                <input type="hidden" name="nomina_id" value="{{ $nomina->id }}">
                <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Nombre</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Puesto</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">L</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">M</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">MI</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">J</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">V</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">S</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Total Días</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Precio Diario</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Extras/Menos</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Precio</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="detalle-costo-body">
                        @foreach ($detalles->where('nomina_id', $nomina->id) as $detalle)
                            <tr>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="nombre[]" value="{{ $detalle->nombre }}" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="puesto[]" value="{{ $detalle->puesto }}" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="lunes[]" value="{{ $detalle->lunes }}" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="martes[]" value="{{ $detalle->martes }}" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="miercoles[]" value="{{ $detalle->miercoles }}" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="jueves[]" value="{{ $detalle->jueves }}" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="viernes[]" value="{{ $detalle->viernes }}" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="sabado[]" value="{{ $detalle->sabado }}" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" name="total_dias[]" value="{{ $detalle->total_horas }}" class="form-control total-horas" style="border: none; background: transparent; text-align: center; width: 100%;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" step="0.01" name="precio_diario[]" value="{{ $detalle->precio_hora }}" class="form-control precio-hora" style="border: none; background: transparent; text-align: center; width: 100%;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" step="0.01" name="extras_menos[]" value="{{ $detalle->extras_menos }}" class="form-control extras-menos" style="border: none; background: transparent; text-align: center; width: 100%;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="subtotal[]" value="{{ $detalle->subtotal }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center; width: 100%;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow(this)">Añadir Fila</button>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
            </form>
        </div>
    @endforeach
</div>

<script>
    function toggleTableVisibility(nominaId) {
        const table = document.getElementById("table-container-" + nominaId);
        const button = event.target;
        if (table.style.display === "none") {
            table.style.display = "block";
            button.textContent = "-"; // Cambiar el texto del botón a "-"
        } else {
            table.style.display = "none";
            button.textContent = "+"; // Cambiar el texto del botón a "+"
        }
    }
    function eliminarDetalle(button) {
        const row = button.closest("tr");
        row.remove();
    }

    
     function crearTabla() {
        const nombreNomina = document.getElementById('nombre_nomina').value;
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        if (!nombreNomina || !fechaInicio || !fechaFin) {
            alert('Por favor, complete todos los campos de la nómina.');
            return;
        }

        const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const fechaInicioDate = new Date(fechaInicio + 'T00:00:00');
        const fechaFinDate = new Date(fechaFin + 'T00:00:00');
        const fechaInicioStr = `${diasSemana[fechaInicioDate.getUTCDay()]} ${fechaInicioDate.toLocaleDateString()}`;
        const fechaFinStr = `${diasSemana[fechaFinDate.getUTCDay()]} ${fechaFinDate.toLocaleDateString()}`;

        const container = document.getElementById('tablas-detalles-container');
        const newTable = document.createElement('div');
        newTable.classList.add('table-container');
        newTable.style.marginTop = '40px';
        newTable.innerHTML = `
            <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">Detalles de Nómina: ${nombreNomina} (${fechaInicioStr} - ${fechaFinStr})</h2>
            <form action="{{ route('manoObra.store', ['obraId' => $obraId]) }}" method="POST">
                @csrf
                <input type="hidden" name="nombre_nomina" value="${nombreNomina}">
                <input type="hidden" name="fecha_inicio" value="${fechaInicio}">
                <input type="hidden" name="fecha_fin" value="${fechaFin}">
                <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Nombre</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Puesto</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">L</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">M</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">MI</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">J</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">V</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;">S</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Total Días</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Precio Diario</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Extras/Menos</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Precio</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="detalle-costo-body">
                        <!-- Filas dinámicas -->
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow(this)">Añadir Fila</button>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
            </form>
        `;
        container.appendChild(newTable);
    }

    function updateSubtotal(input) {
        const row = input.parentNode.parentNode;
        const dias = Array.from(row.querySelectorAll('.horas')).reduce((total, input) => total + (parseFloat(input.value) || 0), 0);
        const precioDiario = parseFloat(row.querySelector('.precio-hora').value) || 0;
        const extrasMenos = parseFloat(row.querySelector('.extras-menos').value) || 0;
        const subtotal = (dias * precioDiario) + extrasMenos;
        row.querySelector('.total-horas').value = dias;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        updateTotal(row.closest('.table-container'));
    }

    function updateTotal(container) {
    let total = 0;
    container.querySelectorAll('.subtotal').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const nominaId = container.querySelector('input[name="nomina_id"]').value;
    document.getElementById(`total-nomina-${nominaId}`).innerText = `TOTAL Nómina: $${total.toFixed(2)}`;

    // Enviar el total al backend con AJAX
    fetch(`/actualizar-total-nomina/${nominaId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify({ total })
    })
    .then(response => response.json())
    .then(data => console.log("Total actualizado:", data))
    .catch(error => console.error("Error al actualizar total:", error));
}
    function addRow(button) {
        const tableBody = button.closest('form').querySelector('.detalle-costo-body');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="nombre[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="puesto[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="lunes[]" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="martes[]" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="miercoles[]" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="jueves[]" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="viernes[]" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px; width: 40px;"><input type="number" name="sabado[]" class="form-control horas" style="border: none; background: transparent; text-align: center; width: 100%;" min="0" max="1" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" name="total_dias[]" class="form-control total-horas" style="border: none; background: transparent; text-align: center; width: 100%;" readonly></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" step="0.01" name="precio_diario[]" class="form-control precio-hora" style="border: none; background: transparent; text-align: center; width: 100%;" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" step="0.01" name="extras_menos[]" class="form-control extras-menos" style="border: none; background: transparent; text-align: center; width: 100%;" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="subtotal[]" class="form-control subtotal" style="border: none; background: transparent; text-align: center; width: 100%;" readonly></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function removeRow(button) {
        const row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
        updateTotal(row.closest('.table-container'));
    }

    
</script>
@endsection
