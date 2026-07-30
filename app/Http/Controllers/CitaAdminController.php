<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CitaAdminController extends Controller
{
    public function index(Request $request): View
    {
        $citas = Cita::with(['cliente.persona', 'sucursal', 'servicio', 'vehiculo.modelo.marca'])
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->fecha_desde, fn($q, $f) => $q->whereDate('fecha', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->whereDate('fecha', '<=', $f))
            ->when($request->search, fn($q, $s) => $q->whereHas('cliente.persona', fn($p) => $p->where('nombre', 'like', "%{$s}%")))
            ->orderByDesc('fecha')->orderByDesc('hora')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'pendientes'  => Cita::where('estado', 'pendiente')->count(),
            'confirmadas' => Cita::where('estado', 'confirmada')->whereDate('fecha', '>=', today())->count(),
            'hoy'         => Cita::whereDate('fecha', today())->count(),
        ];

        return view('citas.index', compact('citas', 'stats'));
    }

    public function show(Cita $cita): View
    {
        $cita->load(['cliente.persona', 'sucursal', 'servicio', 'vehiculo.modelo.marca']);
        return view('citas.show', compact('cita'));
    }

    public function update(Request $request, Cita $cita): RedirectResponse
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
        ]);

        $cita->update(['estado' => $request->estado]);

        $labels = [
            'confirmada' => 'Cita confirmada.',
            'cancelada'  => 'Cita cancelada.',
            'completada' => 'Cita marcada como completada.',
            'pendiente'  => 'Cita devuelta a pendiente.',
        ];

        return back()->with('success', $labels[$request->estado] ?? 'Estado actualizado.');
    }
}
