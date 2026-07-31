<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMecanicoRequest;
use App\Http\Requests\UpdateMecanicoRequest;
use App\Models\Especialidad;
use App\Models\Mecanico;
use App\Models\Persona;
use App\Models\Role;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MecanicoController extends Controller
{
    private function autorizar(string $permiso = 'mecanicos.ver'): void
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->hasPermission($permiso),
            403
        );
    }

    public function index(Request $request): View
    {
        $this->autorizar();

        $search      = $request->input('search');
        $sucursalId  = $request->input('sucursal_id');
        $especialidadId = $request->input('especialidad_id');
        $activo      = $request->input('activo', '');

        $mecanicos = Mecanico::with(['persona', 'sucursal', 'especialidad'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('persona', fn($p) => $p->where('nombre', 'like', "%{$search}%"))
                  ->orWhere('cedula', 'like', "%{$search}%");
            })
            ->when($sucursalId, fn($q, $s) => $q->where('sucursal_id', $s))
            ->when($especialidadId, fn($q, $e) => $q->where('especialidad_id', $e))
            ->when($activo !== '', fn($q) => $q->where('activo', (bool) $activo))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $sucursales    = Sucursal::orderBy('nombre')->get();
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('mecanicos.index', compact(
            'mecanicos', 'sucursales', 'especialidades',
            'search', 'sucursalId', 'especialidadId', 'activo'
        ));
    }

    public function create(): View
    {
        $this->autorizar('mecanicos.crear');

        $sucursales     = Sucursal::orderBy('nombre')->get();
        $especialidades = Especialidad::orderBy('nombre')->get();
        $roles          = Role::where('activo', true)->orderBy('nombre')->get();

        return view('mecanicos.create', compact('sucursales', 'especialidades', 'roles'));
    }

    public function store(StoreMecanicoRequest $request): RedirectResponse
    {
        $mecanico = DB::transaction(function () use ($request) {
            $persona = Persona::create([
                'nombre'   => $request->nombre,
                'telefono' => $request->telefono,
                'email'    => $request->email,
                'activo'   => true,
            ]);

            $mecanico = Mecanico::create([
                'persona_id'      => $persona->id,
                'sucursal_id'     => $request->sucursal_id,
                'especialidad_id' => $request->especialidad_id,
                'cedula'          => $request->cedula,
                'fecha_ingreso'   => $request->fecha_ingreso,
                'salario'         => $request->salario,
                'activo'          => true,
            ]);

            if ($request->boolean('crear_usuario') && $request->user_email) {
                $user = User::create([
                    'persona_id' => $persona->id,
                    'email'      => $request->user_email,
                    'password'   => $request->user_password,
                ]);

                if ($request->filled('roles')) {
                    $user->roles()->sync($request->input('roles', []));
                }
            }

            return $mecanico;
        });

        return redirect()->route('mecanicos.show', $mecanico)
            ->with('success', 'Mecánico registrado correctamente.');
    }

    public function show(Mecanico $mecanico): View
    {
        $this->autorizar();

        $mecanico->load(['persona', 'sucursal', 'especialidad', 'persona.user.roles']);

        $ordenes = $mecanico->ordenes()
            ->with(['vehiculo.cliente.persona', 'vehiculo.modelo.marca'])
            ->latest()
            ->paginate(10);

        $stats = DB::table('ordenes_servicio')
            ->where('mecanico_id', $mecanico->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'Entregado' THEN 1 ELSE 0 END) as entregadas,
                SUM(CASE WHEN estado NOT IN ('Entregado','Cancelado') THEN 1 ELSE 0 END) as activas,
                SUM(total) as facturacion_total
            ")
            ->first();

        return view('mecanicos.show', compact('mecanico', 'ordenes', 'stats'));
    }

    public function edit(Mecanico $mecanico): View
    {
        $this->autorizar('mecanicos.editar');

        $mecanico->load(['persona', 'sucursal', 'especialidad']);
        $sucursales     = Sucursal::orderBy('nombre')->get();
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('mecanicos.edit', compact('mecanico', 'sucursales', 'especialidades'));
    }

    public function update(UpdateMecanicoRequest $request, Mecanico $mecanico): RedirectResponse
    {
        DB::transaction(function () use ($request, $mecanico) {
            $mecanico->persona->update([
                'nombre'   => $request->nombre,
                'telefono' => $request->telefono,
                'email'    => $request->email,
            ]);

            $mecanico->update([
                'sucursal_id'     => $request->sucursal_id,
                'especialidad_id' => $request->especialidad_id,
                'cedula'          => $request->cedula,
                'fecha_ingreso'   => $request->filled('fecha_ingreso') ? $request->fecha_ingreso : $mecanico->fecha_ingreso,
                'salario'         => $request->salario,
                'activo'          => $request->boolean('activo', true),
            ]);
        });

        return redirect()->route('mecanicos.show', $mecanico)
            ->with('success', 'Mecánico actualizado correctamente.');
    }
}
