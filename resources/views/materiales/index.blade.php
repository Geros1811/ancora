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
            <form action="{{ route('materiales.storeGenerales', ['obraId' => $obraId]) }}" method="POST">
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
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="detalle-generales-body">
                        @foreach ($generales as $detalle)
                            <tr>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_generales[]" value="{{ $detalle['fecha'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_generales[]" value="{{ $detalle['concepto'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_generales[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                        <option value="M3" {{ $detalle['unidad'] == 'M3' ? 'selected' : '' }}>M3</option>
                                        <option value="KG" {{ $detalle['unidad'] == 'KG' ? 'selected' : '' }}>KG</option>
                                        <option value="PZ" {{ $detalle['unidad'] == 'PZ' ? 'selected' : '' }}>PZ</option>
                                        <option value="LOTE" {{ $detalle['unidad'] == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_generales[]" value="{{ $detalle['cantidad'] }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_generales[]" value="{{ $detalle['precio_unitario'] }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_generales[]" value="{{ $detalle['subtotal'] }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
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
            <form action="{{ route('materiales.storeAgregados', ['obraId' => $obraId]) }}" method="POST">
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
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="detalle-agregados-body">
                        @foreach ($agregados as $detalle)
                            <tr>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_agregados[]" value="{{ $detalle['fecha'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_agregados[]" value="{{ $detalle['concepto'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_agregados[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                        <option value="M3" {{ $detalle['unidad'] == 'M3' ? 'selected' : '' }}>M3</option>
                                        <option value="KG" {{ $detalle['unidad'] == 'KG' ? 'selected' : '' }}>KG</option>
                                        <option value="PZ" {{ $detalle['unidad'] == 'PZ' ? 'selected' : '' }}>PZ</option>
                                        <option value="LOTE" {{ $detalle['unidad'] == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_agregados[]" value="{{ $detalle['cantidad'] }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_agregados[]" value="{{ $detalle['precio_unitario'] }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_agregados[]" value="{{ $detalle['subtotal'] }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
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
            <form action="{{ route('materiales.storeAceros', ['obraId' => $obraId]) }}" method="POST">
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
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="detalle-aceros-body">
                        @foreach ($aceros as $detalle)
                            <tr>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_aceros[]" value="{{ $detalle['fecha'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_aceros[]" value="{{ $detalle['concepto'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_aceros[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                        <option value="KG" {{ $detalle['unidad'] == 'KG' ? 'selected' : '' }}>KG</option>
                                        <option value="M3" {{ $detalle['unidad'] == 'M3' ? 'selected' : '' }}>M3</option>
                                        <option value="PZ" {{ $detalle['unidad'] == 'PZ' ? 'selected' : '' }}>PZ</option>
                                        <option value="LOTE" {{ $detalle['unidad'] == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_aceros[]" value="{{ $detalle['cantidad'] }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_aceros[]" value="{{ $detalle['precio_unitario'] }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_aceros[]" value="{{ $detalle['subtotal'] }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
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
            <form action="{{ route('materiales.storeCemento', ['obraId' => $obraId]) }}" method="POST">
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
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="detalle-cemento-body">
                        @foreach ($cemento as $detalle)
                            <tr>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_cemento[]" value="{{ $detalle['fecha'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_cemento[]" value="{{ $detalle['concepto'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_cemento[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                        <option value="BOLSA" {{ $detalle['unidad'] == 'BOLSA' ? 'selected' : '' }}>BOLSA</option>
                                        <option value="KG" {{ $detalle['unidad'] == 'KG' ? 'selected' : '' }}>KG</option>
                                        <option value="PZ" {{ $detalle['unidad'] == 'PZ' ? 'selected' : '' }}>PZ</option>
                                        <option value="LOTE" {{ $detalle['unidad'] == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_cemento[]" value="{{ $detalle['cantidad'] }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_cemento[]" value="{{ $detalle['precio_unitario'] }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_cemento[]" value="{{ $detalle['subtotal'] }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
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
            <form action="{{ route('materiales.storeLosas', ['obraId' => $obraId]) }}" method="POST">
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
                            <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="detalle-losas-body">
                        @foreach ($losas as $detalle)
                            <tr>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_losas[]" value="{{ $detalle['fecha'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_losas[]" value="{{ $detalle['concepto'] }}" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                                    <select name="unidad_losas[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                                        <option value="M2" {{ $detalle['unidad'] == 'M2' ? 'selected' : '' }}>M2</option>
                                        <option value="KG" {{ $detalle['unidad'] == 'KG' ? 'selected' : '' }}>KG</option>
                                        <option value="PZ" {{ $detalle['unidad'] == 'PZ' ? 'selected' : '' }}>PZ</option>
                                        <option value="LOTE" {{ $detalle['unidad'] == 'LOTE' ? 'selected' : '' }}>LOTE</option>
                                    </select>
                                </td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_losas[]" value="{{ $detalle['cantidad'] }}" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_losas[]" value="{{ $detalle['precio_unitario'] }}" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_losas[]" value="{{ $detalle['subtotal'] }}" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
                                <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" style="margin-top: 10px;" onclick="addRow('losas')">Añadir Fila</button>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar Losas</button>
            </form>
        </div>
    </div>

    <!-- Botón para regresar -->
    <div class="actions" style="margin-top: 20px; text-align: center;">
        <a href="{{ route('materiales.index', ['obraId' => $obraId]) }}" class="btn btn-primary" style="display: inline-block; background-color: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; transition: background-color 0.3s ease;">Regresar</a>
    </div>
</div>

<script>
    function addRow(type) {
        const tableBody = document.getElementById(`detalle-${type}-body`);
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="date" name="fecha_${type}[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="concepto_${type}[]" class="form-control" style="border: none; background: transparent; text-align: center;"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;">
                <select name="unidad_${type}[]" class="form-control" style="border: none; background: transparent; text-align: center;">
                    <option value="M3">M3</option>
                    <option value="KG">KG</option>
                    <option value="PZ">PZ</option>
                    <option value="LOTE">LOTE</option>
                </select>
            </td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="cantidad_${type}[]" class="form-control cantidad" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="number" name="precio_unitario_${type}[]" class="form-control precio-unitario" style="border: none; background: transparent; text-align: center;" oninput="updateSubtotal(this)"></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><input type="text" name="subtotal_${type}[]" class="form-control subtotal" style="border: none; background: transparent; text-align: center;" readonly></td>
            <td style="border: 1px solid #ddd; text-align: center; padding: 10px;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button></td>
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
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precioUnitario = parseFloat(row.querySelector('.precio-unitario').value) || 0;
        const subtotal = cantidad * precioUnitario;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('costo-total').innerText = `$${total.toFixed(2)}`;
    }

    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const toggleButton = section.previousElementSibling.querySelector('.toggle-button');
        if (section.style.display === 'none') {
            section.style.display = 'block';
            toggleButton.innerText = '-';
        } else {
            section.style.display = 'none';
            toggleButton.innerText = '+';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.hidden-section').forEach(section => {
            section.style.display = 'none';
        });
    });
</script>
@endsection
