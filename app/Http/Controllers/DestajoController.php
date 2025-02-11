<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destajo;
use Illuminate\Support\Facades\Log;
use App\Models\Nomina; // Asegúrate de que este es el nombre correcto del modelo

class DestajoController extends Controller
{
    public function index($obraId)
{
    // Recuperar todas las nóminas de la obra
    $nominas = Nomina::where('obra_id', $obraId)->get();
    
    // Recuperar los destajos de la obra
    $detalles = Destajo::where('obra_id', $obraId)->get();
    
    // Pasar los detalles y otras variables a la vista
    return view('destajo.index', compact('detalles', 'obraId', 'nominas'));
}

    
public function store(Request $request)
{
    $request->validate([
        'nomina_id'       => 'required|exists:nominas,id',
        'frente'          => 'required|array',
    ]);

    foreach ($request->frente as $index => $frente) {
        Destajo::updateOrCreate(
            [
                'obra_id' => $request->obraId,
                'nomina_id' => $request->nomina_id,
                'frente' => $frente === "Otros" ? $request->frente_custom[$index] : $frente,
            ],
            [
                'cantidad' => 0,
                'monto_aprobado' => 0,
            ]
        );
    }

    return redirect()->route('destajos.index', ['obraId' => $request->obraId])
                     ->with('success', 'Destajos guardados correctamente.');
}


    public function nomina()
    {
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }
    
    public function destroy(Destajo $destajo)
    {
        $destajo->delete();
        return response()->json(['success' => 'Destajo deleted successfully']);
    }
}
