<div class="table-container" style="margin-top: 20px;">
    <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">
        <span class="toggle-button" onclick="toggleSection('agregados')">+</span>
        Agregados
    </h2>
    <div id="agregados" class="hidden-section">
        <form action="{{ route('materiales.storeAgregados', ['obraId' => $obraId]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Concepto</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Unidad</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Cantidad</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Precio Unitario</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Subtotal</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fotos</th>
                        <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="detalle-agregados-body">
                    @foreach ($agregados as $index => $detalle)
                        <tr>
                            <input type="hidden" name="id_agregados[]" value="{{ $detalle->id }}">
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_agregados[]" value="{{ $detalle->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_agregados[]" value="{{ $detalle->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                <input type="text" name="unidad_agregados[]" class="form-control" style="border: none; background: transparent; text-align: center;" value="{{ $detalle->unidad ?? '' }}" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_agregados[]" value="{{ $detalle->cantidad }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" step="any" oninput="updateSubtotal(this, 'agregados')" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_agregados[]" value="{{ $detalle->precio_unitario }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" step="any" oninput="updateSubtotal(this, 'agregados')" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_agregados[]" value="{{ $detalle->subtotal }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <input type="file" name="fotos_agregados[]" class="form-control" style="border: none; background: transparent; text-align: center;" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}>
                                    @if($detalle->foto)
                                        <a href="{{ asset('tickets/' . basename($detalle->foto)) }}" target="_blank">Ver foto</a>
                                    @else
                                        <span>Imagen no Subida</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    @if(Auth::check() && Auth::user()->role != 'maestro_obra' && Auth::user()->role != 'residente')
                                        <button type="button" class="btn btn-danger" onclick="removeRow(this, {{$detalle->id}}, 'agregados')">Eliminar</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(Auth::check() && Auth::user()->role != 'maestro_obra' && Auth::user()->role != 'residente')
                    <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('agregados')">Añadir Fila</button>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar Agregados</button>
                @else
                    <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('agregados')" style="display:none;">Añadir Fila</button>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;" disabled>Guardar Agregados</button>
                @endif
            </form>

            <script>
                function removeRow(button, detalleId, type) {
                    if (confirm('¿Estás seguro de que quieres eliminar este registro?')) {
                        fetch(`/${type}/${detalleId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(response => {
                            if (response.ok) {
                                const row = button.parentNode.parentNode;
                                row.parentNode.removeChild(row);
                                updateTotal();
                            } else {
                                alert('Error al eliminar el registro.');
                            }
                        });
                    }
                }
            </script>
        </div>
    </div>
