@extends('layouts.app')

@section('title', 'Nueva orden de servicio')
@section('page-title', 'Nueva orden de servicio')

@section('content')

<div x-data="ordenForm({{ $servicios->toJson() }}, {{ old('servicios') ? json_encode(old('servicios')) : '[]' }})">

    <form method="POST" action="{{ route('ordenes.store') }}" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Columna principal --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Vehículo --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3 mb-4">Vehículo</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar vehículo <span class="text-red-500">*</span></label>
                        <select name="vehiculo_id" required
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('vehiculo_id') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">Buscar por placa o cliente...</option>
                            @foreach ($vehiculos as $v)
                                <option value="{{ $v->id }}" {{ old('vehiculo_id', $vehiculoSeleccionado?->id) == $v->id ? 'selected' : '' }}>
                                    {{ $v->placa }} — {{ $v->cliente->persona->nombre }} ({{ $v->modelo->marca->nombre }} {{ $v->modelo->nombre }} {{ $v->ano }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehiculo_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Servicios --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <p class="text-sm font-semibold text-gray-700">Servicios a realizar</p>
                        <button type="button" @click="agregarServicio()"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar servicio
                        </button>
                    </div>

                    @error('servicios') <p class="mb-3 text-xs text-red-600">{{ $message }}</p> @enderror

                    <div class="space-y-3">
                        <template x-for="(item, index) in lineas" :key="index">
                            <div class="grid grid-cols-12 gap-2 items-end p-3 bg-gray-50 rounded-lg">
                                {{-- Servicio --}}
                                <div class="col-span-5">
                                    <label class="block text-xs text-gray-500 mb-1">Servicio</label>
                                    <select :name="`servicios[${index}][servicio_id]`" x-model="item.servicio_id"
                                            @change="onServicioChange(index)"
                                            class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccionar...</option>
                                        <template x-for="s in todosServicios" :key="s.id">
                                            <option :value="s.id" x-text="s.nombre" :selected="s.id == item.servicio_id"></option>
                                        </template>
                                    </select>
                                </div>
                                {{-- Cantidad --}}
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Cant.</label>
                                    <input type="number" :name="`servicios[${index}][cantidad]`"
                                           x-model.number="item.cantidad" @input="calcularTotales()"
                                           min="1" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                {{-- Precio --}}
                                <div class="col-span-3">
                                    <label class="block text-xs text-gray-500 mb-1">Precio unitario</label>
                                    <input type="number" :name="`servicios[${index}][precio_unitario]`"
                                           x-model.number="item.precio_unitario" @input="calcularTotales()"
                                           step="0.01" min="0" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                {{-- Subtotal --}}
                                <div class="col-span-1 text-right">
                                    <label class="block text-xs text-gray-500 mb-1">Subtotal</label>
                                    <p class="text-sm font-semibold text-gray-900 py-1.5" x-text="'Bs ' + (item.cantidad * item.precio_unitario).toFixed(2)"></p>
                                </div>
                                {{-- Eliminar --}}
                                <div class="col-span-1 text-center">
                                    <button type="button" @click="eliminarLinea(index)"
                                            class="mt-5 text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="lineas.length === 0" class="py-6 text-center text-gray-400 text-sm bg-gray-50 rounded-lg">
                            Agrega al menos un servicio para continuar.
                        </div>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3 mb-4">Observaciones</p>
                    <textarea name="observaciones" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Notas adicionales, diagnóstico inicial, condiciones del vehículo...">{{ old('observaciones') }}</textarea>
                </div>
            </div>

            {{-- Sidebar derecho --}}
            <div class="space-y-4">

                {{-- Asignación --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3 mb-4">Asignación</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                            <select name="sucursal_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sin asignar</option>
                                @foreach ($sucursales as $s)
                                    <option value="{{ $s->id }}" {{ old('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mecánico</label>
                            <select name="mecanico_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sin asignar</option>
                                @foreach ($mecanicos as $m)
                                    <option value="{{ $m->id }}" {{ old('mecanico_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->persona->nombre }} {{ $m->especialidad ? '('.$m->especialidad->nombre.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                            <select name="prioridad" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach (['Baja','Media','Alta','Urgente'] as $p)
                                    <option value="{{ $p }}" {{ old('prioridad', 'Media') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Entrega estimada</label>
                            <input type="datetime-local" name="fecha_entrega_estimada"
                                   value="{{ old('fecha_entrega_estimada') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Totales --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3 mb-4">Resumen</p>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span x-text="'Bs ' + subtotal.toFixed(2)">Bs 0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-600 items-center">
                            <span>Descuento</span>
                            <input type="number" name="descuento" x-model.number="descuento" @input="calcularTotales()"
                                   step="0.01" min="0" placeholder="0.00"
                                   class="w-24 text-right px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>IVA (13%)</span>
                            <span x-text="'Bs ' + iva.toFixed(2)">Bs 0.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-900 text-base border-t border-gray-100 pt-2 mt-2">
                            <span>Total</span>
                            <span x-text="'Bs ' + total.toFixed(2)">Bs 0.00</span>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Crear orden de servicio
                </button>
                <a href="{{ route('ordenes.index') }}"
                   class="block w-full text-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

<script>
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
            this.subtotal = this.lineas.reduce((sum, l) => sum + (l.cantidad * l.precio_unitario), 0);
            const base = Math.max(0, this.subtotal - this.descuento);
            this.iva   = Math.round(base * 0.13 * 100) / 100;
            this.total = Math.round((base + this.iva) * 100) / 100;
        }
    };
}
</script>

@endsection
