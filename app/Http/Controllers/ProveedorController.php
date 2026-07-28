<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('verRepuestos', \App\Policies\InventarioPolicy::class);

        $proveedores = Proveedor::withCount('repuestos')
            ->when($request->search, fn($q, $s) =>
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('nit', 'like', "%{$s}%")
                  ->orWhere('ciudad', 'like', "%{$s}%")
            )
            ->when($request->filled('activo'), fn($q) => $q->where('activo', $request->boolean('activo')))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('inventario.proveedores.index', compact('proveedores'));
    }

    public function create(): View
    {
        $this->authorize('gestionarRepuestos', \App\Policies\InventarioPolicy::class);
        return view('inventario.proveedores.create');
    }

    public function store(StoreProveedorRequest $request): RedirectResponse
    {
        Proveedor::create($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        $this->authorize('gestionarRepuestos', \App\Policies\InventarioPolicy::class);
        return view('inventario.proveedores.edit', compact('proveedor'));
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $this->authorize('gestionarRepuestos', \App\Policies\InventarioPolicy::class);

        if ($proveedor->repuestos()->exists()) {
            return back()->with('error', 'No se puede eliminar un proveedor con repuestos asociados.');
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado.');
    }
}
