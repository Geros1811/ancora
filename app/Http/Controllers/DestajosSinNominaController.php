<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DestajosSinNominaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $obraId)
    {
        $partidas = \App\Models\Partida::where('obra_id', $obraId)->get();
        return view('destajoSinNomina.index', ['obraId' => $obraId, 'partidas' => $partidas]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $obraId)
    {
        $partida = new \App\Models\Partida();
        $partida->obra_id = $obraId;
        $partida->title = $request->input('partida_title');
        $partida->save();

        return redirect()->route('destajosSinNomina.index', ['obraId' => $obraId]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
