@extends('layouts.app')
@section('title', 'Facturas')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Facturas</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Registro de facturas emitidas</p>
        </div>
    </div>

    {{-- KPIs (skill: tailwind-automotive-ui — tarjetas de estadística) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total facturado</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">Bs {{ number_format($totalEmitido, 2) }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total facturas</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $facturas->total() }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anuladas</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ $facturas->getCollection()->where('estado', 'Anulada')->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por N° factura u orden..."
                   class="flex-1 min-w-56 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="estado"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                @foreach(['Borrador','Emitida','Anulada'] as $e)
                    <option value="{{ $e }}" @selected(request('estado') === $e)>{{ $e }}</option>
                @endforeach
            </select>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                   class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                   class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Filtrar
            </button>
            <a href="{{ route('facturas.index') }}"
               class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                Limpiar
            </a>
        </form>
    </div>

    {{-- Tabla (skill: tailwind-automotive-ui — tabla responsive) --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">N° Factura</th>
                    <th class="px-4 py-3 text-left">Orden</th>
                    <th class="px-4 py-3 text-left">Cliente</th>
                    <th class="px-4 py-3 text-left">Fecha emisión</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                    <th class="px-4 py-3 text-right">IVA 13%</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($facturas as $factura)
                @php
                    $badges = [
                        'Emitida'  => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        'Borrador' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                        'Anulada'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    ];
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $factura->estaAnulada() ? 'opacity-60' : '' }}">
                    <td class="px-4 py-3 font-mono font-semibold text-gray-900 dark:text-white text-xs">
                        {{ $factura->numero }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('ordenes.show', $factura->orden) }}"
                           class="font-mono text-blue-600 dark:text-blue-400 hover:underline text-xs">
                            {{ $factura->orden->numero }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                        {{ $factura->orden->vehiculo->cliente->persona->nombre ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap">
                        {{ $factura->fecha_emision?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                        Bs {{ number_format($factura->subtotal, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                        Bs {{ number_format($factura->iva, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">
                        Bs {{ number_format($factura->total, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badges[$factura->estado] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $factura->estado }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('facturas.show', $factura) }}"
                               class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="Ver detalle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if(!$factura->estaAnulada())
                            <a href="{{ route('facturas.pdf', $factura) }}"
                               class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Descargar PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                        No se encontraron facturas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($facturas->hasPages())
    <div>{{ $facturas->links() }}</div>
    @endif
</div>
@endsection
