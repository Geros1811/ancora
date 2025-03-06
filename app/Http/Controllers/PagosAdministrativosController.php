<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PagosAdministrativosController extends Controller
{
    public function index($obraId)
    {
        $obra = Obra::findOrFail($obraId);

        $pagosAdministrativosOcultos = 0;
        $pagosBD = DB::table('pagos_administrativos')
            ->where('obra_id', $obraId)
            ->pluck('costo', 'nombre');

        foreach ($pagosBD as $nombre => $costo) {
            if (Session::get('pagos_administrativos.' . $nombre) === false) {
                $pagosAdministrativosOcultos += $costo;
            }
        }

        // Obtener los pagos administrativos desde la base de datos
        $pagosBD = DB::table('pagos_administrativos')
            ->where('obra_id', $obraId)
            ->pluck('costo', 'nombre'); // Devuelve un array asociativo con nombre => costo

        // Lista de pagos con costos actualizados
        $pagosAdministrativos = [
            [
                'nombre' => 'Sueldo Residente',
                'costo' => $pagosBD['Sueldo Residente'] ?? 0.00, // Si no existe, usa 0.00
                'link' => route('sueldo-residente.index', ['obraId' => $obra->id]),
            ],
            [
                'nombre' => 'IMSS',
                'costo' => $pagosBD['IMSS'] ?? 0.00,
                'link' => route('imss.index', ['obraId' => $obra->id]),
            ],
            [
                'nombre' => 'Contador',
                'costo' => $pagosBD['Contador'] ?? 0.00,
                'link' => route('contador.index', ['obraId' => $obra->id]),
            ],
            [
                'nombre' => 'IVA',
                'costo' => $pagosBD['IVA'] ?? 0.00,
                'link' => route('iva.index', ['obraId' => $obra->id]),
            ],
            [
                'nombre' => 'Otros Pagos Administrativos',
                'costo' => $pagosBD['Otros Pagos Administrativos'] ?? 0.00,
                'link' => route('otros-pagos-administrativos.index', ['obraId' => $obra->id]),
            ],
        ];

        $totalPagosAdministrativos = array_sum(array_column($pagosAdministrativos, 'costo'));

        return view('obra.pagos-administrativos', [
            'pagosAdministrativos' => $pagosAdministrativos,
            'obra' => $obra,
            'obraId' => $obra->id, // 🔹 Asegúrate de incluir esto
            'pagosAdministrativosOcultos' => $pagosAdministrativosOcultos,
            'totalPagosAdministrativos' => $totalPagosAdministrativos,
        ]);
    }

    public function togglePago(Request $request)
    {
        $nombre = $request->input('nombre');
        $active = $request->input('active');

        Session::put('pagos_administrativos.' . $nombre, $active);

        return response()->json(['success' => true]);
    }

    public function generateConsolidatedPdf($obraId)
    {
        $obra = Obra::findOrFail($obraId);

        $sueldoResidente = \App\Models\SueldoResidente::where('obra_id', $obraId)->get();
        $imss = \App\Models\Imss::where('obra_id', $obraId)->get();
        $contador = \App\Models\Contador::where('obra_id', $obraId)->get();
        $iva = \App\Models\Iva::where('obra_id', $obraId)->get();
        $otrosPagos = \App\Models\OtrosPagosAdministrativos::where('obra_id', $obraId)->get();

        $costoTotal = 0;
        if ($sueldoResidente) {
            $costoTotal += $sueldoResidente->sum('importe');
        }
        if ($imss) {
            $costoTotal += $imss->sum('importe');
        }
        if ($contador) {
            $costoTotal += $contador->sum('importe');
        }
        if ($iva) {
            $costoTotal += $iva->sum('importe');
        }
        if ($otrosPagos) {
            $costoTotal += $otrosPagos->sum('importe');
        }

        $data = [
            'obra' => $obra,
            'sueldoResidente' => $sueldoResidente,
            'imss' => $imss,
            'contador' => $contador,
            'iva' => $iva,
            'otrosPagos' => $otrosPagos,
            'costoTotal' => $costoTotal,
            'obraId' => $obraId,
        ];

        $pdf = \PDF::loadView('pagosAdministrativos.consolidated_pdf', $data);

        // Prevent automatic download - stream the PDF to the browser
        return $pdf->stream('pagosAdministrativos_consolidated.pdf');
    }
}
