<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CajaChica;

class CajaChicaController extends Controller
{
    public function index(Request $request)
    {
        $obraId = $request->obraId;
        $users = User::where('role', 'maestro_obra')->get();
        $cajaChicas = CajaChica::where('obra_id', $obraId)->get();
        return view('cajaChica.index', compact('obraId', 'users', 'cajaChicas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'maestro_obra_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'cantidad' => 'required|numeric',
            'detalles' => 'required|array',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.vista' => 'required|string',
            'detalles.*.gasto' => 'required|numeric',
            'detalles.*.foto' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $detalles = $request->detalles;
        foreach ($detalles as &$detalle) {
            if (isset($detalle['foto'])) {
                $detalle['foto'] = $detalle['foto']->store('fotos', 'public');
            }
        }

        CajaChica::create([
            'obra_id' => $request->obra_id,
            'maestro_obra_id' => $request->maestro_obra_id,
            'fecha' => $request->fecha,
            'cantidad' => $request->cantidad,
            'detalles' => json_encode($detalles),
        ]);

        return redirect()->back()->with('success', 'Datos guardados exitosamente.');
    }
}
