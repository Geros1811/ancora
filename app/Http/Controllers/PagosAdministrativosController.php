<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obra;

class PagosAdministrativosController extends Controller
{
    public function index($obraId)
    {
        $obra = Obra::findOrFail($obraId);

        $pagosAdministrativos = [
            [
                'nombre' => 'Sueldo Residente',
                'costo' => 0.00,
                'link' => route('sueldo-residente.index', ['obraId' => $obra->id]),
            ],
            [
                'nombre' => 'IMSS',
                'costo' => 0.00,
                'link' => route('imss.index', ['obraId' => $obra->id]),
            ],
            [
                'nombre' => 'Contador',
                'costo' => 0.00,
                'link' => route('contador.index', ['obraId' => $obra->id]),
            ],
            [
                'nombre' => 'IVA',
                'costo' => 0.00,
                'link' => route('iva.index', ['obraId' => $obra->id]),
            ],
        ];

        return view('obra.pagos-administrativos', ['pagosAdministrativos' => $pagosAdministrativos, 'obra' => $obra]);
    }
}
