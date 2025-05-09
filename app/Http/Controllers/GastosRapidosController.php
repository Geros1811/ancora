<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\User;

class GastosRapidosController extends Controller
{
    //
    public function create(Request $request)
    {
        $obraId = $request->input('obraId');
        $obra = \App\Models\Obra::find($obraId);

        return view('gastos_rapidos.create', compact('obra'));
    }

    public function store(Request $request)
    {
        $tabla = $request->input('tabla');
        $obraId = $request->input('obraId');
        $fecha = $request->input('fecha');
        $concepto = $request->input('concepto');
        $unidad = $request->input('unidad');
        $cantidad = $request->input('cantidad');
        $precio_unitario = $request->input('precio_unitario');
        $fotos = $request->file('fotos');

        if ($tabla == 'gasolina') {
            $tableName = 'detalle_gasolinas';
        } elseif ($tabla == 'maquinariaMayor') {
            $tableName = 'detalle_maquinaria_mayor';
        } elseif ($tabla == 'maquinariaMenor') {
            $tableName = 'detalle_maquinaria_menor';
        } elseif ($tabla == 'herramientaMenor') {
            $tableName = 'detalle_herramienta_menor';
        } elseif ($tabla == 'equipoSeguridad') {
            $tableName = 'detalle_equipo_seguridad';
        } elseif ($tabla == 'rentaMaquinaria') {
            $tableName = 'renta_maquinarias';
        } elseif ($tabla == 'rentas') {
            $tableName = 'detalle_rentas';
        } elseif ($tabla == 'utilidades') {
            $tableName = 'detalle_utilidades';
        } elseif ($tabla == 'acarreos') {
            $tableName = 'detalle_acarreos';
        } elseif ($tabla == 'comida') {
            $tableName = 'detalle_comidas';
        } elseif ($tabla == 'tramites') {
            $tableName = 'detalle_tramites';
        } elseif ($tabla == 'cimbras') {
            $tableName = 'detalle_cimbras';
        } elseif ($tabla == 'limpieza') {
            $tableName = 'detalle_limpieza';
        } elseif ($tabla == 'papeleria') {
            $tableName = 'detalles_papeleria';
        } elseif ($tabla == 'materiales' && $request->input('materialesSub') == 'agregados') {
            $tableName = 'agregados';
        } elseif ($tabla == 'materiales' && $request->input('materialesSub') == 'aceros') {
            $tableName = 'aceros';
        } elseif ($tabla == 'materiales' && $request->input('materialesSub') == 'cemento') {
            $tableName = 'cemento';
        } elseif ($tabla == 'materiales' && $request->input('materialesSub') == 'losas') {
            $tableName = 'losas';
        } elseif ($tabla == 'materiales') {
            $tableName = 'generales';
        }  else {
            $tableName = $tabla;
        }

        try {
            $data = array_map(
                function ($fecha, $concepto, $unidad, $cantidad, $precio_unitario, $subtotal, $index) use ($obraId, $fotos, $tableName) {
                    $record = [
                        'obra_id' => $obraId,
                        'fecha' => $fecha,
                        'concepto' => $concepto,
                        'unidad' => $unidad,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio_unitario,
                        'subtotal' => $subtotal,
                    ];

                    if ($tableName == 'detalles_papeleria') {
                        $record = [
                            'obra_id' => $obraId,
                            'fecha' => $fecha,
                            'concepto' => $concepto,
                            'unidad' => $unidad,
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio_unitario,
                            'subtotal' => $subtotal,
                        ];
                    }

                    // Handle image upload
                    if (isset($fotos[$index])) {
                        $image = $fotos[$index];
                        if ($image->isValid()) {
                            $imageName = time() . '_' . $image->getClientOriginalName();
                            $image->storeAs('public/tickets', $imageName);
                            $record['foto'] = 'storage/tickets/' . $imageName;
                        }
                    }

                    return $record;
                },
                $fecha,
                $concepto,
                $unidad,
                $cantidad,
                $precio_unitario,
                $request->input('subtotal'),
                array_keys($fecha) // Use array keys as index
            );

            DB::table($tableName)->insert($data);

            // Create notification for the architect
            $architects = User::where('role', 'arquitecto')->get();
            $tableNameMap = [
                'detalle_gasolinas' => 'Gasolina',
                'detalle_maquinaria_mayor' => 'Maquinaria Mayor',
                'detalle_maquinaria_menor' => 'Maquinaria Menor',
                'detalle_herramienta_menor' => 'Herramienta Menor',
                'detalle_equipo_seguridad' => 'Equipo de Seguridad',
                'renta_maquinarias' => 'Renta de Maquinaria',
                'detalle_rentas' => 'Rentas',
                'detalle_utilidades' => 'Utilidades',
                'detalle_acarreos' => 'Acarreos',
                'detalle_comidas' => 'Comidas',
                'detalle_tramites' => 'Trámites',
                'detalle_cimbras' => 'Cimbras',
                'detalle_limpieza' => 'Limpieza',
                'detalles_papeleria' => 'Papelería',
                'agregados' => 'Agregados',
                'aceros' => 'Aceros',
                'cemento' => 'Cemento',
                'losas' => 'Losas',
                'generales' => 'Materiales',
            ];
            $friendlyTableName = $tableNameMap[$tableName] ?? ucfirst($tableName);
            foreach ($architects as $architect) {
                $notification = new Notification();
                $notification->user_id = $architect->id;
                $notification->obra_id = $obraId;
                $notification->message = "Nuevo Gasto en $friendlyTableName.";
                $notification->save();
            }

            return redirect()->back()->with('success', 'Gasto rápido guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar el gasto rápido: ' . $e->getMessage());
        }
    }
}
