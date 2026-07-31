@extends('layouts.app')
@section('title', 'Órdenes de servicio')
@section('page-title', 'Órdenes de servicio')

@section('header-actions')
    @can('create', App\Models\OrdenServicio::class)
        <a href="{{ route('ordenes.create') }}" id="btnNuevaOrden"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
           style="background:#D71920;">
            <span class="relative inline-flex items-center" style="font-size:15px;">
                <i class="bi bi-clipboard2-fill"></i>
                <i class="bi bi-plus-lg" style="font-size:9px; font-weight:900; position:absolute; top:-4px; right:-5px;"></i>
            </span> Nueva orden
        </a>
    @endcan
@endsection

@section('content')

@php
$colores = [
    'Recibido'            => 'bg-blue-900/40 text-blue-400 border border-blue-800',
    'En diagnóstico'      => 'bg-yellow-900/40 text-yellow-400 border border-yellow-800',
    'En reparación'       => 'bg-orange-900/40 text-orange-400 border border-orange-800',
    'Esperando repuestos' => 'bg-purple-900/40 text-purple-400 border border-purple-800',
    'Listo'               => 'bg-green-900/40 text-green-400 border border-green-800',
    'Entregado'           => 'bg-gray-700 text-gray-400 border border-gray-600',
    'Cancelado'           => 'bg-red-900/40 text-red-400 border border-red-800',
];
$prioridadColor = ['Baja' => 'text-gray-400', 'Media' => 'text-blue-400', 'Alta' => 'text-orange-400', 'Urgente' => 'text-red-400'];
@endphp

<form id="filtroForm" method="GET" action="{{ route('ordenes.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                   placeholder="N° orden, placa o cliente..." autocomplete="off"
                   class="w-full pl-9 pr-8 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </span>
        </div>
    </div>
    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
        <select name="estado" class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            @foreach (['Recibido','En diagnóstico','En reparación','Esperando repuestos','Listo','Entregado','Cancelado'] as $e)
                <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ $e }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-36">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Prioridad</label>
        <select name="prioridad" class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todas</option>
            @foreach (['Baja','Media','Alta','Urgente'] as $p)
                <option value="{{ $p }}" {{ request('prioridad') === $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Mecánico</label>
        <select name="mecanico_id" class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            @foreach ($mecanicos as $m)
                <option value="{{ $m->id }}" {{ request('mecanico_id') == $m->id ? 'selected' : '' }}>{{ $m->persona->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-36">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Desde</label>
        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
    </div>
    <div class="w-36">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Hasta</label>
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
    </div>
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
    @if (request()->hasAny(['search','estado','prioridad','mecanico_id','fecha_desde','fecha_hasta']))
        <a href="{{ route('ordenes.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p id="listCount" class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $ordenes->total() }}</span>
            {{ $ordenes->total() == 1 ? 'orden' : 'órdenes' }} encontradas
        </p>
    </div>

    @if ($ordenes->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-clipboard2-x text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron órdenes de servicio.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">N° Orden</th>
                        <th class="px-6 py-3 text-left">Vehículo / Cliente</th>
                        <th class="px-6 py-3 text-left">Mecánico</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-center">Prioridad</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-left">Fecha</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach ($ordenes as $orden)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-3.5 font-mono text-sm font-semibold text-gray-100">{{ $orden->numero }}</td>
                        <td class="px-6 py-3.5">
                            <p class="text-sm font-semibold text-gray-100">{{ $orden->vehiculo->placa }}</p>
                            <p class="text-xs text-gray-300">{{ $orden->vehiculo->cliente->persona->nombre }}</p>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-300">{{ $orden->mecanico?->persona->nombre ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $colores[$orden->estado] ?? 'bg-gray-700 text-gray-400' }}">
                                {{ $orden->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="text-xs font-bold {{ $prioridadColor[$orden->prioridad] ?? 'text-gray-400' }}">
                                {{ $orden->prioridad }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-right text-sm font-semibold {{ $orden->estado === 'Cancelado' ? 'text-gray-500 line-through' : 'text-gray-100' }}">
                            Bs {{ number_format($orden->total, 2) }}
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('ordenes.show', $orden) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                                    <i class="bi bi-eye" style="font-size:11px;"></i> Ver
                                </a>
                                @can('update', $orden)
                                    @unless(in_array($orden->estado, ['Entregado', 'Cancelado']))
                                        <a href="{{ route('ordenes.edit', $orden) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800">
                                            <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                                        </a>
                                    @endunless
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div id="listPag" class="px-6 py-4 border-t border-gray-700 {{ $ordenes->hasPages() ? '' : 'hidden' }}">
        {{ $ordenes->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script @nonce>
(function () {
    const input   = document.getElementById('searchInput');
    const spinner = document.getElementById('searchSpinner');
    const baseUrl = '{{ route('ordenes.index') }}';
    let controller = null, timer = null;

    async function buscar() {
        if (controller) controller.abort();
        controller = new AbortController();
        spinner.classList.remove('hidden');
        const form = document.getElementById('filtroForm');
        const params = new URLSearchParams(new FormData(form));
        // Limpiar entradas vacías
        for (const [k, v] of [...params.entries()]) { if (!v) params.delete(k); }
        try {
            const res = await fetch(baseUrl + (params.toString() ? '?' + params : ''), { signal: controller.signal, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
            ['tbody', '#listCount', '#listPag'].forEach(sel => {
                const n = doc.querySelector(sel), c = document.querySelector(sel);
                if (n && c) c.innerHTML = n.innerHTML;
            });
            history.replaceState(null, '', baseUrl + (params.toString() ? '?' + params : ''));
        } catch(e) { if (e.name !== 'AbortError') console.error(e); }
        finally { spinner.classList.add('hidden'); controller = null; }
    }

    input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(buscar, 250); });
    document.querySelectorAll('#filtroForm select').forEach(s => s.addEventListener('change', buscar));

    const btnNuevaOrden = document.getElementById('btnNuevaOrden');
    if (btnNuevaOrden) {
        btnNuevaOrden.addEventListener('mouseover', function () { this.style.background = '#b81218'; });
        btnNuevaOrden.addEventListener('mouseout',  function () { this.style.background = '#D71920'; });
    }
})();
</script>
@endpush
