@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Materiales</h1>
    </div>

    <!-- Información general de materiales -->
    <div class="info-box" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Nombre:</span>
            <span class="info-value" style="color: #2c3e50;">Materiales</span>
        </div>
        <div class="info-item" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span class="info-label" style="font-weight: bold; color: #34495e;">Costo Total:</span>
            <span class="info-value" id="costo-total" style="color: #2c3e50;">${{ number_format($costoTotal, 2) }}</span>
        </div>
    </div>

    <!-- Tabla de Generales -->
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

    <!-- Tabla de Agregados -->
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
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_agregados[]" value="{{ $detalle->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_agregados[]" value="{{ $detalle->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_agregados[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                        <option value="M3" {{ $detalle->unidad == 'M3' ? 'selected' : '' }}>M3</option>
                                        <option value="KG" {{ $detalle->unidad == 'KG' ? 'selected' : '' }}>KG</option>
                                        <option value="PZ" {{ $detalle->unidad == 'PZ' ? 'selected' : '' }}>PZ</option>
                                        <option value="LOTE" {{ $detalle->unidad == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_agregados[]" value="{{ $detalle->cantidad }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'agregados')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_agregados[]" value="{{ $detalle->precio_unitario }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'agregados')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_agregados[]" value="{{ $detalle->subtotal }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <input type="file" name="fotos_agregados[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                    @if($detalle->foto)
                                        <a href="{{ asset('storage/tickets/' . basename($detalle->foto)) }}" target="_blank">Ver foto</a>
                                    @else
                                        <span>Imagen no Subida</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <button type="button" class="btn btn-danger" onclick="removeRow(this, {{$detalle->id}}, 'agregados')">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('agregados')">Añadir Fila</button>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar Agregados</button>
            </form>
        </div>
    </div>

    <!-- Tabla de Aceros -->
    <div class="table-container" style="margin-top: 20px;">
        <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">
            <span class="toggle-button" onclick="toggleSection('aceros')">+</span>
            Aceros
        </h2>
        <div id="aceros" class="hidden-section">
            <form action="{{ route('materiales.storeAceros', ['obraId' => $obraId]) }}" method="POST" enctype="multipart/form-data">
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
                    <tbody id="detalle-aceros-body">
                        @foreach ($aceros as $index => $detalle)
                            <tr>
                                <input type="hidden" name="id_aceros[]" value="{{ $detalle->id }}">
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_aceros[]" value="{{ $detalle->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_aceros[]" value="{{ $detalle->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_aceros[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                        <option value="KG" {{ $detalle->unidad == 'KG' ? 'selected' : '' }}>KG</option>
                                        <option value="M3" {{ $detalle->unidad == 'M3' ? 'selected' : '' }}>M3</option>
                                        <option value="PZ" {{ $detalle->unidad == 'PZ' ? 'selected' : '' }}>PZ</option>
                                        <option value="LOTE" {{ $detalle->unidad == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_aceros[]" value="{{ $detalle->cantidad }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'aceros')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_aceros[]" value="{{ $detalle->precio_unitario }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'aceros')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_aceros[]" value="{{ $detalle->subtotal }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <input type="file" name="fotos_aceros[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                    @if($detalle->foto)
                                        <a href="{{ asset('storage/tickets/' . basename($detalle->foto)) }}" target="_blank">Ver foto</a>
                                    @else
                                        <span>Imagen no Subida</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <button type="button" class="btn btn-danger" onclick="removeRow(this, {{$detalle->id}}, 'aceros')">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('aceros')">Añadir Fila</button>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar Aceros</button>
            </form>
        </div>
    </div>

    <!-- Tabla de Cemento -->
    <div class="table-container" style="margin-top: 20px;">
        <h2 class="table-title" style="font-size: 20px; color: #34495e; margin-bottom: 10px;">
            <span class="toggle-button" onclick="toggleSection('cemento')">+</span>
            Cemento
        </h2>
        <div id="cemento" class="hidden-section">
            <form action="{{ route('materiales.storeCemento', ['obraId' => $obraId]) }}" method="POST" enctype="multipart/form-data">
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
                    <tbody id="detalle-cemento-body">
                        @foreach ($cemento as $index => $detalle)
                            <tr>
                                <input type="hidden" name="id_cemento[]" value="{{ $detalle->id }}">
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_cemento[]" value="{{ $detalle->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_cemento[]" value="{{ $detalle->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_cemento[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                        <option value="BOLSA" {{ $detalle->unidad == 'BOLSA' ? 'selected' : '' }}>BOLSA</option>
                                        <option value="KG" {{ $detalle->unidad == 'KG' ? 'selected' : '' }}>KG</option>
                                        <option value="PZ" {{ $detalle->unidad == 'PZ' ? 'selected' : '' }}>PZ</option>
                                        <option value="LOTE" {{ $detalle->unidad == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_cemento[]" value="{{ $detalle->cantidad }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'cemento')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_cemento[]" value="{{ $detalle->precio_unitario }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this, 'cemento')"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_cemento[]" value="{{ $detalle->subtotal }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <input type="file" name="fotos_cemento[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                    @if($detalle->foto)
                                        <a href="{{ asset('storage/tickets/' . basename($detalle->foto)) }}" target="_blank">Ver foto</a>
                                    @else
                                        <span>Imagen no Subida</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <button type="button" class="btn btn-danger" onclick="removeRow(this, {{$detalle->id}}, 'cemento')">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('cemento')">Añadir Fila</button>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar Cemento</button>
            </form>
        </div>
    </div>

    <!-- Tabla de Losas -->
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
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_losas[]" value="{{ $detalle->fecha }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_losas[]" value="{{ $detalle->concepto }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_losas[]" class="form-control" style="border: none; background: transparent; text-align: center;">
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
                                    <input type="file" name="fotos_losas[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                    @if($detalle->foto)
                                        <a href="{{ asset('storage/tickets/' . basename($detalle->foto)) }}" target="_blank">Ver foto</a>
                                    @else
                                        <span>Imagen no Subida</span>
                                    @endif
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding:
