@extends('layouts.app')

@section('title', 'Nueva orden de servicio')
@section('page-title', 'Nueva orden de servicio')

@section('header-actions')
    <a href="{{ route('ordenes.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
@endsection

@section('content')

@php
$vehiculosData = $vehiculos->map(fn($v) => [
    'id'    => $v->id,
    'label' => $v->placa . ' — ' . $v->cliente->persona->nombre
             . ' (' . $v->modelo->marca->nombre . ' ' . $v->modelo->nombre . ' ' . $v->ano . ')',
])->values();
@endphp

<div x-data="ordenForm({{ $servicios->toJson() }}, {{ old('servicios') ? json_encode(old('servicios')) : '[]' }})">

    <form method="POST" action="{{ route('ordenes.store') }}" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Columna principal --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Vehículo --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl">
                    <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30 rounded-t-xl">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-car-front" style="color:#D71920;"></i> Vehículo
                        </p>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Seleccionar vehículo <span class="text-red-400">*</span></label>

                        <div x-data="vehiculoBuscador({{ $vehiculosData->toJson() }}, '{{ old('vehiculo_id', $vehiculoSeleccionado?->id ?? '') }}')"
                             class="relative" @click.outside="open = false">
                            <div class="relative">
                                <input type="text"
                                       x-model="busqueda"
                                       @focus="open = true"
                                       @input="open = true; vehiculoId = ''"
                                       @keydown.escape="open = false"
                                       placeholder="Buscar por placa o cliente..."
                                       autocomplete="off"
                                       class="w-full px-3 py-2.5 pr-8 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400 {{ $errors->has('vehiculo_id') ? 'border-red-500' : 'border-gray-600' }}">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                    <i class="bi bi-search" style="font-size:12px;"></i>
                                </span>
                            </div>

                            <input type="hidden" name="vehiculo_id" :value="vehiculoId">

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-50 w-full mt-1 bg-gray-900 border border-gray-600 rounded-lg shadow-xl overflow-hidden"
                                 style="max-height:240px;overflow-y:auto;">

                                <template x-if="filtrados.length === 0">
                                    <div class="px-4 py-3 text-sm text-gray-500 text-center">Sin resultados</div>
                                </template>

                                <template x-for="v in filtrados" :key="v.id">
                                    <div @click="vehiculoId = v.id; busqueda = v.label; open = false"
                                         :class="vehiculoId == v.id ? 'bg-red-900/30 text-red-300' : 'text-gray-200 hover:bg-gray-700'"
                                         class="px-3 py-2.5 text-sm cursor-pointer border-b border-gray-700/50 last:border-0 transition-colors">
                                        <span x-text="v.label"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        @error('vehiculo_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Servicios --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30 flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-tools" style="color:#D71920;"></i> Servicios a realizar
                        </p>
                        <button type="button" @click="agregarServicio()"
                                class="text-sm text-red-400 hover:text-red-300 font-medium flex items-center gap-1 transition-colors">
                            <i class="bi bi-plus-lg" style="font-size:13px;"></i> Agregar servicio
                        </button>
                    </div>
                    <div class="p-6">
                        @error('servicios') <p class="mb-3 text-xs text-red-400">{{ $message }}</p> @enderror

                        <div class="space-y-3">
                            <template x-for="(item, index) in lineas" :key="index">
                                <div class="flex items-center gap-3 p-3 bg-gray-900/50 rounded-lg border border-gray-700">

                                    {{-- Servicio (ocupa todo el espacio disponible) --}}
                                    <div class="flex-1 min-w-0">
                                        <label class="block text-xs text-gray-500 mb-1">Servicio</label>
                                        <select :name="`servicios[${index}][servicio_id]`" x-model="item.servicio_id"
                                                @change="onServicioChange(index)"
                                                class="w-full px-2 py-1.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                            <option value="" style="background:#111827;">Seleccionar servicio...</option>
                                            <template x-for="s in todosServicios" :key="s.id">
                                                <option :value="s.id" x-text="s.nombre" :selected="s.id == item.servicio_id" style="background:#111827;"></option>
                                            </template>
                                        </select>
                                    </div>

                                    {{-- Precio (ancho fijo, solo lectura) --}}
                                    <div class="w-32 shrink-0 text-right">
                                        <label class="block text-xs text-gray-500 mb-1">Precio</label>
                                        <p class="text-sm font-semibold text-gray-200 py-1.5"
                                           x-text="item.precio_unitario > 0 ? 'Bs ' + item.precio_unitario.toFixed(2) : '—'"></p>
                                        <input type="hidden" :name="`servicios[${index}][cantidad]`" value="1">
                                        <input type="hidden" :name="`servicios[${index}][precio_unitario]`" :value="item.precio_unitario">
                                    </div>

                                    {{-- Eliminar --}}
                                    <div class="shrink-0">
                                        <button type="button" @click="eliminarLinea(index)"
                                                class="mt-5 text-red-500 hover:text-red-400 transition-colors">
                                            <i class="bi bi-x-lg" style="font-size:15px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <div x-show="lineas.length === 0" class="py-8 text-center text-gray-500 text-sm bg-gray-900/30 rounded-lg border border-gray-700 border-dashed">
                                <i class="bi bi-tools text-gray-600" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                Agrega al menos un servicio para continuar.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-chat-left-text" style="color:#D71920;"></i> Observaciones
                        </p>
                    </div>
                    <div class="p-6">
                        <textarea name="observaciones" rows="3"
                                  class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400"
                                  placeholder="Notas adicionales, diagnóstico inicial, condiciones del vehículo...">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">

                {{-- Asignación --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-person-gear" style="color:#D71920;"></i> Asignación
                        </p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Sucursal</label>
                            <select name="sucursal_id"
                                    class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                <option value="" style="background:#111827;">Sin asignar</option>
                                @foreach ($sucursales as $s)
                                    <option value="{{ $s->id }}" style="background:#111827;" {{ old('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Mecánico</label>
                            <select name="mecanico_id"
                                    class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                <option value="" style="background:#111827;">Sin asignar</option>
                                @foreach ($mecanicos as $m)
                                    <option value="{{ $m->id }}" style="background:#111827;" {{ old('mecanico_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->persona->nombre }}{{ $m->especialidad ? ' ('.$m->especialidad->nombre.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Prioridad</label>
                            <select name="prioridad"
                                    class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                @foreach (['Baja','Media','Alta','Urgente'] as $p)
                                    <option value="{{ $p }}" style="background:#111827;" {{ old('prioridad', 'Media') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Entrega estimada</label>
                            <input type="datetime-local" name="fecha_entrega_estimada"
                                   value="{{ old('fecha_entrega_estimada') }}"
                                   class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>
                </div>

                {{-- Resumen --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Resumen</p>
                    </div>
                    <div class="p-5 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-400">
                            <span>Subtotal</span>
                            <span x-text="'Bs ' + subtotal.toFixed(2)">Bs 0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-400 items-center">
                            <span>Descuento</span>
                            <input type="number" name="descuento" x-model.number="descuento" @input="calcularTotales()"
                                   step="0.01" min="0" placeholder="0.00"
                                   class="w-24 text-right px-2 py-1 bg-gray-900 border border-gray-600 text-gray-100 rounded text-sm focus:outline-none focus:ring-1 focus:ring-red-600">
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>IVA (13%)</span>
                            <span x-text="'Bs ' + iva.toFixed(2)">Bs 0.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-100 text-base border-t border-gray-700 pt-2 mt-2">
                            <span>Total</span>
                            <span x-text="'Bs ' + total.toFixed(2)">Bs 0.00</span>
                        </div>
                    </div>
                </div>

                <button type="submit" id="btnCrearOrden"
                        class="w-full px-6 py-3 text-white font-semibold rounded-lg transition-colors"
                        style="background:#D71920;">
                    <i class="bi bi-clipboard2-check me-2"></i> Crear orden de servicio
                </button>
                <a href="{{ route('ordenes.index') }}"
                   class="block w-full text-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-medium rounded-lg transition-colors">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

<script @nonce>
function vehiculoBuscador(vehiculos, seleccionadoId) {
    return {
        vehiculoId: seleccionadoId,
        busqueda: '',
        open: false,
        vehiculos: vehiculos,
        filtrados: [],
        init() {
            this.filtrados = this.vehiculos;
            if (this.vehiculoId) {
                const v = this.vehiculos.find(v => String(v.id) === String(this.vehiculoId));
                if (v) this.busqueda = v.label;
            }
            this.$watch('busqueda', (val) => {
                if (!val) {
                    this.filtrados = this.vehiculos;
                } else {
                    const q = val.toLowerCase();
                    this.filtrados = this.vehiculos.filter(v => v.label.toLowerCase().includes(q));
                }
            });
        }
    };
}

function ordenForm(servicios, lineasIniciales) {
    return {
        todosServicios: servicios,
        lineas: lineasIniciales.length ? lineasIniciales : [],
        descuento: 0,
        subtotal: 0,
        iva: 0,
        total: 0,

        agregarServicio() {
            this.lineas.push({ servicio_id: '', cantidad: 1, precio_unitario: 0 });
        },

        eliminarLinea(index) {
            this.lineas.splice(index, 1);
            this.calcularTotales();
        },

        onServicioChange(index) {
            const id = this.lineas[index].servicio_id;
            const servicio = this.todosServicios.find(s => s.id == id);
            if (servicio) {
                this.lineas[index].precio_unitario = parseFloat(servicio.precio);
            }
            this.calcularTotales();
        },

        calcularTotales() {
            this.subtotal = this.lineas.reduce((sum, l) => sum + ((l.cantidad || 0) * (l.precio_unitario || 0)), 0);
            const base = Math.max(0, this.subtotal - this.descuento);
            this.iva   = Math.round(base * 0.13 * 100) / 100;
            this.total = Math.round((base + this.iva) * 100) / 100;
        },

        init() {
            const btn = document.getElementById('btnCrearOrden');
            if (btn) {
                btn.addEventListener('mouseover', function () { this.style.background = '#b81218'; });
                btn.addEventListener('mouseout',  function () { this.style.background = '#D71920'; });
            }
        }
    };
}
</script>

@endsection
