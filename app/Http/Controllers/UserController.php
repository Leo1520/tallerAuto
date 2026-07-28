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
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::where('activo', true)->orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'roles', 'search', 'rolId'));
    }

    public function create(): View
    {
        $this->autorizar();
        $roles = Role::where('activo', true)->orderBy('nombre')->get();
        return view('usuarios.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $persona = Persona::create([
                'nombre'   => $request->nombre,
                'telefono' => $request->telefono,
                'email'    => $request->email,
                'activo'   => true,
            ]);

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
