<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agregado;
use App\Models\Acero;
use App\Models\Cemento;
use App\Models\Losa;
use App\Models\CostoDirecto; // Importar la clase CostoDirecto
use App\Models\General; // Importar la clase General
use App\Models\Obra;

class MaterialesController extends Controller
{
    public function index($obraId)
    {
        $agregados = Agregado::where('obra_id', $obraId)->get();
        $aceros = Acero::where('obra_id', $obraId)->get();
        $cemento = Cemento::where('obra_id', $obraId)->get();
        $losas = Losa::where('obra_id', $obraId)->get();
        $generales = General::where('obra_id', $obraId)->get(); // Definir la variable $generales
        $obra = Obra::findOrFail($obraId);

        $costoTotal = $agregados->sum('subtotal') +
                      $aceros->sum('subtotal') +
                      $cemento->sum('subtotal') +
                      $losas->sum('subtotal') +
                      $generales->sum('subtotal'); // Incluir el costo total de generales

        return view('materiales.index', compact('agregados', 'aceros', 'cemento', 'losas', 'generales', 'obraId', 'costoTotal', 'obra'));
    }

    public function storeAgregados(Request $request, $obraId)
    {
        // Eliminar los registros existentes para la obra antes de guardar los nuevos datos
        Agregado::where('obra_id', $obraId)->delete();

        $costoTotal = 0;

        // Guardar agregados
        $fechas = $request->input('fecha_agregados', []);
        $conceptos = $request->input('concepto_agregados', []);
        $unidades = $request->input('unidad_agregados', []);
        $cantidades = $request->input('cantidad_agregados', []);
        $precios_unitarios = $request->input('precio_unitario_agregados', []);

        foreach ($fechas as $index => $fecha) {
            $cantidad = $cantidades[$index];
            $precio_unitario = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio_unitario;

            $detalle = new Agregado();
            $detalle->obra_id = $obraId;
            $detalle->fecha = $fecha;
            $detalle->concepto = $conceptos[$index];
            $detalle->unidad = $unidades[$index];
            $detalle->cantidad = $cantidad;
            $detalle->precio_unitario = $precio_unitario;
            $detalle->subtotal = $subtotal;
            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos directos
        $this->updateCostoTotal($obraId);

        return redirect()->route('materiales.index', ['obraId' => $obraId]);
    }

    public function storeAceros(Request $request, $obraId)
    {
        // Eliminar los registros existentes para la obra antes de guardar los nuevos datos
        Acero::where('obra_id', $obraId)->delete();

        $costoTotal = 0;

        // Guardar aceros
        $fechas = $request->input('fecha_aceros', []);
        $conceptos = $request->input('concepto_aceros', []);
        $unidades = $request->input('unidad_aceros', []);
        $cantidades = $request->input('cantidad_aceros', []);
        $precios_unitarios = $request->input('precio_unitario_aceros', []);

        foreach ($fechas as $index => $fecha) {
            $cantidad = $cantidades[$index];
            $precio_unitario = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio_unitario;

            $detalle = new Acero();
            $detalle->obra_id = $obraId;
            $detalle->fecha = $fecha;
            $detalle->concepto = $conceptos[$index];
            $detalle->unidad = $unidades[$index];
            $detalle->cantidad = $cantidad;
            $detalle->precio_unitario = $precio_unitario;
            $detalle->subtotal = $subtotal;
            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos directos
        $this->updateCostoTotal($obraId);

        return redirect()->route('materiales.index', ['obraId' => $obraId]);
    }

    public function storeCemento(Request $request, $obraId)
    {
        // Eliminar los registros existentes para la obra antes de guardar los nuevos datos
        Cemento::where('obra_id', $obraId)->delete();

        $costoTotal = 0;

        // Guardar cemento
        $fechas = $request->input('fecha_cemento', []);
        $conceptos = $request->input('concepto_cemento', []);
        $unidades = $request->input('unidad_cemento', []);
        $cantidades = $request->input('cantidad_cemento', []);
        $precios_unitarios = $request->input('precio_unitario_cemento', []);

        foreach ($fechas as $index => $fecha) {
            $cantidad = $cantidades[$index];
            $precio_unitario = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio_unitario;

            $detalle = new Cemento();
            $detalle->obra_id = $obraId;
            $detalle->fecha = $fecha;
            $detalle->concepto = $conceptos[$index];
            $detalle->unidad = $unidades[$index];
            $detalle->cantidad = $cantidad;
            $detalle->precio_unitario = $precio_unitario;
            $detalle->subtotal = $subtotal;
            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos directos
        $this->updateCostoTotal($obraId);

        return redirect()->route('materiales.index', ['obraId' => $obraId]);
    }

    public function storeLosas(Request $request, $obraId)
    {
        // Eliminar los registros existentes para la obra antes de guardar los nuevos datos
        Losa::where('obra_id', $obraId)->delete();

        $costoTotal = 0;

        // Guardar losas
        $fechas = $request->input('fecha_losas', []);
        $conceptos = $request->input('concepto_losas', []);
        $unidades = $request->input('unidad_losas', []);
        $cantidades = $request->input('cantidad_losas', []);
        $precios_unitarios = $request->input('precio_unitario_losas', []);

        foreach ($fechas as $index => $fecha) {
            $cantidad = $cantidades[$index];
            $precio_unitario = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio_unitario;

            $detalle = new Losa();
            $detalle->obra_id = $obraId;
            $detalle->fecha = $fecha;
            $detalle->concepto = $conceptos[$index];
            $detalle->unidad = $unidades[$index];
            $detalle->cantidad = $cantidad;
            $detalle->precio_unitario = $precio_unitario;
            $detalle->subtotal = $subtotal;
            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos directos
        $this->updateCostoTotal($obraId);

        return redirect()->route('materiales.index', ['obraId' => $obraId]);
    }

    public function storeGenerales(Request $request, $obraId)
    {
        // Eliminar los registros existentes para la obra antes de guardar los nuevos datos
        General::where('obra_id', $obraId)->delete();

        $costoTotal = 0;

        // Guardar generales
        $fechas = $request->input('fecha_generales', []);
        $conceptos = $request->input('concepto_generales', []);
        $unidades = $request->input('unidad_generales', []);
        $cantidades = $request->input('cantidad_generales', []);
        $precios_unitarios = $request->input('precio_unitario_generales', []);

        foreach ($fechas as $index => $fecha) {
            $cantidad = $cantidades[$index];
            $precio_unitario = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio_unitario;

            $detalle = new General();
            $detalle->obra_id = $obraId;
            $detalle->fecha = $fecha;
            $detalle->concepto = $conceptos[$index];
            $detalle->unidad = $unidades[$index];
            $detalle->cantidad = $cantidad;
            $detalle->precio_unitario = $precio_unitario;
            $detalle->subtotal = $subtotal;
            $detalle->save();

            $costoTotal += $subtotal;
        }

        // Actualizar el costo total en la tabla de costos directos
        $this->updateCostoTotal($obraId);

        return redirect()->route('materiales.index', ['obraId' => $obraId]);
    }

    private function updateCostoTotal($obraId)
    {
        $costoTotal = Agregado::where('obra_id', $obraId)->sum('subtotal') +
                      Acero::where('obra_id', $obraId)->sum('subtotal') +
                      Cemento::where('obra_id', $obraId)->sum('subtotal') +
                      Losa::where('obra_id', $obraId)->sum('subtotal') +
                      General::where('obra_id', $obraId)->sum('subtotal'); // Incluir el costo total de generales

        CostoDirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Materiales'],
            ['costo' => $costoTotal]
        );
    }
}
