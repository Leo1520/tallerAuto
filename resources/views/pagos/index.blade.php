@extends('layouts.app')
@section('title', 'Pagos')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pagos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Historial de cobros y transacciones</p>
        </div>
        <a href="{{ route('pagos.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Registrar Pago
        </a>
    </div>

    {{-- Resumen --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total confirmado</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">Bs {{ number_format($totalConfirmado, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pagos en el período</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $pagos->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pendientes de confirmar</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                {{ $pagos->getCollection()->where('estado', 'Pendiente')->count() }}
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="estado"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                @foreach(['Pendiente','Confirmado','Anulado'] as $e)
                    <option value="{{ $e }}" @selected(request('estado') === $e)>{{ $e }}</option>
                @endforeach
            </select>

            <select name="metodo_pago_id"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los métodos</option>
                @foreach($metodos as $m)
                    <option value="{{ $m->id }}" @selected(request('metodo_pago_id') == $m->id)>{{ $m->nombre }}</option>
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
            <a href="{{ route('pagos.index') }}"
               class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                Limpiar
            </a>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Orden</th>
                    <th class="px-4 py-3 text-left">Cliente</th>
                    <th class="px-4 py-3 text-left">Método</th>
                    <th class="px-4 py-3 text-right">Monto</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($pagos as $pago)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $pago->created_at->format('d/m/Y') }}<br>{{ $pago->created_at->format('H:i') }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('ordenes.show', $pago->orden) }}"
                           class="font-mono text-blue-600 dark:text-blue-400 hover:underline text-xs">{{ $pago->orden->numero }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                        {{ $pago->orden->vehiculo->cliente->persona->nombre ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        {{ $pago->metodoPago->nombre }}
                        @if($pago->transaccion_externa)
                            <span class="ml-1 text-xs text-gray-400" title="{{ $pago->transaccion_externa }}">
                                <svg class="w-3 h-3 inline text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                        Bs {{ number_format($pago->monto, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $pago->estado === 'Confirmado' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                               ($pago->estado === 'Anulado'    ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400') }}">
                            {{ $pago->estado }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            @if($pago->estado === 'Pendiente')
                            <form method="POST" action="{{ route('pagos.confirmar', $pago) }}">
                                @csrf
                                <button type="submit"
                                        class="p-1.5 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors" title="Confirmar pago">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            @if($pago->estado !== 'Anulado' && auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('pagos.anular', $pago) }}"
                                  onsubmit="return confirm('¿Anular este pago?')">
                                @csrf
                                <button type="submit"
                                        class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Anular">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                        No se encontraron pagos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pagos->hasPages())
    <div>{{ $pagos->links() }}</div>
    @endif
</div>
@endsection
