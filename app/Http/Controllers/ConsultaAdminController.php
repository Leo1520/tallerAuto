<?php

namespace App\Http\Controllers;

use App\Models\ConsultaRepuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $consultaRepuesto->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado de la solicitud actualizado.');
    }
}
