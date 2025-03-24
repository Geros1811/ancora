<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CostoIndirecto;
use App\Models\CostoDirecto;
use App\Models\CalendarioPago;
use App\Models\PagosAdministrativos;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ObraController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && (Auth::user()->role == 'cliente' || Auth::user()->role == 'maestro_obra' || Auth::user()->role == 'residente')) {
                if (in_array($request->route()->getActionMethod(), ['create', 'store'])) {
                    return redirect()->route('dashboard')->with('error', 'No tienes permiso para crear obras.');
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        $obras = [];
        if (Auth::user()->role == 'arquitecto') {
            $obras = Obra::where('user_id', Auth::id())
                         ->orWhere('arquitecto_id', Auth::id())
                         ->get();
        } elseif (Auth::user()->role == 'maestro_obra') {
            $obras = Obra::where('residente', Auth::id())->get();
        } else {
            $obras = Obra::where('cliente', Auth::id())->get();
        }

        // Share the $obra variable with all views
        $obra = Obra::where('cliente', Auth::id())->first();
        view()->share('obra', $obra);

        return view('dashboard', compact('obras'));
    }

    public function create()
    {
        $userId = Auth::id();
        $architects = User::where('role', 'arquitecto')->where('created_by', $userId)->get();
        $maestroObras = User::where('role', 'maestro_obra')->where('created_by', $userId)->get();
        $clientes = User::where('role', 'cliente')->where('created_by', $userId)->get();
        return view('obra.create', compact('architects', 'maestroObras', 'clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'presupuesto' => 'required|numeric',
            'metros_cuadrados' => 'nullable|numeric',
            'cliente' => 'required',
            'fecha_inicio' => 'nullable|date',
            'fecha_termino' => 'nullable|date',
            'residente' => 'required',
            'ubicacion' => 'required',
            'descripcion' => 'nullable',
            'architects' => 'nullable|exists:users,id',
        ]);

        $obra = new Obra();
        $obra->nombre = $request->input('nombre');
        $obra->presupuesto = $request->input('presupuesto');
        $obra->metros_cuadrados = $request->input('metros_cuadrados');
        $obra->cliente = $request->input('cliente');
        $obra->fecha_inicio = $request->input('fecha_inicio');
        $obra->fecha_termino = $request->input('fecha_termino');
        $obra->residente = $request->input('residente');
        $obra->ubicacion = $request->input('ubicacion');
        $obra->descripcion = $request->input('descripcion');
        $obra->user_id = Auth::id();
        $obra->arquitecto_id = $request->input('architects');

        $obra->save();

        return redirect()->route('dashboard')->with('success', 'Obra creada correctamente.');
    }

    public function show($id)
    {
        $obra = Obra::findOrFail($id);

        // Check if the user is a client and is linked to the obra
        if (Auth::check() && Auth::user()->role == 'cliente') {
            $obra = Obra::where('id', $id)->where('cliente', Auth::id())->firstOrFail();
        } elseif (Auth::check() && Auth::user()->role == 'maestro_obra') {
            $obra = Obra::where('id', $id)->where('residente', Auth::id())->firstOrFail();
        } else {
            $obra = Obra::where('id', $id)->firstOrFail();
        }

        $cliente = User::find($obra->cliente);
        $residente = User::find($obra->residente);

        $costosDirectos = CostoDirecto::where('obra_id', $id)->get();
        $costosIndirectos = CostoIndirecto::where('obra_id', $id)->get();
        $totalPagosCliente = CalendarioPago::where('obra_id', $id)->sum('pago');
        $pagosAdministrativos = PagosAdministrativos::where('obra_id', $id)->where('nombre', '!=', 'Ingresos')->get();
        $ingresos = \App\Models\Ingreso::where('obra_id', $id)->get();

        $pagosAdministrativosOcultos = 0;
        $pagosBD = DB::table('pagos_administrativos')
            ->where('obra_id', $id)
            ->pluck('costo', 'nombre');

        foreach ($pagosBD as $nombre => $costo) {
            if (Session::get('pagos_administrativos.' . $nombre) === false) {
                $pagosAdministrativosOcultos += $costo;
            }
        }

        // Calculate total cantidad from destajos
        $totalCantidadDestajos = \App\Models\Destajo::where('obra_id', $id)->sum('cantidad');

        // Calculate total pagos from destajos sin nomina
        $totalPagosDestajosSinNomina = 0;
        $partidas = \App\Models\Partida::where('obra_id', $id)->get();
        foreach ($partidas as $partida) {
            $detalles = \App\Models\DestajoSinNominaDetalle::where('partida_id', $partida->id)->get();
            foreach ($detalles as $detalle) {
                if (is_array(json_decode($detalle->pagos, true))) {
                    foreach (json_decode($detalle->pagos, true) as $pago) {
                        $totalPagosDestajosSinNomina += $pago['monto'];
                    }
                }
            }
        }

        return view('obras.show', compact('obra', 'costosDirectos', 'costosIndirectos', 'totalPagosCliente', 'totalCantidadDestajos', 'pagosAdministrativos', 'ingresos', 'pagosAdministrativosOcultos', 'cliente', 'residente', 'totalPagosDestajosSinNomina'));
    }

    public function guardarCalendario(Request $request)
    {
        try {
            $pagos = $request->input('pagos'); // Array de pagos

            // Verificar que el array no esté vacío
            if (empty($pagos)) {
                \Log::error('No se han recibido pagos.');
                return response()->json(['success' => false, 'message' => 'No se han recibido pagos.']);
            }

            // Asegúrate de que el ID de la obra se pase correctamente
            $obraId = $request->input('obra_id'); // Asegúrate de enviar el id de la obra desde el formulario si es necesario

            // Check if the user is a client and is linked to the obra
            if (Auth::check() && Auth::user()->role == 'cliente') {
                $obra = Obra::where('id', $obraId)->where('cliente', Auth::id())->firstOrFail();
            }

            // Eliminar los registros existentes para la obra
            DB::table('calendario_pagos')->where('obra_id', $obraId)->delete();

            foreach ($pagos as $pago) {
                // Verificar que todas las claves necesarias estén presentes
                if (!isset($pago['concepto'], $pago['fecha_pago'], $pago['pago'], $pago['acumulado'], $pago['bloqueado'])) {
                    \Log::error('Faltan datos en el pago:', $pago);
                    return response()->json(['success' => false, 'message' => 'Faltan datos en uno de los pagos.']);
                }

                // Registrar los datos recibidos para depuración
                \Log::info('Datos recibidos para guardar:', [
                    'obra_id' => $obraId,
                    'concepto' => $pago['concepto'],
                    'fecha_pago' => $pago['fecha_pago'],
                    'pago' => $pago['pago'],
                    'acumulado' => $pago['acumulado'],
                    'bloqueado' => $pago['bloqueado'],
                    'ticket' => $pago['ticket'] ?? null,
                ]);

                // Guardar cada pago en la base de datos
                DB::table('calendario_pagos')->insert([
                    'obra_id' => $obraId, // id de la obra correspondiente
                    'concepto' => $pago['concepto'],
                    'fecha_pago' => $pago['fecha_pago'],
                    'pago' => $pago['pago'],
                    'acumulado' => $pago['acumulado'], // Asegúrate de enviar este valor si es necesario
                    'bloqueado' => $pago['bloqueado'],
                    'ticket' => $pago['ticket'] ?? null, // El campo ticket es opcional, se puede omitir si no se envía
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Calendario de pagos guardado correctamente.']);
        } catch (\Exception $e) {
            // Registrar el error
            \Log::error('Error al guardar el calendario de pagos: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Hubo un problema al guardar el calendario de pagos.']);
        }
    }

    public function obtenerCalendarioPagos($id)
    {
        $calendarioPagos = DB::table('calendario_pagos')->where('obra_id', $id)->get();
        return response()->json($calendarioPagos);
    }
}
