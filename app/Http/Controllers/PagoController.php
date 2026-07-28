<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoRequest;
use App\Models\MetodoPago;
use App\Models\OrdenServicio;
use App\Models\Pago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class PagoController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // Lista de pagos (todos)
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

        return view('pagos.index', compact('pagos', 'metodos', 'totalConfirmado'));
    }

    // Formulario de pago para una orden
    public function create(Request $request): View
    {
        $orden = OrdenServicio::with(['vehiculo.cliente.persona', 'pagos'])
            ->findOrFail($request->orden_id);

        $metodos   = MetodoPago::where('activo', true)->orderBy('nombre')->get();
        $pendiente = $orden->total - $orden->pagos->where('estado', 'Confirmado')->sum('monto');

        // Si hay un método Stripe, creamos el PaymentIntent de inmediato
        $stripeClientSecret = null;
        $metodosStripe      = $metodos->filter(fn($m) => $m->esStripe ?? false)->pluck('id');

        return view('pagos.create', compact('orden', 'metodos', 'pendiente', 'stripeClientSecret'));
    }

    // Crear PaymentIntent de Stripe (AJAX)
    public function crearIntent(Request $request): JsonResponse
    {
        $request->validate([
            'orden_id' => ['required', 'exists:ordenes_servicio,id'],
            'monto'    => ['required', 'numeric', 'min:1'],
        ]);

        $orden = OrdenServicio::findOrFail($request->orden_id);

        $intent = PaymentIntent::create([
            'amount'   => (int) round($request->monto * 100), // en centavos
            'currency' => 'bob',
            'metadata' => [
                'orden_id'     => $orden->id,
                'orden_numero' => $orden->numero,
            ],
        ]);

        return response()->json(['client_secret' => $intent->client_secret]);
    }

    // Registrar pago (manual o confirmación Stripe)
    public function store(StorePagoRequest $request): RedirectResponse
    {
        $orden   = OrdenServicio::with('pagos')->findOrFail($request->orden_id);
        $metodo  = MetodoPago::findOrFail($request->metodo_pago_id);
        $esStripe = str_contains(strtolower($metodo->nombre), 'stripe') ||
                    str_contains(strtolower($metodo->nombre), 'tarjeta');

        // Si es Stripe necesitamos el PaymentIntent confirmado
        if ($esStripe && !$request->filled('payment_intent')) {
            return back()->withErrors(['payment_intent' => 'El pago con tarjeta no fue completado.'])->withInput();
        }

        DB::transaction(function () use ($request, $orden, $metodo, $esStripe) {
            $estado = $esStripe ? 'Confirmado' : 'Pendiente';

            if ($esStripe) {
                // Verificar que el PaymentIntent esté realmente pagado
                $intent = PaymentIntent::retrieve($request->payment_intent);
                if ($intent->status !== 'succeeded') {
                    throw new \Exception('El PaymentIntent no está confirmado.');
                }
                $estado = 'Confirmado';
            }

            Pago::create([
                'orden_id'            => $orden->id,
                'metodo_pago_id'      => $metodo->id,
                'user_id'             => auth()->id(),
                'monto'               => $request->monto,
                'moneda'              => 'BOB',
                'estado'              => $estado,
                'referencia'          => $request->referencia,
                'transaccion_externa' => $request->payment_intent,
                'webhook_verificado'  => $esStripe,
                'fecha_confirmacion'  => $esStripe ? now() : null,
                'observaciones'       => $request->observaciones,
            ]);

            // Marcar orden como pagada si el total está cubierto
            $totalPagado = $orden->pagos()->where('estado', 'Confirmado')->sum('monto') + ($esStripe ? $request->monto : 0);
            if ($totalPagado >= $orden->total && $orden->estado !== 'Cancelado') {
                $orden->update(['estado' => 'Entregado']);
            }
        });

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Pago registrado correctamente.');
    }

    // Confirmar pago manual (cambiar de Pendiente → Confirmado)
    public function confirmar(Pago $pago): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.confirmar'), 403);

        $pago->update([
            'estado'             => 'Confirmado',
            'fecha_confirmacion' => now(),
        ]);

        return back()->with('success', 'Pago confirmado.');
    }

    // Anular pago
    public function anular(Pago $pago): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($pago->estado === 'Confirmado' && $pago->esStripe()) {
            // Reembolso en Stripe
            \Stripe\Refund::create(['payment_intent' => $pago->transaccion_externa]);
        }

        $pago->update(['estado' => 'Anulado']);

        return back()->with('success', 'Pago anulado.');
    }

    // Webhook de Stripe
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook');

        if (!$webhookSecret) {
            return response()->json(['error' => 'Webhook secret not configured'], 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            Pago::where('transaccion_externa', $intent->id)
                ->update([
                    'estado'             => 'Confirmado',
                    'webhook_verificado' => true,
                    'fecha_confirmacion' => now(),
                ]);
        }

        return response()->json(['received' => true]);
    }
}
