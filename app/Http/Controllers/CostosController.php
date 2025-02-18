<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CostoIndirecto;
use App\Models\Obra;

class CostosController extends Controller
{
    // Muestra los detalles de un costo indirecto
    public function show($id)
    {
        $costo = CostoIndirecto::findOrFail($id);
        $detalleCosto = $costo->detalles;
        $obra = Obra::findOrFail($costo->obra_id);

        // Retornar la vista con los detalles del costo
        return view('costos.show', compact('costo', 'detalleCosto', 'obra'));
    }

    // Actualiza el costo indirecto
    public function updateCostoIndirecto(Request $request, $obraId, $costo)
    {
        $costoIndirecto = CostoIndirecto::where('obra_id', $obraId)->where('nombre', $costo)->firstOrFail();
        $costoIndirecto->costo = $request->input('costo');
        $costoIndirecto->save();

        return response()->json(['success' => true]);
    }
}
