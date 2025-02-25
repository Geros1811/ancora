<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SueldoResidenteController extends Controller
{
    public function index(Request $request, $obraId)
    {
        // Logic to fetch and display Sueldo Residente data
        return view('sueldo-residente.index', ['obraId' => $obraId]);
    }
}
