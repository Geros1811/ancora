<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RentaMaquinaria;
use App\Models\CostoDirecto;
use App\Models\Obra;

class RentaMaquinariaController extends Controller
{
    public function index($obraId)
    {
        $detalles = RentaMaquinaria::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $obra = Obra::findOrFail($obraId);
        return view('rentaMaquinaria.index', compact('detalles', 'obraId', 'costoTotal', 'obra'));
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
                $detalle = RentaMaquinaria::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new RentaMaquinaria();
                }
            } else {
                $detalle = new RentaMaquinaria();
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
            ['obra_id' => $obraId, 'nombre' => 'Renta de Maquinaria'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('rentaMaquinaria.index', ['obraId' => $obraId]);
    }

    public function destroy($id)
    {
        $detalle = RentaMaquinaria::findOrFail($id);
        $detalle->delete();

        return response()->json(['success' => 'Registro eliminado correctamente.']);
    }

    public function generatePdf($obraId)
    {
        $obra = Obra::findOrFail($obraId);
        $rentaMaquinariaDetalles = RentaMaquinaria::where('obra_id', $obraId)->get();
        $costoTotal = $rentaMaquinariaDetalles->sum('subtotal');

        if (!auth()->user()->hasRole('arquitecto')) {
            return redirect()->back()->with('error', 'No tienes permisos para generar el PDF de renta de maquinaria.');
        }

        $data = [
            'obra' => $obra,
            'rentaMaquinariaDetalles' => $rentaMaquinariaDetalles,
            'costoTotal' => $costoTotal,
            'nombre_nomina' => 'N/A', // You might want to fetch this dynamically
            'dia_inicio' => 'N/A', // You might want to fetch this dynamically
            'fecha_inicio' => now(), // You might want to fetch this dynamically
            'dia_fin' => 'N/A', // You might want to fetch this dynamically
            'fecha_fin' => now(), // You might want to fetch this dynamically
        ];

        $pdf = \PDF::loadView('rentaMaquinaria.pdf', $data);

        return $pdf->stream('rentaMaquinaria_' . $obra->nombre . '.pdf');
    }
}
