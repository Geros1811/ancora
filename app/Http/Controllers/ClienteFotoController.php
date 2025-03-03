<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Obra;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClienteFotoController extends Controller
{
    public function index(Request $request, $obraId)
    {
        $obra = Obra::findOrFail($obraId);

        $year = $request->input('year', Carbon::now('America/Mexico_City')->year);
        $month = $request->input('month', Carbon::now('America/Mexico_City')->month);

        $fechaActual = Carbon::create($year, $month, 1, 0, 0, 0, 'America/Mexico_City');

        setlocale(LC_TIME, 'es_ES.UTF-8');
        Carbon::setLocale('es');

        $mesActual = $fechaActual->translatedFormat('F Y');

        $mesAnterior = $fechaActual->copy()->subMonth();
        $mesSiguiente = $fechaActual->copy()->addMonth();

        $primerDia = $fechaActual->copy()->startOfMonth();
        $ultimoDia = $fechaActual->copy()->endOfMonth();
        $diasEnMes = $ultimoDia->day;
        $diaInicioSemana = $primerDia->dayOfWeek;

        $fotos = DB::table('fotos_cliente')
            ->whereYear('fecha', $year)
        ->whereMonth('fecha', $month)
        ->get();

    // Share the $obra variable with all views
    view()->share('obra', $obra);

    return view('cliente_fotos.index', compact('obra', 'mesActual', 'mesAnterior', 'mesSiguiente', 'diasEnMes', 'diaInicioSemana', 'fotos', 'primerDia'));
}
    public function store(Request $request, $obraId)
    {
        $request->validate([
            'fecha' => 'required|date',
            'titulo' => 'required|string|max:255',
            'imagen' => 'required|image|max:2048',
            'comentario' => 'nullable|string',
        ]);

        $rutaImagen = $request->file('imagen')->store('public/fotos_clientes');
        $nombreImagen = str_replace('public/', '', $rutaImagen);

        $obra = Obra::findOrFail($obraId);

        DB::table('fotos_cliente')->insert([
            'obra_id' => $obra->id,
            'fecha' => $request->fecha,
            'titulo' => $request->titulo,
            'imagen' => $nombreImagen,
            'comentario' => $request->comentario,
        ]);

        return redirect()->route('cliente_fotos.index', ['obraId' => $obraId])->with('success', 'Foto subida correctamente.');
    }

}
