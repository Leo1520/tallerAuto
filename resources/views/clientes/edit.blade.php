@extends('layouts.app')

@section('title', 'Editar cliente')
@section('page-title', 'Editar — ' . $cliente->persona->nombre)

@section('header-actions')
    <a href="{{ route('clientes.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
@endsection

@php
$ciudadesBO = [
    'Santa Cruz de la Sierra', 'La Paz', 'Cochabamba', 'Sucre',
    'Oruro', 'Potosí', 'Tarija', 'Trinidad', 'Cobija',
];
@endphp

@section('content')

<div class="max-w-2xl mx-auto">
<form method="POST" action="{{ route('clientes.update', $cliente) }}" class="space-y-4">
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
                <input type="text" name="nombre" value="{{ old('nombre', $cliente->persona->nombre) }}" required
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400 {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-600' }}">
                @error('nombre') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $cliente->persona->telefono) }}"
                       placeholder="Ej: 78901234"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $cliente->persona->email) }}"
                       placeholder="cliente@email.com"
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-600' }}">
                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Documento --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-card-text" style="color:#D71920;"></i> Documento e identificación
            </p>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Tipo de documento</label>
                <select name="tipo_documento"
                        class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                    <option value="">Sin documento</option>
                    @foreach (['CI' => 'Cédula de identidad (CI)', 'NIT' => 'NIT', 'Pasaporte' => 'Pasaporte', 'Otro' => 'Otro'] as $val => $label)
                        <option value="{{ $val }}" {{ old('tipo_documento', $cliente->tipo_documento) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Número de documento</label>
                <input type="text" name="numero_documento" value="{{ old('numero_documento', $cliente->numero_documento) }}"
                       placeholder="Ej: 1234567"
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 font-mono rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400 {{ $errors->has('numero_documento') ? 'border-red-500' : 'border-gray-600' }}">
                @error('numero_documento') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Ubicación --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-geo-alt" style="color:#D71920;"></i> Ubicación
            </p>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Ciudad</label>
                <input type="text" name="ciudad" value="{{ old('ciudad', $cliente->ciudad) }}"
                       placeholder="Ej: Santa Cruz de la Sierra"
                       list="ciudades-bo"
                       autocomplete="off"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
                <datalist id="ciudades-bo">
                    @foreach($ciudadesBO as $c)
                        <option value="{{ $c }}">
                    @endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion) }}"
                       placeholder="Av. Principal N.º 123"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('clientes.index') }}"
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
