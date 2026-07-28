@extends('layouts.app')
@section('title', 'Reporte de Repuestos')

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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Repuestos más usados</h1>
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
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Top N repuestos</label>
                <select name="limite"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach([10, 20, 50, 100] as $n)
                        <option value="{{ $n }}" @selected($limite == $n)>Top {{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                Generar
            </button>
        </form>
    </div>

    {{-- KPIs del período --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $margenTotal = ($totalesRepuestos->ingresos_totales ?? 0) - ($totalesRepuestos->costo_total ?? 0);
            $margenPct   = ($totalesRepuestos->ingresos_totales ?? 0) > 0
                ? ($margenTotal / $totalesRepuestos->ingresos_totales) * 100 : 0;
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tipos distintos</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalesRepuestos->tipos_distintos ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Unidades vendidas</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalesRepuestos->unidades_totales ?? 0) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ingresos generados</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">Bs {{ number_format($totalesRepuestos->ingresos_totales ?? 0, 0) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Margen total</p>
            <p class="text-2xl font-bold {{ $margenTotal >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600' }} mt-1">
                Bs {{ number_format($margenTotal, 0) }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">{{ number_format($margenPct, 1) }}% sobre ventas</p>
        </div>
    </div>

    {{-- Tabla de repuestos --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                Top {{ $limite }} repuestos — ordenados por unidades vendidas
            </h2>
            <span class="text-xs text-gray-400">{{ $repuestos->count() }} resultados</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Código</th>
                        <th class="px-4 py-3 text-left">Repuesto</th>
                        <th class="px-4 py-3 text-left">Proveedor</th>
                        <th class="px-4 py-3 text-center">Usos en órdenes</th>
                        <th class="px-4 py-3 text-center">Unidades</th>
                        <th class="px-4 py-3 text-right">Ingresos</th>
                        <th class="px-4 py-3 text-right">Costo</th>
                        <th class="px-4 py-3 text-right">Margen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php $maxUnidades = $repuestos->max('unidades_vendidas') ?: 1; @endphp
                    @forelse($repuestos as $i => $rep)
                    @php
                        $margen = $rep->margen ?? 0;
                        $margenRepPct = ($rep->ingresos_generados ?? 0) > 0 ? ($margen / $rep->ingresos_generados) * 100 : 0;
                        $barWidth = ($rep->unidades_vendidas / $maxUnidades) * 100;
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 text-center text-gray-400 dark:text-gray-500 font-mono text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $rep->codigo }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('repuestos.show', $rep->id) }}"
                               class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $rep->nombre }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $rep->proveedor ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $rep->veces_usado }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 dark:bg-green-400 rounded-full"
                                         style="width: {{ number_format($barWidth, 1) }}%"></div>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white w-8 text-right">{{ $rep->unidades_vendidas }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                            Bs {{ number_format($rep->ingresos_generados ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">
                            Bs {{ number_format($rep->costo_total ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold {{ $margen >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600' }}">
                                Bs {{ number_format($margen, 2) }}
                            </span>
                            <span class="block text-xs {{ $margenRepPct >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                {{ number_format($margenRepPct, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-gray-400">Sin datos para el período seleccionado.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($repuestos->isNotEmpty())
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t-2 border-gray-200 dark:border-gray-600">
                    <tr>
                        <td colspan="5" class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300 text-xs uppercase">TOTALES</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-white">{{ $repuestos->sum('unidades_vendidas') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Bs {{ number_format($repuestos->sum('ingresos_generados'), 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-500 dark:text-gray-400">Bs {{ number_format($repuestos->sum('costo_total'), 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400">Bs {{ number_format($repuestos->sum('margen'), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
