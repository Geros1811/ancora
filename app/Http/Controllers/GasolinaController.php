<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleGasolina;
use App\Models\CostoIndirecto;
use App\Models\Obra;

class GasolinaController extends Controller
{
    public function index($obraId)
    {
        $detalles = DetalleGasolina::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $obra = Obra::findOrFail($obraId);
        return view('gasolina.index', compact('detalles', 'obraId', 'costoTotal', 'obra'));
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
                $detalle = DetalleGasolina::find($ids[$index]);
                if (!$detalle) {
                    $detalle = new DetalleGasolina();
                }
            } else {
                $detalle = new DetalleGasolina();
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

        // Actualizar el costo total en la tabla de costos indirectos
        CostoIndirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Gasolina'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('gasolina.index', ['obraId' => $obraId]);
    }

    public function destroy($id)
    {
        $detalle = DetalleGasolina::findOrFail($id);
        $detalle->delete();

        return response()->json(['success' => 'Registro eliminado correctamente.']);
    }

    public function destroyDetalle($obraId, $detalleId)
    {
        $detalle = DetalleGasolina::findOrFail($detalleId);
        $detalle->delete();

        // Actualizar el costo total en la tabla de costos indirectos
        $detalles = DetalleGasolina::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        CostoIndirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Gasolina'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('gasolina.index', ['obraId' => $obraId]);
    }

    public function generatePdf($obraId)
    {
        $obra = Obra::findOrFail($obraId);
        $gasolinaDetalles = DetalleGasolina::where('obra_id', $obraId)->get();
        $costoTotal = $gasolinaDetalles->sum('subtotal');

        if (!auth()->user()->hasRole('arquitecto')) {
            return redirect()->back()->with('error', 'No tienes permisos para generar el PDF de gasolina.');
        }

        $data = [
            'obra' => $obra,
            'gasolinaDetalles' => $gasolinaDetalles,
            'costoTotal' => $costoTotal,
            'nombre_nomina' => 'N/A', // You might want to fetch this dynamically
            'dia_inicio' => 'N/A', // You might want to fetch this dynamically
            'fecha_inicio' => now(), // You might want to fetch this dynamically
            'dia_fin' => 'N/A', // You might want to fetch this dynamically
            'fecha_fin' => now(), // You might want to fetch this dynamically
        ];

        $pdf = \PDF::loadView('gasolina.pdf', $data);

        return $pdf->stream('gasolina_' . $obra->nombre . '.pdf');
    }
}
