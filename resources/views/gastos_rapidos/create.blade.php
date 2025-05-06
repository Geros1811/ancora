@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/gastos_rapidos.css') }}">
@endsection

@section('content')
    <div class="container gastos-rapidos-container">
        <h1 class="gastos-rapidos-title">Gastos Rápidos</h1>

        @if (session('success'))
            <div class="alert alert-success gastos-rapidos-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger gastos-rapidos-alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('gastos_rapidos.store') }}" method="POST" class="gastos-rapidos-form">
            @csrf
            <input type="hidden" name="obraId" value="{{ request('obraId') }}">

            <div class="form-group">
                <label for="tabla" class="gastos-rapidos-label">Seleccionar Tabla:</label>
                <div style="display: flex; align-items: center;">
                    <select class="form-control gastos-rapidos-select" id="tabla" name="tabla">
                        <option value="papeleria">Papelería</option>
                        <option value="gasolina">Gasolina</option>
                        <option value="rentas">Rentas</option>
                        <option value="utilidades">Utilidades</option>
                        <option value="acarreos">Acarreos</option>
                        <option value="comida">Comida</option>
                        <option value="tramites">Trámites</option>
                        <option value="cimbras">Cimbras</option>
                        <option value="maquinariaMayor">Maquinaria Mayor</option>
                        <option value="maquinariaMenor">Maquinaria Menor</option>
                        <option value="herramientaMenor">Herramienta Menor</option>
                        <option value="equipoSeguridad">Equipo de Seguridad</option>
                        <option value="limpieza">Limpieza</option>
                        <optgroup label="Materiales" class="gastos-rapidos-optgroup-label">
                            <option value="generales">Generales</option>
                            <option value="agregados">Agregados</option>
                            <option value="aceros">Aceros</option>
                            <option value="cemento">Cemento</option>
                            <option value="losas">Losas</option>
                        </optgroup>
                        <option value="rentaMaquinaria">Renta de Maquinaria</option>
                    </select>
                    <button type="button" class="btn gastos-rapidos-button-primary" onclick="crearTabla()">Crear Tabla</button>
                </div>
            </div>

        <script>
            function crearTabla() {
                var tablaSeleccionada = document.getElementById("tabla").value;
                console.log("Tabla seleccionada: " + tablaSeleccionada);

                // Create table element
                var table = document.createElement("table");
                table.className = "obra-table";
                table.style = "width: 100%; border-collapse: collapse; margin-top: 10px;";

                // Create table header
                var thead = document.createElement("thead");
                thead.innerHTML = `
                    <tr>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Concepto</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Unidad</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Cantidad</th>
                        <th class="gastos-rapidos-th">Precio Unitario</th>
                        <th class="gastos-rapidos-th">Subtotal</th>
                        <th class="gastos-rapidos-th">Fotos</th>
                    </tr>
                `;
                table.appendChild(thead);

                // Create table body
                var tbody = document.createElement("tbody");
                var newRow = document.createElement("tr");
                newRow.innerHTML = `
                    <td class="gastos-rapidos-td"><input type="date" name="fecha[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;"></td>
                    <td class="gastos-rapidos-td"><input type="text" name="concepto[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;"></td>
                    <td class="gastos-rapidos-td">
                    <input type="text" name="unidad[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                    </td>
                    <td class="gastos-rapidos-td"><input type="number" name="cantidad[]" step="any" class="form-control cantidad gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                    <td class="gastos-rapidos-td"><input type="number" name="precio_unitario[]" step="any" class="form-control precio-unitario gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)">
                    </td>
                    <td class="gastos-rapidos-td">
                        <input type="text" class="form-control subtotal display-subtotal gastos-rapidos-input" style="border: none; background: transparent; text-align: center;" readonly>
                        <input type="hidden" name="subtotal[]" class="subtotal-hidden">
                    </td>
                    <td class="gastos-rapidos-td">
                        <input type="file" name="fotos[]" class="form-control gastos-rapidos-input" style="border: none; background: transparent; text-align: center;">
                    </td>
                `;
                tbody.appendChild(newRow);
                table.appendChild(tbody);

                // Append the table to the dynamicFields div
                var dynamicFields = document.getElementById("dynamicFields");
                dynamicFields.innerHTML = ''; // Clear previous content

                // Create form element
var form = document.createElement("form");
form.action = "{{ route('gastos_rapidos.store') }}";
form.method = "POST";
form.enctype = "multipart/form-data";  // Agrega esta línea


                var tablaInput = document.createElement("input");
                tablaInput.type = "hidden";
                tablaInput.name = "tabla";

                // Add CSRF token
                var csrfToken = document.createElement("input");
                csrfToken.type = "hidden";
                csrfToken.name = "_token";
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);

                // Add obraId input
                var obraIdInput = document.createElement("input");
                obraIdInput.type = "hidden";
                obraIdInput.name = "obraId";
                obraIdInput.value = "{{ request('obraId') }}";
                form.appendChild(obraIdInput);

                // Add tabla input
                var tablaInput = document.createElement("input");
                tablaInput.type = "hidden";
                tablaInput.name = "tabla";
                tablaInput.value = tablaSeleccionada;
                form.appendChild(tablaInput);

                form.appendChild(table);

                // Create submit button
                var submitButton = document.createElement("button");
                submitButton.type = "submit";
                submitButton.className = "btn btn-primary";
                submitButton.textContent = "Guardar";
                form.appendChild(submitButton);

                dynamicFields.appendChild(form);

                // Add event listeners to cantidad and precio_unitario inputs
                const cantidadInputs = dynamicFields.querySelectorAll('.cantidad');
                cantidadInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        updateSubtotal(this);
                    });
                });

                const precioUnitarioInputs = dynamicFields.querySelectorAll('.precio-unitario');
                precioUnitarioInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        updateSubtotal(this);
                    });
                });
            }

            function updateSubtotal(input) {
                const row = input.parentNode.parentNode;
                const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
                const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
                const subtotal = cantidad * precioUnitario;
                row.querySelector('.display-subtotal').value = subtotal.toFixed(2);
                row.querySelector('.subtotal-hidden').value = subtotal.toFixed(2);
            }
        </script>

        <div id="dynamicFields">
            <!-- Dynamic fields will be added here -->
        </div>
