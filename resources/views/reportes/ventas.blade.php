@extends('layouts.app')
@section('title', 'Reporte de Ventas')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('reportes.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ventas por período</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ $desde }}"
                       class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ $hasta }}"
                       class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Agrupar por</label>
                <select name="agrupar"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="dia"    @selected($agrupar === 'dia')>Día</option>
                    <option value="semana" @selected($agrupar === 'semana')>Semana</option>
                    <option value="mes"    @selected($agrupar === 'mes')>Mes</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sucursal</label>
                <select name="sucursal_id"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas</option>
                    @foreach($sucursales as $s)
                        <option value="{{ $s->id }}" @selected($sucursalId == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                Generar
            </button>
        </form>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $kpis = [
                ['label' => 'Total facturado',    'value' => 'Bs ' . number_format($resumen->total ?? 0, 2),         'color' => 'blue',   'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Facturas emitidas',  'value' => number_format($resumen->total_facturas ?? 0),           'color' => 'indigo', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['label' => 'Ticket promedio',    'value' => 'Bs ' . number_format($resumen->ticket_promedio ?? 0, 2),'color' => 'purple', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'],
                ['label' => 'IVA retenido',       'value' => 'Bs ' . number_format($resumen->iva ?? 0, 2),           'color' => 'green',  'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
            ];
        @endphp
        @foreach($kpis as $k)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
            <div class="p-3 bg-{{ $k['color'] }}-50 dark:bg-{{ $k['color'] }}-900/20 rounded-lg flex-shrink-0">
                <svg class="w-6 h-6 text-{{ $k['color'] }}-600 dark:text-{{ $k['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $k['icon'] }}"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide truncate">{{ $k['label'] }}</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">{{ $k['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Tabla de ventas por período --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Detalle por {{ $agrupar === 'dia' ? 'día' : ($agrupar === 'semana' ? 'semana' : 'mes') }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Período</th>
                            <th class="px-4 py-3 text-center">Facturas</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                            <th class="px-4 py-3 text-right">IVA</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($ventas as $v)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $v->periodo }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $v->total_facturas }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">Bs {{ number_format($v->subtotal, 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-500">Bs {{ number_format($v->iva, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Bs {{ number_format($v->total, 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-500">Bs {{ number_format($v->ticket_promedio, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">Sin datos para el período seleccionado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($ventas->isNotEmpty())
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t-2 border-gray-200 dark:border-gray-600">
                        <tr>
                            <td class="px-4 py-3 font-bold text-gray-900 dark:text-white text-xs uppercase">TOTAL</td>
                            <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-white">{{ $resumen->total_facturas }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Bs {{ number_format($resumen->subtotal, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Bs {{ number_format($resumen->iva, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-blue-600 dark:text-blue-400 text-base">Bs {{ number_format($resumen->total, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Bs {{ number_format($resumen->ticket_promedio, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Métodos de pago --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Métodos de pago</h2>
            @php $totalMetodos = $porMetodo->sum('total'); @endphp
            @forelse($porMetodo as $m)
            @php $pct = $totalMetodos > 0 ? ($m->total / $totalMetodos) * 100 : 0; @endphp
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $m->nombre }}</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ $m->cantidad }} cobros</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 dark:bg-blue-400 rounded-full transition-all"
                             style="width: {{ number_format($pct, 1) }}%"></div>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 w-20 text-right">Bs {{ number_format($m->total, 0) }}</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">Sin datos</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
