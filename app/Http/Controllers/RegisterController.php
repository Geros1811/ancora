<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membresia;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register'); // Usar la vista existente
    }
}
