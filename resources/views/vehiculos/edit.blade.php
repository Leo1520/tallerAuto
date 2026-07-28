@extends('layouts.app')

@section('title', 'Editar vehículo')
@section('page-title', 'Editar — ' . $vehiculo->placa)

@section('content')

<div class="max-w-2xl" x-data="vehiculoForm({{ $marcas->toJson() }}, {{ $vehiculo->modelo->marca_id }}, {{ $vehiculo->modelo_id }})">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('vehiculos.update', $vehiculo) }}" class="space-y-5">
            @csrf @method('PUT')

            <p class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Propietario</p>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente <span class="text-red-500">*</span></label>
                <select name="cliente_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id }}" {{ old('cliente_id', $vehiculo->cliente_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->persona->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <p class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3 pt-2">Datos del vehículo</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modelo <span class="text-red-500">*</span></label>
                    <select name="modelo_id" required x-model="modeloId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="!marcaId">
                        <option value="">Seleccionar modelo...</option>
                        <template x-for="modelo in modelosFiltrados" :key="modelo.id">
                            <option :value="modelo.id" x-text="modelo.nombre"
                                    :selected="modelo.id == modeloId"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Año <span class="text-red-500">*</span></label>
                    <input type="number" name="ano" value="{{ old('ano', $vehiculo->ano) }}"
                           min="1900" max="{{ date('Y') + 1 }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color" value="{{ old('color', $vehiculo->color) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placa <span class="text-red-500">*</span></label>
                    <input type="text" name="placa" value="{{ old('placa', $vehiculo->placa) }}" required
                           class="w-full px-3 py-2 border rounded-lg text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('placa') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('placa') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kilometraje</label>
                    <input type="number" name="kilometraje" value="{{ old('kilometraje', $vehiculo->kilometraje) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">VIN <span class="text-red-500">*</span></label>
                    <input type="text" name="vin" value="{{ old('vin', $vehiculo->vin) }}" required maxlength="17" minlength="17"
                           class="w-full px-3 py-2 border rounded-lg text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('vin') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('vin') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $vehiculo->activo) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">Vehículo activo</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Guardar cambios
                </button>
                <a href="{{ route('vehiculos.show', $vehiculo) }}"
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function vehiculoForm(marcas, marcaInicial, modeloInicial) {
    return {
        marcaId: marcaInicial,
        modeloId: modeloInicial,
        marcas: marcas,
        modelosFiltrados: [],
        filtrarModelos() {
            const marca = this.marcas.find(m => m.id == this.marcaId);
            this.modelosFiltrados = marca ? marca.modelos : [];
        },
        init() {
            this.filtrarModelos();
        }
    };
}
</script>

@endsection
