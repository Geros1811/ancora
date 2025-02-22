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
        $obra = \App\Models\Obra::find($obraId);

        // Format the date for each CajaChica
        foreach ($cajaChicas as $cajaChica) {
            $cajaChica->formatted_created_at = $cajaChica->created_at->format('Y-m-d');
        }

        if ($request->has('cajaChica')) {
            $cajaChica = CajaChica::find($request->cajaChica);
        }

        return view('cajaChica.index', compact('obraId', 'users', 'cajaChicas', 'cajaChica', 'obra'));
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
            'subtotal' => 0, // Valor por defecto para subtotal
            'cambio' => 0, // Valor por defecto para cambio
        ]);

        return redirect()->route('cajaChica.index', ['obraId' => $request->obra_id, 'cajaChica' => $cajaChica->id])
            ->with('success', 'Datos guardados exitosamente.');
    }

    public function addDetail(Request $request)
    {
        $request->validate([
            'caja_chica_id' => 'required|exists:caja_chicas,id',
            'fecha.*' => 'required|date',
            'concepto.*' => 'required|string',
            'unidad.*' => 'required|string',
            'cantidad.*' => 'required|numeric',
            'precio_unitario.*' => 'required|numeric',
            'subtotal.*' => 'required|numeric',
            'vista.*' => 'required|string',
        ]);

        $cajaChicaId = $request->input('caja_chica_id');
        $fecha = $request->input('fecha');
        $concepto = $request->input('concepto');
        $unidad = $request->input('unidad');
        $cantidad = $request->input('cantidad');
        $precioUnitario = $request->input('precio_unitario');
        $subtotal = $request->input('subtotal');
        $vista = $request->input('vista');

        // Delete existing details
        DetalleCajaChica::where('caja_chica_id', $cajaChicaId)->delete();

        // Add new details
        foreach ($fecha as $index => $value) {
            DetalleCajaChica::create([
                'caja_chica_id' => $cajaChicaId,
                'fecha' => $fecha[$index],
                'concepto' => $concepto[$index],
                'unidad' => $unidad[$index],
                'cantidad' => $cantidad[$index],
                'precio_unitario' => $precioUnitario[$index],
                'subtotal' => $subtotal[$index],
                'vista' => $vista[$index],
            ]);
        }

        // Calculate subtotal
        $subtotal = DetalleCajaChica::where('caja_chica_id', $cajaChicaId)->sum('subtotal');

        // Get cantidad from CajaChica
        $cajaChica = CajaChica::find($cajaChicaId);
        $cantidadCajaChica = $cajaChica->cantidad;

        // Calculate cambio
        $cambio = $cantidadCajaChica - $subtotal;

        // Update CajaChica
        $cajaChica->subtotal = $subtotal;
        $cajaChica->cambio = $cambio;
        $cajaChica->save();

        $obraId = $request->input('obra_id');
        return redirect()->route('cajaChica.index', ['obraId' => $obraId, 'cajaChica' => $cajaChicaId])
            ->with('success', 'Detalles guardados exitosamente.');
    }
}
