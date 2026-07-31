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

<div x-data="ordenForm({{ $servicios->toJson() }}, {{ old('servicios') ? json_encode(old('servicios')) : '[]' }})">

    <form method="POST" action="{{ route('ordenes.store') }}" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Columna principal --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Vehículo --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-car-front" style="color:#D71920;"></i> Vehículo
                        </p>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Seleccionar vehículo <span class="text-red-400">*</span></label>
                        <select name="vehiculo_id" required
                                class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 {{ $errors->has('vehiculo_id') ? 'border-red-500' : 'border-gray-600' }}">
                            <option value="" style="background:#111827;">Buscar por placa o cliente...</option>
                            @foreach ($vehiculos as $v)
                                <option value="{{ $v->id }}"
                                        style="background:#111827;"
                                        {{ old('vehiculo_id', $vehiculoSeleccionado?->id) == $v->id ? 'selected' : '' }}>
                                    {{ $v->placa }} — {{ $v->cliente->persona->nombre }} ({{ $v->modelo->marca->nombre }} {{ $v->modelo->nombre }} {{ $v->ano }})
                                </option>
                            @endforeach
                        </select>
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
                                <div class="grid grid-cols-12 gap-2 items-end p-3 bg-gray-900/50 rounded-lg border border-gray-700">
                                    <div class="col-span-5">
                                        <label class="block text-xs text-gray-500 mb-1">Servicio</label>
                                        <select :name="`servicios[${index}][servicio_id]`" x-model="item.servicio_id"
                                                @change="onServicioChange(index)"
                                                class="w-full px-2 py-1.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                            <option value="" style="background:#111827;">Seleccionar...</option>
                                            <template x-for="s in todosServicios" :key="s.id">
                                                <option :value="s.id" x-text="s.nombre" :selected="s.id == item.servicio_id" style="background:#111827;"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs text-gray-500 mb-1">Cant.</label>
                                        <input type="number" :name="`servicios[${index}][cantidad]`"
                                               x-model.number="item.cantidad" @input="calcularTotales()"
                                               min="1" class="w-full px-2 py-1.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs text-gray-500 mb-1">Precio unitario</label>
                                        <input type="number" :name="`servicios[${index}][precio_unitario]`"
                                               x-model.number="item.precio_unitario" @input="calcularTotales()"
                                               step="0.01" min="0" class="w-full px-2 py-1.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                    </div>
                                    <div class="col-span-1 text-right">
                                        <label class="block text-xs text-gray-500 mb-1">Subtotal</label>
                                        <p class="text-sm font-semibold text-gray-100 py-1.5" x-text="'Bs ' + (item.cantidad * item.precio_unitario).toFixed(2)"></p>
                                    </div>
                                    <div class="col-span-1 text-center">
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

                <button type="submit"
                        class="w-full px-6 py-3 text-white font-semibold rounded-lg transition-colors"
                        style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
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
