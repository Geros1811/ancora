@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="summary-table" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #007bff;">
        <thead>
            <tr style="background-color: #007bff; color: white; text-align: left;">
                <th style="padding: 10px; border: 1px solid #ddd;">Semana No</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Del</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Al</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Días Trabajados</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Monto de Nómina</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Observaciones</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nominas as $nomina)
                <tr class="fila-nomina {{ $nomina->bloqueado ? 'bloqueada' : '' }}" style="border-bottom: 1px solid #ddd;" data-id="{{ $nomina->id }}">
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $loop->iteration }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <span class="fecha-inicio">{{ \Carbon\Carbon::parse($nomina->fecha_inicio)->locale('es')->format('d M Y') }}</span>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <span class="fecha-fin">{{ \Carbon\Carbon::parse($nomina->fecha_fin)->locale('es')->format('d M Y') }}</span>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <input type="number" name="dias_trabajados" value="{{ $nomina->dias_trabajados }}" class="dias-trabajados" data-id="{{ $nomina->id }}" style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 3px;" {{ $nomina->editado ? 'disabled' : '' }}>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <span class="monto-nomina">${{ number_format($nomina->total, 2) }}</span>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <input type="text" name="observaciones" value="{{ $nomina->observaciones }}" class="observaciones" data-id="{{ $nomina->id }}" style="width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 3px;" {{ $nomina->editado ? 'disabled' : '' }}>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        @if(!$nomina->editado)
                            <button class="guardar-btn" data-id="{{ $nomina->id }}" style="background-color: #27ae60; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;">
                                Guardar
                            </button>
                        @else
                            <span style="color: gray;">Guardado</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    document.querySelectorAll('.guardar-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = button.getAttribute('data-id');
            const fila = document.querySelector(`.fila-nomina[data-id="${id}"]`);
            const dias_trabajados = fila.querySelector(`.dias-trabajados`).value;
            const observaciones = fila.querySelector(`.observaciones`).value;

            fetch(`/mano-de-obra/${id}/actualizar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    dias_trabajados: dias_trabajados,
                    observaciones: observaciones
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Nómina actualizada correctamente');

                    // Bloquear toda la fila
                    fila.querySelector(`.dias-trabajados`).disabled = true;
                    fila.querySelector(`.observaciones`).disabled = true;

                    // Cambiar el botón a "Guardado"
                    fila.querySelector('.guardar-btn').outerHTML = '<span style="color: gray;">Guardado</span>';
                } else {
                    console.error('Error al actualizar nómina');
                }
            })
            .catch(error => console.error('Error al hacer la solicitud', error));
        });
    });
</script>

@endsection

<style>
    .bloqueada {
        background-color: #d4edda; /* Verde claro */
    }
</style>