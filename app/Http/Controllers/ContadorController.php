<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContadorController extends Controller
{
    public function index(Request $request, $obraId)
    {
        // Logic to fetch and display Contador data
        return view('contador.index', ['obraId' => $obraId]);
    }
}
