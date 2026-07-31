<?php

namespace App\Http\Controllers;

use App\Models\ConsultaRepuesto;
use App\Models\User;
use App\Notifications\ConsultaAtendidaNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
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
            'pendientes' => ConsultaRepuesto::where('estado', 'Pendiente')->count(),
            'atendidas'  => ConsultaRepuesto::where('estado', 'Atendida')->count(),
            'total'      => ConsultaRepuesto::count(),
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
        ]);

        $estadoAnterior = $consultaRepuesto->estado;
        $consultaRepuesto->update(['estado' => $request->estado]);

        // Notificar al cliente solo cuando se marca como Atendida (no en cada cambio)
        if ($request->estado === 'Atendida' && $estadoAnterior !== 'Atendida') {
            $consultaRepuesto->load('repuesto');
            $this->notificarCliente($consultaRepuesto);
        }

        return back()->with('success', 'Estado actualizado.' .
            ($request->estado === 'Atendida' ? ' Se envió confirmación al cliente.' : '')
        );
    }

    private function notificarCliente(ConsultaRepuesto $consulta): void
    {
        // 1. Intentar notificar por cuenta de usuario si existe
        if ($consulta->cliente?->persona?->email) {
            $user = User::where('email', $consulta->cliente->persona->email)->first();
            if ($user) {
                $user->notify(new ConsultaAtendidaNotification($consulta));
                return;
            }
        }

        // 2. Usar el email guardado en la consulta como fallback
        $email = $consulta->email ?? $consulta->cliente?->persona?->email;
        if ($email) {
            Notification::route('mail', $email)
                ->notify(new ConsultaAtendidaNotification($consulta));
        }
    }
}
