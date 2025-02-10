<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DestajoDetalle;
use Illuminate\Support\Facades\Log;

use App\Models\Obra;
use App\Models\Destajo;

class DestajosDetallesController extends Controller
{
    public function show($id)
    {
        $detalle = Destajo::findOrFail($id);
        $obraId = $detalle->obra_id;
        $obra = Obra::findOrFail($obraId);

        // Access fecha_inicio and fecha_fin from the related Nomina model
        $fecha_inicio = $detalle->nomina->fecha_inicio;
        $fecha_fin = $detalle->nomina->fecha_fin;
        $nombre_nomina = $detalle->nomina->nombre;
        $dia_inicio = $detalle->nomina->dia_inicio;
        $dia_fin = $detalle->nomina->dia_fin;

        return view('destajo.detallesdestajos', compact('detalle', 'obra', 'fecha_inicio', 'fecha_fin', 'nombre_nomina', 'dia_inicio', 'dia_fin'));
    }

    public function store(Request $request, $obraId)
    {
        // This method will be used later
    }
}
