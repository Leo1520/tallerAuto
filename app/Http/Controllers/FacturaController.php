<?php

namespace App\Http\Controllers;

use App\Actions\Facturas\GenerarFacturaAction;
use App\Http\Requests\EmitirFacturaRequest;
use App\Models\Factura;
use App\Models\OrdenServicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FacturaController extends Controller
{
    public function __construct(private GenerarFacturaAction $generarFactura) {}

    // Lista de facturas (skill: eager loading estándar)
    public function index(Request $request): View
    {
        abort_unless(
            auth()->user()->hasPermission('facturas.ver') || auth()->user()->isAdmin(),
            403
        );

        $facturas = Factura::with(['orden.vehiculo.cliente.persona', 'orden.sucursal'])
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->search, fn($q, $s) =>
                $q->where('numero', 'like', "%{$s}%")
                  ->orWhereHas('orden', fn($o) => $o->where('numero', 'like', "%{$s}%"))
            )
            ->when($request->fecha_desde, fn($q, $f) => $q->whereDate('fecha_emision', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->whereDate('fecha_emision', '<=', $f))
            ->latest('fecha_emision')
            ->paginate(20)
            ->withQueryString();

        $totalEmitido = Factura::where('estado', 'Emitida')
            ->when($request->fecha_desde, fn($q, $f) => $q->whereDate('fecha_emision', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->whereDate('fecha_emision', '<=', $f))
            ->sum('total');

        return view('facturas.index', compact('facturas', 'totalEmitido'));
    }

    // Vista detalle de factura
    public function show(Factura $factura): View
    {
        abort_unless(
            auth()->user()->hasPermission('facturas.ver') || auth()->user()->isAdmin(),
            403
        );

        // Skill: eager loading completo
        $factura->load([
            'orden.vehiculo.cliente.persona',
            'orden.vehiculo.modelo.marca',
            'orden.sucursal',
            'orden.detalles.servicio',
            'orden.repuestos.repuesto',
            'orden.pagos.metodoPago',
            'orden.mecanico.persona',
        ]);

        return view('facturas.show', compact('factura'));
    }

    // Emitir factura desde una orden
    public function emitir(EmitirFacturaRequest $request): RedirectResponse
    {
        $orden = OrdenServicio::with('factura', 'pagos')->findOrFail($request->orden_id);

        // Seguridad extra: solo órdenes con pagos confirmados
        $totalPagado = $orden->pagos->where('estado', 'Confirmado')->sum('monto');
        if ($totalPagado < $orden->total) {
            return back()->with('error', 'No se puede emitir factura: la orden tiene saldo pendiente.');
        }

        try {
            $factura = $this->generarFactura->execute($orden, $request->observaciones);
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('facturas.show', $factura)
            ->with('success', "Factura {$factura->numero} emitida correctamente.");
    }

    // Anular factura (skill: auditing — Auditable trait registra el cambio)
    public function anular(Request $request, Factura $factura): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($factura->estaAnulada()) {
            return back()->with('error', 'La factura ya está anulada.');
        }

        $factura->update([
            'estado'        => 'Anulada',
            'observaciones' => ($factura->observaciones ? $factura->observaciones . "\n" : '')
                               . "ANULADA: " . ($request->motivo ?? 'Sin motivo') . " — " . now()->format('d/m/Y H:i'),
        ]);

        return back()->with('success', 'Factura anulada.');
    }

    // Descargar PDF (skill: laravel-security — no {!! !!} con datos de usuario)
    public function pdf(Factura $factura): Response
    {
        abort_unless(
            auth()->user()->hasPermission('facturas.ver') || auth()->user()->isAdmin(),
            403
        );

        $factura->load([
            'orden.vehiculo.cliente.persona',
            'orden.vehiculo.modelo.marca',
            'orden.sucursal',
            'orden.detalles.servicio',
            'orden.repuestos.repuesto',
            'orden.pagos.metodoPago',
        ]);

        $pdf = Pdf::loadView('facturas.pdf', compact('factura'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("factura-{$factura->numero}.pdf");
    }
}
