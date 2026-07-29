<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\TipoServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicioController extends Controller
{
    public function index(Request $request): View
    {
        $servicios = Servicio::with('tipoServicio')
            ->when($request->search, fn($q, $s) =>
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('descripcion', 'like', "%{$s}%")
            )
            ->when($request->tipo_servicio_id, fn($q, $id) => $q->where('tipo_servicio_id', $id))
            ->when($request->filled('activo'), fn($q) => $q->where('activo', $request->boolean('activo')))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        $tipos = TipoServicio::orderBy('nombre')->get();

        return view('servicios.index', compact('servicios', 'tipos'));
    }

    public function create(): View
    {
        $tipos = TipoServicio::orderBy('nombre')->get();
        return view('servicios.create', compact('tipos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'            => 'required|string|max:100',
            'tipo_servicio_id'  => 'nullable|exists:tipos_servicio,id',
            'descripcion'       => 'nullable|string|max:500',
            'precio'            => 'required|numeric|min:0',
            'tiempo_estimado'   => 'nullable|integer|min:1|max:9999',
            'requiere_repuestos'=> 'boolean',
            'activo'            => 'boolean',
        ]);

        $data['requiere_repuestos'] = $request->boolean('requiere_repuestos');
        $data['activo']             = $request->boolean('activo', true);

        Servicio::create($data);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Servicio $servicio): View
    {
        $tipos = TipoServicio::orderBy('nombre')->get();
        return view('servicios.edit', compact('servicio', 'tipos'));
    }

    public function update(Request $request, Servicio $servicio): RedirectResponse
    {
        $data = $request->validate([
            'nombre'            => 'required|string|max:100',
            'tipo_servicio_id'  => 'nullable|exists:tipos_servicio,id',
            'descripcion'       => 'nullable|string|max:500',
            'precio'            => 'required|numeric|min:0',
            'tiempo_estimado'   => 'nullable|integer|min:1|max:9999',
            'requiere_repuestos'=> 'boolean',
            'activo'            => 'boolean',
        ]);

        $data['requiere_repuestos'] = $request->boolean('requiere_repuestos');
        $data['activo']             = $request->boolean('activo', true);

        $servicio->update($data);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $servicio->delete();

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio eliminado.');
    }
}
