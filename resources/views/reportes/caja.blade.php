@extends('layouts.app')
@section('title', 'Reporte de Caja')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4 flex-wrap">
        <a href="{{ route('reportes.index') }}"
           class="p-2 rounded-lg text-gray-400 hover:text-gray-200 hover:bg-gray-700 transition-colors">
            <i class="bi bi-arrow-left" style="font-size:16px;"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Libro de Caja</h1>
            <p class="text-sm text-gray-400">Movimientos con trazabilidad completa de confirmación</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $desde }}"
                   class="px-3 py-2 text-sm bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $hasta }}"
                   class="px-3 py-2 text-sm bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">Tipo</label>
            <select name="tipo" class="px-3 py-2 text-sm bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                <option value="">Todos</option>
                <option value="Ingreso" @selected($tipo === 'Ingreso')>Solo ingresos</option>
                <option value="Egreso"  @selected($tipo === 'Egreso')>Solo egresos</option>
            </select>
        </div>
        <button type="submit"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
            <i class="bi bi-search" style="font-size:12px;"></i> Filtrar
        </button>
        @if(request()->hasAny(['fecha_desde','fecha_hasta','tipo']))
        <a href="{{ route('reportes.caja') }}" class="px-3 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:11px;"></i> Limpiar
        </a>
        @endif
    </form>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gray-800 border border-green-900/40 rounded-xl p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(74,222,128,.1);">
                <i class="bi bi-arrow-down-circle" style="color:#4ade80;font-size:20px;"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Ingresos</p>
                <p class="text-xl font-bold text-gray-100">Bs {{ number_format($resumen->total_ingresos ?? 0, 2) }}</p>
            </div>
        </div>
        <div class="bg-gray-800 border border-orange-900/40 rounded-xl p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(251,146,60,.1);">
                <i class="bi bi-arrow-up-circle" style="color:#fb923c;font-size:20px;"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Egresos</p>
                <p class="text-xl font-bold text-gray-100">Bs {{ number_format($resumen->total_egresos ?? 0, 2) }}</p>
            </div>
        </div>
        @php $balance = ($resumen->total_ingresos ?? 0) - ($resumen->total_egresos ?? 0); @endphp
        <div class="bg-gray-800 border {{ $balance >= 0 ? 'border-blue-900/40' : 'border-red-900/40' }} rounded-xl p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                 style="background:{{ $balance >= 0 ? 'rgba(96,165,250,.1)' : 'rgba(248,113,113,.1)' }};">
                <i class="bi bi-cash-stack" style="color:{{ $balance >= 0 ? '#60a5fa' : '#f87171' }};font-size:20px;"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Balance</p>
                <p class="text-xl font-bold {{ $balance >= 0 ? 'text-blue-400' : 'text-red-400' }}">
                    {{ $balance >= 0 ? '+' : '' }}Bs {{ number_format($balance, 2) }}
                </p>
            </div>
        </div>
    </div>

    {{-- Tabla de movimientos --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
            <p class="text-sm text-gray-400">
                <span class="font-semibold text-gray-200">{{ $movimientos->total() }}</span>
                movimientos encontrados
            </p>
            <span class="text-xs text-gray-500">{{ $desde }} — {{ $hasta }}</span>
        </div>

        @if($movimientos->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-inbox text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">Sin movimientos para el período seleccionado.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-4 py-3 text-left w-36">Fecha</th>
                        <th class="px-4 py-3 text-left">Concepto</th>
                        <th class="px-4 py-3 text-center w-24">Tipo</th>
                        <th class="px-4 py-3 text-right w-32">Monto</th>
                        <th class="px-4 py-3 text-left">Confirmado por</th>
                        <th class="px-4 py-3 text-left">IP</th>
                        <th class="px-4 py-3 text-left w-28">Canal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach($movimientos as $mov)
                    @php
                        $esIngreso = $mov->tipo === 'Ingreso';
                        $confirmadoPor = $mov->pago?->confirmadoPor?->persona?->nombre
                            ?? $mov->pago?->confirmadoPor?->email
                            ?? $mov->user?->persona?->nombre
                            ?? '—';
                        $ip     = $mov->pago?->confirmado_ip ?? '—';
                        $canal  = $mov->pago?->metodo_confirmacion ?? '—';
                        $cliente = $mov->pago?->orden?->vehiculo?->cliente?->persona?->nombre ?? null;
                    @endphp
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 text-xs text-gray-400 font-mono whitespace-nowrap">
                            {{ $mov->created_at->format('d/m/Y') }}<br>
                            <span class="text-gray-600">{{ $mov->created_at->format('H:i:s') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-200">{{ $mov->concepto }}</p>
                            @if($cliente)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $cliente }}</p>
                            @endif
                            @if($mov->pago)
                            <a href="{{ route('pagos.show', $mov->pago) }}"
                               class="text-xs mt-0.5 hover:underline" style="color:#60a5fa;">
                                Pago #{{ $mov->pago->id }}
                            </a>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                  style="background:{{ $esIngreso ? 'rgba(74,222,128,.1)' : 'rgba(251,146,60,.1)' }};
                                         color:{{ $esIngreso ? '#4ade80' : '#fb923c' }};
                                         border:1px solid {{ $esIngreso ? 'rgba(74,222,128,.2)' : 'rgba(251,146,60,.2)' }};">
                                {{ $mov->tipo }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold font-mono text-sm"
                            style="color:{{ $esIngreso ? '#4ade80' : '#fb923c' }};">
                            {{ $esIngreso ? '+' : '-' }}Bs {{ number_format($mov->monto, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-300">{{ $confirmadoPor }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $ip }}</td>
                        <td class="px-4 py-3">
                            @if($canal !== '—')
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                  style="background:rgba(167,139,250,.1);color:#a78bfa;border:1px solid rgba(167,139,250,.2);">
                                {{ $canal }}
                            </span>
                            @else
                            <span class="text-xs text-gray-600">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="px-5 py-4 border-t border-gray-700 {{ $movimientos->hasPages() ? '' : 'hidden' }}">
            {{ $movimientos->links() }}
        </div>
    </div>

</div>
@endsection
