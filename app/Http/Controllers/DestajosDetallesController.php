<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DestajoDetalle;
use Illuminate\Support\Facades\Log;

class DestajosDetallesController extends Controller
{
    public function store(Request $request, $obraId)
    {
        try {
            Log::info('Datos recibidos en store:', $request->all());

            $frente = $request->input('frente'); // Asumiendo que es el mismo para todas las filas
            $cotizaciones = $request->input('cotizacion', []);
            $montosAprobados = $request->input('monto_aprobado', []);
            $pendientes = $request->input('pendiente', []);
            $estados = $request->input('estado', []);

            $pagoFechas = $request->input('pago_fecha', []);
            $pagosMontos = $request->input('pago', []);

            $numRows = count($cotizaciones);

            // Calcular el número de columnas de pagos
            $pagosPorFila = 0;
            if ($numRows > 0) {
                $totalPagos = count($pagoFechas);
                $pagosPorFila = $totalPagos / $numRows;
            }

            $pagoIndex = 0;

            for ($i = 0; $i < $numRows; $i++) {
                $pagos = [];

                // Procesar los pagos para esta fila
                for ($j = 0; $j < $pagosPorFila; $j++) {
                    $fechaPago = $pagoFechas[$pagoIndex] ?? null;
                    $montoPago = $pagosMontos[$pagoIndex] ?? null;
                    $pagoIndex++;

                    if (!empty($fechaPago) || !empty($montoPago)) {
                        $pagos[] = [
                            'fecha' => $fechaPago,
                            'monto' => $montoPago,
                        ];
                    }
                }

                // Crear el detalle de destajo
                $detalleData = [
                    'obra_id' => $obraId,
                    'frente' => $frente,
                    'cotizacion' => $cotizaciones[$i],
                    'monto_aprobado' => $montosAprobados[$i],
                    'pagos' => json_encode($pagos),
                    'pendiente' => $pendientes[$i],
                    'estado' => $estados[$i],
                    'monto_aprobado_total' => 0,
                ];

                Log::info('Datos a insertar:', $detalleData);

                DestajoDetalle::create($detalleData);
            }

            return redirect()->back()->with('success', 'Detalles guardados correctamente.');
        } catch (\Exception $e) {
            Log::error('Error en store de DestajosDetallesController: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al guardar los detalles.');
        }
    }

    public function show($id)
    {
        try {
            $detalle = DestajoDetalle::findOrFail($id);
            return response()->json($detalle);
        } catch (\Exception $e) {
            Log::error('Error en show de DestajosDetallesController: ' . $e->getMessage());
            return response()->json(['error' => 'Detalle no encontrado'], 404);
        }
    }

    public function index($obraId)
    {
        $obra = Obra::findOrFail($obraId);
        $destajos = Destajo::where('obra_id', $obraId)->get();
        
        return view('inventario.detallesdestajos', compact('obra', 'destajos'));
    }
    
    public function create($obraId)
    {
        $obra = Obra::findOrFail($obraId);
        return view('destajos.create', compact('obra'));
    }

    
}
