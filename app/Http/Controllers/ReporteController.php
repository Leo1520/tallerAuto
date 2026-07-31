<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\DetalleOrdenRepuesto;
use App\Models\Mecanico;
use App\Models\MovimientoCaja;
use App\Models\OrdenServicio;
use App\Models\Pago;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReporteController extends Controller
{
    // Skill: laravel-security — verificar permiso antes de cada reporte
    private function autorizarReportes(): void
    {
        abort_unless(
            auth()->user()->hasPermission('reportes.ver') || auth()->user()->isAdmin(),
            403,
            'No tienes permiso para ver reportes.'
        );
    }

    public function index(): View
    {
        $this->autorizarReportes();
        return view('reportes.index');
    }

    // ─── Reporte 1: Ventas por período ──────────────────────────────────
    public function ventas(Request $request): View
    {
        $this->autorizarReportes();

        $desde     = $request->fecha_desde ?? now()->startOfMonth()->format('Y-m-d');
        $hasta     = $request->fecha_hasta ?? now()->format('Y-m-d');
        $agrupar   = $request->agrupar ?? 'dia'; // dia | semana | mes
        $sucursalId = $request->sucursal_id;

        // Skill: performance — agregación directa en DB, no en PHP
        $formatoDb = match($agrupar) {
            'mes'    => "DATE_FORMAT(fecha_emision, '%Y-%m')",
            'semana' => "YEARWEEK(fecha_emision, 1)",
            default  => "DATE(fecha_emision)",
        };

        $ventas = DB::table('facturas')
            ->join('ordenes_servicio', 'facturas.orden_id', '=', 'ordenes_servicio.id')
            ->where('facturas.estado', 'Emitida')
            ->whereBetween('facturas.fecha_emision', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->when($sucursalId, fn($q) => $q->where('ordenes_servicio.sucursal_id', $sucursalId))
            ->selectRaw("
                {$formatoDb} as periodo,
                COUNT(facturas.id) as total_facturas,
                SUM(facturas.subtotal) as subtotal,
                SUM(facturas.iva) as iva,
                SUM(facturas.total) as total,
                AVG(facturas.total) as ticket_promedio
            ")
            ->groupByRaw($formatoDb)
            ->orderByRaw($formatoDb)
            ->get();

        // Totales del período
        $resumen = DB::table('facturas')
            ->join('ordenes_servicio', 'facturas.orden_id', '=', 'ordenes_servicio.id')
            ->where('facturas.estado', 'Emitida')
            ->whereBetween('facturas.fecha_emision', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->when($sucursalId, fn($q) => $q->where('ordenes_servicio.sucursal_id', $sucursalId))
            ->selectRaw('
                COUNT(facturas.id) as total_facturas,
                SUM(facturas.subtotal) as subtotal,
                SUM(facturas.iva) as iva,
                SUM(facturas.total) as total,
                AVG(facturas.total) as ticket_promedio
            ')
            ->first();

        // Ventas por método de pago
        $porMetodo = DB::table('pagos')
            ->join('metodos_pago', 'pagos.metodo_pago_id', '=', 'metodos_pago.id')
            ->join('ordenes_servicio', 'pagos.orden_id', '=', 'ordenes_servicio.id')
            ->where('pagos.estado', 'Confirmado')
            ->whereBetween('pagos.created_at', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->when($sucursalId, fn($q) => $q->where('ordenes_servicio.sucursal_id', $sucursalId))
            ->selectRaw('metodos_pago.nombre, COUNT(*) as cantidad, SUM(pagos.monto) as total')
            ->groupBy('metodos_pago.id', 'metodos_pago.nombre')
            ->orderByDesc('total')
            ->get();

        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();

        return view('reportes.ventas', compact(
            'ventas', 'resumen', 'porMetodo', 'sucursales',
            'desde', 'hasta', 'agrupar', 'sucursalId'
        ));
    }

    // ─── Reporte: Libro de caja ──────────────────────────────────────────
    public function caja(Request $request): View
    {
        $this->autorizarReportes();

        $desde = $request->fecha_desde ?? now()->startOfMonth()->format('Y-m-d');
        $hasta = $request->fecha_hasta ?? now()->format('Y-m-d');
        $tipo  = $request->tipo; // Ingreso | Egreso | null=todos

        $movimientos = MovimientoCaja::with([
            'user.persona',
            'pago.metodoPago',
            'pago.confirmadoPor.persona',
            'pago.orden.vehiculo.cliente.persona',
        ])
        ->whereBetween('created_at', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
        ->when($tipo, fn($q, $t) => $q->where('tipo', $t))
        ->orderByDesc('created_at')
        ->paginate(50)
        ->withQueryString();

        $resumen = DB::table('movimientos_caja')
            ->whereBetween('created_at', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->selectRaw("
                SUM(CASE WHEN tipo='Ingreso' THEN monto ELSE 0 END) as total_ingresos,
                SUM(CASE WHEN tipo='Egreso'  THEN monto ELSE 0 END) as total_egresos,
                COUNT(*) as total_movimientos
            ")
            ->first();

        return view('reportes.caja', compact('movimientos', 'resumen', 'desde', 'hasta', 'tipo'));
    }

    // ─── Reporte: Historial de auditoría de pagos ────────────────────────
    public function auditoria(Request $request): View
    {
        $this->autorizarReportes();

        $desde      = $request->fecha_desde ?? now()->startOfMonth()->format('Y-m-d');
        $hasta      = $request->fecha_hasta ?? now()->format('Y-m-d');
        $operacion  = $request->operacion;
        $userId     = $request->user_id;

        $logs = Auditoria::with('user.persona')
            ->where('tabla', 'pagos')
            ->whereBetween('created_at', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->when($operacion, fn($q, $op) => $q->where('tipo_operacion', $op))
            ->when($userId,    fn($q, $id) => $q->where('user_id', $id))
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        $operaciones = Auditoria::where('tabla', 'pagos')
            ->distinct()
            ->pluck('tipo_operacion')
            ->filter()
            ->sort()
            ->values();

        $usuarios = User::with('persona')
            ->whereHas('auditorias', fn($q) => $q->where('tabla', 'pagos'))
            ->orderBy('email')
            ->get();

        return view('reportes.auditoria', compact(
            'logs', 'desde', 'hasta', 'operacion', 'userId', 'operaciones', 'usuarios'
        ));
    }

    // ─── Reporte 2: Mecánicos más activos ───────────────────────────────
    public function mecanicos(Request $request): View
    {
        $this->autorizarReportes();

        $desde      = $request->fecha_desde ?? now()->startOfMonth()->format('Y-m-d');
        $hasta      = $request->fecha_hasta ?? now()->format('Y-m-d');
        $sucursalId = $request->sucursal_id;

        // Skill: performance — withCount + agregaciones en una sola query
        $mecanicos = DB::table('mecanicos')
            ->join('persona', 'mecanicos.persona_id', '=', 'persona.id')
            ->join('sucursales', 'mecanicos.sucursal_id', '=', 'sucursales.id')
            ->leftJoin('ordenes_servicio', function ($join) use ($desde, $hasta) {
                $join->on('ordenes_servicio.mecanico_id', '=', 'mecanicos.id')
                     ->whereBetween('ordenes_servicio.fecha_ingreso', ["{$desde} 00:00:00", "{$hasta} 23:59:59"]);
            })
            ->when($sucursalId, fn($q) => $q->where('mecanicos.sucursal_id', $sucursalId))
            ->where('mecanicos.activo', true)
            ->selectRaw('
                mecanicos.id,
                persona.nombre,
                persona.telefono,
                sucursales.nombre as sucursal,
                COUNT(ordenes_servicio.id) as total_ordenes,
                SUM(CASE WHEN ordenes_servicio.estado = "Entregado" THEN 1 ELSE 0 END) as ordenes_entregadas,
                SUM(ordenes_servicio.total) as facturacion_total,
                AVG(ordenes_servicio.total) as ticket_promedio,
                SUM(CASE WHEN ordenes_servicio.estado NOT IN ("Entregado","Cancelado") THEN 1 ELSE 0 END) as ordenes_activas
            ')
            ->groupBy('mecanicos.id', 'persona.nombre', 'persona.telefono', 'sucursales.nombre')
            ->orderByDesc('total_ordenes')
            ->get();

        // Órdenes por estado y mecánico (para gráfico)
        $estadosPorMecanico = DB::table('ordenes_servicio')
            ->join('mecanicos', 'ordenes_servicio.mecanico_id', '=', 'mecanicos.id')
            ->join('persona', 'mecanicos.persona_id', '=', 'persona.id')
            ->whereBetween('ordenes_servicio.fecha_ingreso', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->when($sucursalId, fn($q) => $q->where('mecanicos.sucursal_id', $sucursalId))
            ->selectRaw('persona.nombre, ordenes_servicio.estado, COUNT(*) as cantidad')
            ->groupBy('mecanicos.id', 'persona.nombre', 'ordenes_servicio.estado')
            ->get()
            ->groupBy('nombre');

        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();

        return view('reportes.mecanicos', compact(
            'mecanicos', 'estadosPorMecanico', 'sucursales',
            'desde', 'hasta', 'sucursalId'
        ));
    }

    // ─── Reporte 3: Repuestos más usados ────────────────────────────────
    public function repuestos(Request $request): View
    {
        $this->autorizarReportes();

        $desde      = $request->fecha_desde ?? now()->startOfMonth()->format('Y-m-d');
        $hasta      = $request->fecha_hasta ?? now()->format('Y-m-d');
        $sucursalId = $request->sucursal_id;
        $limite     = min((int)($request->limite ?? 20), 100);

        // Skill: performance — query directa con join, sin cargar modelos
        $repuestos = DB::table('detalle_orden_repuesto')
            ->join('ordenes_servicio', 'detalle_orden_repuesto.orden_id', '=', 'ordenes_servicio.id')
            ->join('repuestos', 'detalle_orden_repuesto.repuesto_id', '=', 'repuestos.id')
            ->leftJoin('proveedores', 'repuestos.proveedor_id', '=', 'proveedores.id')
            ->whereBetween('ordenes_servicio.fecha_ingreso', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->when($sucursalId, fn($q) => $q->where('ordenes_servicio.sucursal_id', $sucursalId))
            ->selectRaw('
                repuestos.id,
                repuestos.codigo,
                repuestos.nombre,
                repuestos.precio_compra,
                repuestos.precio_venta,
                proveedores.nombre as proveedor,
                COUNT(detalle_orden_repuesto.id) as veces_usado,
                SUM(detalle_orden_repuesto.cantidad) as unidades_vendidas,
                SUM(detalle_orden_repuesto.subtotal) as ingresos_generados,
                SUM(detalle_orden_repuesto.cantidad * repuestos.precio_compra) as costo_total,
                SUM(detalle_orden_repuesto.subtotal) - SUM(detalle_orden_repuesto.cantidad * repuestos.precio_compra) as margen
            ')
            ->groupBy('repuestos.id', 'repuestos.codigo', 'repuestos.nombre',
                      'repuestos.precio_compra', 'repuestos.precio_venta', 'proveedores.nombre')
            ->orderByDesc('unidades_vendidas')
            ->limit($limite)
            ->get();

        // Totales generales del período
        $totalesRepuestos = DB::table('detalle_orden_repuesto')
            ->join('ordenes_servicio', 'detalle_orden_repuesto.orden_id', '=', 'ordenes_servicio.id')
            ->join('repuestos', 'detalle_orden_repuesto.repuesto_id', '=', 'repuestos.id')
            ->whereBetween('ordenes_servicio.fecha_ingreso', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
            ->when($sucursalId, fn($q) => $q->where('ordenes_servicio.sucursal_id', $sucursalId))
            ->selectRaw('
                COUNT(DISTINCT repuestos.id) as tipos_distintos,
                SUM(detalle_orden_repuesto.cantidad) as unidades_totales,
                SUM(detalle_orden_repuesto.subtotal) as ingresos_totales,
                SUM(detalle_orden_repuesto.cantidad * repuestos.precio_compra) as costo_total
            ')
            ->first();

        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();

        return view('reportes.repuestos', compact(
            'repuestos', 'totalesRepuestos', 'sucursales',
            'desde', 'hasta', 'sucursalId', 'limite'
        ));
    }
}
