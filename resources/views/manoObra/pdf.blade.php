<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nómina y Destajos</title>
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
        .firma { text-align: center; width: 30%; display: inline-block; vertical-align: top; }
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
            <h2>Nómina de la Semana {{ $nomina->nombre ?? 'N/A' }}</h2>
            <h3>Del: {{ $nomina->dia_inicio }} : {{ \Carbon\Carbon::parse($nomina->fecha_inicio)->format('d/m/Y') }} Al {{ $nomina->dia_fin }} : {{ \Carbon\Carbon::parse($nomina->fecha_fin)->format('d/m/Y') }}</h3>
        </div>

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

        <h3>Destajos</h3>
        <table>
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
                        <td>${{ number_format($destajo->cantidad, 2) }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="firma-container">
            <div class="firma">
                <p>___________________________</p>
                <p>Autorizó</p>
            </div>
            <div class="firma">
                <p>___________________________</p>
                <p>Recibí Pago</p>
            </div>
            <div class="firma">
                <p>___________________________</p>
                <p>Aprobó</p>
            </div>
        </div>
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
