<div class="table-container" style="margin-top: 20px;">
    <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">
        <span class="toggle-button" onclick="toggleSection('generales')">+</span>
        Generales
    </h2>
    <div id="generales" class="hidden-section">
        <form action="{{ route('materiales.storeGenerales', ['obraId' => $obraId]) }}" method="POST" enctype="multipart/form-data">
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
                <tbody id="detalle-generales-body">
                    @foreach ($generales as $index => $detalle)
                        <tr>
                            <input type="hidden" name="id_generales[]" value="{{ $detalle->id }}">
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_generales[]" value="{{ $detalle->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_generales[]" value="{{ $detalle->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                <select name="unidad_generales[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                    <option value="M3" {{ $detalle->unidad == 'M3' ? 'selected' : '' }}>M3</option>
                                    <option value="KG" {{ $detalle->unidad == 'KG' ? 'selected' : '' }}>KG</option>
                                    <option value="PZ" {{ $detalle->unidad == 'PZ' ? 'selected' : '' }}>PZ</option>
                                    <option value="LOTE" {{ $detalle->unidad == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_generales[]" value="{{ $detalle->cantidad }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'generales')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_generales[]" value="{{ $detalle->precio_unitario }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'generales')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_generales[]" value="{{ $detalle->subtotal }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <input type="file" name="fotos_generales[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                    @if($detalle->foto)
                                        <a href="{{ asset('storage/tickets/' . basename($detalle->foto)) }}" target="_blank">Ver foto</a>
                                    @else
                                        <span>Imagen no Subida</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <button type="button" class="btn btn-danger" onclick="removeRow(this, {{$detalle->id}}, 'generales')">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('generales')">Añadir Fila</button>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar Generales</button>
            </form>
        </div>
    </div>
