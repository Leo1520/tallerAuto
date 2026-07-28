@extends('layouts.app')

@section('title', 'Nuevo vehículo')
@section('page-title', 'Nuevo vehículo')

@section('content')

<div class="max-w-2xl" x-data="vehiculoForm({{ $marcas->toJson() }})">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('vehiculos.store') }}" class="space-y-5">
            @csrf

            <p class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Propietario</p>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente <span class="text-red-500">*</span></label>
                <select name="cliente_id" required
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('cliente_id') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="">Seleccionar cliente...</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id }}" {{ old('cliente_id', $clienteSeleccionado?->id) == $c->id ? 'selected' : '' }}>
                            {{ $c->persona->nombre }} {{ $c->numero_documento ? '— '.$c->numero_documento : '' }}
                        </option>
                    @endforeach
                </select>
                @error('cliente_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <p class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3 pt-2">Datos del vehículo</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Marca --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca <span class="text-red-500">*</span></label>
                    <select x-model="marcaId" @change="filtrarModelos()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar marca...</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Modelo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modelo <span class="text-red-500">*</span></label>
                    <select name="modelo_id" required x-model="modeloId"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('modelo_id') ? 'border-red-400' : 'border-gray-300' }}"
                            :disabled="!marcaId">
                        <option value="">Seleccionar modelo...</option>
                        <template x-for="modelo in modelosFiltrados" :key="modelo.id">
                            <option :value="modelo.id" x-text="modelo.nombre"></option>
                        </template>
                    </select>
                    @error('modelo_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Año --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Año <span class="text-red-500">*</span></label>
                    <input type="number" name="ano" value="{{ old('ano', date('Y')) }}"
                           min="1900" max="{{ date('Y') + 1 }}" required
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('ano') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('ano') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color" value="{{ old('color') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ej. Blanco, Negro...">
                </div>

                {{-- Placa --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placa <span class="text-red-500">*</span></label>
                    <input type="text" name="placa" value="{{ old('placa') }}" required
                           class="w-full px-3 py-2 border rounded-lg text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('placa') ? 'border-red-400' : 'border-gray-300' }}"
                           placeholder="ABC-1234">
                    @error('placa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Kilometraje --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kilometraje actual</label>
                    <input type="number" name="kilometraje" value="{{ old('kilometraje', 0) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- VIN --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        VIN <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-gray-400 ml-1">(17 caracteres)</span>
                    </label>
                    <input type="text" name="vin" value="{{ old('vin') }}" required maxlength="17" minlength="17"
                           class="w-full px-3 py-2 border rounded-lg text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('vin') ? 'border-red-400' : 'border-gray-300' }}"
                           placeholder="1HGBH41JXMN109186">
                    @error('vin') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Registrar vehículo
                </button>
                <a href="{{ route('vehiculos.index') }}"
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
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
