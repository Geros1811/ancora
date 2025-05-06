<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleUtilidades;
use App\Models\CostoIndirecto;
use App\Models\Obra;

class UtilidadesController extends Controller
{
    public function index($obraId)
    {
        $detalles = DetalleUtilidades::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $obra = Obra::findOrFail($obraId);
        return view('utilidades.index', compact('detalles', 'obraId', 'costoTotal', 'obra'));
    }

    public function store(Request $request, $obraId)
    {
        $costoTotal = 0;

        $fechas = $request->input('fecha', []);
        $conceptos = $request->input('concepto', []);
        $unidades = $request->input('unidad', []);
        $cantidades = $request->input('cantidad', []);
        $precios_unitarios = $request->input('precio_unitario', []);
        $ids = $request->input('id', []);

        foreach ($fechas as $index => $fechaInput) {
            $fecha = $fechaInput ?? date('Y-m-d');
            $cantidad = $cantidades[$index];
            $precio_unitario = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio_unitario;

            // Check if an ID exists for this row, if so, update the existing record
            if (isset($ids[$index])) {
                $detalle = DetalleUtilidades::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new DetalleUtilidades();
                }
            } else {
                $detalle = new DetalleUtilidades();
            }

            $detalle->obra_id = $obraId;
            $detalle->fecha = $fecha;
            $detalle->concepto = $conceptos[$index];
            $detalle->unidad = $unidades[$index];
            $detalle->cantidad = $cantidad;
            $detalle->precio_unitario = $precio_unitario;
            $detalle->subtotal = $subtotal;

            // Handle image upload
            if ($request->hasFile('fotos.' . $index)) {
                $image = $request->file('fotos.' . $index);
                $imageName = time().'_'.$image->getClientOriginalName();
                $image->move(base_path('tickets'), $imageName);
                $detalle->foto = 'tickets/' . $imageName;
            }  else {
		    $detalle->foto = null;
	    }

            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos indirectos

        // Actualizar el costo total en la tabla de costos indirectos
        CostoIndirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Utilidades'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('utilidades.index', ['obraId' => $obraId]);
    }

    public function destroy($id)
    {
        $detalle = DetalleUtilidades::findOrFail($id);
        $detalle->delete();

        return response()->json(['success' => 'Registro eliminado correctamente.']);
    }

    public function destroyDetalle($obraId, $detalleId)
    {
        $detalle = DetalleUtilidades::findOrFail($detalleId);
        $detalle->delete();

        // Actualizar el costo total en la tabla de costos indirectos
        $detalles = DetalleUtilidades::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        CostoIndirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Utilidades'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('utilidades.index', ['obraId' => $obraId]);
    }

    public function generatePdf($obraId)
    {
        $detalles = DetalleUtilidades::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $obra = Obra::findOrFail($obraId);

        $data = [
            'utilidadesDetalles' => $detalles,
            'costoTotal' => $costoTotal,
            'obra' => $obra,
        ];

        $pdf = \PDF::loadView('utilidades.pdf', $data);

        // Prevent automatic download - stream the PDF to the browser
        return $pdf->stream('utilidades.pdf');
    }
}
