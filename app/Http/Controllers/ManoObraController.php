<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\DetalleManoObra;
use App\Models\CostoDirecto;
use App\Models\Nomina;
use Carbon\Carbon;
use App\Models\Destajo;
use App\Models\Obra;
use Illuminate\Support\Facades\Storage;
use App\Models\Imagen;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;


class ManoObraController extends Controller
{
    public function index($obraId)
    {
        $nominas = Nomina::where('obra_id', $obraId)->get();
        $detalles = DetalleManoObra::where('obra_id', $obraId)->get();
        $costoTotal = $detalles->sum('subtotal');
        $destajos = Destajo::where('obra_id', $obraId)->get();
        $obra = Obra::findOrFail($obraId);

        return view('manoObra.index', compact('nominas', 'detalles', 'costoTotal', 'destajos', 'obraId', 'obra'));
    }

    public function uploadImage(Request $request, $obraId, $nominaId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(base_path('manoobrafotos'), $imageName);
        $imagePath = 'manoobrafotos/' . $imageName;

        $imagen = new Imagen();
        $imagen->path = $imagePath;
        $imagen->nomina_id = $nominaId;
        $imagen->save();

        return back()->with('success', 'Imagen subida correctamente.');
    }

    public function imagenes($nominaId)
    {
        $nomina = Nomina::findOrFail($nominaId);
        $imagenes = Imagen::where('nomina_id', $nominaId)->get();

        return view('manoObra.imagenes', compact('nomina', 'imagenes'));
    }

    public function store(Request $request, $obraId)
    {
        $nombreNomina = $request->input('nombre_nomina');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $diaInicio = Carbon::parse($fechaInicio)->locale('es')->isoFormat('dddd');
        $diaFin = Carbon::parse($fechaFin)->locale('es')->isoFormat('dddd');

        $nomina = Nomina::where('obra_id', $obraId)
            ->where('nombre', $nombreNomina)
            ->where('fecha_inicio', $fechaInicio)
            ->where('fecha_fin', $fechaFin)
            ->first();

        if (!$nomina) {
            $nomina = new Nomina();
            $nomina->obra_id = $obraId;
            $nomina->nombre = $nombreNomina;
            $nomina->fecha_inicio = $fechaInicio;
            $nomina->fecha_fin = $fechaFin;
            $nomina->dia_inicio = ucfirst($diaInicio);
            $nomina->dia_fin = ucfirst($diaFin);
            $nomina->save();
        }

        if ($nomina->bloqueado) {
            return redirect()->route('manoObra.index', ['obraId' => $obraId])
                ->with('error', 'Esta nómina ya no puede ser editada.');
        }

        $nombres = $request->input('nombre', []);
        $puestos = $request->input('puesto', []);
        $lunes = $request->input('lunes', []);
        $martes = $request->input('martes', []);
        $miercoles = $request->input('miercoles', []);
        $jueves = $request->input('jueves', []);
        $viernes = $request->input('viernes', []);
        $sabado = $request->input('sabado', []);
        $precios_hora = $request->input('precio_diario', []);
        $extras_menos = $request->input('extras_menos', []);

        DetalleManoObra::where('obra_id', $obraId)->where('nomina_id', $nomina->id)->delete();

        $totalNomina = 0;

        foreach ($nombres as $index => $nombre) {
            if (!empty($nombre)) {
                $total_horas = ($lunes[$index] ?? 0) + ($martes[$index] ?? 0) + ($miercoles[$index] ?? 0) +
                               ($jueves[$index] ?? 0) + ($viernes[$index] ?? 0) + ($sabado[$index] ?? 0);
                $subtotal = ($total_horas * ($precios_hora[$index] ?? 0)) + ($extras_menos[$index] ?? 0);

                $detalle = new DetalleManoObra();
                $detalle->obra_id = $obraId;
                $detalle->nomina_id = $nomina->id;
                $detalle->nombre = $nombre;
                $detalle->puesto = $puestos[$index] ?? '';
                $detalle->lunes = $lunes[$index] ?? 0;
                $detalle->martes = $martes[$index] ?? 0;
                $detalle->miercoles = $miercoles[$index] ?? 0;
                $detalle->jueves = $jueves[$index] ?? 0;
                $detalle->viernes = $viernes[$index] ?? 0;
                $detalle->sabado = $sabado[$index] ?? 0;
                $detalle->total_horas = $total_horas;
                $detalle->precio_hora = $precios_hora[$index] ?? 0;
                $detalle->extras_menos = $extras_menos[$index] ?? 0;
                $detalle->subtotal = $subtotal;
                $detalle->save();

                $totalNomina += $subtotal;
            }
        }

        if (!$nomina->bloqueado) {
            $nomina->total = $totalNomina;
            $nomina->save();
        }

        $costoTotal = DetalleManoObra::where('obra_id', $obraId)->sum('subtotal');

        CostoDirecto::updateOrCreate(
            ['obra_id' => $obraId, 'nombre' => 'Mano de Obra'],
            ['costo' => $costoTotal]
        );

        // Create notification for the architect
        $architects = User::where('role', 'arquitecto')->get();
        foreach ($architects as $architect) {
            $notification = new Notification();
            $notification->user_id = $architect->id;
            $notification->obra_id = $obraId;
            $notification->message = "Nueva Nómina creada o Pase de lista Modificado.";
            $notification->save();
        }

        return redirect()->route('manoObra.index', ['obraId' => $obraId])
            ->with('success', 'Nómina actualizada correctamente.');
    }

