<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DestajoDetalle;
use App\Models\Obra;
use App\Models\Destajo;
use App\Models\Nomina;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class DestajosDetallesController extends Controller
{
    // MÉTODOS PARA EL CASO GENERAL (sin filtro adicional por estado)

    public function show($id)
    {
        $detalle = Destajo::findOrFail($id);
        $obraId = $detalle->obra_id;
        $obra = Obra::findOrFail($obraId);

        // Datos de la nómina asociada
        $fecha_inicio  = $detalle->nomina->fecha_inicio;
        $fecha_fin     = $detalle->nomina->fecha_fin;
        $nombre_nomina = $detalle->nomina->nombre;
        $dia_inicio    = $detalle->nomina->dia_inicio;
        $dia_fin       = $detalle->nomina->dia_fin;

        // Obtener todos los detalles asociados al destajo (sin filtro de estado)
        $destajoDetalles = DestajoDetalle::where('destajo_id', $detalle->id)->get();

        // Búsqueda de destajo anterior (si se requiere)
        $previousDetalle = Destajo::where('obra_id', $obraId)
            ->where('frente', $detalle->frente)
            ->where('id', '<', $detalle->id)
            ->whereHas('detalles', function ($query) {
                $query->where('estado', 'En Curso');
            })
            ->orderBy('id', 'desc')
            ->first();

        $previousDestajoDetalles = null;
        if ($previousDetalle) {
            $previousDestajoDetalles = DestajoDetalle::where('destajo_id', $previousDetalle->id)->get();
        }

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

        $cotizaciones    = $request->input('cotizacion');
        $montosAprobados = $request->input('monto_aprobado');
        $pendientes      = $request->input('pendiente');
        $estados         = $request->input('estado');

        // Calcular totales
        $totalMontoAprobado = array_sum($montosAprobados);
        $totalCantidadPagada = 0;
        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'pago_numero_') === 0) {
                foreach ($value as $pago) {
                    $totalCantidadPagada += $pago;
                }
            }
        }

        // Actualizar totales en el registro principal
        $destajo->monto_aprobado = $totalMontoAprobado;
        $destajo->cantidad        = $totalCantidadPagada;
        $destajo->save();

        foreach ($cotizaciones as $index => $cotizacion) {
            $destajoDetalle = DestajoDetalle::where('obra_id', $obraId)
                ->where('destajo_id', $destajoId)
                ->where('cotizacion', $cotizacion)
                ->first();

            $data = [
                'obra_id'        => $obraId,
                'destajo_id'     => $destajoId,
                'cotizacion'     => $cotizacion,
                'monto_aprobado' => $montosAprobados[$index] ?? 0,
                'pendiente'      => $pendientes[$index] ?? 0,
                'estado'         => $estados[$index] ?? 'En Curso',
                'pagos'          => json_encode($this->getPagos($request, $index))
            ];

            if ($destajoDetalle) {
                $destajoDetalle->update($data);
            } else {
                DestajoDetalle::create($data);
            }
        }

        return redirect()->back()->with('success', 'Detalles guardados correctamente.');
    }

    public function generatePdf($id)
    {
        $detalle = Destajo::findOrFail($id);
        $obraId  = $detalle->obra_id;
        $obra    = Obra::findOrFail($obraId);

        $fecha_inicio  = $detalle->nomina->fecha_inicio;
        $fecha_fin     = $detalle->nomina->fecha_fin;
        $nombre_nomina = $detalle->nomina->nombre;
        $dia_inicio    = $detalle->nomina->dia_inicio;
        $dia_fin       = $detalle->nomina->dia_fin;

        $destajoDetalles = DestajoDetalle::where('destajo_id', $detalle->id)->get();

        $pdf = Pdf::loadView('destajo.pdf', compact(
            'detalle', 'obra', 'fecha_inicio', 'fecha_fin', 
            'nombre_nomina', 'dia_inicio', 'dia_fin', 'obraId', 'destajoDetalles'
        ));

        return $pdf->stream('detalles_destajo.pdf');
    }

    // ============================================================================
    // MÉTODOS PARA LOS REGISTROS PENDIENTES (filtrados por estado "En Curso")
    // Se utilizarán los mismos nombres de inputs y la misma vista para que funcione igual.
    // ============================================================================

    public function showPendiente($id)
    {
        $detalle = Destajo::findOrFail($id);
        $obraId  = $detalle->obra_id;
        $obra    = Obra::findOrFail($obraId);
    
        // Obtener únicamente los detalles con estado "En Curso"
        $destajoDetalles = DestajoDetalle::where('destajo_id', $detalle->id)
            ->where('estado', 'En Curso')
            ->get();
    
        // Forzar la edición en modo pendiente (si es lo deseado)
        $editable = true;
    
        return view('destajo.detallesdestajos', compact(
            'detalle', 'obra', 'obraId', 'destajoDetalles', 'editable'
        ));
    }

    public function storePendiente(Request $request, $obraId, $destajoId)
    {
        $destajo = Destajo::findOrFail($destajoId);

        if ($destajo->locked) {
            return redirect()->back()->with('error', 'Este destajo está bloqueado y no se puede editar.');
        }

        $cotizaciones    = $request->input('cotizacion');
        $montosAprobados = $request->input('monto_aprobado');
        $pendientes      = $request->input('pendiente');
        $estados         = $request->input('estado');

        // Calcular totales
        $totalMontoAprobado = array_sum($montosAprobados);
        $totalCantidadPagada = 0;
        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'pago_numero_') === 0) {
                foreach ($value as $pago) {
                    $totalCantidadPagada += $pago;
                }
            }
        }

        // Actualizar totales en el registro principal
        $destajo->monto_aprobado = $totalMontoAprobado;
        $destajo->cantidad        = $totalCantidadPagada;
        $destajo->save();

        foreach ($cotizaciones as $index => $cotizacion) {
            // Filtrar por estado "En Curso" para asegurarnos de trabajar sobre los pendientes
            $destajoDetalle = DestajoDetalle::where('obra_id', $obraId)
                ->where('destajo_id', $destajoId)
                ->where('cotizacion', $cotizacion)
                ->where('estado', 'En Curso')
                ->first();

            $data = [
                'obra_id'        => $obraId,
                'destajo_id'     => $destajoId,
                'cotizacion'     => $cotizacion,
                'monto_aprobado' => $montosAprobados[$index] ?? 0,
                'pendiente'      => $pendientes[$index] ?? 0,
                'estado'         => $estados[$index] ?? 'En Curso',
                'pagos'          => json_encode($this->getPagos($request, $index))
            ];

            if ($destajoDetalle) {
                $destajoDetalle->update($data);
            } else {
                DestajoDetalle::create($data);
            }
        }

        return redirect()->back()->with('success', 'Detalles guardados correctamente.');
    }

    public function generatePdfPendiente($id)
    {
        $detalle = Destajo::findOrFail($id);
        $obraId  = $detalle->obra_id;
        $obra    = Obra::findOrFail($obraId);

        // Obtener solo los detalles con estado "En Curso"
        $destajoDetalles = DestajoDetalle::where('destajo_id', $detalle->id)
            ->where('estado', 'En Curso')
            ->get();

        $pdf = Pdf::loadView('destajo.pdf', compact(
            'detalle', 'obra', 'obraId', 'destajoDetalles'
        ));

        return $pdf->stream('detalles_destajo_pendiente.pdf');
    }

    // ============================================================================
    // Método privado para extraer los pagos del request (sin cambios)
    // ============================================================================

    private function getPagos(Request $request, $index)
    {
        $pagos = [];
        foreach ($request->input() as $key => $value) {
            if (strpos($key, 'pago_fecha_') === 0) {
                $parts = explode('_', $key);
                $pagoNumber = $parts[2];
                if (isset($request->input("pago_fecha_$pagoNumber")[$index])) {
                    $pagos[$pagoNumber] = [
                        'fecha'  => $request->input("pago_fecha_$pagoNumber")[$index],
                        'numero' => $request->input("pago_numero_$pagoNumber")[$index] ?? null,
                    ];
                }
            }
        }
        return $pagos;
    }
}