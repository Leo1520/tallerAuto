<?php

namespace App\Http\Controllers;

use App\Actions\Ordenes\CrearOrdenAction;
use App\Http\Requests\CambiarEstadoOrdenRequest;
use App\Http\Requests\StoreOrdenServicioRequest;
use App\Http\Requests\UpdateOrdenServicioRequest;
use App\Models\Mecanico;
use App\Models\OrdenServicio;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrdenServicioController extends Controller
{
    // Estados en orden de progresión
    const ESTADOS = [
        'Recibido', 'En diagnóstico', 'En reparación',
        'Esperando repuestos', 'Listo', 'Entregado', 'Cancelado',
    ];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', OrdenServicio::class);

        $ordenes = OrdenServicio::with(['vehiculo.cliente.persona', 'mecanico.persona', 'sucursal'])
            ->when($request->search, function ($q, $s) {
                $q->where('numero', 'like', "%{$s}%")
                  ->orWhereHas('vehiculo', fn($v) => $v->where('placa', 'like', "%{$s}%"))
                  ->orWhereHas('vehiculo.cliente.persona', fn($p) => $p->where('nombre', 'like', "%{$s}%"));
            })
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->prioridad, fn($q, $p) => $q->where('prioridad', $p))
            ->when($request->mecanico_id, fn($q, $id) => $q->where('mecanico_id', $id))
            ->when($request->fecha_desde, fn($q, $f) => $q->whereDate('fecha_ingreso', '>=', $f))
            ->when($request->fecha_hasta, fn($q, $f) => $q->whereDate('fecha_ingreso', '<=', $f))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $mecanicos = Mecanico::with('persona')->where('activo', true)->get();

        return view('ordenes.index', compact('ordenes', 'mecanicos'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', OrdenServicio::class);

        $vehiculos  = Vehiculo::with('cliente.persona', 'modelo.marca')->where('activo', true)->get();
        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();
        $mecanicos  = Mecanico::with('persona', 'especialidad')->where('activo', true)->get();
        $servicios  = Servicio::with('tipoServicio')->where('activo', true)->orderBy('nombre')->get();
        $vehiculoSeleccionado = $request->vehiculo_id
            ? Vehiculo::with('cliente.persona', 'modelo.marca')->find($request->vehiculo_id)
            : null;

        return view('ordenes.create', compact('vehiculos', 'sucursales', 'mecanicos', 'servicios', 'vehiculoSeleccionado'));
    }

    public function store(StoreOrdenServicioRequest $request, CrearOrdenAction $action): RedirectResponse
    {
        $orden = $action->execute($request->validated(), $request->validated()['servicios']);

        return redirect()->route('ordenes.show', $orden)
            ->with('success', "Orden {$orden->numero} creada correctamente.");
    }

    public function show(OrdenServicio $orden): View
    {
        $this->authorize('view', $orden);

        $orden->load([
            'vehiculo.cliente.persona',
            'vehiculo.modelo.marca',
            'sucursal',
            'mecanico.persona',
            'detalles.servicio.tipoServicio',
            'repuestos.repuesto',
            'pagos.metodoPago',
            'factura',
        ]);

        return view('ordenes.show', compact('orden'));
    }

    public function edit(OrdenServicio $orden): View
    {
        $this->authorize('update', $orden);

        $orden->load(['vehiculo.cliente.persona', 'vehiculo.modelo.marca', 'mecanico.persona', 'sucursal']);
        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();
        $mecanicos  = Mecanico::with('persona')->where('activo', true)->get();

        return view('ordenes.edit', compact('orden', 'sucursales', 'mecanicos'));
    }

    public function update(UpdateOrdenServicioRequest $request, OrdenServicio $orden): RedirectResponse
    {
        if (in_array($orden->estado, ['Entregado', 'Cancelado'])) {
            return back()->with('error', 'No se puede editar una orden entregada o cancelada.');
        }

        $orden->update($request->validated());

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Orden actualizada correctamente.');
    }

    public function cambiarEstado(CambiarEstadoOrdenRequest $request, OrdenServicio $orden): RedirectResponse
    {
        $nuevoEstado = $request->estado;

        // Si se entrega, registrar fecha real
        $extra = $nuevoEstado === 'Entregado' ? ['fecha_entrega_real' => now()] : [];

        $orden->update(array_merge(['estado' => $nuevoEstado], $extra));

        if ($request->observaciones) {
            $orden->update(['observaciones' => $orden->observaciones
                ? $orden->observaciones . "\n[{$nuevoEstado}] " . $request->observaciones
                : "[{$nuevoEstado}] " . $request->observaciones
            ]);
        }

        return back()->with('success', "Estado actualizado a «{$nuevoEstado}».");
    }

    public function destroy(OrdenServicio $orden): RedirectResponse
    {
        $this->authorize('delete', $orden);

        if (in_array($orden->estado, ['Entregado'])) {
            return back()->with('error', 'No se puede eliminar una orden ya entregada.');
        }

        DB::transaction(function () use ($orden) {
            $orden->detalles()->delete();
            $orden->repuestos()->delete();
            $orden->delete();
        });

        return redirect()->route('ordenes.index')
            ->with('success', 'Orden eliminada correctamente.');
    }
}
