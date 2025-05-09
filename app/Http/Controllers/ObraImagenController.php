<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Obra;
use Carbon\Carbon;

class ObraImagenController extends Controller
{
    public function index($obraId)
    {
        $obra = Obra::findOrFail($obraId);
        
        $pagos = DB::table('calendario_pagos')
            ->where('obra_id', $obraId)
            ->get()
            ->map(function ($pago) use ($obraId) {
                $pago->fecha_pago = Carbon::parse($pago->fecha_pago)->format('d M Y');
                $pago->imagen = DB::table('calendario_pagos_imagenes')
                    ->where('calendario_pago_id', $pago->id)
                    ->first();
                return $pago;
            });
            
        return view('obra.upload_images', compact('obraId', 'pagos', 'obra'));
    }

    public function store(Request $request, $obraId)
    {
        $request->validate([
            'concepto_id' => 'required|exists:calendario_pagos,id',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $conceptoId = $request->input('concepto_id');
            $image = $request->file('foto');
            
            // Directorio en la raíz (htdocs/pagos_cliente)
            $directory = base_path('pagos_cliente');
            
            // Crear directorio si no existe
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            // Generar nombre único para la imagen
            $imageName = 'pago_'.$conceptoId.'_'.time().'.'.$image->getClientOriginalExtension();
            
            // Mover la imagen al directorio raíz
            $image->move($directory, $imageName);
            
            // Ruta para almacenar en BD
            $ruta = 'pagos_cliente/'.$imageName;
            
            // Guardar/actualizar en base de datos
            DB::table('calendario_pagos_imagenes')->updateOrInsert(
                ['calendario_pago_id' => $conceptoId],
                [
                    'obra_id' => $obraId,
                    'ruta' => $ruta,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            
            return redirect()
                ->route('obras.imagenes', ['obraId' => $obraId])
                ->with('success', 'Imagen subida correctamente.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al subir la imagen: '.$e->getMessage());
        }
    }
}