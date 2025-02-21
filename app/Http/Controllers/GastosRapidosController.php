<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GastosRapidosController extends Controller
{
    //
    public function create()
    {
        return view('gastos_rapidos.create');
    }

    public function store(Request $request)
    {
        $tabla = $request->input('tabla');
        $obraId = $request->input('obraId');
        $fecha = $request->input('fecha');
        $concepto = $request->input('concepto');
        $unidad = $request->input('unidad');
        $cantidad = $request->input('cantidad');
        $precio_unitario = $request->input('precio_unitario');
        $materialesSub = $request->input('materialesSub');

        if ($tabla == 'gasolina') {
            $tableName = 'detalle_gasolinas';
        } elseif ($tabla == 'maquinariaMayor') {
            $tableName = 'detalle_maquinaria_mayor';
        } elseif ($tabla == 'maquinariaMenor') {
            $tableName = 'detalle_maquinaria_menor';
        } elseif ($tabla == 'herramientaMenor') {
            $tableName = 'detalle_herramienta_menor';
        } elseif ($tabla == 'equipoSeguridad') {
            $tableName = 'detalle_equipo_seguridad';
        } elseif ($tabla == 'rentaMaquinaria') {
            $tableName = 'renta_maquinarias';
        } elseif ($tabla == 'limpieza') {
            $tableName = 'detalle_limpieza';
        } elseif ($tabla == 'materiales' && $materialesSub == 'agregados') {
            $tableName = 'agregados';
        } elseif ($tabla == 'materiales' && $materialesSub == 'aceros') {
             $tableName = 'aceros';
        } elseif ($tabla == 'materiales' && $materialesSub == 'cemento') {
             $tableName = 'cemento';
        } elseif ($tabla == 'materiales' && $materialesSub == 'losas') {
             $tableName = 'losas';
        } elseif ($tabla == 'materiales') {
            $tableName = 'generales';
        } else {
            $tableName = 'detalles_' . $tabla;
        }

        try {
            DB::table($tableName)->insert(
                array_map(function ($fecha, $concepto, $unidad, $cantidad, $precio_unitario, $subtotal) use ($obraId) {
                    return [
                        'obra_id' => $obraId,
                        'fecha' => $fecha,
                        'concepto' => $concepto,
                        'unidad' => $unidad,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio_unitario,
                        'subtotal' => $subtotal
                    ];
                }, $fecha, $concepto, $unidad, $cantidad, $precio_unitario, $request->input('subtotal'))
            );

            return redirect()->back()->with('success', 'Gasto rápido guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar el gasto rápido: ' . $e->getMessage());
        }
    }
}
