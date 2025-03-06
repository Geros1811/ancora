<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleMaquinariaMenor;
use App\Models\CostoDirecto;
use App\Models\Obra;

class MaquinariaMenorController extends Controller
{
    public function index($obraId)
    {
        $detalles = DetalleMaquinariaMenor::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $obra = Obra::findOrFail($obraId);
        return view('maquinariaMenor.index', compact('detalles', 'obraId', 'costoTotal', 'obra'));
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
                $detalle = DetalleMaquinariaMenor::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new DetalleMaquinariaMenor();
                }
            } else {
                $detalle = new DetalleMaquinariaMenor();
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
                $imageName = time() . '_' . $image->getClientOriginalName();

                // Delete old image if it exists
                if ($detalle->foto) {
                    $oldImagePath = public_path($detalle->foto);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image->storeAs('public/tickets', $imageName);
                $detalle->foto = 'storage/tickets/' . $imageName;
            } elseif (!$detalle->foto) {
                $detalle->foto = null;
            }

            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos directos
        CostoDirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Maquinaria Menor'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('maquinariaMenor.index', ['obraId' => $obraId]);
    }

    public function destroy($id)
    {
        $detalle = DetalleMaquinariaMenor::findOrFail($id);
        $detalle->delete();

        return response()->json(['success' => 'Registro eliminado correctamente.']);
    }

    public function generatePdf($obraId)
    {
        $detalles = DetalleMaquinariaMenor::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $obra = Obra::findOrFail($obraId);

        $data = [
            'maquinariaMenorDetalles' => $detalles,
            'costoTotal' => $costoTotal,
            'obra' => $obra,
        ];

        $pdf = \PDF::loadView('maquinariaMenor.pdf', $data);

        // Prevent automatic download - stream the PDF to the browser
        return $pdf->stream('maquinariaMenor.pdf');
    }
}
