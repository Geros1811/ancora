<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarioPago;

class CalendarioPagoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'concepto' => 'required|string|max:255',
            'fecha_pago' => 'nullable|date',
            'pago' => 'required|numeric|min:0',
            'ticket' => 'nullable|image|max:2048', // Máximo 2MB
        ]);

        if ($request->hasFile('ticket')) {
            $validated['ticket'] = $request->file('ticket')->store('tickets', 'public');
        }

        $pago = CalendarioPago::create($validated);
        return response()->json(['success' => true, 'data' => $pago]);
    }

    public function update(Request $request, $id)
    {
        $pago = CalendarioPago::findOrFail($id);

        $validated = $request->validate([
            'concepto' => 'nullable|string|max:255',
            'fecha_pago' => 'nullable|date',
            'pago' => 'nullable|numeric|min:0',
            'ticket' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('ticket')) {
            $validated['ticket'] = $request->file('ticket')->store('tickets', 'public');
        }

        $pago->update($validated);
        return response()->json(['success' => true, 'data' => $pago]);
    }

    public function destroy($id)
    {
        $pago = CalendarioPago::findOrFail($id);
        $pago->delete();

        return response()->json(['success' => true]);
    }
}
