@extends('layouts.app')

@section('title', 'Editar orden')
@section('page-title', 'Editar — ' . $orden->numero)

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">

        {{-- Info fija (no editable) --}}
        <div class="mb-5 p-4 bg-gray-50 rounded-lg text-sm text-gray-600">
            <p><span class="font-semibold text-gray-800">Vehículo:</span>
                {{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->cliente->persona->nombre }}</p>
            <p><span class="font-semibold text-gray-800">Estado actual:</span> {{ $orden->estado }}</p>
        </div>

        {{-- Errores globales de validación --}}
        @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
            <div>
                <strong>Corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li style="font-size:13px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('ordenes.update', $orden) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                    <select name="sucursal_id"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('sucursal_id') ? 'border-red-500' : 'border-gray-300' }}">
                        <option value="">Sin asignar</option>
                        @foreach ($sucursales as $s)
                            <option value="{{ $s->id }}" {{ old('sucursal_id', $orden->sucursal_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                    @error('sucursal_id')
                        <p class="text-red-500 text-xs mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mecánico</label>
                    <select name="mecanico_id"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('mecanico_id') ? 'border-red-500' : 'border-gray-300' }}">
                        <option value="">Sin asignar</option>
                        @foreach ($mecanicos as $m)
                            <option value="{{ $m->id }}" {{ old('mecanico_id', $orden->mecanico_id) == $m->id ? 'selected' : '' }}>{{ $m->persona->nombre }}</option>
                        @endforeach
                    </select>
                    @error('mecanico_id')
                        <p class="text-red-500 text-xs mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                    <select name="prioridad"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('prioridad') ? 'border-red-500' : 'border-gray-300' }}">
                        @foreach (['Baja','Media','Alta','Urgente'] as $p)
                            <option value="{{ $p }}" {{ old('prioridad', $orden->prioridad) === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                    @error('prioridad')
                        <p class="text-red-500 text-xs mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descuento (Bs)</label>
                    <input type="number" name="descuento" value="{{ old('descuento', $orden->descuento) }}"
                           step="0.01" min="0"
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('descuento') ? 'border-red-500' : 'border-gray-300' }}">
                    @error('descuento')
                        <p class="text-red-500 text-xs mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entrega estimada</label>
                    <input type="datetime-local" name="fecha_entrega_estimada"
                           value="{{ old('fecha_entrega_estimada', $orden->fecha_entrega_estimada?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('fecha_entrega_estimada') ? 'border-red-500' : 'border-gray-300' }}">
                    @error('fecha_entrega_estimada')
                        <p class="text-red-500 text-xs mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="observaciones" rows="3"
                              class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('observaciones') ? 'border-red-500' : 'border-gray-300' }}">{{ old('observaciones', $orden->observaciones) }}</textarea>
                    @error('observaciones')
                        <p class="text-red-500 text-xs mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Guardar cambios
                </button>
                <a href="{{ route('ordenes.show', $orden) }}"
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
