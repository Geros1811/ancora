<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nómina y Destajos</title>
    <style>
        @page { size: landscape; margin: 20px; }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 16px; text-align: center; }
        th { background-color: #ddd; }
        .firma-container { display: flex; justify-content: space-between; margin-top: 30px; width: 100%; }
        .firma { text-align: center; width: 30%; display: inline-block; vertical-align: top; }
        .header, .footer { width: 100%; text-align: center; position: fixed; }
        .header { top: 0; }
        .footer { bottom: 0; }
        .header img { max-width: 100px; max-height: 100px; }
        .footer p { margin: 0; }
    </style>
</head>
<body>
    <div class="header">
        @if(Auth::user()->logo)
        <img src="{{ public_path('storage/' . Auth::user()->logo) }}" alt="Logo" class="company-logo">

    @endif
        @if(Auth::user()->company_name)
            <h2>{{ Auth::user()->company_name }}</h2>
        @endif
    </div>

    <div style="margin-top: 120px; text-align: center;">
        <h1>Obra: {{ $obra->nombre ?? 'N/A' }}</h1>
        <h2>Nómina de la Semana {{ $nomina->nombre ?? 'N/A' }}</h2>
        <h3>Del:  {{ $nomina->dia_inicio }}  {{ \Carbon\Carbon::parse($nomina->fecha_inicio)->format('d/m/Y') }} Al {{ $nomina->dia_fin }}  {{ \Carbon\Carbon::parse($nomina->fecha_fin)->format('d/m/Y') }}</h3>
    </div>

    <!-- Tabla de Mano de Obra -->
    <h3>Mano de Obra</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Puesto</th>
                <th>L</th>
                <th>M</th>
                <th>MI</th>
                <th>J</th>
                <th>V</th>
                <th>S</th>
                <th>Total Días</th>
                <th>Precio Diario</th>
                <th>Extras/Menos</th>
                <th>Precio</th>
                <th>Firmas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $index => $detalle)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detalle->nombre }}</td>
                    <td>{{ $detalle->puesto }}</td>
                    <td>{{ $detalle->lunes }}</td>
                    <td>{{ $detalle->martes }}</td>
                    <td>{{ $detalle->miercoles }}</td>
                    <td>{{ $detalle->jueves }}</td>
                    <td>{{ $detalle->viernes }}</td>
                    <td>{{ $detalle->sabado }}</td>
                    <td>{{ min(7, $detalle->lunes + $detalle->martes + $detalle->miercoles + $detalle->jueves + $detalle->viernes + $detalle->sabado) }}</td>
                    <td>${{ number_format($detalle->precio_hora, 2) }}</td>
                    <td>${{ number_format($detalle->extras_menos, 2) }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de Destajos -->
    <h3>Destajos</h3>
    <table style="margin-bottom: 80px;">
        <thead>
            <tr>
                <th>#</th>
                <th>Frente</th>
                <th>Monto Aprobado</th>
                <th>Cantidad</th>
                <th>Firmas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($destajos as $index => $destajo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $destajo->frente }}</td>
                    <td>${{ number_format($destajo->monto_aprobado, 2) }}</td>
                    <td>${{ number_format( $destajo->cantidad, 2)}}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Firmas -->
    <div class="firma-container">
        <div class="firma">
            <p>___________________________</p>
            <h2>Autorizo</h2>
        </div>
        <div class="firma">
            <p>___________________________</p>
            <h2>Reviso</h2>
        </div>
        <div class="firma">
            <p>___________________________</p>
            <h2>Aprobo</h2>
        </div>
    </div>

    <div class="footer">
        @if(Auth::user()->numero)
            <p>Número: {{ Auth::user()->numero }}</p>
        @endif
        @if(Auth::user()->correo)
            <p>Correo: {{ Auth::user()->correo }}</p>
        @endif
        @if(Auth::user()->direccion)
            <p>Dirección: {{ Auth::user()->direccion }}</p>
        @endif
    </div>
</body>
</html>
