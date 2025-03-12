@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/obra_create.css') }}">
@endsection

@section('content')
    <div class="dashboard-container">
        <h1>Crear Nueva Obra</h1>

        <form action="{{ route('obra.store') }}" method="POST" id="obraForm">
            @csrf

            <!-- Nombre del Proyecto -->
            <label for="nombre">Nombre del Proyecto:</label>
            <input type="text" name="nombre" id="nombre" required><br>

            <!-- Presupuesto y Metros Cuadrados -->
            <div class="row">
                <div class="form-group">
                    <label for="presupuesto">Presupuesto:</label>
                    <input type="number" name="presupuesto" id="presupuesto" required step="0.01" placeholder="Ej: 1000.00">
                </div>

                <div class="form-group">
                    <label for="metros_cuadrados">Metros Cuadrados (mt2):</label>
                    <input type="number" name="metros_cuadrados" id="metros_cuadrados" step="0.01">
                </div>
            </div>

           <!-- Cliente -->
            <div class="form-group">
                <label for="cliente">Cliente:</label>
                <div style="display: flex; align-items: center;">
                    <select class="form-control small-select" name="cliente" id="cliente" required style="width: 70%;">
                        <option>Seleccione</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('register') }}" target="_blank" style="margin-left: 10px;" class="add-link">Agregar Cliente</a>
                </div>
            </div>

            <!-- Fecha de Inicio y Término -->
            <div class="row">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" required>
                </div>

                <div class="form-group">
                    <label for="fecha_termino">Fecha de Término:</label>
                    <input type="date" name="fecha_termino" id="fecha_termino" required>
                </div>
            </div>

           <!-- Maestro de Obra -->
            <div class="form-group">
                <label for="residente">Residente de Obra:</label>
                <div style="display: flex; align-items: center;">
                    <select class="form-control small-select" name="residente" id="residente" required style="width: 70%;">
                        <option>Seleccione</option>
                        @foreach($maestroObras as $maestroObra)
                            <option value="{{ $maestroObra->id }}">{{ $maestroObra->name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('register') }}" target="_blank" style="margin-left: 10px;" class="add-link">Agregar Residente</a>
                </div>
            </div>

            <!-- Ubicación y Descripción del Proyecto -->
            <div class="row">
                <div class="form-group">
                    <label for="ubicacion">Ubicación:</label>
                    <input type="text" name="ubicacion" id="ubicacion" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción del Proyecto:</label>
                    <textarea name="descripcion" id="descripcion"></textarea>
                </div>
            </div>

            <!-- Architects -->
            <div class="form-group">
                <label for="architects">Arquitecto (opcional):</label>
                <div style="display: flex; align-items: center;">
                    <select class="form-control small-select" name="architects" id="architects" style="width: 70%;">
                        <option value="">Seleccione</option>
                        @foreach($architects as $architect)
                            <option value="{{ $architect->id }}">{{ $architect->name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('register') }}" target="_blank" style="margin-left: 10px;" class="add-link">Agregar Arquitecto</a>
                </div>
            </div>

            <!-- Botón de envío -->
            <button type="submit">Crear Obra</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
        });
    </script>
@endsection
