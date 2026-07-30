<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoRequest;
use App\Models\MetodoPago;
use App\Models\OrdenServicio;
use App\Models\Pago;
use App\Services\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function __construct(private PagoService $pagoService) {}

    // ─── Admin: lista de pagos ────────────────────────────────────────────

    public function index(Request $request): View
    {
        $pagos = Pago::with(['orden.vehiculo.cliente.persona', 'metodoPago', 'user.persona'])
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->metodo_pago_id, fn($q, $id) => $q->where('metodo_pago_id', $id))
            ->when($request->fecha_desde, fn($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->whereDate('created_at', '<=', $f))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $metodos = MetodoPago::where('activo', true)->orderBy('nombre')->get();

        $totalConfirmado = Pago::where('estado', 'Confirmado')
            ->when($request->fecha_desde, fn($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->whereDate('created_at', '<=', $f))
            ->sum('monto');

        $pendientesRevision = Pago::where('estado', 'En revisión')->count();

        return view('pagos.index', compact('pagos', 'metodos', 'totalConfirmado', 'pendientesRevision'));
    }

    // ─── Admin: formulario registro manual ───────────────────────────────

    public function create(Request $request): View
    {
        $orden = OrdenServicio::with(['vehiculo.cliente.persona', 'pagos'])
            ->findOrFail($request->orden_id);

        $metodos   = MetodoPago::where('activo', true)->orderBy('nombre')->get();
        $pendiente = $orden->total - $orden->pagos->where('estado', 'Confirmado')->sum('monto');

        return view('pagos.create', compact('orden', 'metodos', 'pendiente'));
    }

    // ─── Admin: registrar pago manual (siempre queda Pendiente) ──────────

    public function store(StorePagoRequest $request): RedirectResponse
    {
        $orden  = OrdenServicio::findOrFail($request->orden_id);
        $metodo = MetodoPago::findOrFail($request->metodo_pago_id);

        DB::transaction(function () use ($request, $orden, $metodo) {
            Pago::create([
                'orden_id'       => $orden->id,
                'metodo_pago_id' => $metodo->id,
                'user_id'        => auth()->id(),
                'monto'          => $request->monto,
                'moneda'         => 'BOB',
                'estado'         => 'Pendiente',
                'referencia'     => $request->referencia,
                'observaciones'  => $request->observaciones,
            ]);
        });

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Pago registrado. Pendiente de confirmación por caja.');
    }

    // ─── Admin: confirmar pago pendiente (protegido contra doble ejecución) ─

    public function confirmar(Pago $pago): RedirectResponse
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.confirmar'),
            403
        );

        try {
            $this->pagoService->confirmarPago($pago, auth()->user());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['pago' => $e->getMessage()]);
        }

        return back()->with('success', 'Pago confirmado.');
    }

    // ─── Admin: anular pago ───────────────────────────────────────────────

    public function anular(Pago $pago): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        abort_unless(
            in_array($pago->estado, ['Pendiente', 'Confirmado', 'En revisión']),
            422,
            'Este pago no puede ser anulado.'
        );

        $pago->update(['estado' => 'Anulado']);

        return back()->with('success', 'Pago anulado.');
    }

    // ─── Admin: cola de revisión QR ──────────────────────────────────────

    public function revisionIndex(Request $request): View
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.confirmar'),
            403
        );

        $pagos = Pago::with(['orden.vehiculo.cliente.persona', 'metodoPago', 'comprobante'])
            ->where('estado', 'En revisión')
            ->latest()
            ->paginate(20);

        return view('pagos.revision.index', compact('pagos'));
    }

    public function revisionShow(Pago $pago): View
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.confirmar'),
            403
        );

        $pago->load(['orden.vehiculo.cliente.persona', 'metodoPago', 'comprobante', 'user.persona']);

        return view('pagos.revision.show', compact('pago'));
    }

    public function cajeroValidar(Request $request, Pago $pago): RedirectResponse
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.confirmar'),
            403
        );
        abort_unless($pago->estado === 'En revisión', 422, 'Solo se pueden validar pagos en revisión.');

        try {
            $this->pagoService->confirmarPago($pago, auth()->user());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['pago' => $e->getMessage()]);
        }

        return redirect()->route('pagos.revision.index')
            ->with('success', "Pago #{$pago->id} confirmado. Factura generada si el total está cubierto.");
    }

    public function cajeroRechazar(Request $request, Pago $pago): RedirectResponse
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.confirmar'),
            403
        );
        abort_unless($pago->estado === 'En revisión', 422, 'Solo se pueden rechazar pagos en revisión.');

        $request->validate(['motivo' => 'required|string|max:500']);

        $this->pagoService->rechazarPago($pago, $request->motivo, auth()->user());

        return redirect()->route('pagos.revision.index')
            ->with('warning', 'Pago rechazado. Se notificó al cliente.');
    }

    // ─── Admin: pago en efectivo ──────────────────────────────────────────

    public function efectivoCreate(Request $request): View
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.crear'),
            403
        );

        $orden = null;
        if ($request->filled('orden_id')) {
            $orden = OrdenServicio::with(['vehiculo.cliente.persona', 'pagos'])
                ->findOrFail($request->orden_id);
        }

        $metodoEfectivo = MetodoPago::where('nombre', 'Efectivo')->first();

        return view('pagos.efectivo', compact('orden', 'metodoEfectivo'));
    }

    public function efectivoStore(Request $request): RedirectResponse
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.crear'),
            403
        );

        $request->validate([
            'orden_id'       => 'required|exists:ordenes_servicio,id',
            'monto'          => 'required|numeric|min:0.01',
            'monto_recibido' => 'required|numeric|min:0.01',
        ], [
            'monto_recibido.min' => 'El monto recibido debe ser mayor a cero.',
        ]);

        $orden          = OrdenServicio::with(['pagos', 'vehiculo.cliente.persona'])->findOrFail($request->orden_id);
        $metodoEfectivo = MetodoPago::where('nombre', 'Efectivo')->firstOrFail();

        if ($request->monto_recibido < $request->monto) {
            return back()->withErrors(['monto_recibido' => 'El monto recibido es menor al monto a cobrar.'])->withInput();
        }

        $pago = $this->pagoService->registrarEfectivo(
            $orden,
            $metodoEfectivo,
            (float) $request->monto,
            (float) $request->monto_recibido,
            auth()->user()
        );

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Pago en efectivo registrado. Cambio: Bs ' . number_format($request->monto_recibido - $request->monto, 2));
    }

    // ─── Cliente: página QR ───────────────────────────────────────────────

    public function qrMostrar(OrdenServicio $orden): View
    {
        $this->autorizarClienteOrden($orden);

        $orden->load(['pagos', 'vehiculo.cliente.persona', 'factura']);
        $metodoQr  = MetodoPago::where('nombre', 'QR Banco Ganadero')->first();
        $pendiente = $orden->montoPendiente();

        abort_if($pendiente <= 0, 302, redirect()->route('cliente.ordenes.show', $orden));

        return view('cliente.pagar.qr', compact('orden', 'metodoQr', 'pendiente'));
    }

    public function qrClienteConfirmar(Request $request, OrdenServicio $orden): RedirectResponse
    {
        $this->autorizarClienteOrden($orden);

        $orden->load('pagos');
        $pendiente = $orden->montoPendiente();

        abort_if($pendiente <= 0, 403, 'Esta orden ya está pagada.');

        $request->validate([
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'comprobante.mimes' => 'Solo se aceptan imágenes (JPG, PNG) o PDF.',
            'comprobante.max'   => 'El archivo no puede superar 5 MB.',
        ]);

        $metodoQr = MetodoPago::where('nombre', 'QR Banco Ganadero')->firstOrFail();

        $this->pagoService->registrarPagoEnRevision(
            $orden,
            $metodoQr,
            $pendiente,
            $request->file('comprobante')
        );

        return redirect()->route('cliente.pagar.enviado', ['orden' => $orden->id])
            ->with('success', 'Comprobante enviado. El cajero lo revisará pronto.');
    }

    public function pagoEnviado(Request $request): View
    {
        $ordenId = $request->query('orden');

        // Verificar propiedad: solo el cliente dueño de la orden puede ver esta página
        $orden = null;
        if ($ordenId) {
            $orden = OrdenServicio::find($ordenId);
            if ($orden) {
                $personaId = $orden->vehiculo?->cliente?->persona_id;
                if (! $personaId || auth()->user()->persona_id !== $personaId) {
                    $orden = null; // no revelar datos de otra orden
                }
            }
        }

        return view('cliente.pagar.enviado', compact('orden'));
    }

    // ─── Helper privado ───────────────────────────────────────────────────

    private function autorizarClienteOrden(OrdenServicio $orden): void
    {
        $user      = auth()->user();
        $personaId = $orden->vehiculo?->cliente?->persona_id;
        abort_unless($personaId && $user->persona_id === $personaId, 403);
    }
}
