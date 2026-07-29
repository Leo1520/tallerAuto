@extends('layouts.app')
@section('title', 'Nuevo Servicio')
@section('page-title', 'Nuevo Servicio')

@section('header-actions')
    <a href="{{ route('servicios.index') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors border border-gray-600">
        <i class="bi bi-arrow-left" style="font-size:13px;"></i> Volver
    </a>
@endsection

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('servicios.store') }}">
        @csrf

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-5">

            {{-- Nombre --}}
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                    Nombre del servicio *
                </label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       placeholder="Ej: Cambio de aceite y filtro"
                       class="w-full px-4 py-2.5 bg-gray-900 border {{ $errors->has('nombre') ? 'border-red-600' : 'border-gray-600' }} text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
                @error('nombre')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Tipo y Precio --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                        Tipo de servicio
                    </label>
                    <select name="tipo_servicio_id"
                            class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                        <option value="">— Sin categoría —</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_servicio_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_servicio_id')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                        Precio (Bs) *
                    </label>
                    <input type="number" name="precio" value="{{ old('precio') }}" required
                           min="0" step="0.01" placeholder="0.00"
                           class="w-full px-4 py-2.5 bg-gray-900 border {{ $errors->has('precio') ? 'border-red-600' : 'border-gray-600' }} text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
                    @error('precio')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                    Descripción
                </label>
                <textarea name="descripcion" rows="3" placeholder="Descripción breve del servicio..."
                          class="w-full px-4 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 resize-none">{{ old('descripcion') }}</textarea>
                @error('descripcion')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Tiempo estimado --}}
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                    Tiempo estimado (minutos)
                </label>
                <input type="number" name="tiempo_estimado" value="{{ old('tiempo_estimado') }}"
                       min="1" placeholder="Ej: 60"
                       class="w-full px-4 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
                @error('tiempo_estimado')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Switches --}}
            <div class="flex items-center gap-8 pt-1">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="requiere_repuestos" value="1"
                           {{ old('requiere_repuestos') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-600">
                    <span class="text-sm text-gray-300">Requiere repuestos</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="activo" value="1"
                           {{ old('activo', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-600">
                    <span class="text-sm text-gray-300">Servicio activo</span>
                </label>
            </div>
        </div>

        <div class="mt-5 flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg text-sm font-bold text-white transition-colors"
                    style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
                <i class="bi bi-check-lg" style="font-size:14px;"></i> Guardar servicio
            </button>
            <a href="{{ route('servicios.index') }}"
               class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors border border-gray-600">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
