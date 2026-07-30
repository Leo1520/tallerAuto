@extends('layouts.app')
@section('title', 'Editar Repuesto')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('repuestos.show', $repuesto) }}"
           class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Repuesto</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $repuesto->codigo }} — {{ $repuesto->nombre }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('repuestos.update', $repuesto) }}" class="space-y-6" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Información del repuesto</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código *</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $repuesto->codigo) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('codigo') border-red-500 @enderror">
                    @error('codigo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proveedor</label>
                    <select name="proveedor_id"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sin proveedor</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" @selected(old('proveedor_id', $repuesto->proveedor_id) == $prov->id)>{{ $prov->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $repuesto->nombre) }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror">
                @error('nombre')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                <textarea name="descripcion" rows="2"
                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('descripcion', $repuesto->descripcion) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Precio de compra (Bs) *</label>
                    <input type="number" name="precio_compra" value="{{ old('precio_compra', $repuesto->precio_compra) }}" min="0" step="0.01" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('precio_compra') border-red-500 @enderror">
                    @error('precio_compra')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Precio de venta (Bs) *</label>
                    <input type="number" name="precio_venta" value="{{ old('precio_venta', $repuesto->precio_venta) }}" min="0" step="0.01" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('precio_venta') border-red-500 @enderror">
                    @error('precio_venta')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Imagen --}}
            <div x-data="{ preview: '{{ $repuesto->imagen ? asset('storage/' . $repuesto->imagen) : '' }}' }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Imagen del repuesto</label>
                <div class="flex items-start gap-4">
                    <div class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden flex-shrink-0"
                         style="background:rgba(255,255,255,.03);">
                        <template x-if="preview">
                            <img :src="preview" class="w-full h-full object-cover rounded-xl">
                        </template>
                        <template x-if="!preview">
                            <i class="bi bi-image text-gray-500" style="font-size:28px;"></i>
                        </template>
                    </div>
                    <div class="flex-1">
                        <label class="cursor-pointer flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-600 rounded-xl hover:border-red-500 transition-colors"
                               style="background:rgba(255,255,255,.02);">
                            <i class="bi bi-cloud-upload text-gray-400" style="font-size:20px;"></i>
                            <span class="text-xs text-gray-400 mt-1">{{ $repuesto->imagen ? 'Cambiar imagen' : 'Subir imagen' }}</span>
                            <span class="text-xs text-gray-600 mt-0.5">JPG, PNG, WEBP — máx. 2 MB</span>
                            <input type="file" name="imagen" accept="image/*" class="sr-only"
                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview">
                        </label>
                        @error('imagen')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', $repuesto->activo))
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="activo" class="text-sm text-gray-700 dark:text-gray-300">Repuesto activo</label>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('repuestos.show', $repuesto) }}"
               class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
