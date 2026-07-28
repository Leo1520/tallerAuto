<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovimientoRequest;
use App\Models\InventarioSucursal;
use App\Models\MovimientoInventario;
use App\Models\Repuesto;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventarioController extends Controller
{
    // Vista principal: stock actual por sucursal
    public function index(Request $request): View
    {
        $this->authorize('verRepuestos', \App\Policies\InventarioPolicy::class);

        $sucursalId = $request->sucursal_id ?? Sucursal::where('activo', true)->value('id');

        $stocks = InventarioSucursal::with(['repuesto.proveedor', 'sucursal'])
            ->where('sucursal_id', $sucursalId)
            ->when($request->search, fn($q, $s) =>
                $q->whereHas('repuesto', fn($r) =>
                    $r->where('nombre', 'like', "%{$s}%")->orWhere('codigo', 'like', "%{$s}%")
                )
            )
            ->when($request->bajo_stock, fn($q) => $q->whereColumn('stock', '<=', 'stock_minimo'))
            ->orderBy('stock')
            ->paginate(20)
            ->withQueryString();

        $sucursales   = Sucursal::where('activo', true)->orderBy('nombre')->get();
        $repuestos    = Repuesto::where('activo', true)->orderBy('nombre')->get();
        $alertasBajoStock = InventarioSucursal::whereColumn('stock', '<=', 'stock_minimo')
            ->where('sucursal_id', $sucursalId)->count();

        return view('inventario.index', compact('stocks', 'sucursales', 'sucursalId', 'repuestos', 'alertasBajoStock'));
    }

    // Registrar entrada o salida de stock
    public function movimiento(StoreMovimientoRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $cantidad = (int) $request->cantidad;
            $signo    = $request->tipo === 'Salida' ? -1 : 1;

            // Upsert del stock en inventario_sucursal
            $inv = InventarioSucursal::firstOrCreate(
                ['sucursal_id' => $request->sucursal_id, 'repuesto_id' => $request->repuesto_id],
                ['stock' => 0, 'stock_minimo' => 0, 'updated_at' => now()]
            );

            $nuevoStock = $inv->stock + ($signo * $cantidad);

            if ($nuevoStock < 0) {
                throw new \Exception('Stock insuficiente para registrar la salida.');
            }

            $inv->update(['stock' => $nuevoStock, 'updated_at' => now()]);

            MovimientoInventario::create([
                'repuesto_id' => $request->repuesto_id,
                'sucursal_id' => $request->sucursal_id,
                'user_id'     => auth()->id(),
                'tipo'        => $request->tipo,
                'cantidad'    => $cantidad,
                'motivo'      => $request->motivo,
                'referencia'  => $request->referencia,
                'created_at'  => now(),
            ]);
        });

        return back()->with('success', 'Movimiento registrado correctamente.');
    }

    // Vista historial de movimientos
    public function movimientos(Request $request): View
    {
        $this->authorize('verRepuestos', \App\Policies\InventarioPolicy::class);

        $movimientos = MovimientoInventario::with(['repuesto', 'sucursal', 'user.persona'])
            ->when($request->repuesto_id, fn($q, $id) => $q->where('repuesto_id', $id))
            ->when($request->sucursal_id, fn($q, $id) => $q->where('sucursal_id', $id))
            ->when($request->tipo, fn($q, $t) => $q->where('tipo', $t))
            ->when($request->fecha_desde, fn($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->whereDate('created_at', '<=', $f))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();
        $repuestos  = Repuesto::where('activo', true)->orderBy('nombre')->get();

        return view('inventario.movimientos', compact('movimientos', 'sucursales', 'repuestos'));
    }

    // Actualizar stock mínimo de alerta
    public function actualizarStockMinimo(Request $request, InventarioSucursal $inventarioSucursal): RedirectResponse
    {
        $this->authorize('gestionarRepuestos', \App\Policies\InventarioPolicy::class);

        $request->validate(['stock_minimo' => ['required', 'integer', 'min:0']]);
        $inventarioSucursal->update(['stock_minimo' => $request->stock_minimo]);

        return back()->with('success', 'Stock mínimo actualizado.');
    }
}
