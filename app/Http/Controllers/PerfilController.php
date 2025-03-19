<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(base_path('logos'), $imageName);
            $logoPath = 'logos/' . $imageName;
            $user->logo = $logoPath;
        }

        $user->company_name = $request->input('company_name');
        $user->numero = $request->input('numero');
        $user->correo = $request->input('correo');
        $user->direccion = $request->input('direccion');
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', 'Perfil actualizado exitosamente.');
    }
}
