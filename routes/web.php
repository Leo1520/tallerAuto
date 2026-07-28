<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RepuestoController;
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

    // Inventario
    Route::resource('repuestos', RepuestoController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/movimiento', [InventarioController::class, 'movimiento'])->name('inventario.movimiento');
    Route::get('/inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
    Route::patch('/inventario/{inventarioSucursal}/stock-minimo', [InventarioController::class, 'actualizarStockMinimo'])
        ->name('inventario.stockMinimo');

    // Pagos
    Route::get('/pagos',                       [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/nuevo',                 [PagoController::class, 'create'])->name('pagos.create');
    Route::post('/pagos',                      [PagoController::class, 'store'])->name('pagos.store');
    Route::post('/pagos/{pago}/confirmar',     [PagoController::class, 'confirmar'])->name('pagos.confirmar');
    Route::post('/pagos/{pago}/anular',        [PagoController::class, 'anular'])->name('pagos.anular');
    Route::post('/pagos/stripe/intent',        [PagoController::class, 'crearIntent'])->name('pagos.intent');

    // Facturas
    Route::get('/facturas',                    [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas/{factura}',          [FacturaController::class, 'show'])->name('facturas.show');
    Route::post('/facturas/emitir',            [FacturaController::class, 'emitir'])->name('facturas.emitir');
    Route::post('/facturas/{factura}/anular',  [FacturaController::class, 'anular'])->name('facturas.anular');
    Route::get('/facturas/{factura}/pdf',      [FacturaController::class, 'pdf'])->name('facturas.pdf');
});

// Webhook de Stripe (sin CSRF ni autenticación)
Route::post('/stripe/webhook', [PagoController::class, 'webhook'])->name('stripe.webhook');
