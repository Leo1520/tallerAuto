<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Persona;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Cliente::class);

        $clientes = Cliente::with('persona')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('persona', fn($p) => $p->where('nombre', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%"))
                  ->orWhere('numero_documento', 'like', "%{$search}%");
            })
            ->when($request->ciudad, fn($q, $ciudad) => $q->where('ciudad', $ciudad))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $ciudades = Cliente::select('ciudad')->distinct()->whereNotNull('ciudad')->orderBy('ciudad')->pluck('ciudad');

        return view('clientes.index', compact('clientes', 'ciudades'));
    }

    public function create(): View
    {
        $this->authorize('create', Cliente::class);

        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $persona = Persona::create([
                'nombre'   => $request->nombre,
                'telefono' => $request->telefono,
                'email'    => $request->email,
            ]);

            Cliente::create([
                'persona_id'       => $persona->id,
                'direccion'        => $request->direccion,
                'ciudad'           => $request->ciudad,
                'tipo_documento'   => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
            ]);
        });

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente): View
    {
        $this->authorize('view', $cliente);

        $cliente->load([
            'persona',
            'vehiculos.modelo.marca',
            'vehiculos.ordenes' => fn($q) => $q->latest()->take(1),
        ]);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente): View
    {
        $this->authorize('update', $cliente);

        $cliente->load('persona');

        return view('clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        DB::transaction(function () use ($request, $cliente) {
            $cliente->persona->update([
                'nombre'   => $request->nombre,
                'telefono' => $request->telefono,
                'email'    => $request->email,
            ]);

            $cliente->update([
                'direccion'        => $request->direccion,
                'ciudad'           => $request->ciudad,
                'tipo_documento'   => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
            ]);
        });

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $this->authorize('delete', $cliente);

        if ($cliente->vehiculos()->exists()) {
            return back()->with('error', 'No se puede eliminar un cliente con vehículos registrados.');
        }

        DB::transaction(function () use ($cliente) {
            $personaId = $cliente->persona_id;
            $cliente->delete();
            Persona::destroy($personaId);
        });

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
