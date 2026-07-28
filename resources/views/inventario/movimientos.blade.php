@extends('layouts.app')
@section('title', 'Historial de Movimientos')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('inventario.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Historial de Movimientos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Entradas, salidas y ajustes de stock</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="sucursal_id"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las sucursales</option>
                @foreach($sucursales as $suc)
                    <option value="{{ $suc->id }}" @selected(request('sucursal_id') == $suc->id)>{{ $suc->nombre }}</option>
                @endforeach
            </select>

            <select name="repuesto_id"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los repuestos</option>
                @foreach($repuestos as $rep)
                    <option value="{{ $rep->id }}" @selected(request('repuesto_id') == $rep->id)>{{ $rep->nombre }}</option>
                @endforeach
            </select>

            <select name="tipo"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los tipos</option>
                <option value="Entrada" @selected(request('tipo') === 'Entrada')>Entrada</option>
                <option value="Salida"  @selected(request('tipo') === 'Salida')>Salida</option>
                <option value="Ajuste"  @selected(request('tipo') === 'Ajuste')>Ajuste</option>
            </select>

            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                   class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                   class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit"
                    class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Filtrar
            </button>
            <a href="{{ route('inventario.movimientos') }}"
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
                    <th class="px-4 py-3 text-left">Repuesto</th>
                    <th class="px-4 py-3 text-left">Sucursal</th>
                    <th class="px-4 py-3 text-center">Tipo</th>
                    <th class="px-4 py-3 text-center">Cantidad</th>
                    <th class="px-4 py-3 text-left">Motivo</th>
                    <th class="px-4 py-3 text-left">Referencia</th>
                    <th class="px-4 py-3 text-left">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($movimientos as $mov)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap text-xs">
                        {{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y') }}<br>
                        <span class="text-gray-400">{{ \Carbon\Carbon::parse($mov->created_at)->format('H:i') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('repuestos.show', $mov->repuesto) }}"
                           class="font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $mov->repuesto->nombre }}</a>
                        <div class="text-xs text-gray-400 font-mono">{{ $mov->repuesto->codigo }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $mov->sucursal->nombre }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $mov->tipo === 'Entrada' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                               ($mov->tipo === 'Salida'  ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400') }}">
                            {{ $mov->tipo }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-lg
                        {{ $mov->tipo === 'Entrada' ? 'text-green-600 dark:text-green-400' :
                           ($mov->tipo === 'Salida'  ? 'text-red-600 dark:text-red-400' : 'text-yellow-600') }}">
                        {{ $mov->tipo === 'Salida' ? '-' : '+' }}{{ $mov->cantidad }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $mov->motivo ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $mov->referencia ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                        {{ $mov->user?->persona?->nombre ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                        No se encontraron movimientos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movimientos->hasPages())
    <div>{{ $movimientos->links() }}</div>
    @endif
</div>
@endsection
