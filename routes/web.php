<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ObraController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/obras/{id}', [ObraController::class, 'show'])->name('obra.show');

// Ruta para obtener los datos del calendario de pagos
Route::get('/obras/{id}/calendario-pagos', [ObraController::class, 'obtenerCalendarioPagos'])->name('obtener.calendario');

Route::get('/dashboard', [ObraController::class, 'index'])->name('dashboard');
Route::get('/obra/crear', [ObraController::class, 'create'])->name('obra.create');
Route::post('/obra', [ObraController::class, 'store'])->name('obra.store');

// Agregar la ruta para guardar el calendario
Route::post('/obras/{id}/guardar-calendario', [ObraController::class, 'guardarCalendario'])->name('guardar.calendario');
Route::post('/guardar-cambios', [ObraController::class, 'guardarCalendario'])->name('guardar.calendario');

Route::middleware('auth')->group(function () {
    Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register'])->name('register');
});

Route::post('/validar-password', function (Illuminate\Http\Request $request) {
    $password = $request->input('password');

    if (Hash::check($password, Auth::user()->password)) {
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false]);
})->middleware('auth');

Route::get('/', function () {
    return view('welcome');
});
