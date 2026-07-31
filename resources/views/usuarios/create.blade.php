@extends('layouts.app')

@section('title', 'Nuevo Usuario')
@section('page-title', 'Nuevo Usuario')

@section('header-actions')
    <a href="{{ route('usuarios.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:15px;"></i> Volver
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
<form method="POST" action="{{ route('usuarios.store') }}" class="space-y-5">
    @csrf

    @if($personaId)
        <input type="hidden" name="persona_id" value="{{ $personaId }}">
    @endif

    @if($esMecanico)
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm"
         style="background:rgba(59,130,246,.08); border-color:rgba(59,130,246,.3); color:#93c5fd;">
        <i class="bi bi-wrench-adjustable-circle-fill" style="font-size:16px;"></i>
        <span>Creando cuenta para un mecánico existente — los datos personales ya están registrados.</span>
    </div>
    @endif

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

    {{-- Datos personales --}}
    @if(!$personaId)
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
                <input type="text" name="nombre" value="{{ old('nombre', $preNombre ?? '') }}" required
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 @error('nombre') border-red-500 @enderror"
                       placeholder="Nombre completo">
                @error('nombre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                           placeholder="+591 7XXXXXXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Rol</label>
                    <select name="rol_id"
                            class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                        <option value="">Sin rol</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- Si es mecánico, solo mostrar nombre (readonly) + rol --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-gray-100 flex items-center gap-2">
                <i class="bi bi-person" style="color:#D71920;"></i>
                Datos personales
            </h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nombre</label>
                <input type="text" value="{{ $preNombre }}" disabled
                       class="w-full px-3 py-2.5 bg-gray-900/50 border border-gray-700 text-gray-400 rounded-lg text-sm cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Rol</label>
                <select name="rol_id"
                        class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                    <option value="">Sin rol</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @endif

    {{-- Credenciales --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-gray-100 flex items-center gap-2">
                <i class="bi bi-shield-lock" style="color:#D71920;"></i>
                Credenciales de acceso
            </h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Correo electrónico <span class="text-red-400">*</span></label>
                <input type="email" name="email" value="{{ old('email', $preEmail ?? '') }}" required
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 @error('email') border-red-500 @enderror"
                       placeholder="correo@ejemplo.com">
                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Contraseña <span class="text-red-400">*</span></label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 @error('password') border-red-500 @enderror"
                           placeholder="Mínimo 8 caracteres">
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirmar contraseña <span class="text-red-400">*</span></label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600"
                           placeholder="Repetir contraseña">
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('usuarios.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
            Cancelar
        </a>
        <button type="submit"
                class="px-6 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors btn-taller-red">
            <i class="bi bi-check-lg me-1"></i> Crear usuario
        </button>
    </div>

</form>
</div>
@endsection

@push('styles')
<style>.btn-taller-red{background:#D71920}.btn-taller-red:hover{background:#b81218}</style>
@endpush
