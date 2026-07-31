@extends('layouts.app')
@section('title', 'Roles')
@section('page-title', 'Roles del sistema')

@section('header-actions')
    <a href="{{ route('roles.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
       style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
        <i class="bi bi-shield-plus" style="font-size:15px;"></i> Nuevo rol
    </a>
@endsection

@section('content')

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
        <p class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $roles->count() }}</span> roles configurados
        </p>
        <p class="text-xs text-gray-500">Solo los administradores pueden gestionar roles</p>
    </div>

    @if($roles->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-shield text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No hay roles configurados.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Rol</th>
                        <th class="px-6 py-3 text-center">Permisos</th>
                        <th class="px-6 py-3 text-center">Usuarios</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach($roles as $rol)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                     style="background:rgba(215,25,32,.15);">
                                    <i class="bi bi-shield-fill" style="color:#D71920;font-size:14px;"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-100">{{ $rol->nombre }}</p>
                                    @if($rol->descripcion)
                                    <p class="text-xs text-gray-500">{{ $rol->descripcion }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-900/30 text-blue-300 border border-blue-800/50">
                                {{ $rol->permissions_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-700 text-gray-300 border border-gray-600">
                                {{ $rol->users_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $rol->activo
                                    ? 'bg-green-900/40 text-green-400 border border-green-800'
                                    : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                                {{ $rol->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('roles.edit', $rol) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                                    <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                                </a>
                                @if($rol->users_count === 0)
                                <form method="POST" action="{{ route('roles.destroy', $rol) }}"
                                      data-confirm="¿Eliminar el rol {{ $rol->nombre }}? Esta acción no se puede deshacer.">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="tpOpen(this.form)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-400 bg-red-900/20 hover:bg-red-900/40 rounded-lg transition-colors border border-red-900/50">
                                        <i class="bi bi-trash" style="font-size:11px;"></i> Eliminar
                                    </button>
                                </form>
                                @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs text-gray-600 border border-gray-700 rounded-lg cursor-not-allowed"
                                      title="Tiene {{ $rol->users_count }} usuario(s) asignado(s)">
                                    <i class="bi bi-lock" style="font-size:11px;"></i> En uso
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
