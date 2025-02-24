<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetallePapeleria;
use App\Models\CostoIndirecto;
use App\Models\Obra;

class PapeleriaController extends Controller
{
    public function index($obraId)
    {
        $detalles = DetallePapeleria::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $obra = Obra::findOrFail($obraId);
        return view('papeleria.index', compact('detalles', 'obraId', 'costoTotal', 'obra'));
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

        foreach ($fechas as $index => $fecha) {
            $cantidad = $cantidades[$index];
            $precio_unitario = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio_unitario;

            // Check if an ID exists for this row, if so, update the existing record
            if (isset($ids[$index])) {
                $detalle = DetallePapeleria::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new DetallePapeleria();
                }
            } else {
                $detalle = new DetallePapeleria();
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
$image->storeAs('public/tickets', $imageName);
$detalle->foto = 'storage/tickets/' . $imageName;
            }

            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos indirectos
        CostoIndirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Papelería'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('papeleria.index', ['obraId' => $obraId]);
    }
}
