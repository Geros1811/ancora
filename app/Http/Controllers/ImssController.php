<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImssController extends Controller
{
    public function index(Request $request, $obraId)
    {
        // Logic to fetch and display IMSS data
        return view('imss.index', ['obraId' => $obraId]);
    }
}
