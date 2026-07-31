<?php

use App\Http\Controllers\AdjuntoController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
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
use App\Http\Controllers\CitaAdminController;
use App\Http\Controllers\ConsultaAdminController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\Cliente\ConsultaController;
use App\Http\Controllers\Cliente\ConsultaPagoController;
use App\Http\Controllers\Cliente\PortalController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// ─── Pública ─────────────────────────────────────────────────
Route::get('/',        [LandingController::class, 'index'])->name('landing');
Route::get('/tienda',  [LandingController::class, 'tienda'])->name('tienda');

// ─── Auth (invitados) ────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',               [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',              [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',            [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',           [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password',     [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',    [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',     [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ─── Verificación de correo ───────────────────────────────────
// Activar cuenta: ruta pública — la firma se valida dentro del controlador
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('throttle:6,1')
    ->name('verification.verify');

// Página "revisa tu correo" (pública, sin login)
Route::get('/registro/activacion', [VerificationController::class, 'activacion'])
    ->name('registro.activacion');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');

    // Cuenta sin rol asignado (trabajador pendiente de aprobación)
    Route::get('/pendiente', [AuthController::class, 'pending'])->name('pending');
});

// ─── Área de cliente ─────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/inicio',                          [PortalController::class, 'inicio'])->name('inicio');
    Route::get('/citas',                           [PortalController::class, 'citasIndex'])->name('citas.index');
    Route::get('/citas/nueva',                     [PortalController::class, 'citasCreate'])->name('citas.create');
    Route::post('/citas',                          [PortalController::class, 'citasStore'])->name('citas.store');
    Route::patch('/citas/{cita}/cancelar',         [PortalController::class, 'citasCancel'])->name('citas.cancel');
    Route::get('/ordenes',                         [PortalController::class, 'ordenesIndex'])->name('ordenes.index');
    Route::get('/ordenes/{orden}',                 [PortalController::class, 'ordenShow'])->name('ordenes.show');
    // Consulta de repuesto desde la tienda
    Route::get('/consultas/enviada',                    [ConsultaController::class, 'enviada'])->name('consultas.enviada');
    Route::get('/consultas/repuesto/{repuesto}',        [ConsultaController::class, 'repuestoForm'])->name('consultas.repuesto');
    Route::post('/consultas/repuesto/{repuesto}',       [ConsultaController::class, 'repuestoStore'])->name('consultas.repuesto.store');

    // Pago de consulta vía QR
    Route::get('/consultas/pagar/{token}',              [ConsultaPagoController::class, 'show'])->name('consultas.pago.show');
    Route::post('/consultas/pagar/{token}',             [ConsultaPagoController::class, 'store'])->name('consultas.pago.store');
    Route::get('/consultas/pago-enviado/{token}',       [ConsultaPagoController::class, 'enviado'])->name('consultas.pago.enviado');

    // Pago QR desde el portal cliente
    Route::get('/pago/enviado',                    [PagoController::class, 'pagoEnviado'])->name('pagar.enviado');
    Route::get('/ordenes/{orden}/pagar',           [PagoController::class, 'qrMostrar'])->name('ordenes.pagar');
    Route::post('/ordenes/{orden}/pagar',          [PagoController::class, 'qrClienteConfirmar'])->name('ordenes.pagar.confirmar');
});

