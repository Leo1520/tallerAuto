@extends('layouts.app')
@section('title', 'Editar Repuesto')
@section('page-title', 'Editar Repuesto')

@section('header-actions')
    <a href="{{ route('repuestos.show', $repuesto) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('repuestos.update', $repuesto) }}" class="space-y-5" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Información básica --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-box-seam" style="color:#D71920;"></i> Información del repuesto
                    </p>
                </div>
                <div class="p-6 space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                                Código <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="codigo" value="{{ old('codigo', $repuesto->codigo) }}"
                                   placeholder="Ej: FIL-0102" required
                                   class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('codigo') ? 'border-red-500' : 'border-gray-600' }}">
                            @error('codigo')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Proveedor</label>
                            <select name="proveedor_id"
                                    class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                <option value="" style="background:#111827;">Sin proveedor</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}" style="background:#111827;" @selected(old('proveedor_id', $repuesto->proveedor_id) == $prov->id)>{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">
                            Nombre <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="nombre" value="{{ old('nombre', $repuesto->nombre) }}" required
                               class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-600' }}">
                        @error('nombre')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Descripción</label>
                        <textarea name="descripcion" rows="2"
                                  class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                                  placeholder="Especificaciones, compatibilidad, notas...">{{ old('descripcion', $repuesto->descripcion) }}</textarea>
                    </div>

                    {{-- Imagen --}}
                    <div x-data="{ preview: '{{ $repuesto->imagen ? asset('storage/' . $repuesto->imagen) : '' }}' }">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Imagen del repuesto</label>
                        <div class="flex items-start gap-4">
                            <div class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden flex-shrink-0 bg-gray-900/60">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full h-full object-cover rounded-xl">
                                </template>
                                <template x-if="!preview">
                                    <i class="bi bi-image text-gray-600" style="font-size:28px;"></i>
                                </template>
                            </div>
                            <label class="cursor-pointer flex flex-col items-center justify-center flex-1 h-24 border-2 border-dashed border-gray-700 rounded-xl hover:border-red-600 transition-colors bg-gray-900/40">
                                <i class="bi bi-cloud-upload text-gray-500" style="font-size:20px;"></i>
                                <span class="text-xs text-gray-400 mt-1">{{ $repuesto->imagen ? 'Cambiar imagen' : 'Subir imagen' }}</span>
                                <span class="text-xs text-gray-600 mt-0.5">JPG, PNG, WEBP — máx. 2 MB</span>
                                <input type="file" name="imagen" accept="image/*" class="sr-only"
                                       @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview">
                            </label>
                        </div>
                        @error('imagen')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- Estado activo --}}
                    <div class="flex items-center gap-2.5 pt-1">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" id="activo" name="activo" value="1"
                               @checked(old('activo', $repuesto->activo))
                               class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-600 cursor-pointer">
                        <label for="activo" class="text-sm font-medium text-gray-300 cursor-pointer">
                            Repuesto activo
                        </label>
                        <span class="text-xs text-gray-600">(desmarcar para desactivar sin eliminar)</span>
                    </div>

                </div>
            </div>

        </div>

        {{-- Sidebar: Precios + Acciones --}}
        <div class="space-y-4">

            {{-- Precios --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-currency-dollar" style="color:#D71920;"></i> Precios (Bs)
                    </p>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">
                            Precio de compra <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="precio_compra"
                               value="{{ old('precio_compra', $repuesto->precio_compra) }}"
                               min="0" step="0.01" required
                               class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 {{ $errors->has('precio_compra') ? 'border-red-500' : 'border-gray-600' }}">
                        @error('precio_compra')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">
                            Precio de venta <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="precio_venta"
                               value="{{ old('precio_venta', $repuesto->precio_venta) }}"
                               min="0" step="0.01" required
                               class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 {{ $errors->has('precio_venta') ? 'border-red-500' : 'border-gray-600' }}">
                        @error('precio_venta')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-600">Debe ser ≥ precio de compra</p>
                    </div>

                    {{-- Margen calculado --}}
                    @if($repuesto->precio_compra > 0)
                    <div class="pt-2 border-t border-gray-700">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Margen actual</span>
                            @php $margen = $repuesto->precio_venta - $repuesto->precio_compra; @endphp
                            <span class="{{ $margen >= 0 ? 'text-green-400' : 'text-red-400' }} font-semibold">
                                Bs {{ number_format($margen, 2) }}
                                ({{ $repuesto->precio_compra > 0 ? number_format(($margen/$repuesto->precio_compra)*100, 1) : '—' }}%)
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Acciones --}}
            <button type="submit"
                    class="w-full px-6 py-3 text-white font-semibold rounded-lg transition-colors btn-taller-red">
                <i class="bi bi-floppy me-2"></i> Guardar Cambios
            </button>
            <a href="{{ route('repuestos.show', $repuesto) }}"
               class="block w-full text-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-medium rounded-lg transition-colors">
                Cancelar
            </a>
        </div>

    </div>
</form>

@endsection

@push('styles')
<style>.btn-taller-red{background:#D71920}.btn-taller-red:hover{background:#b81218}</style>
@endpush
