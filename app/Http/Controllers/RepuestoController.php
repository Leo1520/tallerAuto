<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepuestoRequest;
use App\Http\Requests\UpdateRepuestoRequest;
use App\Models\InventarioSucursal;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RepuestoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('verRepuestos', \App\Policies\InventarioPolicy::class);

        $repuestos = Repuesto::with(['proveedor', 'inventarios.sucursal'])
            ->when($request->search, fn($q, $s) =>
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('codigo', 'like', "%{$s}%")
            )
            ->when($request->proveedor_id, fn($q, $id) => $q->where('proveedor_id', $id))
            ->when($request->filled('activo'), fn($q) => $q->where('activo', $request->boolean('activo')))
            ->when($request->bajo_stock, fn($q) =>
                $q->whereHas('inventarios', fn($i) => $i->whereColumn('stock', '<=', 'stock_minimo'))
            )
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();

        return view('inventario.repuestos.index', compact('repuestos', 'proveedores'));
    }

    public function create(): View
    {
        $this->authorize('gestionarRepuestos', \App\Policies\InventarioPolicy::class);

        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $sucursales  = Sucursal::where('activo', true)->orderBy('nombre')->get();

        return view('inventario.repuestos.create', compact('proveedores', 'sucursales'));
    }

    public function store(StoreRepuestoRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $repuesto = Repuesto::create($request->only(['proveedor_id', 'nombre', 'codigo', 'descripcion', 'precio_compra', 'precio_venta']));

            // Stock inicial en sucursal
            if ($request->filled('sucursal_id_inicial') && $request->stock_inicial > 0) {
                InventarioSucursal::create([
                    'sucursal_id' => $request->sucursal_id_inicial,
                    'repuesto_id' => $repuesto->id,
                    'stock'       => $request->stock_inicial,
                    'stock_minimo'=> $request->stock_minimo ?? 0,
                    'updated_at'  => now(),
                ]);

                MovimientoInventario::create([
                    'repuesto_id' => $repuesto->id,
                    'sucursal_id' => $request->sucursal_id_inicial,
                    'user_id'     => auth()->id(),
                    'tipo'        => 'Entrada',
                    'cantidad'    => $request->stock_inicial,
                    'motivo'      => 'Stock inicial',
                    'created_at'  => now(),
                ]);
            }
        });

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto registrado correctamente.');
    }

    public function show(Repuesto $repuesto): View
    {
        $this->authorize('verRepuestos', \App\Policies\InventarioPolicy::class);

        $repuesto->load(['proveedor', 'inventarios.sucursal']);
        $movimientos = $repuesto->movimientos()
            ->with(['sucursal', 'user.persona'])
            ->latest('created_at')
            ->paginate(20);

        return view('inventario.repuestos.show', compact('repuesto', 'movimientos'));
    }

    public function edit(Repuesto $repuesto): View
    {
        $this->authorize('gestionarRepuestos', \App\Policies\InventarioPolicy::class);

        $repuesto->load('proveedor');
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();

        return view('inventario.repuestos.edit', compact('repuesto', 'proveedores'));
    }

    public function update(UpdateRepuestoRequest $request, Repuesto $repuesto): RedirectResponse
    {
        $repuesto->update($request->validated());

        return redirect()->route('repuestos.show', $repuesto)
            ->with('success', 'Repuesto actualizado correctamente.');
    }

    public function destroy(Repuesto $repuesto): RedirectResponse
    {
        $this->authorize('gestionarRepuestos', \App\Policies\InventarioPolicy::class);

        if ($repuesto->inventarios()->where('stock', '>', 0)->exists()) {
            return back()->with('error', 'No se puede eliminar un repuesto con stock disponible.');
        }

        $repuesto->delete();

        return redirect()->route('repuestos.index')
            ->with('success', 'Repuesto eliminado.');
    }
}
