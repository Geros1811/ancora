<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CajaChica;
use App\Models\DetalleCajaChica;

class CajaChicaController extends Controller
{
    public function index(Request $request)
    {
        $obraId = $request->obraId;
        $users = User::where('role', 'maestro_obra')->get();
        $cajaChicas = CajaChica::where('obra_id', $obraId)->get();
        $cajaChica = null;

        if ($request->has('cajaChica')) {
            $cajaChica = CajaChica::find($request->cajaChica);
        }

        return view('cajaChica.index', compact('obraId', 'users', 'cajaChicas', 'cajaChica'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'maestro_obra_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'cantidad' => 'required|numeric',
        ]);

        $cajaChica = CajaChica::create([
            'obra_id' => $request->obra_id,
            'maestro_obra_id' => $request->maestro_obra_id,
            'fecha' => $request->fecha,
            'cantidad' => $request->cantidad,
        ]);

        return redirect()->route('cajaChica.index', ['obraId' => $request->obra_id, 'cajaChica' => $cajaChica->id])
            ->with('success', 'Datos guardados exitosamente.');
    }

    public function addDetail(Request $request)
    {
        $request->validate([
            'caja_chica_id' => 'required|exists:caja_chicas,id',
            'descripcion' => 'required|string',
            'vista' => 'required|string',
            'gasto' => 'required|numeric',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos', 'public');
        }

        DetalleCajaChica::create([
            'caja_chica_id' => $request->caja_chica_id,
            'descripcion' => $request->descripcion,
            'vista' => $request->vista,
            'gasto' => $request->gasto,
            'foto' => $fotoPath,
        ]);

        $obraId = $request->obra_id;
        $cajaChicaId = $request->caja_chica_id;
        return redirect()->route('cajaChica.index', ['obraId' => $obraId, 'cajaChica' => $cajaChicaId])
                         ->with('success', 'Detalle guardado exitosamente.');
    }
}
