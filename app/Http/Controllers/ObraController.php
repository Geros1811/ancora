<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObraController extends Controller
{
    public function index()
    {
        $obras = Obra::all();
        return view('dashboard', compact('obras'));
    }

    public function create()
    {
        return view('obra.create');
    }

    public function store(Request $request)
{
    DB::table('obras')->insert([
        'nombre' => $request->nombre,
        'presupuesto' => $request->presupuesto,
        'cliente' => $request->cliente,
        'fecha_inicio' => $request->fecha_inicio,
        'fecha_termino' => $request->fecha_termino,
        'residente' => $request->residente,
        'ubicacion' => $request->ubicacion,
        'descripcion' => $request->descripcion,
    ]);

    return redirect()->route('dashboard')->with('success', 'Obra creada correctamente.');
}

public function show($id)
{
    $obra = Obra::findOrFail($id);
    return view('obras.show', compact('obra'));
}

public function guardarCalendario(Request $request)
{
    try {
        $pagos = $request->input('pagos'); // Array de pagos

        // Verificar que el array no esté vacío
        if (empty($pagos)) {
            \Log::error('No se han recibido pagos.');
            return response()->json(['success' => false, 'message' => 'No se han recibido pagos.']);
        }

        // Asegúrate de que el ID de la obra se pase correctamente
        $obraId = $request->input('obra_id'); // Asegúrate de enviar el id de la obra desde el formulario si es necesario

        // Eliminar los registros existentes para la obra
        DB::table('calendario_pagos')->where('obra_id', $obraId)->delete();

        foreach ($pagos as $pago) {
            // Verificar que todas las claves necesarias estén presentes
            if (!isset($pago['concepto'], $pago['fecha_pago'], $pago['pago'], $pago['acumulado'], $pago['bloqueado'])) {
                \Log::error('Faltan datos en el pago:', $pago);
                return response()->json(['success' => false, 'message' => 'Faltan datos en uno de los pagos.']);
            }

            // Registrar los datos recibidos para depuración
            \Log::info('Datos recibidos para guardar:', [
                'obra_id' => $obraId,
                'concepto' => $pago['concepto'],
                'fecha_pago' => $pago['fecha_pago'],
                'pago' => $pago['pago'],
                'acumulado' => $pago['acumulado'],
                'bloqueado' => $pago['bloqueado'],
                'ticket' => $pago['ticket'] ?? null,
            ]);

            // Guardar cada pago en la base de datos
            DB::table('calendario_pagos')->insert([
                'obra_id' => $obraId, // id de la obra correspondiente
                'concepto' => $pago['concepto'],
                'fecha_pago' => $pago['fecha_pago'],
                'pago' => $pago['pago'],
                'acumulado' => $pago['acumulado'], // Asegúrate de enviar este valor si es necesario
                'bloqueado' => $pago['bloqueado'],
                'ticket' => $pago['ticket'] ?? null, // El campo ticket es opcional, se puede omitir si no se envía
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Calendario de pagos guardado correctamente.']);
    } catch (\Exception $e) {
        // Registrar el error
        \Log::error('Error al guardar el calendario de pagos: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Hubo un problema al guardar el calendario de pagos.']);
    }
}

public function obtenerCalendarioPagos($id)
{
    $calendarioPagos = DB::table('calendario_pagos')->where('obra_id', $id)->get();
    return response()->json($calendarioPagos);
}

}
