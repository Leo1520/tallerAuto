@extends('layouts.app')
@section('title', 'Factura ' . $factura->numero)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('facturas.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-mono">{{ $factura->numero }}</h1>
                    @php
                        $badges = [
                            'Emitida'  => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'Borrador' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'Anulada'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $badges[$factura->estado] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $factura->estado }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Emitida el {{ $factura->fecha_emision?->format('d/m/Y \a \l\a\s H:i') ?? 'Sin fecha' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(!$factura->estaAnulada())
            <a href="{{ route('facturas.pdf', $factura) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar PDF
            </a>
            @endif
            @if(auth()->user()->isAdmin() && !$factura->estaAnulada())
            <button onclick="document.getElementById('modalAnular').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 border border-red-200 dark:border-red-700 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Anular Factura
            </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Datos del cliente / vehículo --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Facturar a</h2>
                <div class="grid grid-cols-2 gap-6">
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-gray-400 text-xs">Cliente</dt>
                            <dd class="font-semibold text-gray-900 dark:text-white">
                                {{ $factura->orden->vehiculo->cliente->persona->nombre ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 text-xs">CI / NIT</dt>
                            <dd class="text-gray-700 dark:text-gray-300">
                                {{ $factura->orden->vehiculo->cliente->numero_documento ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 text-xs">Teléfono</dt>
                            <dd class="text-gray-700 dark:text-gray-300">
                                {{ $factura->orden->vehiculo->cliente->persona->telefono ?? '—' }}
                            </dd>
                        </div>
                    </dl>
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="text-gray-400 text-xs">Vehículo</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">
                                {{ $factura->orden->vehiculo->modelo->marca->nombre ?? '' }}
                                {{ $factura->orden->vehiculo->modelo->nombre ?? '' }}
                                ({{ $factura->orden->vehiculo->anio ?? '' }})
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 text-xs">Placa</dt>
                            <dd class="font-mono font-bold text-gray-900 dark:text-white">
                                {{ $factura->orden->vehiculo->placa }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 text-xs">Sucursal</dt>
                            <dd class="text-gray-700 dark:text-gray-300">
                                {{ $factura->orden->sucursal->nombre ?? '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Servicios --}}
            @if($factura->orden->detalles->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Servicios realizados</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Servicio</th>
                            <th class="px-4 py-3 text-right">Precio unit.</th>
                            <th class="px-4 py-3 text-center">Cant.</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($factura->orden->detalles as $det)
                        <tr>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                {{ $det->servicio->nombre }}
                                @if($det->descripcion)
                                    <p class="text-xs text-gray-400">{{ $det->descripcion }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">Bs {{ number_format($det->precio_unitario, 2) }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $det->cantidad }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">Bs {{ number_format($det->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Repuestos --}}
            @if($factura->orden->repuestos->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Repuestos utilizados</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Repuesto</th>
                            <th class="px-4 py-3 text-right">Precio unit.</th>
                            <th class="px-4 py-3 text-center">Cant.</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($factura->orden->repuestos as $rep)
                        <tr>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $rep->repuesto->nombre }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">Bs {{ number_format($rep->precio_unitario, 2) }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $rep->cantidad }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">Bs {{ number_format($rep->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Totales --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Resumen</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Subtotal</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">Bs {{ number_format($factura->subtotal, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">IVA (13%)</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">Bs {{ number_format($factura->iva, 2) }}</dd>
                    </div>
                    @if($factura->orden->descuento > 0)
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <dt>Descuento</dt>
                        <dd>- Bs {{ number_format($factura->orden->descuento, 2) }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-600">
                        <dt class="font-bold text-gray-900 dark:text-white text-base">TOTAL</dt>
                        <dd class="font-bold text-gray-900 dark:text-white text-xl">Bs {{ number_format($factura->total, 2) }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Pagos de la orden --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Pagos recibidos</h2>
                @forelse($factura->orden->pagos->where('estado', 'Confirmado') as $pago)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0 text-sm">
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $pago->metodoPago->nombre }}</p>
                        <p class="text-xs text-gray-400">{{ $pago->created_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="font-semibold text-green-600 dark:text-green-400">Bs {{ number_format($pago->monto, 2) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400">Sin pagos confirmados</p>
                @endforelse
            </div>

            {{-- Observaciones --}}
            @if($factura->observaciones)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Observaciones</h2>
                {{-- Skill: laravel-security — usar {{ }} nunca {!! !!} con datos de usuario --}}
                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $factura->observaciones }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal anular (solo admin) --}}
@if(auth()->user()->isAdmin() && !$factura->estaAnulada())
<div id="modalAnular" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Anular Factura</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Esta acción quedará registrada en auditoría y no se puede deshacer.</p>
        <form method="POST" action="{{ route('facturas.anular', $factura) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo de anulación</label>
                <textarea name="motivo" rows="3" required
                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Describe el motivo..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalAnular').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    Confirmar anulación
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
