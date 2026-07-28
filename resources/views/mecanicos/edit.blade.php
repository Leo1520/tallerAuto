@extends('layouts.app')
@section('title', 'Editar mecánico')
@section('page-title', 'Editar — ' . $mecanico->persona->nombre)

@section('header-actions')
    <a href="{{ route('mecanicos.show', $mecanico) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

@if($errors->any())
<div class="mb-4 p-4 rounded-xl border text-sm flex items-start gap-3"
     style="background:rgba(215,25,32,.1); border-color:rgba(215,25,32,.3); color:#f87171;">
    <i class="bi bi-exclamation-circle-fill mt-0.5 flex-shrink-0" style="font-size:15px;"></i>
    <div>
        <p class="font-semibold mb-1">Corrige los siguientes errores:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" action="{{ route('mecanicos.update', $mecanico) }}" class="space-y-4">
    @csrf @method('PUT')

    {{-- Datos personales --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-person" style="color:#D71920;"></i> Datos personales
            </p>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nombre completo <span class="text-red-400">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $mecanico->persona->nombre) }}" required
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-600' }}">
                @error('nombre') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $mecanico->persona->telefono) }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $mecanico->persona->email) }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-600' }}">
                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Datos laborales --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-briefcase" style="color:#D71920;"></i> Datos laborales
            </p>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Cédula <span class="text-red-400">*</span></label>
                <input type="text" name="cedula" value="{{ old('cedula', $mecanico->cedula) }}" required
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 font-mono rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('cedula') ? 'border-red-500' : 'border-gray-600' }}">
                @error('cedula') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Fecha de ingreso <span class="text-red-400">*</span></label>
                <input type="date" name="fecha_ingreso"
                       value="{{ old('fecha_ingreso', $mecanico->fecha_ingreso?->format('Y-m-d') ?? '') }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Sucursal <span class="text-red-400">*</span></label>
                <select name="sucursal_id"
                        class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                    @foreach($sucursales as $s)
                        <option value="{{ $s->id }}" @selected(old('sucursal_id', $mecanico->sucursal_id) == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Especialidad <span class="text-red-400">*</span></label>
                <select name="especialidad_id"
                        class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                    @foreach($especialidades as $e)
                        <option value="{{ $e->id }}" @selected(old('especialidad_id', $mecanico->especialidad_id) == $e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Salario <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Bs</span>
                    <input type="number" name="salario" value="{{ old('salario', $mecanico->salario) }}"
                           step="0.01" min="0"
                           class="w-full pl-9 pr-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Estado</label>
                <select name="activo"
                        class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                    <option value="1" @selected(old('activo', $mecanico->activo))>Activo</option>
                    <option value="0" @selected(!old('activo', $mecanico->activo))>Inactivo</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('mecanicos.show', $mecanico) }}"
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
</div>

@endsection
