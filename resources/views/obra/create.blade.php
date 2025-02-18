@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
    <div class="dashboard-container">
        <h1>Crear Nueva Obra</h1>

        <form action="{{ route('obra.store') }}" method="POST" id="obraForm">
            @csrf

            <!-- Nombre del Proyecto -->
            <label for="nombre">Nombre del Proyecto:</label>
            <input type="text" name="nombre" id="nombre" required><br>

            <!-- Presupuesto -->
            <label for="presupuesto">Presupuesto:</label>
            <input type="text" name="presupuesto" id="presupuesto" required><br>

            <!-- Metros Cuadrados -->
            <label for="metros_cuadrados">Metros Cuadrados (mt2):</label>
            <input type="number" name="metros_cuadrados" id="metros_cuadrados" step="0.01"><br>

            <!-- Cliente -->
            <label for="cliente">Cliente:</label>
            <input type="text" name="cliente" id="cliente" required><br>

            <!-- Fecha de Inicio -->
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" required><br>

            <!-- Fecha de Término -->
            <label for="fecha_termino">Fecha de Término:</label>
            <input type="date" name="fecha_termino" id="fecha_termino" required><br>

            <!-- Residente de Obra -->
            <label for="residente">Residente de Obra:</label>
            <input type="text" name="residente" id="residente" required><br>

            <!-- Ubicación -->
            <label for="ubicacion">Ubicación:</label>
            <input type="text" name="ubicacion" id="ubicacion" required><br>

            <!-- Descripción del Proyecto -->
            <label for="descripcion">Descripción del Proyecto:</label>
            <textarea name="descripcion" id="descripcion"></textarea><br>

            <!-- Botón de envío -->
            <button type="submit">Crear Obra</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const presupuestoInput = document.getElementById("presupuesto");
            const obraForm = document.getElementById("obraForm");

            presupuestoInput.addEventListener("input", function () {
                let value = this.value.replace(/,/g, ""); // Eliminar comas previas
                if (!isNaN(value) && value !== "") {
                    this.value = new Intl.NumberFormat("es-MX").format(value);
                }
            });

            // Antes de enviar, eliminamos las comas para evitar errores en el backend
            obraForm.addEventListener("submit", function () {
                presupuestoInput.value = presupuestoInput.value.replace(/,/g, "");
            });
        });
    </script>
@endsection
