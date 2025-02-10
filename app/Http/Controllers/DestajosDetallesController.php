<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DestajoDetalle;
use Illuminate\Support\Facades\Log;

use App\Models\Obra;
use App\Models\Destajo;

class DestajosDetallesController extends Controller
{
    public function show($id)
    {
        $detalle = Destajo::findOrFail($id);
        $obraId = $detalle->obra_id;
        $obra = Obra::findOrFail($obraId);

        // Access fecha_inicio and fecha_fin from the related Nomina model
        $fecha_inicio = $detalle->nomina->fecha_inicio;
        $fecha_fin = $detalle->nomina->fecha_fin;
        $nombre_nomina = $detalle->nomina->nombre;
        $dia_inicio = $detalle->nomina->dia_inicio;
        $dia_fin = $detalle->nomina->dia_fin;

        $destajoDetalles = DestajoDetalle::where('destajo_id', $detalle->id)->get();

        return view('destajo.detallesdestajos', compact('detalle', 'obra', 'fecha_inicio', 'fecha_fin', 'nombre_nomina', 'dia_inicio', 'dia_fin', 'obraId', 'destajoDetalles'));
    }

    public function store(Request $request, $obraId)
    {
        $cotizaciones = $request->input('cotizacion');
        $montosAprobados = $request->input('monto_aprobado');
        $pendientes = $request->input('pendiente');
        $estados = $request->input('estado');
        $nominaId = $request->input('nomina_id');

        // Find the Destajo model
        $destajo = Destajo::where('obra_id', $obraId)->where('nomina_id', $nominaId)->firstOrFail();

        foreach ($cotizaciones as $index => $cotizacion) {
            $destajoDetalle = new DestajoDetalle();
            $destajoDetalle->setTable('destajos_detalles');
            $destajoDetalle->obra_id = $obraId;
            $destajoDetalle->destajo_id = $destajo->id;
            $destajoDetalle->cotizacion = $cotizacion;
            $destajoDetalle->monto_aprobado = $montosAprobados[$index];
            $destajoDetalle->pendiente = $pendientes[$index];
            $destajoDetalle->estado = $estados[$index];

            // Handle dynamic payments
            $pagos = [];
            foreach ($request->input() as $key => $value) {
                if (strpos($key, 'pago_fecha_') === 0) {
                    $parts = explode('_', $key);
                    $pagoNumber = $parts[2];
                    if (isset($request->input("pago_fecha_$pagoNumber")[$index])) {
                        $pagos[$pagoNumber] = [
                            'fecha' => $request->input("pago_fecha_$pagoNumber")[$index],
                            'numero' => $request->input("pago_numero_$pagoNumber")[$index] ?? null,
                        ];
                    }
                }
            }
            $destajoDetalle->pagos = json_encode($pagos);

            $destajoDetalle->save();
        }

        return redirect()->back()->with('success', 'Detalles guardados correctamente.');
    }
}