@extends('layouts.app')
@section('title', 'Reporte de Mecánicos')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('reportes.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mecánicos más activos</h1>
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

    {{-- Tabla ranking --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Ranking de mecánicos</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Mecánico</th>
                        <th class="px-4 py-3 text-left">Sucursal</th>
                        <th class="px-4 py-3 text-center">Total órdenes</th>
                        <th class="px-4 py-3 text-center">Entregadas</th>
                        <th class="px-4 py-3 text-center">Activas</th>
                        <th class="px-4 py-3 text-right">Facturación</th>
                        <th class="px-4 py-3 text-right">Ticket prom.</th>
                        <th class="px-4 py-3 text-center">% Completadas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php $maxOrdenes = $mecanicos->max('total_ordenes') ?: 1; @endphp
                    @forelse($mecanicos as $i => $mec)
                    @php
                        $pct = $mec->total_ordenes > 0 ? ($mec->ordenes_entregadas / $mec->total_ordenes) * 100 : 0;
                        $medals = ['🥇', '🥈', '🥉'];
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 text-center font-bold text-gray-400 dark:text-gray-500">
                            @if($i < 3) <span title="{{ $medals[$i] }}">{{ $i + 1 }}</span>
                            @else {{ $i + 1 }} @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-orange-700 dark:text-orange-400">
                                        {{ strtoupper(substr($mec->nombre, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $mec->nombre }}</p>
                                    @if($mec->telefono)
                                    <p class="text-xs text-gray-400">{{ $mec->telefono }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $mec->sucursal }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $mec->total_ordenes }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-green-600 dark:text-green-400 font-semibold">
                            {{ $mec->ordenes_entregadas }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($mec->ordenes_activas > 0)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $mec->ordenes_activas }}
                            </span>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                            Bs {{ number_format($mec->facturacion_total ?? 0, 0) }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">
                            Bs {{ number_format($mec->ticket_promedio ?? 0, 0) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all
                                        {{ $pct >= 80 ? 'bg-green-500' : ($pct >= 50 ? 'bg-yellow-500' : 'bg-red-400') }}"
                                         style="width: {{ number_format($pct, 1) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-10 text-right">{{ number_format($pct, 0) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-gray-400">Sin datos para el período seleccionado.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($mecanicos->isNotEmpty())
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t-2 border-gray-200 dark:border-gray-600">
                    <tr>
                        <td colspan="3" class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300 text-xs uppercase">TOTALES</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-white">{{ $mecanicos->sum('total_ordenes') }}</td>
                        <td class="px-4 py-3 text-center font-bold text-green-600 dark:text-green-400">{{ $mecanicos->sum('ordenes_entregadas') }}</td>
                        <td class="px-4 py-3 text-center font-bold text-blue-600 dark:text-blue-400">{{ $mecanicos->sum('ordenes_activas') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Bs {{ number_format($mecanicos->sum('facturacion_total'), 0) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
