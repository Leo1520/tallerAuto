<?php

use App\Http\Controllers\AdjuntoController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MecanicoController;
use App\Http\Controllers\OrdenServicioController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;

// ─── Públicas ────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─── Auth ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',          [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',         [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',       [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',      [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password',[AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',[AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',[AuthController::class, 'resetPassword'])->name('password.update');
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
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor']);
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

    // Reportes
    Route::get('/reportes',                    [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/ventas',             [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('/reportes/mecanicos',          [ReporteController::class, 'mecanicos'])->name('reportes.mecanicos');
    Route::get('/reportes/repuestos',          [ReporteController::class, 'repuestos'])->name('reportes.repuestos');

    // Usuarios (admin only)
    Route::resource('usuarios', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('/usuarios/{usuario}/password', [UserController::class, 'cambiarPassword'])->name('usuarios.password');

    // Mecánicos
    Route::resource('mecanicos', MecanicoController::class);

    // Sucursales
    Route::get('/sucursales/mapa', [SucursalController::class, 'mapa'])->name('sucursales.mapa');
    Route::resource('sucursales', SucursalController::class)
        ->only(['index', 'create', 'store', 'edit', 'update'])
        ->parameters(['sucursales' => 'sucursal']);

    // Adjuntos de órdenes
    Route::post('/ordenes/{orden}/adjuntos',          [AdjuntoController::class, 'store'])->name('adjuntos.store');
    Route::get('/adjuntos/{adjunto}/download',         [AdjuntoController::class, 'download'])->name('adjuntos.download');
    Route::delete('/adjuntos/{adjunto}',               [AdjuntoController::class, 'destroy'])->name('adjuntos.destroy');
});

// Webhook de Stripe (sin CSRF ni autenticación)
Route::post('/stripe/webhook', [PagoController::class, 'webhook'])->name('stripe.webhook');
