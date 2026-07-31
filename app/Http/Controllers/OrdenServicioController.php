<?php

namespace App\Http\Controllers;

use App\Actions\Ordenes\CrearOrdenAction;
use App\Http\Requests\CambiarEstadoOrdenRequest;
use App\Http\Requests\StoreOrdenServicioRequest;
use App\Http\Requests\UpdateOrdenServicioRequest;
use App\Models\DetalleOrdenRepuesto;
use App\Models\InventarioSucursal;
use App\Models\Mecanico;
use App\Models\MovimientoInventario;
use App\Models\OrdenServicio;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use App\Notifications\OrdenAsignadaNotification;
use App\Notifications\OrdenEstadoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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

        $mecanicos = Mecanico::with('persona')
            ->where(fn($q) => $q->where('activo', true)->orWhereHas('ordenes'))
            ->orderBy('id')
            ->get();

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
            'adjuntos.user.persona',
        ]);

        // Repuestos disponibles en la sucursal de la orden (con stock > 0)
        $repuestosDisponibles = $orden->sucursal_id
            ? InventarioSucursal::with('repuesto')
                ->where('sucursal_id', $orden->sucursal_id)
                ->where('stock', '>', 0)
                ->whereHas('repuesto', fn($q) => $q->where('activo', true))
                ->get()
            : collect();

        return view('ordenes.show', compact('orden', 'repuestosDisponibles'));
    }

    public function agregarRepuesto(Request $request, OrdenServicio $orden): RedirectResponse
    {
        $this->authorize('update', $orden);

        if (in_array($orden->estado, ['Entregado', 'Cancelado'])) {
            return back()->with('error', 'No se pueden agregar repuestos a una orden entregada o cancelada.');
        }

        $data = $request->validate([
            'repuesto_id'     => ['required', 'exists:repuestos,id'],
            'cantidad'        => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $orden) {
            $repuesto  = Repuesto::findOrFail($data['repuesto_id']);
            $cantidad  = (int) $data['cantidad'];
            $precio    = (float) $data['precio_unitario'];
            $subtotal  = round($cantidad * $precio, 2);

            // Verificar stock en la sucursal
            $inv = InventarioSucursal::where('sucursal_id', $orden->sucursal_id)
                ->where('repuesto_id', $repuesto->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($inv->stock < $cantidad) {
                throw new \Exception("Stock insuficiente. Disponible: {$inv->stock}.");
            }

            // Descontar stock
            $inv->decrement('stock', $cantidad);
            $inv->touch();

            // Registrar movimiento
            MovimientoInventario::create([
                'repuesto_id' => $repuesto->id,
                'sucursal_id' => $orden->sucursal_id,
                'user_id'     => auth()->id(),
                'tipo'        => 'Salida',
                'cantidad'    => $cantidad,
                'motivo'      => "Usado en orden {$orden->numero}",
                'created_at'  => now(),
            ]);

            // Agregar detalle a la orden
            DetalleOrdenRepuesto::create([
                'orden_id'        => $orden->id,
                'repuesto_id'     => $repuesto->id,
                'cantidad'        => $cantidad,
                'precio_unitario' => $precio,
                'subtotal'        => $subtotal,
            ]);

            // Recalcular total de la orden
            $orden->recalcularTotal();
        });

        return back()->with('success', 'Repuesto agregado y stock descontado.');
    }

    public function quitarRepuesto(OrdenServicio $orden, DetalleOrdenRepuesto $detalle): RedirectResponse
    {
        $this->authorize('update', $orden);

        if (in_array($orden->estado, ['Entregado', 'Cancelado'])) {
            return back()->with('error', 'No se pueden quitar repuestos de una orden entregada o cancelada.');
        }

        DB::transaction(function () use ($orden, $detalle) {
            // Devolver stock
            $inv = InventarioSucursal::firstOrCreate(
                ['sucursal_id' => $orden->sucursal_id, 'repuesto_id' => $detalle->repuesto_id],
                ['stock' => 0, 'stock_minimo' => 0, 'updated_at' => now()]
            );
            $inv->increment('stock', $detalle->cantidad);
            $inv->touch();

            // Registrar movimiento de devolución
            MovimientoInventario::create([
                'repuesto_id' => $detalle->repuesto_id,
                'sucursal_id' => $orden->sucursal_id,
                'user_id'     => auth()->id(),
                'tipo'        => 'Entrada',
                'cantidad'    => $detalle->cantidad,
                'motivo'      => "Devolución al quitar de orden {$orden->numero}",
                'created_at'  => now(),
            ]);

            $detalle->delete();
            $orden->recalcularTotal();
        });

        return back()->with('success', 'Repuesto quitado y stock devuelto.');
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

        $mecAnterior = $orden->mecanico_id;
        $orden->update($request->validated());

        // Notificar al mecánico si fue recién asignado
        if ($orden->mecanico_id && $orden->mecanico_id !== $mecAnterior) {
            $orden->load(['mecanico.persona', 'vehiculo.cliente.persona']);
            $email  = $orden->mecanico->persona->email ?? null;
            $nombre = $orden->mecanico->persona->nombre ?? '';
            if ($email) {
                Notification::route('mail', [$email => $nombre])
                    ->notify(new OrdenAsignadaNotification($orden));
            }
        }

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

        // Notificar al cliente cuando el vehículo está listo o fue entregado
        if (in_array($nuevoEstado, ['Listo', 'Entregado'])) {
            $email  = $orden->vehiculo->cliente->persona->email ?? null;
            $nombre = $orden->vehiculo->cliente->persona->nombre ?? '';
            if ($email) {
                $orden->load(['vehiculo.cliente.persona', 'vehiculo']);
                Notification::route('mail', [$email => $nombre])
                    ->notify(new OrdenEstadoNotification($orden));
            }
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
