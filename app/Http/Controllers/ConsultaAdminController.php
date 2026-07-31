<?php

namespace App\Http\Controllers;

use App\Models\ConsultaRepuesto;
use App\Models\InventarioSucursal;
use App\Models\MovimientoCaja;
use App\Models\User;
use App\Notifications\ConsultaAtendidaNotification;
use App\Notifications\ConsultaPagoConfirmadoNotification;
use App\Notifications\ConsultaPagoRechazadoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConsultaAdminController extends Controller
{
    public function index(Request $request): View
    {
        $consultas = ConsultaRepuesto::with(['repuesto', 'cliente.persona'])
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->search, fn($q, $s) => $q->where(function ($sub) use ($s) {
                $sub->where('nombre', 'like', "%{$s}%")
                    ->orWhereHas('repuesto', fn($r) => $r->where('nombre', 'like', "%{$s}%"));
            }))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'pendientes'   => ConsultaRepuesto::where('estado', 'Pendiente')->count(),
            'atendidas'    => ConsultaRepuesto::where('estado', 'Atendida')->count(),
            'en_revision'  => ConsultaRepuesto::where('pago_estado', 'En revisión')->count(),
            'total'        => ConsultaRepuesto::count(),
        ];

        return view('consultas.index', compact('consultas', 'stats'));
    }

    public function show(ConsultaRepuesto $consultaRepuesto): View
    {
        $consultaRepuesto->load(['repuesto.inventarios.sucursal', 'cliente.persona']);
        return view('consultas.show', compact('consultaRepuesto'));
    }

    public function update(Request $request, ConsultaRepuesto $consultaRepuesto): RedirectResponse
    {
        $request->validate([
            'estado' => 'required|in:Pendiente,Atendida,Cancelada',
            'qr'     => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $estadoAnterior = $consultaRepuesto->estado;

        $datos = ['estado' => $request->estado];

        // Generar token si aún no tiene
        $datos['token'] = $consultaRepuesto->token ?? Str::random(48);

        // Guardar QR si se adjuntó uno nuevo
        if ($request->hasFile('qr')) {
            $datos['qr_path'] = $request->file('qr')
                ->store("qr_consultas/{$consultaRepuesto->id}", 'public');
        }

        $consultaRepuesto->update($datos);

        if ($request->estado === 'Atendida' && $estadoAnterior !== 'Atendida') {
            $consultaRepuesto->load('repuesto');
            $this->notificarCliente($consultaRepuesto);
        }

        return back()->with('success', 'Estado actualizado.' .
            ($request->estado === 'Atendida' ? ' Se envió el QR de pago al cliente.' : '')
        );
    }

    // ─── Admin confirma pago del cliente ─────────────────────────────────

    public function confirmarPago(Request $request, ConsultaRepuesto $consultaRepuesto): RedirectResponse
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.confirmar'),
            403
        );

        $consulta = null;

        try {
            DB::transaction(function () use ($request, $consultaRepuesto, &$consulta) {
                // Releer con lock exclusivo de fila — la segunda petición concurrente
                // esperará aquí hasta que la primera confirme y haga COMMIT.
                // Cuando la segunda obtenga el lock, el estado ya será 'Confirmado'
                // y el guard de abajo la rechazará controladamente.
                $consulta = ConsultaRepuesto::lockForUpdate()->findOrFail($consultaRepuesto->id);

                if ($consulta->pago_estado !== 'En revisión') {
                    throw new \RuntimeException('No hay comprobante pendiente o el pago ya fue procesado.');
                }

                $consulta->load('repuesto');
                $monto = round($consulta->cantidad * ($consulta->repuesto->precio_venta ?? 0), 2);

                $consulta->update([
                    'pago_estado'        => 'Confirmado',
                    'pago_notas'         => $request->notas,
                    'monto'              => $monto,
                    'pago_confirmado_at' => now(),
                ]);

                // Descontar stock del inventario
                InventarioSucursal::where('repuesto_id', $consulta->repuesto_id)
                    ->orderByDesc('stock')
                    ->first()
                    ?->decrement('stock', $consulta->cantidad);

                // Registrar ingreso en caja.
                // El índice UNIQUE en movimientos_caja.referencia actúa como
                // red de seguridad final si el lock falla por algún motivo.
                MovimientoCaja::create([
                    'pago_id'    => null,
                    'user_id'    => auth()->id(),
                    'tipo'       => 'Ingreso',
                    'concepto'   => "Venta tienda: {$consulta->repuesto->nombre} × {$consulta->cantidad} (Consulta #{$consulta->id})",
                    'monto'      => $monto,
                    'referencia' => "consulta_{$consulta->id}",
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            // Violación del índice UNIQUE en referencia — segunda petición concurrente.
            return back()->with('error', 'El pago ya fue confirmado por otro administrador.');
        }

        // Notificación FUERA de la transacción: un fallo de email no revierte el pago.
        $this->notificarPago($consulta, 'confirmado');

        return back()->with('success', 'Pago confirmado y stock actualizado. Se notificó al cliente.');
    }

    // ─── Admin rechaza pago del cliente ──────────────────────────────────

    public function rechazarPago(Request $request, ConsultaRepuesto $consultaRepuesto): RedirectResponse
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission('pagos.confirmar'),
            403
        );
        abort_unless($consultaRepuesto->pago_estado === 'En revisión', 422, 'No hay comprobante pendiente.');

        $request->validate(['notas' => 'required|string|max:500']);

        $consultaRepuesto->update([
            'pago_estado' => 'Rechazado',
            'pago_notas'  => $request->notas,
        ]);

        $consultaRepuesto->load('repuesto');
        $this->notificarPago($consultaRepuesto, 'rechazado');

        return back()->with('warning', 'Pago rechazado. Se notificó al cliente.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    private function notificarCliente(ConsultaRepuesto $consulta): void
    {
        $email = $consulta->email
            ?? $consulta->cliente?->persona?->email;

        if (! $email) return;

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->notify(new ConsultaAtendidaNotification($consulta));
        } else {
            Notification::route('mail', $email)
                ->notify(new ConsultaAtendidaNotification($consulta));
        }
    }

    private function notificarPago(ConsultaRepuesto $consulta, string $tipo): void
    {
        $email = $consulta->email ?? $consulta->cliente?->persona?->email;
        if (! $email) return;

        $notif = $tipo === 'confirmado'
            ? new ConsultaPagoConfirmadoNotification($consulta)
            : new ConsultaPagoRechazadoNotification($consulta);

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->notify($notif);
        } else {
            Notification::route('mail', $email)->notify($notif);
        }
    }
}
