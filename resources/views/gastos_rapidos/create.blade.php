@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/gastos_rapidos.css') }}">
@endsection

@section('content')
    <h1>Gastos Rápidos</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('gastos_rapidos.store') }}" method="POST">
        @csrf
        <input type="hidden" name="obraId" value="{{ request('obraId') }}">

        <div class="form-group">
            <label for="tabla">Seleccionar Tabla:</label>
            <div style="display: flex; align-items: center;">
                <select class="form-control" id="tabla" name="tabla">
                    <option value="papeleria">Papelería</option>
                    <option value="gasolina">Gasolina</option>
                    <option value="rentas">Rentas</option>
                    <option value="utilidades">Utilidades</option>
                    <option value="acarreos">Acarreos</option>
                    <option value="comidas">Comidas</option>
                    <option value="tramites">Trámites</option>
                    <option value="cimbras">Cimbras</option>
                    <option value="maquinariaMayor">MaquinariaMayor</option>
                    <option value="maquinariaMenor">MaquinariaMenor</option>
                    <option value="herramientaMenor">HerramientaMenor</option>
                    <option value="equipoSeguridad">Equipo de Seguridad</option>
                    <option value="limpieza">Limpieza</option>
                    <option value="materiales">Materiales</option>
                    <option value="rentaMaquinaria">Renta de Maquinaria</option>
                </select>
                <select class="form-control" id="materialesSub" name="materialesSub" style="display:none;">
                    <option value="generales">Generales</option>
                    <option value="agregados">Agregados</option>
                    <option value="aceros">Aceros</option>
                    <option value="cemento">Cemento</option>
                    <option value="losas">Losas</option>
                </select>
                <button type="button" class="btn btn-secondary" onclick="crearTabla()">Crear Tabla</button>
            </div>
        </div>

        <script>
            document.getElementById('tabla').addEventListener('change', function() {
                var materialesSub = document.getElementById('materialesSub');
                if (this.value === 'materiales') {
                    materialesSub.style.display = 'inline-block';
                } else {
                    materialesSub.style.display = 'none';
                }
            });
        </script>

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
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Precio Unitario</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Subtotal</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                    </tr>
                `;
                table.appendChild(thead);

                // Create table body
                var tbody = document.createElement("tbody");
                var newRow = document.createElement("tr");
                newRow.innerHTML = `
                    <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                    <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                    <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                        <select name="unidad[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                            <option value="KG">KG</option>
                            <option value="LTS">LTS</option>
                            <option value="PZ">PZ</option>
                            <option value="LOTE">LOTE</option>
                        </select>
                    </td>
                    <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad[]" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                    <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario[]" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)">
                    </td>
                    <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                    <input type="text" class="form-control subtotal display-subtotal" style="border: none; background: transparent; text-align: center;" readonly>
                    <input type="hidden" name="subtotal[]" class="subtotal-hidden">
                    </td>
                    <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-success">Guardar</button></td>
                `;
                tbody.appendChild(newRow);
                table.appendChild(tbody);

                // Append the table to the dynamicFields div
                var dynamicFields = document.getElementById("dynamicFields");
                dynamicFields.innerHTML = ''; // Clear previous content

                // Get the selected sub-tabla
                var materialesSubSeleccionada = document.getElementById("materialesSub").value;
                if (tablaSeleccionada === 'materiales') {
                    tablaSeleccionada = materialesSubSeleccionada;
                }

                // Create form element
                var form = document.createElement("form");
                form.action = "{{ route('gastos_rapidos.store') }}";
                form.method = "POST";

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
