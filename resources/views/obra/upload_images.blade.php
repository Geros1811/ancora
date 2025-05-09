@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Subir Imágenes de los Pagos</h1>

    @foreach($pagos as $pago)
    <div class="image-item">
        <h3>{{ $pago->concepto }} ({{ $pago->fecha_pago }})</h3>
        @php
            $imagen = DB::table('calendario_pagos_imagenes')->where('calendario_pago_id', $pago->id)->first();
        @endphp
        @if($imagen)
            <!-- Mostrar imagen directamente desde la raíz -->
            <img src="/pagos_cliente/{{ basename($imagen->ruta) }}" alt="Ticket" style="max-width: 600px; max-height: 600px;">
        @else
            <form action="{{ route('obra_imagenes.store', ['obraId' => $obraId]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="concepto_id" value="{{ $pago->id }}">
                <div class="form-group">
                    <label for="foto">Seleccionar Imagen:</label>
                    <input type="file" class="form-control" id="foto" name="foto" required>
                </div>
                <button type="submit" class="btn btn-primary">Subir Imagen</button>
            </form>
        @endif
    </div>
    @endforeach
</div>

<style>
.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #007bff;
}

.form-group {
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    color: #495057;
}

.form-control {
    border-radius: 5px;
    border: 1px solid #ced4da;
    padding: 10px;
    width: 100%;
    box-sizing: border-box;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.image-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 20px;
}

.image-item {
    text-align: center;
    margin: 10px;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    background-color: white;
}

.image-item h3 {
    margin-bottom: 5px;
    color: #495057;
}
</style>
@endsection