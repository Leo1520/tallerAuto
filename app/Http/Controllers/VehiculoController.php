<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehiculoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Vehiculo::class);

        $vehiculos = Vehiculo::with(['cliente.persona', 'modelo.marca'])
            ->when($request->search, function ($q, $search) {
                $q->where('placa', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhereHas('cliente.persona', fn($p) => $p->where('nombre', 'like', "%{$search}%"));
            })
            ->when($request->marca_id, fn($q, $id) => $q->whereHas('modelo', fn($m) => $m->where('marca_id', $id)))
            ->when($request->filled('activo'), fn($q) => $q->where('activo', $request->boolean('activo')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $marcas = Marca::where('activo', true)->orderBy('nombre')->get();

        return view('vehiculos.index', compact('vehiculos', 'marcas'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Vehiculo::class);

        $marcas   = Marca::where('activo', true)->with(['modelos' => fn($q) => $q->where('activo', true)->orderBy('nombre')])->orderBy('nombre')->get();
        $clientes = Cliente::with('persona')->get()->sortBy('persona.nombre')->values();
        $clienteSeleccionado = $request->cliente_id ? Cliente::with('persona')->find($request->cliente_id) : null;

        return view('vehiculos.create', compact('marcas', 'clientes', 'clienteSeleccionado'));
    }

    public function store(StoreVehiculoRequest $request): RedirectResponse
    {
        $vehiculo = Vehiculo::create($request->validated() + ['activo' => true]);

        return redirect()->route('vehiculos.show', $vehiculo)
            ->with('success', 'Vehículo registrado correctamente.');
    }

    public function show(Vehiculo $vehiculo): View
    {
        $this->authorize('view', $vehiculo);

        $vehiculo->load([
            'cliente.persona',
            'modelo.marca',
            'ordenes' => fn($q) => $q->with('mecanico.persona')->latest()->take(10),
            'mantenimientos' => fn($q) => $q->orderBy('proxima_fecha'),
        ]);

        return view('vehiculos.show', compact('vehiculo'));
    }

    public function edit(Vehiculo $vehiculo): View
    {
        $this->authorize('update', $vehiculo);

        $vehiculo->load(['cliente.persona', 'modelo.marca']);
        $marcas   = Marca::where('activo', true)->with(['modelos' => fn($q) => $q->where('activo', true)->orderBy('nombre')])->orderBy('nombre')->get();
        $clientes = Cliente::with('persona')->get();

        return view('vehiculos.edit', compact('vehiculo', 'marcas', 'clientes'));
    }

    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo): RedirectResponse
    {
        $vehiculo->update($request->validated());

        return redirect()->route('vehiculos.show', $vehiculo)
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehiculo $vehiculo): RedirectResponse
    {
        $this->authorize('delete', $vehiculo);

        if ($vehiculo->ordenes()->exists()) {
            return back()->with('error', 'No se puede eliminar un vehículo con órdenes de servicio registradas.');
        }

        $vehiculo->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }
}
