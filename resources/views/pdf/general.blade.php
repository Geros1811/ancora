<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $obra->nombre }} - {{ $title }}</title>
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

            <div class="company-name">

            </div>
        </div>


        <div style="text-align: center; margin-bottom: 20px;">
            <h1>Obra: {{ $obra->nombre ?? 'N/A' }}</h1>
            <div style="font-size: 1.5em; display: block; text-align: right;">Costo Mensual Total: ${{ number_format($costoMensualTotal, 2) }}</div>
            <div style="font-size: 1.5em; display: block; text-align: right;">Ingreso Mensual: ${{ number_format($ingresoMensual, 2) }}</div>
            <h2>Reporte General</h2>
        </div>

      
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Unidad</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalCosto = 0;
                @endphp
                @foreach($acarreos as $index => $acarreo)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $acarreo->fecha }}</td>
                        <td>{{ $acarreo->concepto }}</td>
                        <td>{{ $acarreo->unidad }}</td>
                        <td>{{ $acarreo->cantidad }}</td>
                        <td>${{ number_format($acarreo->precio_unitario, 2) }}</td>
                        <td>${{ number_format($acarreo->subtotal, 2) }}</td>
                        <td>Acarreos</td>
                    </tr>
                    @php
                        $totalCosto += $acarreo->subtotal;
                    @endphp
                @endforeach
                @foreach($cimbras as $index => $cimbra)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $cimbra->fecha }}</td>
                        <td>{{ $cimbra->concepto }}</td>
                        <td>{{ $cimbra->unidad }}</td>
                        <td>{{ $cimbra->cantidad }}</td>
                        <td>${{ number_format($cimbra->precio_unitario, 2) }}</td>
                        <td>${{ number_format($cimbra->subtotal, 2) }}</td>
                        <td>Cimbras</td>
                    </tr>
                    @php
                        $totalCosto += $cimbra->subtotal;
                    @endphp
                @endforeach
                @foreach($maquinariaMayor as $index => $maquinaria)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $maquinaria->fecha }}</td>
                        <td>{{ $maquinaria->concepto }}</td>
                        <td>{{ $maquinaria->unidad }}</td>
                        <td>{{ $maquinaria->cantidad }}</td>
                        <td>${{ number_format($maquinaria->precio_unitario, 2) }}</td>
                        <td>${{ number_format($maquinaria->subtotal, 2) }}</td>
                        <td>Maquinaria Mayor</td>
                    </tr>
                    @php
                        $totalCosto += $maquinaria->subtotal;
                    @endphp
                @endforeach
                @foreach($utilidades as $index => $utilidad)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $utilidad->fecha }}</td>
                        <td>{{ $utilidad->concepto }}</td>
                        <td>{{ $utilidad->unidad }}</td>
                        <td>{{ $utilidad->cantidad }}</td>
                        <td>${{ number_format($utilidad->precio_unitario, 2) }}</td>
                        <td>${{ number_format($utilidad->subtotal, 2) }}</td>
                        <td>Utilidades</td>
                    </tr>
                    @php
                        $totalCosto += $utilidad->subtotal;
                    @endphp
                @endforeach
                @foreach($tramites as $index => $tramite)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $tramite->fecha }}</td>
                        <td>{{ $tramite->concepto }}</td>
                        <td>{{ $tramite->unidad }}</td>
                        <td>{{ $tramite->cantidad }}</td>
                        <td>${{ number_format($tramite->precio_unitario, 2) }}</td>
                        <td>${{ number_format($tramite->subtotal, 2) }}</td>
                        <td>Tramites</td>
                    </tr>
                    @php
                        $totalCosto += $tramite->subtotal;
                    @endphp
                @endforeach
                @foreach($rentas as $index => $renta)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $renta->fecha }}</td>
                        <td>{{ $renta->concepto }}</td>
                        <td>{{ $renta->unidad }}</td>
                        <td>{{ $renta->cantidad }}</td>
                        <td>${{ number_format($renta->precio_unitario, 2) }}</td>
                        <td>${{ number_format($renta->subtotal, 2) }}</td>
                        <td>Rentas</td>
                    </tr>
                    @php
                        $totalCosto += $renta->subtotal;
                    @endphp
                @endforeach
                @foreach($rentaMaquinaria as $index => $renta)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $renta->fecha }}</td>
                        <td>{{ $renta->concepto }}</td>
                        <td>{{ $renta->unidad }}</td>
                        <td>{{ $renta->cantidad }}</td>
                        <td>${{ number_format($renta->precio_unitario, 2) }}</td>
                        <td>${{ number_format($renta->subtotal, 2) }}</td>
                        <td>Renta Maquinaria</td>
                    </tr>
                    @php
                        $totalCosto += $renta->subtotal;
                    @endphp
                @endforeach
                @foreach($agregados as $index => $agregado)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $agregado->fecha }}</td>
                        <td>{{ $agregado->concepto }}</td>
                        <td>{{ $agregado->unidad }}</td>
                        <td>{{ $agregado->cantidad }}</td>
                        <td>${{ number_format($agregado->precio_unitario, 2) }}</td>
                        <td>${{ number_format($agregado->subtotal, 2) }}</td>
                        <td>Agregados</td>
                    </tr>
                    @php
                        $totalCosto += $agregado->subtotal;
                    @endphp
                @endforeach
                @foreach($aceros as $index => $acero)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $acero->fecha }}</td>
                        <td>{{ $acero->concepto }}</td>
                        <td>{{ $acero->unidad }}</td>
                        <td>{{ $acero->cantidad }}</td>
                        <td>${{ number_format($acero->precio_unitario, 2) }}</td>
                        <td>${{ number_format($acero->subtotal, 2) }}</td>
                        <td>Aceros</td>
                    </tr>
                    @php
                        $totalCosto += $acero->subtotal;
                    @endphp
                @endforeach
                @foreach($cemento as $index => $c)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $c->fecha }}</td>
                        <td>{{ $c->concepto }}</td>
                        <td>{{ $c->unidad }}</td>
                        <td>{{ $c->cantidad }}</td>
                        <td>${{ number_format($c->precio_unitario, 2) }}</td>
                        <td>${{ number_format($c->subtotal, 2) }}</td>
                        <td>Cemento</td>
                    </tr>
                    @php
                        $totalCosto += $c->subtotal;
                    @endphp
                @endforeach
                @foreach($losas as $index => $losa)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $losa->fecha }}</td>
                        <td>{{ $losa->concepto }}</td>
                        <td>{{ $losa->unidad }}</td>
                        <td>{{ $losa->cantidad }}</td>
                        <td>${{ number_format($losa->precio_unitario, 2) }}</td>
                        <td>${{ number_format($losa->subtotal, 2) }}</td>
                        <td>Losas</td>
                    </tr>
                    @php
                        $totalCosto += $losa->subtotal;
                    @endphp
                @endforeach
                @foreach($generales as $index => $general)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $general->fecha }}</td>
                        <td>{{ $general->concepto }}</td>
                        <td>{{ $general->unidad }}</td>
                        <td>{{ $general->cantidad }}</td>
                        <td>${{ number_format($general->precio_unitario, 2) }}</td>
                        <td>${{ number_format($general->subtotal, 2) }}</td>
                        <td>Generales</td>
                    </tr>
                    @php
                        $totalCosto += $general->subtotal;
                    @endphp
                @endforeach
                @foreach($maquinariaMenor as $index => $maquinaria)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $maquinaria->fecha }}</td>
                        <td>{{ $maquinaria->concepto }}</td>
                        <td>{{ $maquinaria->unidad }}</td>
                        <td>{{ $maquinaria->cantidad }}</td>
                        <td>${{ number_format($maquinaria->precio_unitario, 2) }}</td>
                        <td>${{ number_format($maquinaria->subtotal, 2) }}</td>
                        <td>Maquinaria Menor</td>
                    </tr>
                    @php
                        $totalCosto += $maquinaria->subtotal;
                    @endphp
                @endforeach
                @foreach($comidas as $index => $comida)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $comida->fecha }}</td>
                        <td>{{ $comida->concepto }}</td>
                        <td>{{ $comida->unidad }}</td>
                        <td>{{ $comida->cantidad }}</td>
                        <td>${{ number_format($comida->precio_unitario, 2) }}</td>
                        <td>${{ number_format($comida->subtotal, 2) }}</td>
                        <td>Comidas</td>
                    </tr>
                    @php
                        $totalCosto += $comida->subtotal;
                    @endphp
                @endforeach
                @foreach($equipoSeguridad as $index => $equipo)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $equipo->fecha }}</td>
                        <td>{{ $equipo->concepto }}</td>
                        <td>{{ $equipo->unidad }}</td>
                        <td>{{ $equipo->cantidad }}</td>
                        <td>${{ number_format($equipo->precio_unitario, 2) }}</td>
                        <td>${{ number_format($equipo->subtotal, 2) }}</td>
                        <td>Equipo Seguridad</td>
                    </tr>
                    @php
                        $totalCosto += $equipo->subtotal;
                    @endphp
                @endforeach
                @foreach($gasolina as $index => $gas)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $gas->fecha }}</td>
                        <td>{{ $gas->concepto }}</td>
                        <td>{{ $gas->unidad }}</td>
                        <td>{{ $gas->cantidad }}</td>
                        <td>${{ number_format($gas->precio_unitario, 2) }}</td>
                        <td>${{ number_format($gas->subtotal, 2) }}</td>
                        <td>Gasolina</td>
                    </tr>
                    @php
                        $totalCosto += $gas->subtotal;
                    @endphp
                @endforeach
                @foreach($herramientaMenor as $index => $herramienta)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $herramienta->fecha }}</td>
                        <td>{{ $herramienta->concepto }}</td>
                        <td>{{ $herramienta->unidad }}</td>
                        <td>{{ $herramienta->cantidad }}</td>
                        <td>${{ number_format($herramienta->precio_unitario, 2) }}</td>
                        <td>${{ number_format($herramienta->subtotal, 2) }}</td>
                        <td>Herramienta Menor</td>
                    </tr>
                    @php
                        $totalCosto += $herramienta->subtotal;
                    @endphp
                @endforeach
                @foreach($ingresos as $index => $ingreso)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $ingreso->fecha }}</td>
                        <td>{{ $ingreso->concepto }}</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>${{ number_format($ingreso->importe, 2) }}</td>
                        <td>Ingresos</td>
                    </tr>
                    @php
                        $totalCosto += $ingreso->importe;
                    @endphp
                @endforeach
                @foreach($limpieza as $index => $lim)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $lim->fecha }}</td>
                        <td>{{ $lim->concepto }}</td>
                        <td>{{ $lim->unidad }}</td>
                        <td>{{ $lim->cantidad }}</td>
                        <td>${{ number_format($lim->precio_unitario, 2) }}</td>
                        <td>${{ number_format($lim->subtotal, 2) }}</td>
                        <td>Limpieza</td>
                    </tr>
                    @php
                        $totalCosto += $lim->subtotal;
                    @endphp
                @endforeach
                @foreach($sueldoResidente as $index => $sueldo)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $sueldo->fecha }}</td>
                        <td>{{ $sueldo->concepto }}</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>${{ number_format($sueldo->importe, 2) }}</td>
                        <td>Sueldo Residente</td>
                    </tr>
                    @php
                        $totalCosto += $sueldo->importe;
                    @endphp
                @endforeach
                @foreach($imss as $index => $i)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $i->fecha }}</td>
                        <td>{{ $i->concepto }}</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>${{ number_format($i->importe, 2) }}</td>
                        <td>IMSS</td>
                    </tr>
                    @php
                        $totalCosto += $i->importe;
                    @endphp
                @endforeach
                @foreach($contador as $index => $c)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $c->fecha }}</td>
                        <td>{{ $c->concepto }}</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>${{ number_format($c->importe, 2) }}</td>
                        <td>Contador</td>
                    </tr>
                    @php
                        $totalCosto += $c->importe;
                    @endphp
                @endforeach
                @foreach($iva as $index => $i)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $i->fecha }}</td>
                        <td>{{ $i->concepto }}</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>${{ number_format($i->importe, 2) }}</td>
                        <td>IVA</td>
                    </tr>
                    @php
                        $totalCosto += $i->importe;
                    @endphp
                @endforeach
                @foreach($otrosPagos as $otro)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $otro->fecha }}</td>
                        <td>{{ $otro->concepto }}</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>${{ number_format($otro->importe, 2) }}</td>
                        <td>Otros Pagos</td>
                    </tr>
                    @php
                        $totalCosto += $otro->importe;
                    @endphp
                @endforeach
                @foreach($papeleria as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->fecha }}</td>
                        <td>{{ $p->concepto }}</td>
                        <td>{{ $p->unidad }}</td>
                        <td>{{ $p->cantidad }}</td>
                        <td>${{ number_format($p->precio_unitario, 2) }}</td>
                        <td>${{ number_format($p->subtotal, 2) }}</td>
                        <td>Papeleria</td>
                    </tr>
                    @php
                        $totalCosto += $p->subtotal;
                    @endphp
                @endforeach
        </tbody>
    </table>

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
</body>
</html>
