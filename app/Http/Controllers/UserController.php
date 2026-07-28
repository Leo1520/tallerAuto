<?php

namespace App\Http\Controllers;

use App\Http\Requests\CambiarPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Persona;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    private function autorizar(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function index(Request $request): View
    {
        $this->autorizar();

        $search = $request->input('search');
        $rolId  = $request->input('rol_id');

        $usuarios = User::with(['persona', 'roles'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('persona', fn($p) => $p->where('nombre', 'like', "%{$search}%"))
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($rolId, function ($q) use ($rolId) {
                $q->whereHas('roles', fn($r) => $r->where('roles.id', $rolId));
            })
            // Pendientes (sin rol) primero
            ->orderByRaw('(SELECT COUNT(*) FROM role_user WHERE role_user.user_id = users.id) ASC')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Mecánicos que aún no tienen cuenta de usuario
        $personasConCuenta = User::pluck('persona_id');
        $mecanicosSinCuenta = \App\Models\Mecanico::with(['persona', 'especialidad', 'sucursal'])
            ->whereNotIn('persona_id', $personasConCuenta)
            ->when($search, fn($q) => $q->whereHas('persona',
                fn($p) => $p->where('nombre', 'like', "%{$search}%")
            ))
            ->when($rolId, fn($q) => $q->whereRaw('0=1')) // si filtran por rol, no mostrar mecánicos sin cuenta
            ->orderBy('id')
            ->get();

        $roles = Role::where('activo', true)->orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'roles', 'search', 'rolId', 'mecanicosSinCuenta'));
    }

    public function create(Request $request): View
    {
        $this->autorizar();
        $roles = Role::where('activo', true)->orderBy('nombre')->get();

        // Pre-relleno cuando viene de "Crear cuenta" de un mecánico existente
        $personaId   = $request->query('persona_id');
        $preNombre   = $request->query('nombre');
        $preEmail    = $request->query('email');
        $esMecanico  = $personaId ? \App\Models\Mecanico::where('persona_id', $personaId)->exists() : false;

        return view('usuarios.create', compact('roles', 'personaId', 'preNombre', 'preEmail', 'esMecanico'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            // Si ya existe una persona (mecánico), reutilizarla
            if ($request->persona_id && Persona::find($request->persona_id)) {
                $persona = Persona::find($request->persona_id);
                $persona->update([
                    'email'  => $request->email,
                ]);
            } else {
                $persona = Persona::create([
                    'nombre'   => $request->nombre,
                    'telefono' => $request->telefono,
                    'email'    => $request->email,
                    'activo'   => true,
                ]);
            }

            $user = User::create([
                'persona_id' => $persona->id,
                'email'      => $request->email,
                'password'   => $request->password,
            ]);

            if ($request->rol_id) {
                $user->roles()->sync([$request->rol_id]);
            }
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        $this->autorizar();
        $roles = Role::where('activo', true)->orderBy('nombre')->get();
        $usuario->load(['persona', 'roles']);
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        DB::transaction(function () use ($request, $usuario) {
            $usuario->persona->update([
                'nombre'   => $request->nombre,
                'telefono' => $request->telefono,
                'email'    => $request->email,
            ]);

            $usuario->update(['email' => $request->email]);

            $usuario->roles()->sync($request->rol_id ? [$request->rol_id] : []);
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function cambiarPassword(CambiarPasswordRequest $request, User $usuario): RedirectResponse
    {
        $usuario->update(['password' => $request->password]);

        return redirect()->route('usuarios.edit', $usuario)
            ->with('success', 'Contraseña actualizada correctamente.');
    }
}
