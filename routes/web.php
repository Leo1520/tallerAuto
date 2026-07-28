<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;

// ─── Públicas ────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─── Auth ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Dashboard y módulos (autenticado) ───────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clientes', ClienteController::class);
    Route::resource('vehiculos', VehiculoController::class);

    Route::resource('ordenes', OrdenServicioController::class)
        ->parameters(['ordenes' => 'orden']);
    Route::patch('ordenes/{orden}/estado', [OrdenServicioController::class, 'cambiarEstado'])
        ->name('ordenes.estado');
});
