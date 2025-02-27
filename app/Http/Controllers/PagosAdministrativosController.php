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
            'pagosAdministrativos' => $pagosAdministrativos, // Aquí se envía
            'obra' => $obra,
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
}
