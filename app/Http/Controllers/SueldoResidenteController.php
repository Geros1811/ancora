<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SueldoResidente;
use App\Models\CostoIndirecto;
use App\Models\Obra;

class SueldoResidenteController extends Controller
{
    public function index($obraId)
    {
        $detalles = SueldoResidente::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('importe');
        $obra = Obra::findOrFail($obraId);
        return view('sueldo-residente.index', compact('detalles', 'obraId', 'costoTotal', 'obra'));
    }

    public function store(Request $request, $obraId)
    {
        $costoTotal = 0;

        $fechas = $request->input('fecha', []);
        $conceptos = $request->input('concepto', []);
        $importes = $request->input('importe', []);
        $observaciones = $request->input('observaciones', []);
        $ids = $request->input('id', []);

        foreach ($fechas as $index => $fechaInput) {
            $fecha = $fechaInput ?? date('Y-m-d');
            $importe = $importes[$index];
            $observacion = $observaciones[$index];

            // Check if an ID exists for this row, if so, update the existing record
            if (isset($ids[$index])) {
                $detalle = SueldoResidente::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new SueldoResidente();
                }
            } else {
                $detalle = new SueldoResidente();
            }

            $detalle->obra_id = $obraId;
            $detalle->fecha = $fecha;
            $detalle->concepto = $conceptos[$index];
            $detalle->importe = $importe;
            $detalle->observaciones = $observacion;

            $detalle->save();

            $costoTotal += $importe;
        }

        // Actualizar el costo total en la tabla de costos indirectos
        CostoIndirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Sueldo Residente'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('sueldo-residente.index', ['obraId' => $obraId]);
    }

    public function destroy($id)
    {
        $detalle = SueldoResidente::findOrFail($id);
        $detalle->delete();

        return response()->json(['success' => 'Registro eliminado correctamente.']);
    }
}
