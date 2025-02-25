<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CajaChica;
use App\Models\DetalleCajaChica;

class CajaChicaController extends Controller
{
    public function index(Request $request)
    {
        $obraId = $request->obraId;
        $users = User::where('role', 'maestro_obra')->get();
        $cajaChicas = CajaChica::where('obra_id', $obraId)->get();
        $cajaChica = null;
        $obra = \App\Models\Obra::find($obraId);

        // Format the date for each CajaChica
        foreach ($cajaChicas as $cajaChica) {
            $cajaChica->formatted_created_at = $cajaChica->created_at->format('Y-m-d');
        }

        if ($request->has('cajaChica')) {
            $cajaChica = CajaChica::find($request->cajaChica);
        }

        return view('cajaChica.index', compact('obraId', 'users', 'cajaChicas', 'cajaChica', 'obra'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'maestro_obra_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'cantidad' => 'required|numeric',
        ]);

        $cajaChica = CajaChica::create([
            'obra_id' => $request->obra_id,
            'maestro_obra_id' => $request->maestro_obra_id,
            'fecha' => $request->fecha,
            'cantidad' => $request->cantidad,
            'subtotal' => 0, // Valor por defecto para subtotal
            'cambio' => 0, // Valor por defecto para cambio
        ]);

        return redirect()->route('cajaChica.index', ['obraId' => $request->obra_id, 'cajaChica' => $cajaChica->id])
            ->with('success', 'Datos guardados exitosamente.');
    }

