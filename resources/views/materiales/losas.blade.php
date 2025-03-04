<div class="table-container" style="margin-top: 20px;">
    <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">
        <span class="toggle-button" onclick="toggleSection('losas')">+</span>
        Losas
    </h2>
    <div id="losas" class="hidden-section">
        <form action="{{ route('materiales.storeLosas', ['obraId' => $obraId]) }}" method="POST" enctype="multipart/form-data">
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
                <tbody id="detalle-losas-body">
                    @foreach ($losas as $index => $detalle)
                        <tr>
                            <input type="hidden" name="id_losas[]" value="{{ $detalle->id }}">
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_losas[]" value="{{ $detalle->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_losas[]" value="{{ $detalle->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}></td>
                            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                <select name="unidad_losas[]" class="form-control" style="border: none; background: transparent; text-align: center;" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}>
                                    <option value="M2" {{ $detalle->unidad == 'M2' ? 'selected' : '' }}>M2</option>
                                    <option value="KG" {{ $detalle->unidad == 'KG' ? 'selected' : '' }}>KG</option>
                                    <option value="PZ" {{ $detalle->unidad == 'PZ' ? 'selected' : '' }}>PZ</option>
                                    <option value="LOTE" {{ $detalle->unidad == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_losas[]" value="{{ $detalle->cantidad }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'losas')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_losas[]" value="{{ $detalle->precio_unitario }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'losas')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_losas[]" value="{{ $detalle->subtotal }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <input type="file" name="fotos_losas[]" class="form-control" style="border: none; background: transparent; text-align: center;" {{ Auth::check() && (Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente') ? 'disabled' : '' }}>
                                    @if($detalle->foto)
                                        <a href="{{ asset('storage/tickets/' . basename($detalle->foto)) }}" target="_blank">Ver foto</a>
                                    @else
                                        <span>Imagen no Subida</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    @if(Auth::check() && Auth::user()->role != 'maestro_obra' && Auth::user()->role != 'residente')
                                        <button type="button" class="btn btn-danger" onclick="removeRow(this, {{$detalle->id}}, 'losas')">Eliminar</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(Auth::check() && Auth::user()->role != 'maestro_obra' && Auth::user()->role != 'residente')
                    <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('losas')">Añadir Fila</button>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar Losas</button>
                @else
                    <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('losas')" style="display:none;">Añadir Fila</button>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;" disabled>Guardar Losas</button>
                @endif
            </form>
        </div>
    </div>
