<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iva;
use App\Models\PagosAdministrativos;
use App\Models\Obra;

class IvaController extends Controller
{
    public function index($obraId)
    {
        $detalles = Iva::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('importe');
        $obra = Obra::findOrFail($obraId);
        return view('iva.index', compact('detalles', 'obraId', 'costoTotal', 'obra'));
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
                $detalle = Iva::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new Iva();
                }
            } else {
                $detalle = new Iva();
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
        PagosAdministrativos::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'IVA'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('iva.index', ['obraId' => $obraId]);
    }

    public function destroy($id)
    {
        $detalle = Iva::findOrFail($id);
        $detalle->delete();

        return response()->json(['success' => 'Registro eliminado correctamente.']);
    }
}
