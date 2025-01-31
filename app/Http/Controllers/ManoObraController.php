<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleManoObra;
use App\Models\CostoDirecto;
use App\Models\Nomina;
use App\Models\Destajo;

class ManoObraController extends Controller
{
    public function index($obraId)
    {
        $detalles = DetalleManoObra::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $nominas = Nomina::where('obra_id', $obraId)->get();
        $destajos = Destajo::where('obra_id', $obraId)->get();
        return view('manoObra.index', compact('detalles', 'obraId', 'costoTotal', 'nominas', 'destajos'));
    }

    public function store(Request $request, $obraId)
    {
        $nombreNomina = $request->input('nombre_nomina');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Verificar si ya existe una nómina con el mismo nombre y fechas
        $nomina = Nomina::where('obra_id', $obraId)
            ->where('nombre', $nombreNomina)
            ->where('fecha_inicio', $fechaInicio)
            ->where('fecha_fin', $fechaFin)
            ->first();

        if (!$nomina) {
            $nomina = new Nomina();
            $nomina->obra_id = $obraId;
            $nomina->nombre = $nombreNomina;
            $nomina->fecha_inicio = $fechaInicio;
            $nomina->fecha_fin = $fechaFin;
            $nomina->save();
        }

        $nombres = $request->input('nombre', []);
        $puestos = $request->input('puesto', []);
        $lunes = $request->input('lunes', []);
        $martes = $request->input('martes', []);
        $miercoles = $request->input('miercoles', []);
        $jueves = $request->input('jueves', []);
        $viernes = $request->input('viernes', []);
        $sabado = $request->input('sabado', []);
        $precios_hora = $request->input('precio_diario', []);
        $extras_menos = $request->input('extras_menos', []);
        $detallesExistentes = DetalleManoObra::where('obra_id', $obraId)->where('nomina_id', $nomina->id)->get();

        foreach ($detallesExistentes as $detalleExistente) {
            $detalleExistente->delete();
        }

        foreach ($nombres as $index => $nombre) {
            if (!empty($nombre)) {
                $total_horas = ($lunes[$index] ?? 0) + ($martes[$index] ?? 0) + ($miercoles[$index] ?? 0) + ($jueves[$index] ?? 0) + ($viernes[$index] ?? 0) + ($sabado[$index] ?? 0);
                $subtotal = ($total_horas * ($precios_hora[$index] ?? 0)) + ($extras_menos[$index] ?? 0);

                $detalle = new DetalleManoObra();
                $detalle->obra_id = $obraId;
                $detalle->nomina_id = $nomina->id;
                $detalle->nombre = $nombre;
                $detalle->puesto = $puestos[$index] ?? '';
                $detalle->lunes = $lunes[$index] ?? 0;
                $detalle->martes = $martes[$index] ?? 0;
                $detalle->miercoles = $miercoles[$index] ?? 0;
                $detalle->jueves = $jueves[$index] ?? 0;
                $detalle->viernes = $viernes[$index] ?? 0;
                $detalle->sabado = $sabado[$index] ?? 0;
                $detalle->total_horas = $total_horas;
                $detalle->precio_hora = $precios_hora[$index] ?? 0;
                $detalle->extras_menos = $extras_menos[$index] ?? 0;
                $detalle->subtotal = $subtotal;
                $detalle->save();
            }
        }

        // Calcular el costo total de todas las nóminas
        $costoTotal = DetalleManoObra::where('obra_id', $obraId)->sum('subtotal');

        // Actualizar el costo total en la tabla de costos directos
        CostoDirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Mano de Obra'],
            ['costo' => $costoTotal]
        );

        return redirect()->route('manoObra.index', ['obraId' => $obraId]);
    }
}
