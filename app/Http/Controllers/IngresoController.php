<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingreso;
use App\Models\Obra;
use App\Models\PagosAdministrativos;

class IngresoController extends Controller
{
    public function index($obraId)
    {
        $ingresos = Ingreso::where('obra_id', $obraId)->get();
        $costoTotal = $ingresos->sum('importe');
        $obra = Obra::findOrFail($obraId);

        return view('ingresos.index', compact('ingresos', 'costoTotal', 'obraId', 'obra'));
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
                $detalle = Ingreso::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new Ingreso();
                }
            } else {
                $detalle = new Ingreso();
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
            ['obra_id' => $obraId, 'nombre' => 'Ingresos'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('ingresos.index', ['obraId' => $obraId]);
    }

    public function destroy($id)
    {
        $detalle = Ingreso::findOrFail($id);
        $detalle->delete();

        return response()->json(['success' => 'Registro eliminado correctamente.']);
    }

    public function generatePdf($obraId)
    {
        $ingresos = Ingreso::where('obra_id', $obraId)->get();
        $costoTotal = $ingresos->sum('importe');
        $obra = Obra::findOrFail($obraId);

        $data = [
            'ingresosDetalles' => $ingresos,
            'costoTotal' => $costoTotal,
            'obra' => $obra,
        ];

        $pdf = \PDF::loadView('ingresos.pdf', $data);

        // Prevent automatic download - stream the PDF to the browser
        return $pdf->stream('ingresos.pdf');
    }
}
