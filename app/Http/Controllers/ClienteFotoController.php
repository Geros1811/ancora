<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obra;
use Illuminate\Support\Facades\Auth;

class ClienteFotoController extends Controller
{
    public function index()
    {
        $obra = Obra::where('cliente', Auth::id())->first();
        return view('cliente_fotos.index', compact('obra'));
    }
}
