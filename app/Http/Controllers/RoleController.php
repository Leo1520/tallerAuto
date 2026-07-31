<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    private function soloAdmin(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function index(): View
    {
        $this->soloAdmin();

        $roles = Role::withCount(['users', 'permissions'])->orderBy('nombre')->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->soloAdmin();

        $permisos = Permission::orderBy('modulo')->orderBy('accion')->get()->groupBy('modulo');

        return view('roles.create', compact('permisos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->soloAdmin();

        $data = $request->validate([
            'nombre'      => 'required|string|max:50|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:255',
            'activo'      => 'boolean',
        ]);

        $role = Role::create([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'activo'      => $request->boolean('activo', true),
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$role->nombre}\" creado correctamente.");
    }

    public function edit(Role $role): View
    {
        $this->soloAdmin();

        $permisos        = Permission::orderBy('modulo')->orderBy('accion')->get()->groupBy('modulo');
        $permisosActivos = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permisos', 'permisosActivos'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->soloAdmin();

        $data = $request->validate([
            'nombre'      => "required|string|max:50|unique:roles,nombre,{$role->id}",
            'descripcion' => 'nullable|string|max:255',
            'activo'      => 'boolean',
        ]);

        $role->update([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'activo'      => $request->boolean('activo', true),
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$role->nombre}\" actualizado.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->soloAdmin();

        if ($role->users()->exists()) {
            return back()->with('error', "No se puede eliminar el rol \"{$role->nombre}\" porque tiene usuarios asignados.");
        }

        $nombre = $role->nombre;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$nombre}\" eliminado.");
    }
}
