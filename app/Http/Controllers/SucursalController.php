<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SucursalController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $sucursales = Sucursal::withCount([
            'mecanicos as mecanicos_activos' => fn($q) => $q->where('activo', true),
            'ordenes as ordenes_activas'     => fn($q) => $q->whereNotIn('estado', ['Entregado','Cancelado']),
        ])->orderBy('nombre')->get();

        return view('sucursales.index', compact('sucursales'));
    }

    public function mapa(): View
    {
        $sucursales = Sucursal::where('activo', true)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->withCount([
                'mecanicos as mecanicos_activos' => fn($q) => $q->where('activo', true),
                'ordenes as ordenes_activas'     => fn($q) => $q->whereNotIn('estado', ['Entregado','Cancelado']),
            ])
            ->get();

        return view('sucursales.mapa', compact('sucursales'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('sucursales.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad'    => ['nullable', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:100'],
            'latitud'   => ['nullable', 'numeric', 'between:-90,90'],
            'longitud'  => ['nullable', 'numeric', 'between:-180,180'],
            'activo'    => ['boolean'],
        ]);

        Sucursal::create($data);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal creada correctamente.');
    }

    public function edit(Sucursal $sucursal): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        return view('sucursales.edit', compact('sucursal'));
    }

    public function update(Request $request, Sucursal $sucursal): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad'    => ['nullable', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:100'],
            'latitud'   => ['nullable', 'numeric', 'between:-90,90'],
            'longitud'  => ['nullable', 'numeric', 'between:-180,180'],
            'activo'    => ['boolean'],
        ]);

        $sucursal->update($data);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal actualizada.');
    }
}
