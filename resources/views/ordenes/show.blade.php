@extends('layouts.app')

@section('title', $orden->numero)
@section('page-title', 'Orden ' . $orden->numero)

@php
$colores = [
    'Recibido'            => 'bg-blue-100 text-blue-800',
    'En diagnóstico'      => 'bg-yellow-100 text-yellow-800',
    'En reparación'       => 'bg-orange-100 text-orange-800',
    'Esperando repuestos' => 'bg-purple-100 text-purple-800',
    'Listo'               => 'bg-green-100 text-green-800',
    'Entregado'           => 'bg-gray-100 text-gray-600',
    'Cancelado'           => 'bg-red-100 text-red-700',
];
$prioridadColor = ['Baja' => 'text-gray-400', 'Media' => 'text-blue-500', 'Alta' => 'text-orange-500', 'Urgente' => 'text-red-600'];
$estados = App\Http\Controllers\OrdenServicioController::ESTADOS;
@endphp

@section('header-actions')
    @can('update', $orden)
        <a href="{{ route('ordenes.edit', $orden) }}"
           class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Editar
        </a>
    @endcan
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Servicios detalle --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Servicios</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Servicio</th>
                            <th class="px-6 py-3 text-center">Cant.</th>
                            <th class="px-6 py-3 text-right">Precio unit.</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($orden->detalles as $det)
                        <tr>
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $det->servicio->nombre }}</p>
                                <p class="text-xs text-gray-500">{{ $det->servicio->tipoServicio?->nombre }}</p>
                            </td>
                            <td class="px-6 py-3 text-center text-sm text-gray-700">{{ $det->cantidad }}</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-700">Bs {{ number_format($det->precio_unitario, 2) }}</td>
                            <td class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Bs {{ number_format($det->subtotal, 2) }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $det->estado === 'Completado' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $det->estado }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 text-sm">
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-gray-500">Subtotal</td>
                            <td class="px-6 py-2 text-right font-medium text-gray-900">Bs {{ number_format($orden->subtotal, 2) }}</td>
                            <td></td>
                        </tr>
                        @if ($orden->descuento > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-gray-500">Descuento</td>
                            <td class="px-6 py-2 text-right font-medium text-red-600">- Bs {{ number_format($orden->descuento, 2) }}</td>
                            <td></td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-gray-500">IVA (13%)</td>
                            <td class="px-6 py-2 text-right font-medium text-gray-900">Bs {{ number_format($orden->impuestos, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr class="border-t border-gray-200">
                            <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-900">Total</td>
                            <td class="px-6 py-3 text-right font-bold text-lg text-gray-900">Bs {{ number_format($orden->total, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Repuestos utilizados --}}
        @if ($orden->repuestos->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Repuestos utilizados</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">Repuesto</th>
                            <th class="px-6 py-3 text-center">Cant.</th>
                            <th class="px-6 py-3 text-right">Precio</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($orden->repuestos as $rep)
                        <tr>
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $rep->repuesto->nombre }} <span class="text-gray-400 font-mono text-xs">{{ $rep->repuesto->codigo }}</span></td>
                            <td class="px-6 py-3 text-center text-gray-700">{{ $rep->cantidad }}</td>
                            <td class="px-6 py-3 text-right text-gray-700">Bs {{ number_format($rep->precio_unitario, 2) }}</td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-900">Bs {{ number_format($rep->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Pagos --}}
        @if ($orden->pagos->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Pagos</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach ($orden->pagos as $pago)
                <div class="px-6 py-4 flex items-center justify-between text-sm">
                    <div>
                        <p class="font-medium text-gray-900">{{ $pago->metodoPago->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $pago->created_at->format('d/m/Y H:i') }}
                            @if ($pago->referencia) · Ref: {{ $pago->referencia }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $pago->estado === 'Confirmado' ? 'bg-green-100 text-green-700' : ($pago->estado === 'Pendiente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $pago->estado }}
                        </span>
                        <span class="font-bold text-gray-900">Bs {{ number_format($pago->monto, 2) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Observaciones --}}
        @if ($orden->observaciones)
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-sm font-semibold text-gray-700 mb-2">Observaciones</p>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $orden->observaciones }}</p>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

        {{-- Info de la orden --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $colores[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $orden->estado }}
                </span>
                <span class="text-sm font-bold {{ $prioridadColor[$orden->prioridad] ?? '' }}">
                    ● {{ $orden->prioridad }}
                </span>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ingreso</dt>
                    <dd class="font-medium text-gray-900">{{ $orden->fecha_ingreso->format('d/m/Y H:i') }}</dd>
                </div>
                @if ($orden->fecha_entrega_estimada)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Entrega estimada</dt>
                    <dd class="font-medium text-gray-900">{{ $orden->fecha_entrega_estimada->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
                @if ($orden->fecha_entrega_real)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Entrega real</dt>
                    <dd class="font-medium text-green-700">{{ $orden->fecha_entrega_real->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Sucursal</dt>
                    <dd class="font-medium text-gray-900">{{ $orden->sucursal?->nombre ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mecánico</dt>
                    <dd class="font-medium text-gray-900">{{ $orden->mecanico?->persona->nombre ?? 'Sin asignar' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Vehículo --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Vehículo</p>
            <p class="font-bold text-gray-900 font-mono text-lg">{{ $orden->vehiculo->placa }}</p>
            <p class="text-sm text-gray-600">{{ $orden->vehiculo->modelo->marca->nombre }} {{ $orden->vehiculo->modelo->nombre }} {{ $orden->vehiculo->ano }}</p>
            <p class="text-xs text-gray-500 mt-1">VIN: <span class="font-mono">{{ $orden->vehiculo->vin }}</span></p>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500">Propietario</p>
                <p class="text-sm font-semibold text-gray-900">{{ $orden->vehiculo->cliente->persona->nombre }}</p>
                <p class="text-xs text-gray-500">{{ $orden->vehiculo->cliente->persona->telefono ?? '' }}</p>
            </div>
            <a href="{{ route('vehiculos.show', $orden->vehiculo) }}" class="block mt-2 text-xs text-blue-600 hover:underline">Ver vehículo →</a>
        </div>

        {{-- Cambiar estado --}}
        @can('cambiarEstado', $orden)
        @if (! in_array($orden->estado, ['Entregado', 'Cancelado']))
        <div class="bg-white rounded-xl shadow-sm p-5" x-data="{ open: false }">
            <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-sm font-semibold text-gray-700">
                Cambiar estado
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-cloak class="mt-4">
                <form method="POST" action="{{ route('ordenes.estado', $orden) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nuevo estado</label>
                        <select name="estado" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ($estados as $e)
                                <option value="{{ $e }}" {{ $orden->estado === $e ? 'selected' : '' }}>{{ $e }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nota (opcional)</label>
                        <textarea name="observaciones" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Motivo del cambio..."></textarea>
                    </div>
                    <button type="submit"
                            class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Actualizar estado
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endcan

    </div>

</div>

@endsection
