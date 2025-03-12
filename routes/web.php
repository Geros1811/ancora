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
use App\Http\Controllers\RentaMaquinariaController;
use App\Http\Controllers\CajaChicaController;
use App\Http\Controllers\GastosRapidosController;
use App\Http\Controllers\PagosAdministrativosController;
use App\Http\Controllers\SueldoResidenteController;
use App\Http\Controllers\ImssController;
use App\Http\Controllers\ContadorController;
use App\Http\Controllers\IvaController;
use App\Http\Controllers\OtrosPagosAdministrativosController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\ClienteFotoController;
use App\Http\Controllers\NotificationController;

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
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register');

Route::middleware('auth')->group(function () {
});

// Rutas para gastos rápidos
Route::get('/gastos_rapidos/create', [GastosRapidosController::class, 'create'])->name('gastos_rapidos.create');
Route::post('/gastos_rapidos/store', [GastosRapidosController::class, 'store'])->name('gastos_rapidos.store');

// Validación de contraseña
Route::post('/validar-password', function (Illuminate\Http\Request $request) {
    $password = $request->input('password');

    if (Hash::check($password, Auth::user()->password)) {
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false]);
})->middleware('auth');

// Página de bienvenida
use App\Http\Controllers\PdfGeneratorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/obras/{obraId}/general-pdf', [PdfGeneratorController::class, 'showSelectMonthForm'])->name('general_pdf.select_month');
Route::post('/general-pdf/generate', [PdfGeneratorController::class, 'generatePdf'])->name('general_pdf.generate');

// Rutas para papelería y gasolina
Route::get('/papeleria/{obraId}', [PapeleriaController::class, 'index'])->name('papeleria.index');
Route::post('/papeleria/{obraId}', [PapeleriaController::class, 'store'])->name('papeleria.store');
Route::get('/gasolina/{obraId}', [GasolinaController::class, 'index'])->name('gasolina.index');
Route::post('/gasolina/{obraId}', [GasolinaController::class, 'store'])->name('gasolina.store');
Route::delete('/gasolina/{id}', [GasolinaController::class, 'destroy'])->name('gasolina.destroy');

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

// Rutas para renta de maquinaria
Route::get('/rentaMaquinaria/{obraId}', [RentaMaquinariaController::class, 'index'])->name('rentaMaquinaria.index');
Route::post('/rentaMaquinaria/{obraId}', [RentaMaquinariaController::class, 'store'])->name('rentaMaquinaria.store');
Route::delete('/rentaMaquinaria/{id}', [RentaMaquinariaController::class, 'destroy'])->name('rentaMaquinaria.destroy');

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
Route::post('/manoObra/{obraId}/{nominaId}/upload-image', [ManoObraController::class, 'uploadImage'])->name('manoObra.uploadImage');
Route::post('/mano-de-obra/{id}/actualizar', [ManoObraController::class, 'actualizar']);
Route::get('/manoObra/{nominaId}/imagenes', [ManoObraController::class, 'imagenes'])->name('manoObra.imagenes');

// Ruta para la vista de destajos
Route::get('/manoObra/{obraId}/destajos', [DestajoController::class, 'destajos'])->name('manoObra.destajos');
Route::post('/manoObra/{obraId}/destajos', [DestajoController::class, 'store'])->name('manoObra.storeDestajos');
Route::post('/destajos/{obraId}', [DestajoController::class, 'store'])
    ->name('destajos.store');

Route::get('/destajos/{obraId}', [DestajoController::class, 'index'])->name('destajos.index');
Route::get('/obra/{obraId}/destajo', [DestajoController::class, 'index'])->name('destajo.index');
Route::post('/obra/{obraId}/destajo/store', [DestajoController::class, 'store'])->name('destajo.store');
Route::get('/detalles-destajos/{id}', [DestajosDetallesController::class, 'show'])->name('detalles.destajos');
Route::post('/obra/{obraId}/destajo/store', [DestajosDetallesController::class, 'store'])->name('destajo.store');
Route::get('/detalles-destajos/{id}', [DestajosDetallesController::class, 'show'])->name('detalles.destajos');
Route::post('/detalles-destajos/{obraId}/{destajoId}', [DestajosDetallesController::class, 'store'])->name('detalles.destajos.store');
Route::post('/detalles-destajos/{obraId}/{destajoId}/upload-image', [DestajosDetallesController::class, 'uploadImage'])->name('detalles.destajos.uploadImage');

