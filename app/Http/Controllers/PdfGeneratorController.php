<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\DetalleAcarreos;
use App\Models\DetalleCimbras;
use App\Models\DetalleMaquinariaMayor;
use App\Models\DetalleUtilidades;
use App\Models\DetalleTramites;
use App\Models\DetalleRentas;
use App\Models\RentaMaquinaria;
use App\Models\Agregado;
use App\Models\Acero;
use App\Models\Cemento;
use App\Models\Losa;
use App\Models\General;
use App\Models\DetalleMaquinariaMenor;
use App\Models\DetalleComidas;
use App\Models\DetalleEquipoSeguridad;
use App\Models\DetalleGasolina;
use App\Models\DetalleHerramientaMenor;
use App\Models\Ingreso;
use App\Models\DetalleLimpieza;
use App\Models\DetalleManoObra;
use App\Models\Nomina;
use App\Models\Destajo;
use App\Models\SueldoResidente;
use App\Models\Imss;
use App\Models\Contador;
use App\Models\Iva;
use App\Models\OtrosPagosAdministrativos;

class PdfGeneratorController extends Controller
{
    public function showSelectMonthForm($obraId)
    {
        return view('pdf.select_month', compact('obraId'));
    }

    public function generatePdf(Request $request)
    {
        $month = $request->input('month');
        $obraId = $request->input('obraId');

        $data = [
            'title' => 'General PDF - ' . $this->getMonthName($month),
            'date' => date('m/d/Y'),
            'month' => $month,
            'acarreos' => DetalleAcarreos::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'cimbras' => DetalleCimbras::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'maquinariaMayor' => DetalleMaquinariaMayor::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'utilidades' => DetalleUtilidades::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'tramites' => DetalleTramites::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'rentas' => DetalleRentas::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'rentaMaquinaria' => RentaMaquinaria::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'agregados' => Agregado::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'aceros' => Acero::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'cemento' => Cemento::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'losas' => Losa::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'generales' => General::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'maquinariaMenor' => DetalleMaquinariaMenor::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'comidas' => DetalleComidas::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'equipoSeguridad' => DetalleEquipoSeguridad::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'gasolina' => DetalleGasolina::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'herramientaMenor' => DetalleHerramientaMenor::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'ingresos' => Ingreso::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'limpieza' => DetalleLimpieza::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
           
            'nominas' => Nomina::where('obra_id', $obraId)->whereMonth('fecha_inicio', $month)->get(),
           
            'sueldoResidente' => SueldoResidente::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'imss' => Imss::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'contador' => Contador::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'iva' => Iva::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
            'otrosPagos' => OtrosPagosAdministrativos::where('obra_id', $obraId)->whereMonth('fecha', $month)->get(),
        ];

        $pdf = PDF::loadView('pdf.general', $data);

        return $pdf->stream('general.pdf');
    }

    public static function getMonthName($month)
    {
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return $months[$month];
    }
}
