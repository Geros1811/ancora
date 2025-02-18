<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destajo;
use Illuminate\Support\Facades\Log;
use App\Models\Nomina; // Asegúrate de que este es el nombre correcto del modelo
use App\Models\Obra;

class DestajoController extends Controller
{
    public function index($obraId)
    {
        // Recuperar todas las nóminas de la obra
        $nominas = Nomina::where('obra_id', $obraId)->get();
        
        // Recuperar los destajos de la obra
        $detalles = Destajo::where('obra_id', $obraId)->get();
        
        $obra = Obra::findOrFail($obraId);

        // Pasar los detalles y otras variables a la vista
        return view('destajo.index', compact('detalles', 'obraId', 'nominas', 'obra'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomina_id'       => 'required|exists:nominas,id',
            'frente'          => 'required|array',
        ]);

        $obraId = $request->obraId;
        $nominaId = $request->nomina_id;

        // Crear nuevos destajos para la semana actual
        foreach ($request->frente as $index => $frente) {
            Destajo::updateOrCreate(
                [
                    'obra_id' => $obraId,
                    'nomina_id' => $nominaId,
                    'frente' => $frente === "Otros" ? $request->frente_custom[$index] : $frente,
                ],
                [
                    'cantidad' => 0,
                    'monto_aprobado' => 0,
                    'editable' => true, // Marcar como editable
                ]
            );
        }

        return redirect()->route('destajos.index', ['obraId' => $obraId])
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

    public function toggleLock(Request $request, $id)
    {
        $destajo = Destajo::findOrFail($id);
        $destajo->locked = !$destajo->locked;
        $destajo->save();

        return response()->json(['success' => true, 'locked' => $destajo->locked]);
    }
}