    public function resumen($obraId)
    {
        $nominas = Nomina::where('obra_id', $obraId)->orderBy('fecha_inicio', 'asc')->get();
        $obra = Obra::findOrFail($obraId);

        foreach ($nominas as $nomina) {
            $nomina->destajos = Destajo::where('obra_id', $obraId)
                ->where('nomina_id', $nomina->id)
            ->get();
    }

    $totalNomina = $nominas->sum('total');
    $totalDestajos = Destajo::where('obra_id', $obraId)->sum('cantidad');

    return view('manoObra.resumen', compact('nominas', 'obra', 'obraId', 'totalNomina', 'totalDestajos'));
}

    public function actualizar(Request $request, $id) {
        $nomina = Nomina::findOrFail($id);

        if ($nomina->editado) {
            return response()->json(['success' => false, 'message' => 'Esta nómina ya no puede ser editada.']);
        }

        $nomina->dias_trabajados = $request->dias_trabajados;
        $nomina->observaciones = $request->observaciones;
        $nomina->editado = true;
        $nomina->bloqueado = true;
        $nomina->save();

        return response()->json(['success' => true]);
    }

    public function generarPDF($nomina_id)
    {
        $nomina = Nomina::findOrFail($nomina_id);
        if (!$nomina) {
            abort(404, 'Nómina no encontrada');
        }
        $obra = Obra::findOrFail($nomina->obra_id);
        $destajos = Destajo::where('nomina_id', $nomina_id)->get();
        $detalles = DetalleManoObra::where('nomina_id', $nomina_id)->get();
        $user = Auth::user();

        $pdf = PDF::loadView('manoObra.pdf', compact('nomina', 'destajos', 'detalles', 'obra', 'user'));

        return $pdf->stream("nomina_semana_{$nomina_id}.pdf");
    }

    public function bloquear(Request $request, $id)
    {
        $nomina = Nomina::findOrFail($id);
        $nomina->bloqueado = true;
        $nomina->save();

        return response()->json(['success' => true]);
    }

    public function desbloquear(Request $request, $id)
    {
        $nomina = Nomina::findOrFail($id);
        $nomina->bloqueado = false;
        $nomina->save();

        return response()->json(['success' => true]);
    }

    public function generateResumenPdf($obraId)
    {
        $nominas = Nomina::with('destajos')->where('obra_id', $obraId)->orderBy('fecha_inicio', 'asc')->get();
        $obra = Obra::findOrFail($obraId);

        $totalNomina = $nominas->sum('total');
        $totalDestajos = Destajo::where('obra_id', $obraId)->sum('cantidad');

        $data = [
            'nominas' => $nominas,
            'obra' => $obra,
            'obraId' => $obraId,
            'totalNomina' => $totalNomina,
            'totalDestajos' => $totalDestajos,
        ];

        $pdf = \PDF::loadView('manoObra.resumen_pdf', $data);

        // Prevent automatic download - stream the PDF to the browser
        return $pdf->stream('manoObra_resumen.pdf');
    }
}
