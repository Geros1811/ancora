<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Destajo Sin Nomina</title>
    <style>
        @page { size: landscape; margin: 20px; }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .container { width: 95%; margin: auto; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { max-width: 100px; max-height: 100px; }
        .company-name { font-size: 20px; font-weight: bold; text-align: center; flex-grow: 1; }
        .footer { display: flex; justify-content: space-between; align-items: center; position: fixed; bottom: 0; width: 90%; margin: auto; padding-top: 10px; border-top: 2px solid #000; }
        .footer div { display: flex; align-items: center; }
        .footer img { width: 15px; height: 15px; margin-right: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        th { background-color: #ddd; }
        .firma-container { display: flex; justify-content: space-between; margin-top: 30px; width: 100%; }
        .firma { text-align: center; width: 45%; display: inline-block; vertical-align: top; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(Auth::user()->logo)
                <img src="{{ public_path('img/ancora.png') }}" alt="Logo" width="150"/>
            @endif
            <div class="company-name">
                @if(Auth::user()->company_name)
                    {{ Auth::user()->company_name }}
                @endif
            </div>
        </div>
        <h1 style="text-align: center;">Detalles de Destajo Sin Nomina</h1>
        <h2 style="text-align: center;">Obra: {{ $obra->nombre }}</h2>
        <h3 style="text-align: center;">Partida: {{ $partida->title }}</h3>

        <table>
            <thead>
                <tr>
                    <th>Clave</th>
                    <th>Concepto</th>
                    <th>Unidad</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Pagos</th>
                    <th>Pendiente</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($partida->detalles as $destajoDetalle)
                <tr>
                    <td>{{ $destajoDetalle->clave }}</td>
                    <td>{{ $destajoDetalle->concepto }}</td>
                    <td>{{ $destajoDetalle->unidad }}</td>
                    <td>{{ $destajoDetalle->cantidad }}</td>
                    <td>{{ $destajoDetalle->precio_unitario }}</td>
                    <td>{{ $destajoDetalle->subtotal }}</td>
                    <td>
                        @php
                            $pagos = $destajoDetalle->pagos;
                            $pagos_str = '';
                            $totalPagos = 0;
                            if (is_array($pagos)) {
                                foreach ($pagos as $key => $pago) {
                                    $pagos_str .= "Pago " . $key . ": $" . number_format($pago['monto'], 2) . " - " . $pago['fecha'] . "<br>";
                                    $totalPagos += $pago['monto'];
                                }
                            }
                            echo $pagos_str;
                        @endphp
                    </td>
                    <td>{{ number_format($destajoDetalle->subtotal - $totalPagos, 2) }}</td>
                  
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
