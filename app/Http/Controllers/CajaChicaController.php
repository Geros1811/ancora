<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CajaChicaController extends Controller
{
    public function index(Request $request)
    {
        $obraId = $request->obraId;
        return view('cajaChica.index', compact('obraId'));
    }
}
