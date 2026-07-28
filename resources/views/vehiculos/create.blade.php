@extends('layouts.app')

@section('title', 'Nuevo vehículo')
@section('page-title', 'Nuevo vehículo')

@section('header-actions')
    <a href="{{ route('vehiculos.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
@endsection

@section('content')

<div class="max-w-2xl mx-auto" x-data="vehiculoForm({{ $marcas->toJson() }})">
<form method="POST" action="{{ route('vehiculos.store') }}" class="space-y-4">
    @csrf

    {{-- Propietario --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-person" style="color:#D71920;"></i> Propietario
            </p>
        </div>
        <div class="p-6">
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Cliente <span class="text-red-400">*</span></label>
            <select name="cliente_id" required
                    class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 {{ $errors->has('cliente_id') ? 'border-red-500' : 'border-gray-600' }}">
                <option value="">Seleccionar cliente...</option>
                @foreach ($clientes as $c)
                    <option value="{{ $c->id }}" {{ old('cliente_id', $clienteSeleccionado?->id) == $c->id ? 'selected' : '' }}>
                        {{ $c->persona->nombre }}{{ $c->numero_documento ? ' — ' . $c->numero_documento : '' }}
                    </option>
                @endforeach
            </select>
            @error('cliente_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Datos del vehículo --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-car-front" style="color:#D71920;"></i> Datos del vehículo
            </p>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Marca <span class="text-red-400">*</span></label>
                <select x-model="marcaId" @change="filtrarModelos()"
                        class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                    <option value="">Seleccionar marca...</option>
                    @foreach ($marcas as $marca)
                        <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Modelo <span class="text-red-400">*</span></label>
                <select name="modelo_id" required x-model="modeloId"
                        class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 disabled:opacity-50 {{ $errors->has('modelo_id') ? 'border-red-500' : 'border-gray-600' }}"
                        :disabled="!marcaId">
                    <option value="">Seleccionar modelo...</option>
                    <template x-for="modelo in modelosFiltrados" :key="modelo.id">
                        <option :value="modelo.id" x-text="modelo.nombre"></option>
                    </template>
                </select>
                @error('modelo_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Año <span class="text-red-400">*</span></label>
                <input type="number" name="ano" value="{{ old('ano', date('Y')) }}"
                       min="1900" max="{{ date('Y') + 1 }}" required
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 {{ $errors->has('ano') ? 'border-red-500' : 'border-gray-600' }}">
                @error('ano') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Color</label>
                <input type="text" name="color" value="{{ old('color') }}"
                       placeholder="Ej: Blanco, Negro..."
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Placa <span class="text-red-400">*</span></label>
                <input type="text" name="placa" value="{{ old('placa') }}" required
                       placeholder="ABC-1234"
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 font-mono uppercase rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('placa') ? 'border-red-500' : 'border-gray-600' }}">
                @error('placa') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Kilometraje actual</label>
                <input type="number" name="kilometraje" value="{{ old('kilometraje', 0) }}" min="0"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">
                    VIN <span class="text-red-400">*</span>
                    <span class="text-xs font-normal text-gray-500 ml-1">(17 caracteres)</span>
                </label>
                <input type="text" name="vin" value="{{ old('vin') }}" required maxlength="17" minlength="17"
                       placeholder="1HGBH41JXMN109186"
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 font-mono uppercase rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 {{ $errors->has('vin') ? 'border-red-500' : 'border-gray-600' }}">
                @error('vin') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

    {{-- Acciones --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('vehiculos.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
            Cancelar
        </a>
        <button type="submit"
                class="px-6 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors"
                style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
            <i class="bi bi-check-lg me-1"></i> Registrar vehículo
        </button>
    </div>

</form>
</div>

<script>
function vehiculoForm(marcas) {
    return {
        marcaId: '{{ old('marca_id', '') }}',
        modeloId: '{{ old('modelo_id', '') }}',
        marcas: marcas,
        modelosFiltrados: [],
        filtrarModelos() {
            const marca = this.marcas.find(m => m.id == this.marcaId);
            this.modelosFiltrados = marca ? marca.modelos : [];
            this.modeloId = '';
        },
        init() {
            if (this.marcaId) this.filtrarModelos();
        }
    };
}
</script>

@endsection
