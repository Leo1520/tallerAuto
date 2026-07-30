<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\ConsultaRepuesto;
use App\Models\InventarioSucursal;
use App\Models\Pago;
use Illuminate\Http\JsonResponse;

class NotificacionController extends Controller
{
    public function resumen(): JsonResponse
    {
        $items = [];

        $citas = Cita::where('estado', 'pendiente')->count();
        if ($citas > 0) {
            $items[] = [
                'tipo'    => 'cita',
                'mensaje' => "{$citas} cita(s) pendiente(s) de confirmación",
                'url'     => route('citas.index', ['estado' => 'pendiente']),
                'icono'   => 'bi-calendar2-check',
                'color'   => '#facc15',
            ];
        }

        $consultas = ConsultaRepuesto::where('estado', 'Pendiente')->count();
        if ($consultas > 0) {
            $items[] = [
                'tipo'    => 'consulta',
                'mensaje' => "{$consultas} solicitud(es) de repuesto pendientes",
                'url'     => route('consultas.index', ['estado' => 'Pendiente']),
                'icono'   => 'bi-box-seam',
                'color'   => '#60a5fa',
            ];
        }

        $stockBajo = InventarioSucursal::whereColumn('stock', '<=', 'stock_minimo')->count();
        if ($stockBajo > 0) {
            $items[] = [
                'tipo'    => 'stock',
                'mensaje' => "{$stockBajo} repuesto(s) con stock bajo o agotado",
                'url'     => route('inventario.index'),
                'icono'   => 'bi-exclamation-triangle-fill',
                'color'   => '#f97316',
            ];
        }

        $pagos = Pago::where('estado', 'En revisión')->count();
        if ($pagos > 0) {
            $items[] = [
                'tipo'    => 'pago',
                'mensaje' => "{$pagos} pago(s) QR en revisión",
                'url'     => route('pagos.revision.index'),
                'icono'   => 'bi-credit-card-2-front',
                'color'   => '#34d399',
            ];
        }

        $total = $citas + $consultas + $stockBajo + $pagos;

        return response()->json(['total' => $total, 'items' => $items]);
    }
}
