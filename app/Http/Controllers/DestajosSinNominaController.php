<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DestajosSinNominaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request, $obraId)
    {
        $partidas = \App\Models\Partida::where('obra_id', $obraId)->get();

       // Load the details for each partida
        foreach ($partidas as $partida) {
            $detalles = \App\Models\DestajoSinNominaDetalle::where('partida_id', $partida->id)->get();
            foreach ($detalles as $detalle) {
                $detalle->pagos = json_decode($detalle->pagos, true);
            }
            $partida->detalles = $detalles;
        }

        return view('destajoSinNomina.index', ['obraId' => $obraId, 'partidas' => $partidas]);
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
        $claves = $request->input('clave');
        $conceptos = $request->input('concepto');
        $unidades = $request->input('unidad');
        $cantidades = $request->input('cantidad');
        $preciosUnitarios = $request->input('precio_unitario');
        $subtotales = $request->input('subtotal');

        Log::info('DestajosSinNominaController - storeDetalles - Request data:', $request->all());

        // Process dynamic payments
        $pagos = [];
        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'pago_') === 0) {
                $pagoIndex = explode('_', $key)[1];
                $pagoFechas = $request->input("pago_fecha_{$pagoIndex}");
                if (is_array($value) && is_array($pagoFechas)) {
                    foreach ($value as $index => $pago) {
                        $pagos[$index][$pagoIndex] = [
                            'monto' => $pago,
                            'fecha' => $pagoFechas[$index] ?? null
                        ];
                    }
                }
            }
        }

         if (is_array($claves)) {
            foreach ($claves as $index => $clave) {
                // Check if at least one field is not empty
                if (empty($clave) && empty($conceptos[$index]) && empty($unidades[$index]) && empty($cantidades[$index]) && empty($preciosUnitarios[$index])) {
                    continue;
                }

                 $destajoSinNominaDetalle = \App\Models\DestajoSinNominaDetalle::where('partida_id', $partidaId)
                    ->where('clave', $clave)
                    ->first();

                $data = [
                    'partida_id' => $partidaId,
                    'clave' => $clave,
                    'concepto' => $conceptos[$index],
                    'unidad' => $unidades[$index],
                    'cantidad' => $cantidades[$index],
                    'precio_unitario' => $preciosUnitarios[$index],
                    'subtotal' => $subtotales[$index],
                    'pagos' => json_encode($pagos[$index] ?? []),
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
            }
        }

         // Fetch the updated details for the partida
        $detalles = \App\Models\DestajoSinNominaDetalle::where('partida_id', $partidaId)->get();

        // Return the updated details as a JSON response
        return response()->json($detalles);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
