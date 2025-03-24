<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class DestajosSinNominaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request, $obraId)
    {
        $partidas = \App\Models\Partida::where('obra_id', $obraId)->get();

       // Load the details and totals for each partida
        foreach ($partidas as $partida) {
            $detalles = \App\Models\DestajoSinNominaDetalle::where('partida_id', $partida->id)->get();
            foreach ($detalles as $detalle) {
                $detalle->pagos = json_decode($detalle->pagos, true);
            }
            $partida->detalles = $detalles;

            // Ensure totals are loaded from the database
            $partida->monto_total_aprobado = $partida->monto_total_aprobado ?? 0;
            $partida->cantidad_total_pagada = $partida->cantidad_total_pagada ?? 0;
        }

        $obra = \App\Models\Obra::find($obraId);

        // Calculate total pagos
        $totalPagos = 0;
        foreach ($partidas as $partida) {
            foreach ($partida->detalles as $detalle) {
                if (is_array($detalle->pagos)) {
                    foreach ($detalle->pagos as $pago) {
                        $totalPagos += $pago['monto'];
                    }
                }
            }
        }

        return view('destajoSinNomina.index', ['obraId' => $obraId, 'partidas' => $partidas, 'obra' => $obra, 'totalPagos' => $totalPagos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $obraId)
    {
        $partida = new \App\Models\Partida();
        $partida->obra_id = $obraId;
        $partida->title = $request->input('partida_title');
        $partida->save();

        return redirect()->route('destajosSinNomina.index', ['obraId' => $obraId]);
    }

    public function storeDetalles(Request $request, $obraId, $partidaId)
    {
        $detalleIds = $request->input('detalle_id');
        $claves = $request->input('clave');
        $conceptos = $request->input('concepto');
        $unidades = $request->input('unidad');
        $cantidades = $request->input('cantidad');
        $preciosUnitarios = $request->input('precio_unitario');
        $subtotales = $request->input('subtotal');

        Log::info('DestajosSinNominaController - storeDetalles - Request data:', $request->all());

        // Process dynamic payments
        $pagos = [];
        foreach ($claves as $index => $clave) {
            $pagoIndex = 1;
            while ($request->has("pago_numero_{$pagoIndex}")) {
                $pagoNumeros = $request->input("pago_numero_{$pagoIndex}");
                $pagoFechas = $request->input("pago_fecha_{$pagoIndex}");

                if (isset($pagoNumeros[$index])) {
                    $pagos[$index][$pagoIndex] = [
                        'monto' => $pagoNumeros[$index],
                        'fecha' => $pagoFechas[$index] ?? null,
                    ];
                }
                $pagoIndex++;
            }
        }

        $montoTotalAprobado = 0;
        $cantidadTotalPagada = 0;

        if (is_array($claves)) {
            foreach ($claves as $index => $clave) {
                // Check if at least one field is not empty
                if (empty($clave) && empty($conceptos[$index]) && empty($unidades[$index]) && empty($cantidades[$index]) && empty($preciosUnitarios[$index])) {
                    continue;
                }

                $detalleId = $detalleIds[$index] ?? null;
                if ($detalleId) {
                    $destajoSinNominaDetalle = \App\Models\DestajoSinNominaDetalle::find($detalleId);
                } else {
                    $destajoSinNominaDetalle = null;
                }

                $subtotal = $subtotales[$index] ?? 0;
                $totalPagos = 0;

                // Asegúrate de que los pagos sean procesados correctamente
                $pagosValidos = [];
                if (isset($pagos[$index]) && is_array($pagos[$index])) {
                    foreach ($pagos[$index] as $pagoIndex => $pago) {
                        if (!empty($pago['monto']) && $pago['monto'] > 0) {
                            $totalPagos += $pago['monto'];
                            $pagosValidos[$pagoIndex] = $pago; // Solo agrega pagos válidos
                        }
                    }
                }

                $pendiente = max($subtotal - $totalPagos, 0); // Asegúrate de que el pendiente no sea negativo

                $data = [
                    'partida_id' => $partidaId,
                    'clave' => $clave,
                    'concepto' => $conceptos[$index],
                    'unidad' => $unidades[$index],
                    'cantidad' => $cantidades[$index],
                    'precio_unitario' => $preciosUnitarios[$index],
                    'subtotal' => $subtotal,
                    'pagos' => json_encode($pagosValidos), // Guarda solo los pagos válidos
                    'pendiente' => $pendiente,
                ];

                Log::info('DestajosSinNominaController - storeDetalles - Data to be saved:', $data);

                try {
                    if ($destajoSinNominaDetalle) {
                        $destajoSinNominaDetalle->update($data);
                        Log::info('DestajosSinNominaController - storeDetalles - Updated detalle with ID: ' . $destajoSinNominaDetalle->id);
                    } else {
                        $destajoSinNominaDetalle = \App\Models\DestajoSinNominaDetalle::create($data);
                        Log::info('DestajosSinNominaController - storeDetalles - Created new detalle with ID: ' . $destajoSinNominaDetalle->id);
                    }
                } catch (\Exception $e) {
                    Log::error('DestajosSinNominaController - storeDetalles - Error saving detalle: ' . $e->getMessage());
                }

                // Suma los subtotales y los pagos para calcular los totales
                $montoTotalAprobado += $subtotal;
                $cantidadTotalPagada += $totalPagos;
            }
        }

        // Actualiza los totales en la tabla de partidas
        $partida = \App\Models\Partida::find($partidaId);
        if ($partida) {
            $partida->monto_total_aprobado = $montoTotalAprobado;
            $partida->cantidad_total_pagada = $cantidadTotalPagada;
            $partida->save();
            Log::info('DestajosSinNominaController - storeDetalles - Updated partida with ID: ' . $partidaId);
        }

        // Fetch the updated details for the partida
        $detalles = \App\Models\DestajoSinNominaDetalle::where('partida_id', $partidaId)->get();
        foreach ($detalles as $detalle) {
            $detalle->pagos = json_decode($detalle->pagos, true);
        }

        // Redirect back to the index page
        return redirect()->route('destajosSinNomina.index', ['obraId' => $obraId]);
    }

    public function generatePdf(Request $request, $obraId, $partidaId)
    {
        $partida = \App\Models\Partida::where('id', $partidaId)->where('obra_id', $obraId)->firstOrFail();

        $detalles = \App\Models\DestajoSinNominaDetalle::where('partida_id', $partida->id)->get();
        foreach ($detalles as $detalle) {
            $detalle->pagos = json_decode($detalle->pagos, true);
        }
        $partida->detalles = $detalles;

        $obra = \App\Models\Obra::find($obraId);

        $pdf = Pdf::loadView('destajoSinNomina.pdf', compact('obraId', 'partida', 'obra'))->setPaper('a4', 'landscape');

        return $pdf->stream('destajos-sin-nomina.pdf');
    }
}
