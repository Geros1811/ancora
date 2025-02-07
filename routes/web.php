<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ObraController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\CostosController;
use App\Http\Controllers\PapeleriaController;
use App\Http\Controllers\GasolinaController;
use App\Http\Controllers\RentasController;
use App\Http\Controllers\UtilidadesController;
use App\Http\Controllers\AcarreosController;
use App\Http\Controllers\ComidasController;
use App\Http\Controllers\TramitesController;
use App\Http\Controllers\CimbrasController;
use App\Http\Controllers\MaquinariaMayorController;
use App\Http\Controllers\LimpiezaController;
use App\Http\Controllers\MaquinariaMenorController;
use App\Http\Controllers\HerramientaMenorController;
use App\Http\Controllers\EquipoSeguridadController;
use App\Http\Controllers\ManoObraController;
use App\Http\Controllers\MaterialesController;
use App\Http\Controllers\DestajoController;
use App\Http\Controllers\DestajosDetallesController;


Route::get('/costos/{id}', [CostosController::class, 'show'])->name('costos.show');

// Rutas de login y registro
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/obras/{id}', [ObraController::class, 'show'])->name('obra.show');

// Rutas para el calendario de pagos
Route::get('/obras/{id}/calendario-pagos', [ObraController::class, 'obtenerCalendarioPagos'])->name('obtener.calendario');
Route::post('/obras/{id}/guardar-calendario', [ObraController::class, 'guardarCalendario'])->name('guardar.calendario');
Route::post('/guardar-cambios', [ObraController::class, 'guardarCalendario'])->name('guardar.calendario');

// Ruta para subir archivos, ahora protegida por el middleware 'auth'
Route::post('/subir-archivo', [ArchivoController::class, 'store'])->name('archivo.store');
Route::post('/subir-archivo/{calendarioPagoId}', [ArchivoController::class, 'store'])->name('archivo.store');

// Rutas para el dashboard y registro
Route::get('/dashboard', [ObraController::class, 'index'])->name('dashboard');
Route::get('/obra/crear', [ObraController::class, 'create'])->name('obra.create');
Route::post('/obra', [ObraController::class, 'store'])->name('obra.store');

// Rutas de registro
Route::middleware('auth')->group(function () {
    Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register'])->name('register');
});

// Validación de contraseña
Route::post('/validar-password', function (Illuminate\Http\Request $request) {
    $password = $request->input('password');

    if (Hash::check($password, Auth::user()->password)) {
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false]);
})->middleware('auth');

// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Rutas para papelería y gasolina
Route::get('/papeleria/{obraId}', [PapeleriaController::class, 'index'])->name('papeleria.index');
Route::post('/papeleria/{obraId}', [PapeleriaController::class, 'store'])->name('papeleria.store');
Route::get('/gasolina/{obraId}', [GasolinaController::class, 'index'])->name('gasolina.index');
Route::post('/gasolina/{obraId}', [GasolinaController::class, 'store'])->name('gasolina.store');

// Rutas para rentas y utilidades
Route::get('/rentas/{obraId}', [RentasController::class, 'index'])->name('rentas.index');
Route::post('/rentas/{obraId}', [RentasController::class, 'store'])->name('rentas.store');
Route::get('/utilidades/{obraId}', [UtilidadesController::class, 'index'])->name('utilidades.index');
Route::post('/utilidades/{obraId}', [UtilidadesController::class, 'store'])->name('utilidades.store');

// Rutas para acarreos
Route::get('/acarreos/{obraId}', [AcarreosController::class, 'index'])->name('acarreos.index');
Route::post('/acarreos/{obraId}', [AcarreosController::class, 'store'])->name('acarreos.store');

// Rutas para comidas
Route::get('/comidas/{obraId}', [ComidasController::class, 'index'])->name('comidas.index');
Route::post('/comidas/{obraId}', [ComidasController::class, 'store'])->name('comidas.store');

// Rutas para trámites
Route::get('/tramites/{obraId}', [TramitesController::class, 'index'])->name('tramites.index');
Route::post('/tramites/{obraId}', [TramitesController::class, 'store'])->name('tramites.store');

