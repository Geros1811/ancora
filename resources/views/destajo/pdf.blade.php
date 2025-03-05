<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Destajo</title>
    <style>
        @page { size: landscape; margin: 20px; }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        th { background-color: #ddd; }
        .firma-container { display: flex; justify-content: space-between; margin-top: 30px; width: 100%; }
        .firma { text-align: center; width: 45%; display: inline-block; vertical-align: top; }
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
        <h2>Destajos de la Semana {{ $nombre_nomina ?? 'N/A' }}</h2>
        <h3>Del: {{ $dia_inicio }} : {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} Al {{ $dia_fin }} : {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</h3>
    </div>

    <!-- Tabla de Destajos -->
    <h3>Destajos</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cotización</th>
                <th>Monto Aprobado</th>
                <th>Pagos</th>
                <th>Pendiente</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($destajoDetalles as $index => $destajoDetalle)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $destajoDetalle->cotizacion }}</td>
                    <td>${{ number_format($destajoDetalle->monto_aprobado, 2) }}</td>
                    <td>
                        @php
                            $pagos = $destajoDetalle->pagos ? json_decode($destajoDetalle->pagos, true) : [];
                            $totalPagos = 0;
                        @endphp
                        @foreach($pagos as $index => $pago)
                            Pago {{ $index }}: {{ \Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') }}, ${{ number_format($pago['numero'], 2) }}<br>
                            @php
                                $totalPagos += $pago['numero'];
                            @endphp
                        @endforeach
                    </td>
                    <td>${{ number_format($destajoDetalle->pendiente, 2) }}</td>
                    <td>{{ $destajoDetalle->estado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Firmas -->
    <div class="firma-container">
        <div class="firma">
            <p>___________________________</p>
            <p>Autorizo</p>
        </div>
        <div class="firma">
            <p>___________________________</p>
            <p>Recibi Pago </p>
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
