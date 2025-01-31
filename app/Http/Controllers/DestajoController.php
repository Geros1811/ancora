<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destajo;
use Illuminate\Support\Facades\Log;

class DestajoController extends Controller
{
    public function store(Request $request, $obraId)
    {
        try {
            // Eliminar los registros existentes para la obra antes de guardar los nuevos datos
            Destajo::where('obra_id', $obraId)->delete();

            $costoTotal = 0;

            $frentes = $request->input('frente', []);
            $fechas = $request->input('fecha', []);
            $no_pagos = $request->input('no_pago', []);
            $cantidades = $request->input('cantidad', []);
            $observaciones = $request->input('observaciones', []);
            $nomina_id = $request->input('nomina_id');

            foreach ($frentes as $index => $frente) {
                $cantidad = $cantidades[$index] ?? 0;
                $subtotal = $cantidad; // Asumiendo que el subtotal es igual a la cantidad

                $detalle = new Destajo();
                $detalle->obra_id = $obraId;
                $detalle->nomina_id = $nomina_id;
                $detalle->frente = $frente;
                $detalle->fecha = $fechas[$index] ?? null;
                $detalle->no_pago = $no_pagos[$index] ?? '';
                $detalle->cantidad = $cantidad;
                $detalle->observaciones = $observaciones[$index] ?? '';
                $detalle->save();

                $costoTotal += $subtotal;
            }

            // Actualizar el costo total en la tabla de costos indirectos
            // CostoIndirecto::updateOrCreate(
            //     ['obra_id' => $obraId, 'nombre' => 'Destajos'],
            //     ['costo' => $costoTotal]
            // );

            return redirect()->route('destajo.index', ['obraId' => $obraId])
                             ->with('success', 'Destajos guardados exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al guardar destajos: ' . $e->getMessage());
            return redirect()->route('destajo.index', ['obraId' => $obraId])
                             ->with('error', 'Hubo un error al guardar los destajos. Por favor, intente nuevamente.');
        }
    }

    public function index($obraId)
    {
        $detalles = Destajo::where('obra_id', $obraId)->get();
        return view('destajo.index', compact('detalles', 'obraId'));
    }
}