// Rutas para cimbras
Route::get('/cimbras/{obraId}', [CimbrasController::class, 'index'])->name('cimbras.index');
Route::post('/cimbras/{obraId}', [CimbrasController::class, 'store'])->name('cimbras.store');

// Rutas para maquinaria mayor
Route::get('/maquinariaMayor/{obraId}', [MaquinariaMayorController::class, 'index'])->name('maquinariaMayor.index');
Route::post('/maquinariaMayor/{obraId}', [MaquinariaMayorController::class, 'store'])->name('maquinariaMayor.store');

// Rutas para maquinaria menor
Route::get('/maquinariaMenor/{obraId}', [MaquinariaMenorController::class, 'index'])->name('maquinariaMenor.index');
Route::post('/maquinariaMenor/{obraId}', [MaquinariaMenorController::class, 'store'])->name('maquinariaMenor.store');

// Rutas para limpieza
Route::get('/limpieza/{obraId}', [LimpiezaController::class, 'index'])->name('limpieza.index');
Route::post('/limpieza/{obraId}', [LimpiezaController::class, 'store'])->name('limpieza.store');

// Rutas para herramienta menor
Route::get('/herramientaMenor/{obraId}', [HerramientaMenorController::class, 'index'])->name('herramientaMenor.index');
Route::post('/herramientaMenor/{obraId}', [HerramientaMenorController::class, 'store'])->name('herramientaMenor.store');

// Rutas para equipo de seguridad
Route::get('/equipoSeguridad/{obraId}', [EquipoSeguridadController::class, 'index'])->name('equipoSeguridad.index');
Route::post('/equipoSeguridad/{obraId}', [EquipoSeguridadController::class, 'store'])->name('equipoSeguridad.store');

// Rutas para mano de obra
Route::get('/manoObra/{obraId}', [ManoObraController::class, 'index'])->name('manoObra.index');
Route::post('/manoObra/{obraId}', [ManoObraController::class, 'store'])->name('manoObra.store');
Route::post('/actualizar-total-nomina/{nominaId}', [ManoObraController::class, 'actualizarTotalNomina']);
Route::get('/resumen/{obraId}', [ManoObraController::class, 'resumen'])->name('resumen');
Route::post('/mano-de-obra/{id}/actualizar', [ManoObraController::class, 'actualizar']);



// Ruta para la vista de destajos
Route::get('/manoObra/{obraId}/destajos', [ManoObraController::class, 'destajos'])->name('manoObra.destajos');
Route::post('/manoObra/{obraId}/destajos', [DestajoController::class, 'store'])->name('manoObra.storeDestajos');
Route::post('/destajos/{obraId}', [DestajosDetallesController::class, 'store'])
    ->name('destajos.store');

// Rutas para materiales
Route::get('/materiales/{obraId}', [MaterialesController::class, 'index'])->name('materiales.index');
Route::post('/materiales/agregados/{obraId}', [MaterialesController::class, 'storeAgregados'])->name('materiales.storeAgregados');
Route::post('/materiales/aceros/{obraId}', [MaterialesController::class, 'storeAceros'])->name('materiales.storeAceros');
Route::post('/materiales/cemento/{obraId}', [MaterialesController::class, 'storeCemento'])->name('materiales.storeCemento');
Route::post('/materiales/losas/{obraId}', [MaterialesController::class, 'storeLosas'])->name('materiales.storeLosas');
Route::post('/materiales/generales/{obraId}', [MaterialesController::class, 'storeGenerales'])->name('materiales.storeGenerales');

Route::post('/update-costo-indirecto/{obraId}/{costo}', [CostosController::class, 'updateCostoIndirecto'])->name('updateCostoIndirecto');
Route::post('/update-costo-directo/{obraId}/{costo}', [CostosController::class, 'updateCostoDirecto'])->name('updateCostoDirecto');

Route::get('/obra/{obraId}/destajo', [DestajoController::class, 'index'])->name('destajo.index');
Route::post('/obra/{obraId}/destajo/store', [DestajoController::class, 'store'])->name('destajo.store');

