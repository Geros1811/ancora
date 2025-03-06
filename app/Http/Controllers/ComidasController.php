<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleComidas;
use App\Models\CostoDirecto;
use App\Models\Obra;

class ComidasController extends Controller
{
    public function index($obraId)
    {
        $detalles = DetalleComidas::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $obra = Obra::findOrFail($obraId);
        return view('comidas.index', compact('detalles', 'obraId', 'costoTotal', 'obra'));
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
                $detalle = DetalleComidas::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new DetalleComidas();
                }
            } else {
                $detalle = new DetalleComidas();
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
            ['obra_id' => $obraId, 'nombre' => 'Comidas'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('comidas.index', ['obraId' => $obraId]);
    }

    public function destroy($id)
    {
        $detalle = DetalleComidas::findOrFail($id);
        $detalle->delete();

        return response()->json(['success' => 'Registro eliminado correctamente.']);
    }

    public function generatePdf($obraId)
    {
        $obra = Obra::findOrFail($obraId);
        $comidasDetalles = DetalleComidas::where('obra_id', $obraId)->get();
        $costoTotal = $comidasDetalles->sum('subtotal');

        if (!auth()->user()->hasRole('arquitecto')) {
            return redirect()->back()->with('error', 'No tienes permisos para generar el PDF de comidas.');
        }

        $data = [
            'obra' => $obra,
            'comidasDetalles' => $comidasDetalles,
            'costoTotal' => $costoTotal,
            'nombre_nomina' => 'N/A',
            'dia_inicio' => 'N/A',
            'fecha_inicio' => now(),
            'dia_fin' => 'N/A',
            'fecha_fin' => now(),
        ];

        $pdf = \PDF::loadView('comidas.pdf', $data);

        return $pdf->stream('comidas_' . $obra->nombre . '.pdf');
    }
}