Route::get('/destajos/{id}/imagenes', [DestajosDetallesController::class, 'showImages'])->name('destajos.imagenes');

Route::post('/exportar-destajos/{obraId}', [DestajoController::class, 'exportarDestajos'])->name('exportar.destajos');

Route::get('/mano-obra/pdf/{nomina_id}', [ManoObraController::class, 'generarPDF'])->name('mano-obra.pdf');

Route::post('detalles/destajos/exportar/{obraId}/{destajoId}', [DestajosDetallesController::class, 'exportar'])->name('detalles.destajos.exportar');

// Rutas para materiales
Route::get('/materiales/{obraId}', [MaterialesController::class, 'index'])->name('materiales.index');
Route::post('/materiales/agregados/{obraId}', [MaterialesController::class, 'storeAgregados'])->name('materiales.storeAgregados');
Route::post('/materiales/aceros/{obraId}', [MaterialesController::class, 'storeAceros'])->name('materiales.storeAceros');
Route::post('/materiales/cemento/{obraId}', [MaterialesController::class, 'storeCemento'])->name('materiales.storeCemento');
Route::post('/materiales/losas/{obraId}', [MaterialesController::class, 'storeLosas'])->name('materiales.storeLosas');
Route::post('/materiales/generales/{obraId}', [MaterialesController::class, 'storeGenerales'])->name('materiales.storeGenerales');

Route::post('/update-costo-indirecto/{obraId}/{costo}', [CostosController::class, 'updateCostoIndirecto'])->name('updateCostoIndirecto');
Route::post('/update-costo-directo/{obraId}/{costo}', [CostosController::class, 'updateCostoDirecto'])->name('updateCostoDirecto');

Route::delete('/destajos/{destajo}', [DestajoController::class, 'destroy'])->name('destajos.destroy');

Route::get('/destajos/{id}/pdf', [DestajosDetallesController::class, 'generatePdf'])->name('destajos.detalles.pdf');

Route::post('/api/destajos/toggleLock/{id}', [DestajoController::class, 'toggleLock'])->name('api.destajos.toggleLock');

Route::get('/destajos/detalles/{id}', [DestajosDetallesController::class, 'show'])->name('destajos.detalles');

//CAJA CHICA
Route::get('/cajaChica/{obraId}', [CajaChicaController::class, 'index'])->name('cajaChica.index');
Route::post('/cajaChica', [CajaChicaController::class, 'store'])->name('cajaChica.store');
Route::post('/cajaChica/addDetail', [CajaChicaController::class, 'addDetail'])->name('cajaChica.addDetail');
Route::post('/cajaChica/storeDetail', [CajaChicaController::class, 'storeDetail'])->name('cajaChica.storeDetail');

Route::delete('/papeleria/{obraId}/detalles/{detalleId}', [PapeleriaController::class, 'destroyDetalle'])->name('papeleria.destroyDetalle');

