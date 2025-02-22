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
            'caja_chica_id' => 'required|exists:caja_chicas,id',
            'fecha.*' => 'required|date',
            'concepto.*' => 'required|string',
            'unidad.*' => 'required|string',
            'cantidad.*' => 'required|numeric',
            'precio_unitario.*' => 'required|numeric',
            'subtotal.*' => 'required|numeric',
            'vista.*' => 'required|string',
        ]);

        $cajaChicaId = $request->input('caja_chica_id');
        $fecha = $request->input('fecha');
        $concepto = $request->input('concepto');
        $unidad = $request->input('unidad');
        $cantidad = $request->input('cantidad');
        $precioUnitario = $request->input('precio_unitario');
        $subtotal = $request->input('subtotal');
        $vista = $request->input('vista');

        // Delete existing details
        DetalleCajaChica::where('caja_chica_id', $cajaChicaId)->delete();

        // Add new details
        foreach ($fecha as $index => $value) {
            DetalleCajaChica::create([
                'caja_chica_id' => $cajaChicaId,
                'fecha' => $fecha[$index],
                'concepto' => $concepto[$index],
                'unidad' => $unidad[$index],
                'cantidad' => $cantidad[$index],
                'precio_unitario' => $precioUnitario[$index],
                'subtotal' => $subtotal[$index],
                'vista' => $vista[$index],
            ]);
        }

        $this->updateCajaChicaSubtotalAndCambio($cajaChicaId);

        $obraId = $request->input('obra_id');
        return redirect()->route('cajaChica.index', ['obraId' => $obraId, 'cajaChica' => $cajaChicaId])
            ->with('success', 'Detalles guardados exitosamente.');
    }

    public function storeDetail(Request $request)
    {
        $request->validate([
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
                        $tableName = 'detalles_generales';
                        break;
                }

                DB::table($tableName)->insert([
                    'obra_id' => $obraId,
                    'fecha' => $fecha[$index],
                    'concepto' => $concepto[$index],
                    'unidad' => $unidad[$index],
                    'cantidad' => $cantidad[$index],
                    'precio_unitario' => $precio_unitario[$index],
                    'subtotal' => $subtotal[$index],
                ]);
            }

            $this-> updateCajaChicaSubtotalAndCambio($cajaChicaId);

            return response()->json(['success' => true, 'message' => 'Detalle de caja chica guardado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar el detalle de caja chica: ' . $e->getMessage()]);
        }
    }

    private function updateCajaChicaSubtotalAndCambio($cajaChicaId)
    {
        $cajaChica = CajaChica::find($cajaChicaId);
        $subtotal = 0;

        $tableNames = [
            'detalles_papeleria',
            'detalle_gasolinas',
            'detalle_rentas',
            'detalle_utilidades',
            'detalle_acarreos',
            'detalle_comidas',
            'detalle_tramites',
            'detalle_cimbras',
            'detalle_maquinaria_mayor',
            'detalle_maquinaria_menor',
            'detalle_herramienta_menor',
            'detalle_equipo_seguridad',
            'detalle_limpieza',
            'generales',
            'agregados',
            'aceros',
            'cemento',
            'losas',
            'renta_maquinarias',
            'detalles_generales'
        ];

        foreach ($tableNames as $tableName) {
            $subtotal += DB::table($tableName)->where('obra_id', $cajaChica->obra_id)->sum('subtotal');
        }

        $cantidadCajaChica = $cajaChica->cantidad;
        $cambio = $cantidadCajaChica - $subtotal;

        $cajaChica->subtotal = $subtotal;
        $cajaChica->cambio = $cambio;
        $cajaChica->save();
    }
}
