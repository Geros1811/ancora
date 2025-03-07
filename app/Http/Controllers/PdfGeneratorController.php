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
use App\Models\DetallePapeleria;
use App\Models\Obra;

class PdfGeneratorController extends Controller
{
    public function showSelectMonthForm($obraId)
    {
        return view('pdf.select_month', compact('obraId'));
    }

    public function generatePdf(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $obraId = $request->input('obraId');

        $obra = Obra::findOrFail($obraId);

        $nominas = Nomina::where('obra_id', $obraId)->whereMonth('fecha_inicio', $month)->whereYear('fecha_inicio', $year)->get();

        $costoMensualTotal = 0;
        $ingresoMensual = 0;

        // Calculate the total cost for each nomina
        foreach ($nominas as $nomina) {
            $costoMensualTotal += $nomina->total;
            $nomina->destajos = Destajo::where('obra_id', $obraId)
                ->where('nomina_id', $nomina->id)
                ->get();
        }

        $acarreos = DetalleAcarreos::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($acarreos as $acarreo) {
            $costoMensualTotal += $acarreo->subtotal;
        }

        $cimbras = DetalleCimbras::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($cimbras as $cimbra) {
            $costoMensualTotal += $cimbra->subtotal;
        }

        $maquinariaMayor = DetalleMaquinariaMayor::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($maquinariaMayor as $maquinaria) {
            $costoMensualTotal += $maquinaria->subtotal;
        }

        $utilidades = DetalleUtilidades::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($utilidades as $utilidad) {
            $costoMensualTotal += $utilidad->subtotal;
        }

        $tramites = DetalleTramites::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($tramites as $tramite) {
            $costoMensualTotal += $tramite->subtotal;
        }

        $rentas = DetalleRentas::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($rentas as $renta) {
            $costoMensualTotal += $renta->subtotal;
        }

        $rentaMaquinaria = RentaMaquinaria::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($rentaMaquinaria as $renta) {
            $costoMensualTotal += $renta->subtotal;
        }

        $agregados = Agregado::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($agregados as $agregado) {
            $costoMensualTotal += $agregado->subtotal;
        }

        $aceros = Acero::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($aceros as $acero) {
            $costoMensualTotal += $acero->subtotal;
        }

        $cemento = Cemento::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($cemento as $c) {
            $costoMensualTotal += $c->subtotal;
        }

        $losas = Losa::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($losas as $losa) {
            $costoMensualTotal += $losa->subtotal;
        }

        $generales = General::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($generales as $general) {
            $costoMensualTotal += $general->subtotal;
        }

        $maquinariaMenor = DetalleMaquinariaMenor::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($maquinariaMenor as $maquinaria) {
            $costoMensualTotal += $maquinaria->subtotal;
        }

        $comidas = DetalleComidas::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($comidas as $comida) {
            $costoMensualTotal += $comida->subtotal;
        }

        $equipoSeguridad = DetalleEquipoSeguridad::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($equipoSeguridad as $equipo) {
            $costoMensualTotal += $equipo->subtotal;
        }

        $gasolina = DetalleGasolina::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($gasolina as $gas) {
            $costoMensualTotal += $gas->subtotal;
        }

        $herramientaMenor = DetalleHerramientaMenor::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($herramientaMenor as $herramienta) {
            $costoMensualTotal += $herramienta->subtotal;
        }

        $limpieza = DetalleLimpieza::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($limpieza as $lim) {
            $costoMensualTotal += $lim->subtotal;
        }

        $sueldoResidente = SueldoResidente::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($sueldoResidente as $sueldo) {
            $costoMensualTotal += $sueldo->importe;
        }

        $imss = Imss::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($imss as $i) {
            $costoMensualTotal += $i->importe;
        }

        $contador = Contador::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($contador as $c) {
            $costoMensualTotal += $c->importe;
        }

        $iva = Iva::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($iva as $i) {
            $costoMensualTotal += $i->importe;
        }

        $otrosPagos = OtrosPagosAdministrativos::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($otrosPagos as $otro) {
            $costoMensualTotal += $otro->importe;
        }

        $papeleria = DetallePapeleria::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($papeleria as $p) {
            $costoMensualTotal += $p->subtotal;
        }

        $ingresos = Ingreso::where('obra_id', $obraId)->whereMonth('fecha', $month)->whereYear('fecha', $year)->get();
        foreach ($ingresos as $ingreso) {
            $ingresoMensual += $ingreso->importe;
        }

        $data = [
            'obra' => $obra,
            'title' => 'General PDF - ' . $this->getMonthName($month) . ' ' . $year,
            'date' => date('m/d/Y'),
            'month' => $month,
            'year' => $year,
            'costoMensualTotal' => $costoMensualTotal,
            'ingresoMensual' => $ingresoMensual,
            'acarreos' => $acarreos,
            'cimbras' => $cimbras,
            'maquinariaMayor' => $maquinariaMayor,
            'utilidades' => $utilidades,
            'tramites' => $tramites,
            'rentas' => $rentas,
            'rentaMaquinaria' => $rentaMaquinaria,
            'agregados' => $agregados,
            'aceros' => $aceros,
            'cemento' => $cemento,
            'losas' => $losas,
            'generales' => $generales,
            'maquinariaMenor' => $maquinariaMenor,
            'comidas' => $comidas,
            'equipoSeguridad' => $equipoSeguridad,
            'gasolina' => $gasolina,
            'herramientaMenor' => $herramientaMenor,
            'limpieza' => $limpieza,
            'nominas' => $nominas,
            'sueldoResidente' => $sueldoResidente,
            'imss' => $imss,
            'contador' => $contador,
            'iva' => $iva,
            'otrosPagos' => $otrosPagos,
            'papeleria' => $papeleria,
            'ingresos' => $ingresos,
        ];

        $pdf = PDF::loadView('pdf.general', $data, ['obra' => $obra]);

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