Route::delete('/rentas/{id}', [RentasController::class, 'destroy'])->name('rentas.destroy');
Route::delete('/utilidades/{id}', [UtilidadesController::class, 'destroy'])->name('utilidades.destroy');
Route::delete('/acarreos/{id}', [AcarreosController::class, 'destroy'])->name('acarreos.destroy');
Route::delete('/comidas/{id}', [ComidasController::class, 'destroy'])->name('comidas.destroy');
Route::delete('/tramites/{id}', [TramitesController::class, 'destroy'])->name('tramites.destroy');
Route::delete('/cimbras/{id}', [CimbrasController::class, 'destroy'])->name('cimbras.destroy');
Route::delete('/maquinariaMayor/{id}', [MaquinariaMayorController::class, 'destroy'])->name('maquinariaMayor.destroy');
Route::delete('/herramientaMenor/{id}', [HerramientaMenorController::class, 'destroy'])->name('herramientaMenor.destroy');
Route::delete('/equipoSeguridad/{id}', [EquipoSeguridadController::class, 'destroy'])->name('equipoSeguridad.destroy');
Route::delete('/limpieza/{id}', [LimpiezaController::class, 'destroy'])->name('limpieza.destroy');
Route::delete('/sueldoResidente/{id}', [SueldoResidenteController::class, 'destroy'])->name('sueldoResidente.destroy');

Route::delete('/materiales/agregados/{id}', [MaterialesController::class, 'destroyAgregados'])->name('materiales.destroyAgregados');
Route::delete('/materiales/aceros/{id}', [MaterialesController::class, 'destroyAceros'])->name('materiales.destroyAceros');
Route::delete('/materiales/cemento/{id}', [MaterialesController::class, 'destroyCemento'])->name('materiales.destroyCemento');
Route::delete('/materiales/losas/{id}', [MaterialesController::class, 'destroyLosas'])->name('materiales.destroyLosas');
Route::delete('/materiales/generales/{id}', [MaterialesController::class, 'destroyGenerales'])->name('materiales.destroyGenerales');

Route::delete('/maquinariaMenor/{id}', [MaquinariaMenorController::class, 'destroy'])->name('maquinariaMenor.destroy');

Route::get('/obras/{obraId}/pagos-administrativos', [PagosAdministrativosController::class, 'index'])->name('pagos-administrativos');

Route::get('/sueldo-residente/{obraId}', [SueldoResidenteController::class, 'index'])->name('sueldo-residente.index');
Route::post('/sueldo-residente/{obraId}', [SueldoResidenteController::class, 'store'])->name('store');

Route::get('/imss/{obraId}', [ImssController::class, 'index'])->name('imss.index');

Route::get('/contador/{obraId}', [ContadorController::class, 'index'])->name('contador.index');

Route::get('/iva/{obraId}', [IvaController::class, 'index'])->name('iva.index');
Route::post('/iva/{obraId}', [IvaController::class, 'store'])->name('iva.store');

Route::get('/contador/{obraId}', [ContadorController::class, 'index'])->name('contador.index');
Route::post('/contador/{obraId}', [ContadorController::class, 'store'])->name('contador.store');
Route::delete('/contador/{id}', [ContadorController::class, 'destroy'])->name('contador.destroy');

Route::get('/imss/{obraId}', [ImssController::class, 'index'])->name('imss.index');
Route::post('/imss/{obraId}', [ImssController::class, 'store'])->name('imss.store');
Route::delete('/imss/{id}', [ImssController::class, 'destroy'])->name('imss.destroy');

Route::get('/otros-pagos-administrativos/{obraId}', [OtrosPagosAdministrativosController::class, 'index'])->name('otros_pagos_administrativos.index');
Route::post('/otros-pagos-administrativos/{obraId}', [OtrosPagosAdministrativosController::class, 'store'])->name('otros_pagos_administrativos.store');
Route::delete('/otros-pagos-administrativos/{id}', [OtrosPagosAdministrativosController::class, 'destroy'])->name('otros_pagos_administrativos.destroy');

Route::get('/ingresos/{obraId}', [IngresoController::class, 'index'])->name('ingresos.index');
Route::post('/ingresos/{obraId}', [IngresoController::class, 'store'])->name('ingresos.store');
Route::delete('/ingresos/{id}', [IngresoController::class, 'destroy'])->name('ingresos.destroy');

Route::post('/pagos-administrativos/toggle-pago', [App\Http\Controllers\PagosAdministrativosController::class, 'togglePago'])->name('pagos-administrativos.toggle-pago');

