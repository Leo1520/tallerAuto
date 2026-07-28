@extends('layouts.app')
@section('title', 'Inventario — Stock por Sucursal')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventario</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Stock actual de repuestos por sucursal</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('inventario.movimientos') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Historial
            </a>
            <a href="{{ route('repuestos.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Repuestos
            </a>
        </div>
    </div>

    @if($alertasBajoStock > 0)
    <div class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span><strong>{{ $alertasBajoStock }}</strong> repuesto(s) con stock por debajo del mínimo.</span>
        <a href="?bajo_stock=1&sucursal_id={{ $sucursalId }}" class="underline font-medium">Ver alertas</a>
    </div>
    @endif

    {{-- Selector de sucursal + filtros --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sucursal:</label>
                <select name="sucursal_id" onchange="this.form.submit()"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($sucursales as $suc)
                        <option value="{{ $suc->id }}" @selected($suc->id == $sucursalId)>{{ $suc->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar repuesto..."
                   class="flex-1 min-w-48 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" name="bajo_stock" value="1" @checked(request('bajo_stock'))
                       class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                Solo bajo stock
            </label>
            <button type="submit"
                    class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Filtrar
            </button>
        </form>
    </div>

    {{-- Tabla de stock --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">Repuesto</th>
                    <th class="px-4 py-3 text-left">Proveedor</th>
                    <th class="px-4 py-3 text-center">Stock actual</th>
                    <th class="px-4 py-3 text-center">Stock mínimo</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                    @can('registrarMovimiento', App\Policies\InventarioPolicy::class)
                    <th class="px-4 py-3 text-center">Movimiento rápido</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($stocks as $inv)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $inv->bajoStock() ? 'bg-red-50/30 dark:bg-red-900/10' : '' }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('repuestos.show', $inv->repuesto) }}"
                           class="font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $inv->repuesto->nombre }}</a>
                        <div class="text-xs text-gray-400 font-mono">{{ $inv->repuesto->codigo }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $inv->repuesto->proveedor?->nombre ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-2xl font-bold {{ $inv->bajoStock() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $inv->stock }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex items-center justify-center gap-1">
                            {{ $inv->stock_minimo }}
                            @can('gestionarRepuestos', App\Policies\InventarioPolicy::class)
                            <button x-data
                                    @click="
                                        let nuevo = prompt('Nuevo stock mínimo:', {{ $inv->stock_minimo }});
                                        if (nuevo !== null) {
                                            let f = document.createElement('form');
                                            f.method = 'POST';
                                            f.action = '{{ route('inventario.stockMinimo', $inv) }}';
                                            f.innerHTML = `@csrf @method('PATCH')<input name='stock_minimo' value='${nuevo}'>`;
                                            document.body.appendChild(f);
                                            f.submit();
                                        }
                                    "
                                    class="text-gray-300 hover:text-blue-500 transition-colors" title="Editar mínimo">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            @endcan
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($inv->bajoStock())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                ⚠ Bajo stock
                            </span>
                        @else
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                OK
                            </span>
                        @endif
                    </td>
                    @can('registrarMovimiento', App\Policies\InventarioPolicy::class)
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('inventario.movimiento') }}"
                              class="flex items-center gap-1.5" x-data="{ tipo: 'Entrada' }">
                            @csrf
                            <input type="hidden" name="repuesto_id" value="{{ $inv->repuesto_id }}">
                            <input type="hidden" name="sucursal_id" value="{{ $inv->sucursal_id }}">

                            <select name="tipo" x-model="tipo"
                                    class="px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="Entrada">Entrada</option>
                                <option value="Salida">Salida</option>
                                <option value="Ajuste">Ajuste</option>
                            </select>
                            <input type="number" name="cantidad" value="1" min="1"
                                   class="w-16 px-2 py-1 text-xs text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <button type="submit"
                                    :class="tipo === 'Salida' ? 'bg-red-500 hover:bg-red-600' : (tipo === 'Ajuste' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600')"
                                    class="px-2.5 py-1 text-xs text-white rounded font-medium transition-colors">
                                ✓
                            </button>
                        </form>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                        No hay repuestos en inventario para esta sucursal.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stocks->hasPages())
    <div>{{ $stocks->links() }}</div>
    @endif
</div>
@endsection
