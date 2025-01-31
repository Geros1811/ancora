@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Detalles de Destajos</h1>
    </div>

    <form action="{{ route('destajo.store', ['obraId' => $obraId]) }}" method="POST">
        @csrf
        <table class="obra-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Frente</th>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Fecha</th>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">No. Pago</th>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Cantidad</th>
                    <th style="background-color: #2980b9; color: white; font-weight: bold; border: 1px solid #ddd; text-align: center; padding: 10px;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalles as $detalle)
                    <tr>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 5px;">
                            <select name="frente[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;">
                                <option value="" disabled {{ $detalle->frente == '' ? 'selected' : '' }}>Seleccione una opción</option>
                                <option value="Plomeria" {{ $detalle->frente == 'Plomeria' ? 'selected' : '' }}>Plomeria</option>
                                <option value="Electricidad" {{ $detalle->frente == 'Electricidad' ? 'selected' : '' }}>Electricidad</option>
                                <option value="Colocador de Pisos" {{ $detalle->frente == 'Colocador de Pisos' ? 'selected' : '' }}>Colocador de Pisos</option>
                                <option value="Pintor" {{ $detalle->frente == 'Pintor' ? 'selected' : '' }}>Pintor</option>
                                <option value="Herreria" {{ $detalle->frente == 'Herreria' ? 'selected' : '' }}>Herreria</option>
                                <option value="Carpintero" {{ $detalle->frente == 'Carpintero' ? 'selected' : '' }}>Carpintero</option>
                                <option value="Aluminio" {{ $detalle->frente == 'Aluminio' ? 'selected' : '' }}>Aluminio</option>
                                <option value="Aire Acondicionado" {{ $detalle->frente == 'Aire Acondicionado' ? 'selected' : '' }}>Aire Acondicionado</option>
                                <option value="Tabla Roca" {{ $detalle->frente == 'Tabla Roca' ? 'selected' : '' }}>Tabla Roca</option>
                                <option value="Otros" {{ $detalle->frente == 'Otros' ? 'selected' : '' }}>Otros</option>
                            </select>
                            <input type="text" name="frente_otro[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%; display: {{ $detalle->frente == 'Otros' ? 'block' : 'none' }};" placeholder="Especificar" value="{{ $detalle->frente_otro }}">
                        </td>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="date" name="fecha[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="{{ $detalle->fecha }}"></td>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 5px;">
                            <a href="{{ route('manoObra.destajos', ['obraId' => $obraId]) }}" class="btn btn-link">{{ $detalle->no_pago }}</a>
                        </td>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="number" step="0.01" name="cantidad[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="{{ $detalle->cantidad }}"></td>
                        <td style="border: 1px solid #ddd; text-align: center; padding: 5px;"><input type="text" name="observaciones[]" class="form-control" style="border: none; background: transparent; text-align: center; width: 100%;" value="{{ $detalle->observaciones }}"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Guardar</button>
    </form>
</div>
@endsection
