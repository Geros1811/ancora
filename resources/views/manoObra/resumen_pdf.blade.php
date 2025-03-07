<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Mano de Obra</title>
    <style>
        @page { size: landscape; margin: 20px; }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        
        .container { width: 90%; margin: auto; }
        
        /* Encabezado */
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { max-width: 150px; max-height: 150px; }
        .company-name { font-size: 20px; font-weight: bold; text-align: center; flex-grow: 1; }
        
        /* Pie de página */
        .footer { display: flex; justify-content: space-between; align-items: center; position: fixed; bottom: 0; width: 90%; margin: auto; padding-top: 10px; border-top: 2px solid #000; }
        .footer div { display: flex; align-items: center; }
        .footer img { width: 15px; height: 15px; margin-right: 5px; }
        
        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        th { background-color: #ddd; }
        
        
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
            <h2>Reporte de Resumen Mano de Obra</h2>
            <div style="font-size: 1.5em; display: block; text-align: right;">
                <strong>Total Nómina:</strong> ${{ number_format($totalNomina, 2) }}<br>
                <strong>Total Destajos:</strong> ${{ number_format($totalDestajos, 2) }}
            </div>
        </div>

        <h3>Resumen de Nóminas</h3>
        <table>
            <thead>
                <tr>
                    
                    <th>Semana No</th>
                    <th>Del</th>
                    <th>Al</th>
                    <th>Días Trabajados</th>
                    <th>Monto de Nómina</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nominas as $index => $nomina)
                    <tr>
                        
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($nomina->fecha_inicio)->locale('es')->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($nomina->fecha_fin)->locale('es')->format('d M Y') }}</td>
                        <td>{{ $nomina->dias_trabajados }}</td>
                        <td>${{ number_format($nomina->total, 2) }}</td>
                        <td>{{ $nomina->observaciones }}</td>
                    </tr>
                    @if($nomina->destajos->isNotEmpty())
                        @foreach($nomina->destajos as $destajo)
                            <tr>
                                
                                <td>Destajo</td>
                                <td>{{ \Carbon\Carbon::parse($nomina->fecha_inicio)->locale('es')->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($nomina->fecha_fin)->locale('es')->format('d M Y') }}</td>
                                <td></td>
                                <td>${{ number_format($destajo->cantidad, 2) }}</td>
                                <td>{{ $destajo->frente }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div>
            <img src="https://img.icons8.com/ios-filled/15/000000/phone.png" alt="Teléfono">
            <span>@if(Auth::user()->numero) {{ Auth::user()->numero }} @endif</span>
        </div>
        <div>
            <img src="https://img.icons8.com/ios-filled/15/000000/email.png" alt="Correo">
            <span>@if(Auth::user()->correo) {{ Auth::user()->correo }} @endif</span>
        </div>
        <div>
            <img src="https://img.icons8.com/ios-filled/15/000000/marker.png" alt="Dirección">
            <span>@if(Auth::user()->direccion) {{ Auth::user()->direccion }} @endif</span>
        </div>
    </div>
</body>
</html>
