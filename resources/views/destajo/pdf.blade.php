<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Destajo</title>
    <style>
        @page { size: landscape; margin: 20px; }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        
        .container { width: 90%; margin: auto; }
        
        /* Encabezado */
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { max-width: 100px; max-height: 100px; }
        .company-name { font-size: 20px; font-weight: bold; text-align: center; flex-grow: 1; }
        
        /* Pie de página */
        .footer { display: flex; justify-content: space-between; align-items: center; position: fixed; bottom: 0; width: 90%; margin: auto; padding-top: 10px; border-top: 2px solid #000; }
        .footer div { display: flex; align-items: center; }
        .footer img { width: 15px; height: 15px; margin-right: 5px; }
        
        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        th { background-color: #ddd; }
        
        /* Firmas */
        .firma-container { display: flex; justify-content: space-between; margin-top: 30px; width: 100%; }
        .firma { text-align: center; width: 45%; display: inline-block; vertical-align: top; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(Auth::user()->logo)
                <img src="{{ public_path('storage/' . Auth::user()->logo) }}" alt="Logo">
            @endif
            <div class="company-name">
                @if(Auth::user()->company_name)
                    {{ Auth::user()->company_name }}
                @endif
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <h1>Obra: {{ $obra->nombre ?? 'N/A' }}</h1>
            <h2>Destajos de la Semana {{ $nombre_nomina ?? 'N/A' }}</h2>
            <h3>Del: {{ $dia_inicio }} : {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} Al {{ $dia_fin }} : {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</h3>
        </div>

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
                                @php $totalPagos += $pago['numero']; @endphp
                            @endforeach
                        </td>
                        <td>${{ number_format($destajoDetalle->pendiente, 2) }}</td>
                        <td>{{ $destajoDetalle->estado }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="firma-container">
            <div class="firma">
                <p>___________________________</p>
                <p>Autorizo</p>
            </div>
            <div class="firma">
                <p>___________________________</p>
                <p>Recibi Pago</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>
            <img src="{{ public_path('icons/phone.png') }}" alt="Teléfono">
            <span>@if(Auth::user()->numero) {{ Auth::user()->numero }} @endif</span>
        </div>
        <div>
            <img src="{{ public_path('icons/email.png') }}" alt="Correo">
            <span>@if(Auth::user()->correo) {{ Auth::user()->correo }} @endif</span>
        </div>
        <div>
            <img src="{{ public_path('icons/location.png') }}" alt="Dirección">
            <span>@if(Auth::user()->direccion) {{ Auth::user()->direccion }} @endif</span>
        </div>
    </div>
</body>
</html>
