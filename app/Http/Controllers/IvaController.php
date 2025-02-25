<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IvaController extends Controller
{
    public function index(Request $request, $obraId)
    {
        // Logic to fetch and display IVA data
        return view('iva.index', ['obraId' => $obraId]);
    }
}
