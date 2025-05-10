@extends('layouts.app')

@section('content')
<div class="panel">
    <h1 class="title">Subir Imágenes de los Pagos</h1>
    
    <div id="calendario-pagos">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Fecha de Pago</th>
                    <th>Pago</th>
                    <th>Ver / Subir Foto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                <tr>
                    <td>{{ $pago->concepto }}</td>
                    <td>{{ $pago->fecha_pago }}</td>
                    <td>${{ number_format($pago->pago, 2) }}</td>
                    <td>
                        @php
                            $imagen = DB::table('calendario_pagos_imagenes')->where('calendario_pago_id', $pago->id)->first();
                        @endphp
                        @if($imagen)
                            <a class="btn-ver" href="/pagos_cliente/{{ basename($imagen->ruta) }}" target="_blank">📎 Ver Foto</a>
                        @else
                            <form action="{{ route('obra_imagenes.store', ['obraId' => $obraId]) }}" method="POST" enctype="multipart/form-data" class="form-upload">
                                @csrf
                                <input type="hidden" name="concepto_id" value="{{ $pago->id }}">
                                <input type="file" name="foto" required>
                                <button type="submit">📤 Subir</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.panel {
    max-width: 1000px;
    margin: 40px auto;
    background: #ffffff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.title {
    text-align: center;
    margin-bottom: 30px;
    font-size: 2rem;
    color: #1f2937;
    font-weight: 600;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.tabla {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.tabla th, .tabla td {
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
}

.tabla tr:nth-child(even) {
    background-color: #f9fafb;
}

.tabla th {
    background-color: #007bff;
    color: white;
    font-weight: 600;
}

.btn-ver {
    display: inline-block;
    background-color: #28a745;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-ver:hover {
    background-color: #218838;
}

.form-upload {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.form-upload input[type="file"] {
    border: 1px solid #ced4da;
    padding: 6px;
    border-radius: 6px;
    font-size: 0.85rem;
}

.form-upload button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background 0.3s;
}

.form-upload button:hover {
    background-color: #0056b3;
}
</style>
@endsection
