<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destajo;
use Illuminate\Support\Facades\Log;
use App\Models\Nomina; // Asegúrate de que este es el nombre correcto del modelo

class DestajoController extends Controller
{
    public function index($obraId)
    {
        // Recuperar todas las nóminas de la obra
        $nominas = Nomina::where('obra_id', $obraId)->get();
        
        // Recuperar los destajos de la obra
        $detalles = Destajo::where('obra_id', $obraId)->get();
        
        return view('destajo.index', compact('detalles', 'obraId', 'nominas'));
    }

    public function store(Request $request, $obraId)
    {
        // Validaciones
        $request->validate([
            'nomina_id' => 'required|array',
            'nomina_id.*' => 'integer',
            'frente' => 'required|array',
            'frente.*' => 'string',
            'cantidad' => 'required|array',
            'cantidad.*' => 'numeric',
            'monto_aprobado' => 'required|array',
            'monto_aprobado.*' => 'numeric',
            'paso_actual' => 'required|array',
            'paso_actual.*' => 'string',
        ]);
        
        if (!$request->has('nomina_id')) {
            return back()->with('error', 'No se recibió el ID de nómina.');
        }

        try {
            $frentes = $request->input('frente', []);
            $cantidades = $request->input('cantidad', []);
            $montoAprobado = $request->input('monto_aprobado', []);
            $nomina_id = $request->input('nomina_id');
            $no_pago = $request->input('no_pago', ''); // Asegúrate de que este campo se maneje
            $paso_actual = $request->input('paso_actual'); // Capture the paso_actual field

            foreach ($frentes as $index => $frente) {
                // Si el frente es "Otros", usar el valor de frente_custom
                if ($frente === "Otros") {
                    $frente = $request->input('frente_custom')[$index] ?? 'Otros';
                }

                $cantidad = $cantidades[$index] ?? 0;
                $subtotal = $montoAprobado[$index] ?? 0;

                // Validar que la cantidad y el monto aprobado no sean nulos
                if ($cantidad === null || $subtotal === null) {
                    continue; // O puedes lanzar un error
                }

                // Crear el nuevo registro
                $detalle = new Destajo();
                $detalle->obra_id = $obraId;
                $detalle->nomina_id = $nomina_id;
                $detalle->frente = $frente; // Aquí se asigna el valor correcto
                $detalle->no_pago = $no_pago; // Asegúrate de que este campo se maneje
                $detalle->paso_actual = $paso_actual; // Save the paso_actual field
                $detalle->cantidad = $cantidad;
                $detalle->monto_aprobado = $subtotal; // Asegúrate de que este campo exista en tu modelo
                $detalle->save(); // Guardar el registro
            }

            Log::info('Datos recibidos en store: ' . json_encode($request->all())); // Log the incoming data
            return back()->with('success', 'Destajos guardados exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al guardar destajos: ' . $e->getMessage()); // Log the error message
            return back()->with('error', 'Hubo un error al guardar los destajos. Por favor, intente nuevamente.');
        }
    }
}
