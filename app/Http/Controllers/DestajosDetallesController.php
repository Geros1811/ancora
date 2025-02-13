<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DestajoDetalle;
use Illuminate\Support\Facades\Log;
use App\Models\Obra;
use App\Models\Destajo;
use App\Models\Nomina;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class DestajosDetallesController extends Controller
{
    public function show($id)
{
    $detalle = Destajo::findOrFail($id);
    $obraId = $detalle->obra_id;
    $obra = Obra::findOrFail($obraId);

    // Datos de la nómina asociada
    $fecha_inicio = $detalle->nomina->fecha_inicio;
    $fecha_fin = $detalle->nomina->fecha_fin;
    $nombre_nomina = $detalle->nomina->nombre;
    $dia_inicio = $detalle->nomina->dia_inicio;
    $dia_fin = $detalle->nomina->dia_fin;

    // Obtener detalles del destajo actual
    $destajoDetalles = DestajoDetalle::where('destajo_id', $detalle->id)->get();

    // Buscar un destajo anterior con el mismo frente que esté en curso
    $previousDetalle = Destajo::where('obra_id', $obraId)
        ->where('frente', $detalle->frente) // Asegurar que es el mismo frente
        ->where('id', '<', $detalle->id) // Buscar anteriores
        ->whereHas('detalles', function ($query) {
            $query->where('estado', 'En Curso');
        })
        ->orderBy('id', 'desc')
        ->first();

    $previousDestajoDetalles = null;
    if ($previousDetalle) {
        $previousDestajoDetalles = DestajoDetalle::where('destajo_id', $previousDetalle->id)->get();
    }

    // Determinar si se puede editar
    $editable = !$detalle->locked;

    return view('destajo.detallesdestajos', compact(
        'detalle', 'obra', 'fecha_inicio', 'fecha_fin', 'nombre_nomina', 
        'dia_inicio', 'dia_fin', 'obraId', 'destajoDetalles', 
        'editable', 'previousDetalle', 'previousDestajoDetalles'
    ));
}

    public function store(Request $request, $obraId, $destajoId)
    {
        $destajo = Destajo::findOrFail($destajoId);

        if ($destajo->locked) {
            return redirect()->back()->with('error', 'Este destajo está bloqueado y no se puede editar.');
        }

        $cotizaciones = $request->input('cotizacion');
        $montosAprobados = $request->input('monto_aprobado');
        $pendientes = $request->input('pendiente');
        $estados = $request->input('estado');

        // Calculate total amount approved and total amount paid
        $totalMontoAprobado = array_sum($montosAprobados);
        $totalCantidadPagada = 0;
        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'pago_numero_') === 0) {
                foreach($value as $pago){
                    $totalCantidadPagada += $pago;
                }
            }
        }

        // Find the Destajo model
        $destajo = Destajo::findOrFail($destajoId);

        // Update the Destajo model with the totals
        $destajo->monto_aprobado = $totalMontoAprobado;
        $destajo->cantidad = $totalCantidadPagada;
        $destajo->save();

        foreach ($cotizaciones as $index => $cotizacion) {
            $destajoDetalle = DestajoDetalle::where('obra_id', $obraId)
                ->where('destajo_id', $destajoId)
                ->where('cotizacion', $cotizacion)
                ->first();

            if ($destajoDetalle) {
                // Update existing record
                $destajoDetalle->monto_aprobado = $montosAprobados[$index] ?? 0;
                $destajoDetalle->pendiente = $pendientes[$index] ?? 0;
                $destajoDetalle->estado = $estados[$index] ?? 'En Curso';
                $destajoDetalle->pagos = json_encode($this->getPagos($request, $index));
                $destajoDetalle->save();
            } else {
                // Create new record
                DestajoDetalle::create([
                    'obra_id' => $obraId,
                    'destajo_id' => $destajoId,
                    'cotizacion' => $cotizacion,
                    'monto_aprobado' => $montosAprobados[$index] ?? 0,
                    'pendiente' => $pendientes[$index] ?? 0,
                    'estado' => $estados[$index] ?? 'En Curso',
                    'pagos' => json_encode($this->getPagos($request, $index))
                ]);
            }
              }

        return redirect()->back()->with('success', 'Detalles guardados correctamente.');
    }

    public function generatePdf($id)
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

        $destajoDetalles = DestajoDetalle::where('destajo_id', $detalle->id)->get();

        $pdf = Pdf::loadView('destajo.pdf', compact('detalle', 'obra', 'fecha_inicio', 'fecha_fin', 'nombre_nomina', 'dia_inicio', 'dia_fin', 'obraId', 'destajoDetalles'));

        return $pdf->stream('detalles_destajo.pdf');
    }

    private function getPagos(Request $request, $index)
    {
        $pagos = [];
        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'pago_fecha_') === 0) {
                $parts = explode('_', $key);
                $pagoNumber = $parts[2];
                if (isset($request->input("pago_fecha_$pagoNumber")[$index])) {
                    $pagos[$pagoNumber] = [
                        'fecha' => $request->input("pago_fecha_$pagoNumber")[$index],
                        'numero' => $request->input("pago_numero_$pagoNumber")[$index] ?? null,
                    ];
                }
            }
        }
        return $pagos;
    }
}
