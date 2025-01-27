<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleUtilidades;
use App\Models\CostoIndirecto;

class UtilidadesController extends Controller
{
    public function index($obraId)
    {
        $detalles = DetalleUtilidades::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        return view('utilidades.index', compact('detalles', 'obraId', 'costoTotal'));
    }

    public function store(Request $request, $obraId)
    {
        // Eliminar los registros existentes para la obra antes de guardar los nuevos datos
        DetalleUtilidades::where('obra_id', $obraId)->delete();

        $costoTotal = 0;

        $fechas = $request->input('fecha', []);
        $conceptos = $request->input('concepto', []);
        $unidades = $request->input('unidad', []);
        $cantidades = $request->input('cantidad', []);
        $precios_unitarios = $request->input('precio_unitario', []);

        foreach ($fechas as $index => $fecha) {
            $cantidad = $cantidades[$index];
            $precio_unitario = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio_unitario;

            $detalle = new DetalleUtilidades();
            $detalle->obra_id = $obraId;
            $detalle->fecha = $fecha;
            $detalle->concepto = $conceptos[$index];
            $detalle->unidad = $unidades[$index];
            $detalle->cantidad = $cantidad;
            $detalle->precio_unitario = $precio_unitario;
            $detalle->subtotal = $subtotal;
            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos indirectos
        CostoIndirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Utilidades'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('utilidades.index', ['obraId' => $obraId]);
    }
}
