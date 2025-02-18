<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarioPago;
use App\Models\CalendarioTicket;
use App\Models\Obra;

class ArchivoController extends Controller
{

    public function show($id)
{
    $calendarioPagos = CalendarioPago::where('obra_id', $id)->get(); // Obtén todos los pagos de la obra
    $obra = Obra::findOrFail($id);
    return view('nombre_de_tu_vista', compact('calendarioPagos', 'obra'));
}



    public function store(Request $request)
{
    // Validar los datos
    $request->validate([
        'calendario_pago_id' => 'required|exists:calendario_pagos,id',
        'archivo' => 'required|file|mimes:jpeg,png|max:10240', // Máximo 10MB
    ]);

    // Guardar el archivo en el almacenamiento
    $archivoPath = $request->file('archivo')->store('calendarios-tickets', 'public');

    // Crear el registro en la tabla `calendarios_tickets`
    CalendarioTicket::create([
        'calendario_pago_id' => $request->calendario_pago_id,
        'ruta_archivo' => $archivoPath,
    ]);

    return back()->with('success', 'Imagen subida correctamente.');
}

}