// ─── Área administrativa ──────────────────────────────────────
// Solo roles de personal: clientes nunca llegan aquí aunque estén verificados.
Route::middleware([
        'auth',
        'verified',
        'role:Admin,Recepcion,Mecanico,Bodega,Contador',
    ])
    ->prefix('admin')
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clientes', ClienteController::class);
    Route::resource('vehiculos', VehiculoController::class);

    Route::resource('ordenes', OrdenServicioController::class)
        ->parameters(['ordenes' => 'orden']);
    Route::patch('ordenes/{orden}/estado', [OrdenServicioController::class, 'cambiarEstado'])
        ->name('ordenes.estado');
    Route::post('ordenes/{orden}/repuestos', [OrdenServicioController::class, 'agregarRepuesto'])
        ->name('ordenes.repuestos.agregar');
    Route::delete('ordenes/{orden}/repuestos/{detalle}', [OrdenServicioController::class, 'quitarRepuesto'])
        ->name('ordenes.repuestos.quitar');

    // Inventario
    Route::resource('repuestos', RepuestoController::class);
    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor']);
    Route::get('/inventario',             [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/movimiento', [InventarioController::class, 'movimiento'])->name('inventario.movimiento');
    Route::get('/inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
    Route::patch('/inventario/{inventarioSucursal}/stock-minimo', [InventarioController::class, 'actualizarStockMinimo'])
        ->name('inventario.stockMinimo');

    // Pagos — rutas estáticas PRIMERO (antes de {pago} para evitar conflictos)
    Route::get('/pagos',                        [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/nuevo',                  [PagoController::class, 'create'])->name('pagos.create');
    Route::get('/pagos/revision',               [PagoController::class, 'revisionIndex'])->name('pagos.revision.index');
    Route::get('/pagos/efectivo',               [PagoController::class, 'efectivoCreate'])->name('pagos.efectivo');
    Route::post('/pagos',                       [PagoController::class, 'store'])->name('pagos.store');
    // Stripe eliminado — PAYMENT_DRIVER=manual_qr
    Route::post('/pagos/efectivo',              [PagoController::class, 'efectivoStore'])->name('pagos.efectivo.store');
    // Pagos — con parámetro {pago}
    Route::get('/pagos/{pago}/revision',        [PagoController::class, 'revisionShow'])->name('pagos.revision.show');
    Route::post('/pagos/{pago}/confirmar',      [PagoController::class, 'confirmar'])->name('pagos.confirmar');
    Route::post('/pagos/{pago}/validar',        [PagoController::class, 'cajeroValidar'])->name('pagos.validar');
    Route::post('/pagos/{pago}/rechazar',       [PagoController::class, 'cajeroRechazar'])->name('pagos.rechazar');
    Route::post('/pagos/{pago}/anular',         [PagoController::class, 'anular'])->name('pagos.anular');

    // Facturas
    Route::get('/facturas',                [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas/{factura}',      [FacturaController::class, 'show'])->name('facturas.show');
    Route::post('/facturas/emitir',        [FacturaController::class, 'emitir'])->name('facturas.emitir');
    Route::post('/facturas/{factura}/anular', [FacturaController::class, 'anular'])->name('facturas.anular');
    Route::get('/facturas/{factura}/pdf',  [FacturaController::class, 'pdf'])->name('facturas.pdf');

    // Reportes
    Route::get('/reportes',           [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/ventas',    [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('/reportes/mecanicos', [ReporteController::class, 'mecanicos'])->name('reportes.mecanicos');
    Route::get('/reportes/repuestos', [ReporteController::class, 'repuestos'])->name('reportes.repuestos');

    // Usuarios
    Route::resource('usuarios', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('/usuarios/{usuario}/password', [UserController::class, 'cambiarPassword'])->name('usuarios.password');

    // Servicios
    Route::resource('servicios', ServicioController::class)->except(['show']);

    // Mecánicos
    Route::resource('mecanicos', MecanicoController::class);

    // Sucursales
    Route::get('/sucursales/mapa', [SucursalController::class, 'mapa'])->name('sucursales.mapa');
    Route::resource('sucursales', SucursalController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->parameters(['sucursales' => 'sucursal']);

    // Adjuntos de órdenes
    Route::post('/ordenes/{orden}/adjuntos',     [AdjuntoController::class, 'store'])->name('adjuntos.store');
    Route::get('/adjuntos/{adjunto}/download',   [AdjuntoController::class, 'download'])->name('adjuntos.download');
    Route::delete('/adjuntos/{adjunto}',         [AdjuntoController::class, 'destroy'])->name('adjuntos.destroy');

    // Citas
    Route::get('/citas',                [CitaAdminController::class, 'index'])->name('citas.index');
    Route::get('/citas/{cita}',         [CitaAdminController::class, 'show'])->name('citas.show');
    Route::patch('/citas/{cita}',       [CitaAdminController::class, 'update'])->name('citas.update');

    // Consultas de repuesto
    Route::get('/consultas',                                          [ConsultaAdminController::class, 'index'])->name('consultas.index');
    Route::get('/consultas/{consultaRepuesto}',                       [ConsultaAdminController::class, 'show'])->name('consultas.show');
    Route::patch('/consultas/{consultaRepuesto}',                     [ConsultaAdminController::class, 'update'])->name('consultas.update');
    Route::post('/consultas/{consultaRepuesto}/confirmar-pago',       [ConsultaAdminController::class, 'confirmarPago'])->name('consultas.pago.confirmar');
    Route::post('/consultas/{consultaRepuesto}/rechazar-pago',        [ConsultaAdminController::class, 'rechazarPago'])->name('consultas.pago.rechazar');

    // Notificaciones (campana)
    Route::get('/notificaciones/resumen', [NotificacionController::class, 'resumen'])->name('notificaciones.resumen');
});

// Stripe webhook eliminado — se usa PAYMENT_DRIVER=manual_qr
