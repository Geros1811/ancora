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
        $detalle = $this->getDestajoDetailsById($id);
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

        $editable = !$detalle->locked;

        return view('destajo.detallesdestajos', compact(
            'detalle', 'obra', 'fecha_inicio', 'fecha_fin', 'nombre_nomina',
            'dia_inicio', 'dia_fin', 'obraId', 'destajoDetalles',
            'editable'
        ));
    }

    private function getDestajoDetailsById($id)
    {
        return Destajo::findOrFail($id);
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

    public function exportarDestajos(Request $request, $obraId)
    {
        $nominaId = $request->nomina_id;

        // Obtener la nómina actual
        $currentNomina = Nomina::findOrFail($nominaId);

        // Obtener la siguiente nómina
        $nextNomina = Nomina::where('obra_id', $obraId)
            ->where('fecha_inicio', '>', $currentNomina->fecha_fin)
            ->orderBy('fecha_inicio', 'asc')
            ->first();

        if (!$nextNomina) {
            return redirect()->route('destajos.index', ['obraId' => $obraId])
                             ->with('error', 'No hay nóminas con fechas posteriores.');
        }

        // Obtener los destajos actuales
        $destajos = Destajo::where('obra_id', $obraId)
            ->where('nomina_id', $nominaId)
            ->get();

        // Crear nuevos destajos para la siguiente nómina
        foreach ($destajos as $destajo) {
            $newDestajo = $destajo->replicate();
            $newDestajo->nomina_id = $nextNomina->id;
            $newDestajo->monto_aprobado = 0;
            $newDestajo->cantidad = 0;
            $newDestajo->locked = false;
            $newDestajo->save();

            // Replicar los detalles del destajo
            $destajoDetalles = DestajoDetalle::where('destajo_id', $destajo->id)->get();
            foreach ($destajoDetalles as $detalle) {
                $newDetalle = $detalle->replicate();
                $newDetalle->destajo_id = $newDestajo->id;
                $newDetalle->monto_aprobado = 0;
                $newDetalle->pendiente = $detalle->monto_aprobado;
                $newDetalle->estado = 'En Curso';
                $newDetalle->pagos = json_encode([]);
                $newDetalle->save();
            }
        }

        return redirect()->route('destajos.index', ['obraId' => $obraId])
                         ->with('success', 'Destajos exportados correctamente.');
    }

    public function exportar(Request $request, $obraId, $destajoId)
    {
        $destajo = Destajo::findOrFail($destajoId);
        $currentNomina = $destajo->nomina;

        // Obtener la siguiente nómina
        $nextNomina = Nomina::where('obra_id', $obraId)
            ->where('fecha_inicio', '>', $currentNomina->fecha_fin)
            ->orderBy('fecha_inicio', 'asc')
            ->first();

        if (!$nextNomina) {
            return redirect()->back()->with('error', 'No hay nóminas con fechas posteriores.');
        }

        // Crear nuevo destajo para la siguiente nómina
        $newDestajo = $destajo->replicate();
        $newDestajo->nomina_id = $nextNomina->id;
        $newDestajo->monto_aprobado = 0;
        $newDestajo->cantidad = 0;
        $newDestajo->locked = false;
        $newDestajo->save();

        // Replicar los detalles del destajo
        $destajoDetalles = DestajoDetalle::where('destajo_id', $destajo->id)->get();
        foreach ($destajoDetalles as $detalle) {
            $newDetalle = $detalle->replicate();
            $newDetalle->destajo_id = $newDestajo->id;
            $newDetalle->monto_aprobado = $detalle->monto_aprobado;
            $newDetalle->pendiente = $detalle->monto_aprobado;
            $newDetalle->estado = 'En Curso';
            $newDetalle->pagos = $detalle->pagos; // Mantener los pagos
            $newDetalle->save();
        }

        return redirect()->route('destajos.detalles', ['id' => $newDestajo->id])
                         ->with('success', 'Detalles exportados correctamente.');
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
