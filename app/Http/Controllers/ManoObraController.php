<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\DetalleManoObra;
use App\Models\CostoDirecto;
use App\Models\Nomina;
use Carbon\Carbon;
use App\Models\Destajo;
class ManoObraController extends Controller
{
    public function index($obraId)
    {
        $nominas = Nomina::where('obra_id', $obraId)->get(); // Obtener las nóminas relacionadas con la obra
        $detalles = DetalleManoObra::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $destajos = Destajo::where('obra_id', $obraId)->get();
    
        return view('manoObra.index', compact('nominas', 'detalles', 'costoTotal', 'destajos', 'obraId'));
    }
    
    

    public function store(Request $request, $obraId)
    {
        $nombreNomina = $request->input('nombre_nomina');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
    
        // Convertir fecha en objeto Carbon y obtener día de la semana en español
        $diaInicio = Carbon::parse($fechaInicio)->locale('es')->isoFormat('dddd');
        $diaFin = Carbon::parse($fechaFin)->locale('es')->isoFormat('dddd');
    
        // Verificar si ya existe la nómina
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
            $nomina->dia_inicio = ucfirst($diaInicio);
            $nomina->dia_fin = ucfirst($diaFin);
            $nomina->save();
        }
    
        // Si la nómina está bloqueada, no permitir cambios
        if ($nomina->bloqueado) {
            return redirect()->route('manoObra.index', ['obraId' => $obraId])
                ->with('error', 'Esta nómina ya no puede ser editada.');
        }
    
        // Obtener datos del formulario
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
    
        // Eliminar detalles anteriores solo si la nómina no está bloqueada
        DetalleManoObra::where('obra_id', $obraId)->where('nomina_id', $nomina->id)->delete();
    
        $totalNomina = 0;
    
        foreach ($nombres as $index => $nombre) {
            if (!empty($nombre)) {
                $total_horas = ($lunes[$index] ?? 0) + ($martes[$index] ?? 0) + ($miercoles[$index] ?? 0) + 
                               ($jueves[$index] ?? 0) + ($viernes[$index] ?? 0) + ($sabado[$index] ?? 0);
                $subtotal = ($total_horas * ($precios_hora[$index] ?? 0)) + ($extras_menos[$index] ?? 0);
    
                // Guardar detalle de nómina
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
    
                $totalNomina += $subtotal;
            }
        }
    
        // Actualizar total solo si la nómina no está bloqueada
        if (!$nomina->bloqueado) {
            $nomina->total = $totalNomina;
            $nomina->save();
        }
    
        // Actualizar costos directos
        $costoTotal = DetalleManoObra::where('obra_id', $obraId)->sum('subtotal');
    
        CostoDirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Mano de Obra'],
            ['costo' => $costoTotal]
        );
    
        return redirect()->route('manoObra.index', ['obraId' => $obraId])
            ->with('success', 'Nómina actualizada correctamente.');
    }
    
    public function resumen($obraId)
    {
        // Obtener las nóminas asociadas a la obra filtrando por obra_id
        $nominas = Nomina::where('obra_id', $obraId)->orderBy('fecha_inicio', 'asc')->get();

        // Obtener los destajos asociados a cada nómina
        foreach ($nominas as $nomina) {
            $nomina->destajos = Destajo::where('obra_id', $obraId)
                ->where('nomina_id', $nomina->id)
                ->get();
        }
        
        // Pasar las nóminas a la vista
        return view('manoObra.resumen', compact('nominas'));
    }

    public function actualizar(Request $request, $id) {
        $nomina = Nomina::findOrFail($id);
    
        // Si la nómina ya fue editada, evitar cambios
        if ($nomina->editado) {
            return response()->json(['success' => false, 'message' => 'Esta nómina ya no puede ser editada.']);
        }
    
        // Guardar solo la primera vez
        $nomina->dias_trabajados = $request->dias_trabajados;
        $nomina->observaciones = $request->observaciones;
        $nomina->editado = true; // Bloquear edición futura
        $nomina->bloqueado = true; // Evitar futuras actualizaciones
        $nomina->save();
    
        return response()->json(['success' => true]);
    }
    
}
