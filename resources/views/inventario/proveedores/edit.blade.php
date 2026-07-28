@extends('layouts.app')
@section('title', 'Editar proveedor')
@section('page-title', 'Editar — ' . $proveedor->nombre)

@section('header-actions')
    <a href="{{ route('proveedores.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">
<form method="POST" action="{{ route('proveedores.update', $proveedor) }}" class="space-y-4">
    @csrf @method('PUT')

    {{-- Datos del proveedor --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-truck" style="color:#D71920;"></i> Datos del proveedor
            </p>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nombre <span class="text-red-400">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-600' }}">
                @error('nombre') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">NIT</label>
                <input type="text" name="nit" value="{{ old('nit', $proveedor->nit) }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 font-mono rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('nit') ? 'border-red-500' : 'border-gray-600' }}">
                @error('nit') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $proveedor->email) }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Ciudad</label>
                <input type="text" name="ciudad" value="{{ old('ciudad', $proveedor->ciudad) }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            </div>
            <div class="sm:col-span-2 pt-2 border-t border-gray-700">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $proveedor->activo))
                           class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-600 cursor-pointer">
                    <span class="text-sm text-gray-300">Proveedor activo</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('proveedores.index') }}"
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