    public function addDetail(Request $request)
    {
        $request->validate([
            'id.*' => 'nullable|exists:detalle_caja_chicas,id',
            'caja_chica_id' => 'required|exists:caja_chicas,id',
            'fecha.*' => 'required|date',
            'concepto.*' => 'required|string',
            'unidad.*' => 'required|string',
            'cantidad.*' => 'required|numeric',
            'precio_unitario.*' => 'required|numeric',
            'subtotal.*' => 'required|numeric',
            'vista.*' => 'required|string',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $cajaChicaId = $request->input('caja_chica_id');
        $ids = $request->input('id');
        $fecha = $request->input('fecha');
        $concepto = $request->input('concepto');
        $unidad = $request->input('unidad');
        $cantidad = $request->input('cantidad');
        $precioUnitario = $request->input('precio_unitario');
        $subtotal = $request->input('subtotal');
        $vista = $request->input('vista');
        $fotos = $request->file('foto');

        foreach ($fecha as $index => $value) {
            $detalleId = $ids[$index] ?? null;

            if ($detalleId) {
                $detalle = DetalleCajaChica::find($detalleId);
                if (!$detalle) {
                    $detalle = new DetalleCajaChica();
                }
            } else {
                $detalle = new DetalleCajaChica();
            }

            $detalle->caja_chica_id = $cajaChicaId;
            $detalle->fecha = $fecha[$index];
            $detalle->concepto = $concepto[$index];
            $detalle->unidad = $unidad[$index];
            $detalle->cantidad = $cantidad[$index];
            $detalle->precio_unitario = $precioUnitario[$index];
            $detalle->subtotal = $subtotal[$index];
            $detalle->vista = $vista[$index];

            // Handle image upload
            if (isset($fotos[$index])) {
                $image = $fotos[$index];
                $imageName = time() . '_' . $image->getClientOriginalName();
                $fotoPath = 'storage/tickets/' . $imageName;
                $image->storeAs('public/tickets', $imageName);
                $detalle->foto = $fotoPath;
            }

            $detalle->save();
        }

        $this->updateCajaChicaSubtotalAndCambio($cajaChicaId);

        $obraId = $request->input('obra_id');
        return redirect()->route('cajaChica.index', ['obraId' => $obraId, 'cajaChica' => $cajaChicaId])
            ->with('success', 'Detalles guardados exitosamente.');
    }
    public function storeDetail(Request $request)
    {
        $request->validate([
            'id.*' => 'nullable|exists:detalle_caja_chicas,id',
            'caja_chica_id' => 'required|exists:caja_chicas,id',
            'fecha.*' => 'required|date',
            'concepto.*' => 'required|string',
            'unidad.*' => 'required|string',
            'cantidad.*' => 'required|numeric',
            'precio_unitario.*' => 'required|numeric',
            'subtotal.*' => 'required|numeric',
            'vista.*' => 'required|string',
        ]);

        try {
            $cajaChicaId = $request->input('caja_chica_id');
            $obraId = $request->input('obra_id');
            $fecha = $request->input('fecha');
            $concepto = $request->input('concepto');
            $unidad = $request->input('unidad');
            $cantidad = $request->input('cantidad');
            $precio_unitario = $request->input('precio_unitario');
            $subtotal = $request->input('subtotal');
            $vista = $request->input('vista');

            foreach ($fecha as $index => $value) {
                $tableName = '';
                switch ($vista[$index]) {
                    case 'papeleria':
                        $tableName = 'detalles_papeleria';
                        break;
                    case 'gasolina':
                        $tableName = 'detalle_gasolinas';
                        break;
                    case 'rentas':
                        $tableName = 'detalle_rentas';
                        break;
                    case 'utilidades':
                        $tableName = 'detalle_utilidades';
                        break;
                    case 'acarreos':
                        $tableName = 'detalle_acarreos';
                        break;
                    case 'comida':
                        $tableName = 'detalle_comidas';
                        break;
                    case 'tramites':
                        $tableName = 'detalle_tramites';
                        break;
                    case 'cimbras':
                        $tableName = 'detalle_cimbras';
                        break;
                    case 'maquinariaMayor':
                        $tableName = 'detalle_maquinaria_mayor';
                        break;
                    case 'maquinariaMenor':
                        $tableName = 'detalle_maquinaria_menor';
                        break;
                    case 'herramientaMenor':
                        $tableName = 'detalle_herramienta_menor';
                        break;
                    case 'equipoSeguridad':
                        $tableName = 'detalle_equipo_seguridad';
                        break;
                    case 'limpieza':
                        $tableName = 'detalle_limpieza';
                        break;
                    case 'generales':
                        $tableName = 'generales';
                        break;
                    case 'agregados':
                        $tableName = 'agregados';
                        break;
                    case 'aceros':
                        $tableName = 'aceros';
                        break;
                    case 'cemento':
                        $tableName = 'cemento';
                        break;
                    case 'losas':
                        $tableName = 'losas';
                        break;
                    case 'rentaMaquinaria':
                        $tableName = 'renta_maquinarias';
                        break;
                    default:
                        $tableName = 'generales';
                        break;
                }

                // Retrieve the image link from detalle_caja_chicas table
                $detalleCajaChica = DetalleCajaChica::where('caja_chica_id', $cajaChicaId)
                    ->where('fecha', $fecha[$index])
                    ->where('concepto', $concepto[$index])
                    ->first();

                $fotoPath = $detalleCajaChica ? $detalleCajaChica->foto : null;

                if ($request->hasFile('foto') && isset($request->file('foto')[$index])) {
                    // A new file was uploaded, handle it
                    $image = $request->file('foto')[$index];
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $fotoPath = 'storage/tickets/' . $imageName;
                    $image->storeAs('public/tickets', $imageName);
                } else {
                    // No new file was uploaded, use the existing link
                    $fotoLink = $request->input('foto_link');
                    $fotoPath = $fotoLink;
                }

                $detalleId = $request->input('id')[$index] ?? null;

                $data = [
                    'obra_id' => $obraId,
                    'fecha' => $fecha[$index],
                    'concepto' => $concepto[$index],
                    'unidad' => $unidad[$index],
                    'cantidad' => $cantidad[$index],
                    'precio_unitario' => $precio_unitario[$index],
                    'subtotal' => $subtotal[$index],
                    'foto' => $fotoPath, // Add the image link to the other table
                ];

                if ($detalleId) {
                    // Update existing record
                    DB::table($tableName)
                        ->where('id', $detalleId)
                        ->update($data);
                } else {
                    // Insert new record
                    DB::table($tableName)->insert($data);
                }
            }

             $this->updateCajaChicaSubtotalAndCambio($cajaChicaId);

            return response()->json(['success' => true, 'message' => 'Detalle de caja chica guardado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar el detalle de caja chica: ' . $e->getMessage()]);
        }
    }
    private function updateCajaChicaSubtotalAndCambio($cajaChicaId)
    {
        $cajaChica = CajaChica::find($cajaChicaId);
        $subtotal = DetalleCajaChica::where('caja_chica_id', $cajaChicaId)->sum('subtotal');

        $cantidadCajaChica = $cajaChica->cantidad;
        $cambio = $cantidadCajaChica - $subtotal;

        $cajaChica->subtotal = $subtotal;
        $cajaChica->cambio = $cambio;
        $cajaChica->save();
    }
}
