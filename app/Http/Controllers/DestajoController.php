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
        'monto_aprobado'  => 'required|array',
        'paso_actual'     => 'required|array',
        'cantidad'        => 'required|array',
    ]);

    // Eliminar los destajos existentes para la nómina y obra seleccionadas
    Destajo::where('obra_id', $request->obraId)
           ->where('nomina_id', $request->nomina_id)
           ->delete();

    // Crear nuevos registros
    foreach ($request->frente as $index => $frente) {
        $destajo = new Destajo();
        $destajo->nomina_id = $request->nomina_id;
        $destajo->obra_id   = $request->obraId;
        $destajo->frente    = $frente === "Otros" ? $request->frente_custom[$index] : $frente;
        $destajo->monto_aprobado = $request->monto_aprobado[$index];
        $destajo->paso_actual    = $request->paso_actual[$index];
        $destajo->cantidad       = $request->cantidad[$index];
        $destajo->save();
    }

    return redirect()->route('destajos.index', ['obraId' => $request->obraId])
                     ->with('success', 'Destajos guardados correctamente.');
}


    public function nomina()
    {
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }
    

}
