@extends('layouts.app')
@section('title', 'Nuevo Mecánico')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ crearUsuario: {{ old('crear_usuario') ? 'true' : 'false' }} }">

    <div class="flex items-center gap-4">
        <a href="{{ route('mecanicos.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo mecánico</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Registrar técnico en el equipo</p>
        </div>
    </div>

    <form method="POST" action="{{ route('mecanicos.store') }}" class="space-y-6">
        @csrf

        {{-- Datos personales --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Datos personales</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nombre completo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                       class="w-full px-3 py-2 text-sm border @error('nombre') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3 py-2 text-sm border @error('email') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Datos laborales --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Datos laborales</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Cédula <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="cedula" value="{{ old('cedula') }}"
                           class="w-full px-3 py-2 text-sm border @error('cedula') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('cedula') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Fecha de ingreso <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 text-sm border @error('fecha_ingreso') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sucursal <span class="text-red-500">*</span>
                    </label>
                    <select name="sucursal_id"
                            class="w-full px-3 py-2 text-sm border @error('sucursal_id') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar...</option>
                        @foreach($sucursales as $s)
                            <option value="{{ $s->id }}" @selected(old('sucursal_id') == $s->id)>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                    @error('sucursal_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Especialidad <span class="text-red-500">*</span>
                    </label>
                    <select name="especialidad_id"
                            class="w-full px-3 py-2 text-sm border @error('especialidad_id') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar...</option>
                        @foreach($especialidades as $e)
                            <option value="{{ $e->id }}" @selected(old('especialidad_id') == $e->id)>{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                    @error('especialidad_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Salario <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm text-gray-500">Bs</span>
                    <input type="number" name="salario" value="{{ old('salario') }}" step="0.01" min="0"
                           class="w-full pl-9 pr-3 py-2 text-sm border @error('salario') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @error('salario') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Cuenta de usuario (opcional) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Cuenta del sistema</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Opcional — permite al mecánico iniciar sesión</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="crear_usuario" value="1"
                           x-model="crearUsuario" class="sr-only peer">
                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div x-show="crearUsuario" x-cloak class="space-y-4 pt-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Correo de acceso <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="user_email" value="{{ old('user_email') }}"
                           class="w-full px-3 py-2 text-sm border @error('user_email') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('user_email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="user_password"
                               class="w-full px-3 py-2 text-sm border @error('user_password') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('user_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar contraseña</label>
                        <input type="password" name="user_password_confirmation"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Roles del sistema</label>
                    @if($roles->isEmpty())
                        <p class="text-xs text-gray-500">No hay roles disponibles.</p>
                    @else
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($roles as $rol)
                            <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 cursor-pointer hover:border-blue-400 transition-colors">
                                <input type="checkbox" name="roles[]" value="{{ $rol->id }}"
                                       class="w-4 h-4 rounded text-blue-600 border-gray-400 bg-gray-100 dark:bg-gray-600 focus:ring-blue-500"
                                       {{ in_array($rol->id, old('roles', [])) ? 'checked' : '' }}>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $rol->nombre }}</p>
                                    @if($rol->descripcion)
                                    <p class="text-xs text-gray-500">{{ $rol->descripcion }}</p>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('mecanicos.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors">
                Registrar mecánico
            </button>
        </div>
    </form>
</div>
@endsection
