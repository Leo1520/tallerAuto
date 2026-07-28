@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')

@section('header-actions')
    <a href="{{ route('usuarios.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:15px;"></i> Volver
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    @if($errors->any())
    <div class="p-4 rounded-xl border text-sm flex items-start gap-3"
         style="background:rgba(215,25,32,.1); border-color:rgba(215,25,32,.3); color:#f87171;">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-0.5"></i>
        <ul class="space-y-0.5 list-disc list-inside">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($usuario->roles->isEmpty())
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm"
         style="background:rgba(234,179,8,.08); border-color:rgba(234,179,8,.3); color:#fbbf24;">
        <i class="bi bi-clock-history" style="font-size:16px;"></i>
        <span>Este usuario está <strong>pendiente de rol</strong> — asígnale uno para que pueda acceder al sistema.</span>
    </div>
    @endif

    {{-- Datos personales + rol --}}
    <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-base font-semibold text-gray-100 flex items-center gap-2">
                    <i class="bi bi-person" style="color:#D71920;"></i>
                    Datos personales
                </h2>
            </div>
            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Nombre completo <span class="text-red-400">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $usuario->persona->nombre) }}" required
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 @error('nombre') border-red-500 @enderror">
                    @error('nombre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $usuario->persona->telefono) }}"
                               class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                               placeholder="+591 7XXXXXXX">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Rol</label>
                        <select name="rol_id"
                                class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                            <option value="">Sin rol</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}"
                                    @selected(old('rol_id', $usuario->roles->first()?->id) == $rol->id)>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Correo electrónico <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('usuarios.index') }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors"
                    style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
                <i class="bi bi-check-lg me-1"></i> Guardar cambios
            </button>
        </div>
    </form>

    {{-- Cambiar contraseña --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden"
         x-data="{ open: false }">
        <button type="button" @click="open = !open"
                class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-700/40 transition-colors">
            <div>
                <p class="text-sm font-semibold text-gray-200">Cambiar contraseña</p>
                <p class="text-xs text-gray-500 mt-0.5">Establecer una nueva contraseña para este usuario</p>
            </div>
            <i class="bi bi-chevron-down text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''"></i>
        </button>

        <form x-show="open" x-cloak method="POST"
              action="{{ route('usuarios.password', $usuario) }}"
              class="px-6 pb-6 pt-1 border-t border-gray-700 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-2 gap-4 pt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Nueva contraseña <span class="text-red-400">*</span></label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 @error('password') border-red-500 @enderror"
                           placeholder="Mínimo 8 caracteres">
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirmar <span class="text-red-400">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600"
                           placeholder="Repetir contraseña">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors bg-orange-700 hover:bg-orange-800">
                    <i class="bi bi-key me-1"></i> Actualizar contraseña
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
