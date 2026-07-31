@extends('layouts.app')
@section('title', 'Nuevo Repuesto')
@section('page-title', 'Nuevo Repuesto')

@section('header-actions')
    <a href="{{ route('repuestos.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('repuestos.store') }}" class="space-y-5" enctype="multipart/form-data">
    @csrf

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
                            <input type="text" name="codigo" value="{{ old('codigo') }}"
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
                                    <option value="{{ $prov->id }}" style="background:#111827;" @selected(old('proveedor_id') == $prov->id)>{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">
                            Nombre <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                               placeholder="Ej: Filtro de aceite Toyota Corolla" required
                               class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-600' }}">
                        @error('nombre')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Descripción</label>
                        <textarea name="descripcion" rows="2"
                                  class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                                  placeholder="Especificaciones, compatibilidad, notas...">{{ old('descripcion') }}</textarea>
                    </div>

                    {{-- Imagen --}}
                    <div x-data="{ preview: null }">
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
                                <span class="text-xs text-gray-400 mt-1">Haz clic para subir imagen</span>
                                <span class="text-xs text-gray-600 mt-0.5">JPG, PNG, WEBP — máx. 2 MB</span>
                                <input type="file" name="imagen" accept="image/*" class="sr-only"
                                       @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                            </label>
                        </div>
                        @error('imagen')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            {{-- Stock inicial --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-archive" style="color:#D71920;"></i> Stock inicial
                        <span class="text-gray-600 font-normal normal-case tracking-normal">(opcional)</span>
                    </p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Sucursal</label>
                            <select name="sucursal_id_inicial"
                                    class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                <option value="" style="background:#111827;">Ninguna</option>
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}" style="background:#111827;" @selected(old('sucursal_id_inicial') == $suc->id)>{{ $suc->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Cantidad inicial</label>
                            <input type="number" name="stock_inicial" value="{{ old('stock_inicial', 0) }}" min="0"
                                   class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Stock mínimo</label>
                            <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 5) }}" min="0"
                                   class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
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
                        <input type="number" name="precio_compra" value="{{ old('precio_compra') }}"
                               min="0" step="0.01" required placeholder="0.00"
                               class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('precio_compra') ? 'border-red-500' : 'border-gray-600' }}">
                        @error('precio_compra')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">
                            Precio de venta <span class="text-red-400">*</span>
                        </label>
                        <input type="number" name="precio_venta" value="{{ old('precio_venta') }}"
                               min="0" step="0.01" required placeholder="0.00"
                               class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('precio_venta') ? 'border-red-500' : 'border-gray-600' }}">
                        @error('precio_venta')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-600">Debe ser ≥ precio de compra</p>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <button type="submit"
                    class="w-full px-6 py-3 text-white font-semibold rounded-lg transition-colors btn-taller-red">
                <i class="bi bi-box-seam me-2"></i> Guardar Repuesto
            </button>
            <a href="{{ route('repuestos.index') }}"
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
