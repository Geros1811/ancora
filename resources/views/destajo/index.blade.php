@extends('layouts.app')


<div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
    <form id="form-destajos" action="{{ route('destajos.store', ['obraId' => $obraId]) }}" method="POST">
        @csrf
        <div class="info-item" style="display: flex; flex-wrap: wrap; justify-content: space-between; margin-bottom: 10px;">
            <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                <span class="info-label" style="font-weight: bold; color: #34495e;">Seleccione Nómina:</span>
                <select name="nomina_id" id="nomina_id" class="form-control" onchange="actualizarFechas()">
                    <option value="" disabled selected>Seleccione una nómina</option>
                    @foreach ($nominas as $nomina)
                        <option value="{{ $nomina->id }}" data-fecha-inicio="{{ $nomina->fecha_inicio }}" data-fecha-fin="{{ $nomina->fecha_fin }}">{{ $nomina->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                <span class="info-label" style="font-weight: bold; color: #34495e;">Semana del:</span>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" readonly>
            </div>
            <div style="flex: 1; min-width: 200px; margin-bottom: 10px;">
                <span class="info-label" style="font-weight: bold; color: #34495e;">al:</span>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" readonly>
            </div>
        </div>
        <button type="button" class="btn btn-primary" onclick="crearTabla()">Crear Tabla de Destajos</button>
    </form>
</div>

<div id="contenedor-tablas"></div>

<style>
    .obra-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        color: black;
        border-radius: 8px;
    }

    .obra-table th, .obra-table td {
        padding: 5px;
        text-align: left;
        border: 1px solid #ddd;
        font-size: 14px;
    }

    .obra-table th {
        background-color: #0056b3;
        color: white;
    }

    .obra-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .obra-table tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .obra-table td input {
        width: 100%;
        padding: 3px;
        border-radius: 4px;
        border: 1px solid #ddd;
        box-sizing: border-box;
        background-color: white;
        color: black;
        font-size: 14px;
    }

    .table-container button {
        margin-top: 15px;
    }

    .custom-input {
        display: none; /* Ocultar por defecto */
    }
</style>

<script>
function actualizarFechas() {
    var select = document.getElementById("nomina_id");
    var fechaInicio = select.options[select.selectedIndex].getAttribute("data-fecha-inicio");
    var fechaFin = select.options[select.selectedIndex].getAttribute("data-fecha-fin");
    document.getElementById("fecha_inicio").value = fechaInicio;
    document.getElementById("fecha_fin").value = fechaFin;
}

function crearTabla() {
    var nominaId = document.getElementById("nomina_id").value;
    var nominaTexto = document.getElementById("nomina_id").options[document.getElementById("nomina_id").selectedIndex].text;
    var fechaInicio = document.getElementById("fecha_inicio").value;
    var fechaFin = document.getElementById("fecha_fin").value;

    if (!nominaId) {
        alert("Seleccione una nómina antes de crear la tabla.");
        return;
    }

    var tablaHtml = `
        <div class="table-container" style="margin-top: 20px;">
            <h3>${nominaTexto} - ${fechaInicio} al ${fechaFin}</h3>
            <form class="destajo-form" action="{{ route('destajos.store', ['obraId' => $obraId]) }}" method="POST">
                @csrf
                <table class="obra-table">
                    <thead>
                        <tr>
                            <th>Frente</th>
                            <th>Monto Aprobado</th>
                            <th>Paso Actual</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-${nominaId}">
                        <tr>
                            <td>
                                <select name="frente[]" class="form-control" onchange="toggleCustomInput(this)">
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
                                <input type="text" name="frente_custom[]" class="form-control custom-input" placeholder="Especifique" onblur="this.value = this.value.trim();">
                            </td>
                            <td><input type="number" name="monto_aprobado[]" class="form-control"></td>
                            <td><input type="text" name="paso_actual[]" class="form-control" value="1" readonly></td>
                            <td><input type="number" name="cantidad[]" class="form-control"></td>
                            <td><button type="button" class="btn btn-danger" onclick="eliminarFila(this)">Eliminar</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" onclick="agregarFila(${nominaId})">Agregar Fila</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>`;

    document.getElementById("contenedor-tablas").insertAdjacentHTML("beforeend", tablaHtml);
}

function toggleCustomInput(select) {
    var customInput = select.parentElement.querySelector('.custom-input');
    if (select.value === "Otros") {
        customInput.style.display = "block"; // Mostrar el campo de entrada
        customInput.value = ""; // Limpiar el campo
    } else {
        customInput.style.display = "none"; // Ocultar el campo de entrada
        customInput.value = ""; // Limpiar el campo
    }
}

function agregarFila(nominaId) {
    var filaHtml = `
        <tr>
            <td>
                <select name="frente[]" class="form-control" onchange="toggleCustomInput(this)">
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
                <input type="text" name="frente_custom[]" class="form-control custom-input" placeholder="Especifique" onblur="this.value = this.value.trim();">
            </td>
            <td><input type="number" name="monto_aprobado[]" class="form-control"></td>
            <td><input type="text" name="paso_actual[]" class="form-control" value="1" readonly></td>
            <td><input type="number" name="cantidad[]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger" onclick="eliminarFila(this)">Eliminar</button></td>
        </tr>`;

    document.getElementById("tabla-" + nominaId).insertAdjacentHTML("beforeend", filaHtml);
}

function eliminarFila(button) {
    button.closest("tr").remove();
}
</script>