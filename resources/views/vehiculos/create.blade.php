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

@php
$clientesData = $clientes->map(function ($c) {
    $cTipo = $c->tipo_documento ?? '';
    $cNum  = $c->numero_documento ?? '';
    if ($cTipo && $cNum && preg_match('/^' . preg_quote($cTipo, '/') . '-?/i', $cNum)) {
        $cNum = preg_replace('/^' . preg_quote($cTipo, '/') . '-?/i', '', $cNum);
    }
    $cDoc = $cNum ? ' — ' . ($cTipo ? "{$cTipo}-{$cNum}" : $cNum) : '';
    return ['id' => $c->id, 'label' => $c->persona->nombre . $cDoc];
})->values();
@endphp

<div class="max-w-2xl mx-auto" x-data="vehiculoForm({{ $marcas->toJson() }}, {{ $clientesData->toJson() }})">
<form method="POST" action="{{ route('vehiculos.store') }}" class="space-y-4">
    @csrf

    {{-- Propietario --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30 rounded-t-xl">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-person" style="color:#D71920;"></i> Propietario
            </p>
        </div>
        <div class="p-6">
            <label class="block text-sm font-medium text-gray-300 mb-1.5">
                Cliente <span class="text-red-400">*</span>
            </label>

            {{-- Buscador con dropdown --}}
            <div class="relative" @click.outside="clienteOpen = false">
                <div class="relative">
                    <input type="text"
                           x-model="clienteBusqueda"
                           @focus="clienteOpen = true"
                           @input="clienteOpen = true; clienteId = ''"
                           @keydown.escape="clienteOpen = false"
                           placeholder="Buscar por nombre o documento..."
                           autocomplete="off"
                           class="w-full px-3 py-2.5 pr-8 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400 {{ $errors->has('cliente_id') ? 'border-red-500' : 'border-gray-600' }}">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                        <i class="bi bi-search" style="font-size:12px;"></i>
                    </span>
                </div>

                <input type="hidden" name="cliente_id" :value="clienteId">

                <div x-show="clienteOpen"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute z-50 w-full mt-1 bg-gray-900 border border-gray-600 rounded-lg shadow-xl overflow-hidden"
                     style="max-height:220px;overflow-y:auto;">

                    <template x-if="clientesFiltrados.length === 0">
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">Sin resultados</div>
                    </template>

                    <template x-for="c in clientesFiltrados" :key="c.id">
                        <div @click="clienteId = c.id; clienteBusqueda = c.label; clienteOpen = false"
                             :class="clienteId == c.id ? 'bg-red-900/30 text-red-300' : 'text-gray-200 hover:bg-gray-700'"
                             class="px-3 py-2.5 text-sm cursor-pointer border-b border-gray-700/50 last:border-0 transition-colors">
                            <span x-text="c.label"></span>
                        </div>
                    </template>
                </div>
            </div>

            @error('cliente_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
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
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Tipo de combustible</label>
                <select name="combustible"
                        class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                    <option value="">— Sin especificar —</option>
                    @foreach(['Gasolina','Diesel','Gas Natural','Eléctrico','Híbrido'] as $c)
                        <option value="{{ $c }}" {{ old('combustible') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Placa <span class="text-red-400">*</span></label>
                <input type="text" name="placa" value="{{ old('placa') }}" required
                       placeholder="ABC-1234"
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 font-mono uppercase rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400 {{ $errors->has('placa') ? 'border-red-500' : 'border-gray-600' }}">
                @error('placa') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Kilometraje actual</label>
                <input type="number" name="kilometraje" value="{{ old('kilometraje', 0) }}" min="0"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">
                    VIN
                    <span class="text-xs font-normal text-gray-500 ml-1">(17 caracteres — opcional si no está disponible)</span>
                </label>
                <input type="text" name="vin" value="{{ old('vin') }}" maxlength="17"
                       placeholder="1HGBH41JXMN109186"
                       class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 font-mono uppercase rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400 {{ $errors->has('vin') ? 'border-red-500' : 'border-gray-600' }}">
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
        <button type="submit" id="btnRegistrarVehiculo"
                class="px-6 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors"
                style="background:#D71920;">
            <i class="bi bi-check-lg me-1"></i> Registrar vehículo
        </button>
    </div>

</form>
</div>

<script @nonce>
function vehiculoForm(marcas, clientes) {
    return {
        // — Marca / Modelo —
        marcaId: '{{ old('marca_id', '') }}',
        modeloId: '{{ old('modelo_id', '') }}',
        marcas: marcas,
        modelosFiltrados: [],
        filtrarModelos() {
            const marca = this.marcas.find(m => m.id == this.marcaId);
            this.modelosFiltrados = marca ? marca.modelos : [];
            this.modeloId = '';
        },

        // — Cliente —
        clienteId: '{{ old('cliente_id', $clienteSeleccionado?->id ?? '') }}',
        clienteBusqueda: '',
        clienteOpen: false,
        clientes: clientes,
        clientesFiltrados: [],

        init() {
            if (this.marcaId) this.filtrarModelos();

            // Inicializar lista completa de clientes
            this.clientesFiltrados = this.clientes;

            // Prellenar etiqueta si hay cliente preseleccionado
            if (this.clienteId) {
                const c = this.clientes.find(c => String(c.id) === String(this.clienteId));
                if (c) { this.clienteBusqueda = c.label; }
            }

            // Filtrar reactivamente al escribir en el buscador
            this.$watch('clienteBusqueda', (val) => {
                if (!val) {
                    this.clientesFiltrados = this.clientes;
                } else {
                    const q = val.toLowerCase();
                    this.clientesFiltrados = this.clientes.filter(c => c.label.toLowerCase().includes(q));
                }
            });

            const btn = document.getElementById('btnRegistrarVehiculo');
            if (btn) {
                btn.addEventListener('mouseover', function () { this.style.background = '#b81218'; });
                btn.addEventListener('mouseout',  function () { this.style.background = '#D71920'; });
            }
        }
    };
}
</script>

@endsection