Route::get('/cliente_fotos/{obraId}', [ClienteFotoController::class, 'index'])->name('cliente_fotos.index');
Route::post('/cliente_fotos/{obraId}', [ClienteFotoController::class, 'store'])->name('cliente_fotos.store');
Route::put('cliente_fotos/updateComment', [ClienteFotoController::class, 'updateComment'])->name('cliente_fotos.updateComment');

Route::post('/perfil/update', [App\Http\Controllers\PerfilController::class, 'update'])->name('perfil.update');

Route::post('/mano-de-obra/{id}/bloquear', [ManoObraController::class, 'bloquear']);
Route::post('/mano-de-obra/{id}/desbloquear', [ManoObraController::class, 'desbloquear']);

Route::get('/papeleria/{obraId}/pdf', [PapeleriaController::class, 'generatePdf'])->name('papeleria.pdf');
Route::get('/gasolina/{obraId}/pdf', [GasolinaController::class, 'generatePdf'])->name('gasolina.pdf');
Route::get('/rentas/{obraId}/pdf', [RentasController::class, 'generatePdf'])->name('rentas.pdf');
Route::get('/acarreos/{obraId}/pdf', [AcarreosController::class, 'generatePdf'])->name('acarreos.pdf');
Route::get('/comidas/{obraId}/pdf', [ComidasController::class, 'generatePdf'])->name('comidas.pdf');
Route::get('/tramites/{obraId}/pdf', [TramitesController::class, 'generatePdf'])->name('tramites.pdf');
Route::get('/cimbras/{obraId}/pdf', [CimbrasController::class, 'generatePdf'])->name('cimbras.pdf');
Route::get('/maquinariaMayor/{obraId}/pdf', [MaquinariaMayorController::class, 'generatePdf'])->name('maquinariaMayor.pdf');
Route::get('/rentaMaquinaria/{obraId}/pdf', [RentaMaquinariaController::class, 'generatePdf'])->name('rentaMaquinaria.pdf');
Route::get('/maquinariaMenor/{obraId}/pdf', [MaquinariaMenorController::class, 'generatePdf'])->name('maquinariaMenor.pdf');
Route::get('/obras/{obraId}/pagos-administrativos/pdf', [PagosAdministrativosController::class, 'generateConsolidatedPdf'])->name('pagosAdministrativos.consolidatedPdf');
Route::get('/limpieza/{obraId}/pdf', [LimpiezaController::class, 'generatePdf'])->name('limpieza.pdf');
Route::get('/herramientaMenor/{obraId}/pdf', [HerramientaMenorController::class, 'generatePdf'])->name('herramientaMenor.pdf');
Route::get('/equipoSeguridad/{obraId}/pdf', [EquipoSeguridadController::class, 'generatePdf'])->name('equipoSeguridad.pdf');
Route::get('/utilidades/{obraId}/pdf', [UtilidadesController::class, 'generatePdf'])->name('utilidades.pdf');
Route::get('/ingresos/{obraId}/pdf', [IngresoController::class, 'generatePdf'])->name('ingresos.pdf');
Route::get('/materiales/{obraId}/pdf', [MaterialesController::class, 'generatePdf'])->name('materiales.pdf');
Route::get('/mano-obra/resumen/pdf/{obraId}', [ManoObraController::class, 'generateResumenPdf'])->name('manoObra.resumenPdf');

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/marcar-notificacion-leida', function (Illuminate\Http\Request $request) {
    $notification = \App\Models\Notification::find($request->id);
    if ($notification) {
        $notification->read_at = now();
        $notification->save();
        return response()->json(['success' => true])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
    return response()->json(['success' => false])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
});

Route::post('/marcar-todas-notificaciones-leidas', function (Illuminate\Http\Request $request) {
    $obraId = $request->input('obra_id');
    \App\Models\Notification::where('obra_id', $obraId)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);
    return response()->json(['success' => true]);
});
