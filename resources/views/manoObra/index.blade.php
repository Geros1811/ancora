@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Mano de Obra</h1>
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

    <!-- Contenedor de tablas de detalles -->
    <div id="tablas-detalles-container" style="margin-top: 40px;">
        @foreach ($nominas as $nomina)
            <div class="table-container" style="margin-top: 40px;">
                <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">Detalles de Nómina: {{ $nomina->nombre }} ({{ $nomina->fecha_inicio }} - {{ $nomina->fecha_fin }})</h2>
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
                    <button type="button" class="btn btn-warning" style="margin-top: 10px;" onclick="crearTablaDestajos()">Destajos</button>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
                </form>
            </div>
            <!-- Tabla de Destajos -->
            <div class="table-container" style="margin-top: 40px;">
                <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">Detalles de Destajos</h2>
                <form id="form-destajos" action="{{ route('destajo.store', ['obraId' => $obraId]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="nomina_id" value="{{ $nomina->id }}">
                    <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                            <tr>
                                <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Frente</th>
                                <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                                <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">No. Pago</th>
                                <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Cantidad</th>
                                <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="detalle-destajo-body">
                            @foreach ($destajos->where('nomina_id', $nomina->id) as $destajo)
                                <tr>
                                    <td style="border: 1px solid #ddd; text-align: center; padding: 5px;">
                                        <select name="frente[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;">
                                            <option value="" disabled selected>Seleccione una opción</option>
                                            <option value="Plomeria" {{ $destajo->frente == 'Plomeria' ? 'selected' : '' }}>Plomeria</option>
                                            <option value="Electricidad" {{ $destajo->frente == 'Electricidad' ? 'selected' : '' }}>Electricidad</option>
                                            <option value="Colocador de Pisos" {{ $destajo->frente == 'Colocador de Pisos' ? 'selected' : '' }}>Colocador de Pisos</option>
                                            <option value="Pintor" {{ $destajo->frente == 'Pintor' ? 'selected' : '' }}>Pintor</option>
                                            <option value="Herreria" {{ $destajo->frente == 'Herreria' ? 'selected' : '' }}>Herreria</option>
                                            <option value="Carpintero" {{ $destajo->frente == 'Carpintero' ? 'selected' : '' }}>Carpintero</option>
                                            <option value="Aluminio" {{ $destajo->frente == 'Aluminio' ? 'selected' : '' }}>Aluminio</option>
                                            <option value="Aire Acondicionado" {{ $destajo->frente == 'Aire Acondicionado' ? 'selected' : '' }}>Aire Acondicionado</option>
                                            <option value="Tabla Roca" {{ $destajo->frente == 'Tabla Roca' ? 'selected' : '' }}>Tabla Roca</option>
                                            <option value="Otros" {{ $destajo->frente == 'Otros' ? 'selected' : '' }}>Otros</option>
                                        </select>
                                        <input type="text" name="frente_otro[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%; display: {{ $destajo->frente == 'Otros' ? 'block' : 'none' }};" placeholder="Especificar" value="{{ $destajo->frente_otro }}">
                                    </td>
                                    <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="date" name="fecha[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="{{ $destajo->fecha }}"></td>
                                    <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" name="no_pago[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="{{ $destajo->no_pago }}"></td>
                                    <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" step="0.01" name="cantidad[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="{{ $destajo->cantidad }}"></td>
                                    <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="observaciones[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="{{ $destajo->observaciones }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRowDestajos(this)">Añadir Fila</button>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
                </form>
            </div>
        @endforeach
    </div>
</div>

<script>
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
                <button type="button" class="btn btn-warning" style="margin-top: 10px;" onclick="crearTablaDestajos()">Destajos</button>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
            </form>
        `;
        container.appendChild(newTable);
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
        updateTotal();
    }

    function updateSubtotal(input) {
        const row = input.parentNode.parentNode;
        const dias = Array.from(row.querySelectorAll('.horas')).reduce((total, input) => total + (parseFloat(input.value) || 0), 0);
        const precioDiario = parseFloat(row.querySelector('.precio-hora').value) || 0;
        const extrasMenos = parseFloat(row.querySelector('.extras-menos').value) || 0;
        const subtotal = (dias * precioDiario) + extrasMenos;
        row.querySelector('.total-horas').value = dias;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('costo-total').innerText = `$${total.toFixed(2)}`;
        
        // Actualizar el costo total en la vista de costos directos
        fetch(`{{ route('updateCostoDirecto', ['obraId' => $obraId, 'costo' => 'manoObra']) }}?costo=${total.toFixed(2)}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }

    function crearTablaDestajos() {
        const container = document.getElementById('tablas-detalles-container');
        const newTable = document.createElement('div');
        newTable.classList.add('table-container');
        newTable.style.marginTop = '40px';
        newTable.innerHTML = `
            <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">Detalles de Destajos</h2>
            <form id="form-destajos" action="{{ route('destajo.store', ['obraId' => $obraId]) }}" method="POST">
                @csrf
                <input type="hidden" name="nomina_id" value="{{ $nomina->id }}">
                <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Frente</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">No. Pago</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Cantidad</th>
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="detalle-destajo-body">
                        <tr>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;">
                                <select name="frente[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    <option value="Plomeria">Plomeria</option>
                                    <option value="Electricidad">Electricidad</option>
                                    <option value="Colocador de Pisos">Colocador de Pisos</option>
                                    <option value="Pintor">Pintor</option>
                                    <option value="Herreria">Herreria</option>
                                    <option value="Carpintero">Carpintero</option>
                                    <option value="Aluminio">Aluminio</option>
                                    <option value="Aire Acondicionado">Aire Acondicionado</option>
                                    <option value="Tabla Roca">Tabla Roca</option>
                                    <option value="Otros">Otros</option>
                                </select>
                                <input type="text" name="frente_otro[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%; display: none;" placeholder="Especificar">
                            </td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="date" name="fecha[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" name="no_pago[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="0"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" step="0.01" name="cantidad[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="0"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="observaciones[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;"></td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
                <button type="button" class="btn btn-success" style="margin-top: 10px; margin-left: 10px;" onclick="addRowDestajos(this)">Añadir Fila</button>
            </form>
        `;
        container.appendChild(newTable);
        document.querySelectorAll('.btn-warning').forEach(button => button.style.display = 'none');
    }

    function addRowDestajos(button) {
        const tableBody = button.closest('form').querySelector('.detalle-destajo-body');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;">
                <select name="frente[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="Plomeria">Plomeria</option>
                    <option value="Electricidad">Electricidad</option>
                    <option value="Colocador de Pisos">Colocador de Pisos</option>
                    <option value="Pintor">Pintor</option>
                    <option value="Herreria">Herreria</option>
                    <option value="Carpintero">Carpintero</option>
                    <option value="Aluminio">Aluminio</option>
                    <option value="Aire Acondicionado">Aire Acondicionado</option>
                    <option value="Tabla Roca">Tabla Roca</option>
                    <option value="Otros">Otros</option>
                </select>
                <input type="text" name="frente_otro[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%; display: none;" placeholder="Especificar">
            </td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="date" name="fecha[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" name="no_pago[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="0"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" step="0.01" name="cantidad[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="0"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="observaciones[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;"></td>
        `;
        tableBody.appendChild(newRow);
    }
</script>
@endsection
