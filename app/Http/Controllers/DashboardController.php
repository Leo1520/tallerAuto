<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\InventarioSucursal;
use App\Models\Mecanico;
use App\Models\MovimientoCaja;
use App\Models\OrdenServicio;
use App\Models\Pago;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── KPIs principales ───────────────────────────────────────
        $ingresoOrdenesMes = Pago::where('estado', 'Confirmado')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('monto');

        $ingresoTiendaMes = MovimientoCaja::where('tipo', 'Ingreso')
            ->where('referencia', 'like', 'consulta_%')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('monto');

        $stats = [
            'clientes'        => Cliente::count(),
            'vehiculos'       => Vehiculo::count(),
            'ordenes_activas' => OrdenServicio::whereNotIn('estado', ['Entregado', 'Cancelado'])->count(),
            'ingresos_mes'    => $ingresoOrdenesMes + $ingresoTiendaMes,
        ];

        // ── Ingresos últimos 30 días (para gráfica de línea) ───────
        $ingresosPorDia = Pago::where('estado', 'Confirmado')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha');

        // Sumar ventas de tienda a la gráfica diaria
        $tiendaPorDia = MovimientoCaja::where('tipo', 'Ingreso')
            ->where('referencia', 'like', 'consulta_%')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha');

        $labels30 = collect();
        $data30   = collect();
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $labels30->push(now()->subDays($i)->format('d/m'));
            $data30->push((float) ($ingresosPorDia[$d] ?? 0) + (float) ($tiendaPorDia[$d] ?? 0));
        }

        // ── Órdenes por estado (donut) ─────────────────────────────
        $estadosConteo = OrdenServicio::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $todosEstados = [
            'Recibido', 'En diagnóstico', 'En reparación',
            'Esperando repuestos', 'Listo', 'Entregado', 'Cancelado',
        ];
        $donutLabels = $todosEstados;
        $donutData   = array_map(fn($e) => (int) ($estadosConteo[$e] ?? 0), $todosEstados);

        // ── Top 5 mecánicos por órdenes entregadas (mes actual) ────
        $topMecanicos = Mecanico::with('persona')
            ->withCount(['ordenes as entregadas_mes' => fn($q) =>
                $q->where('estado', 'Entregado')
                  ->whereMonth('updated_at', now()->month)
                  ->whereYear('updated_at', now()->year)
            ])
            ->withCount(['ordenes as activas' => fn($q) =>
                $q->whereNotIn('estado', ['Entregado', 'Cancelado'])
            ])
            ->orderByDesc('entregadas_mes')
            ->limit(5)
            ->get();

        // ── Alertas de bajo stock ──────────────────────────────────
        $alertasStock = InventarioSucursal::with(['repuesto', 'sucursal'])
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        // ── Órdenes recientes ──────────────────────────────────────
        $ordenes_recientes = OrdenServicio::with(['vehiculo.cliente.persona', 'mecanico.persona'])
            ->latest()
            ->take(7)
            ->get();

        return view('dashboard', compact(
            'stats', 'ordenes_recientes',
            'labels30', 'data30',
            'donutLabels', 'donutData',
            'topMecanicos', 'alertasStock'
        ));
    }
}
