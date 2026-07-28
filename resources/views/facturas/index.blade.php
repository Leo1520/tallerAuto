@extends('layouts.app')
@section('title', 'Facturas')
@section('page-title', 'Facturas')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-900/30 border border-blue-800">
            <i class="bi bi-receipt text-blue-400" style="font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Facturas encontradas</p>
            <p class="text-xl font-bold text-gray-100">{{ $facturas->total() }}</p>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-green-900/30 border border-green-800">
            <i class="bi bi-check-circle text-green-400" style="font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total facturado (emitidas)</p>
            <p class="text-xl font-bold text-gray-100">Bs {{ number_format($totalEmitido ?? 0, 2) }}</p>
        </div>
    </div>
</div>

<form id="filtroForm" method="GET" action="{{ route('facturas.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                   placeholder="N° factura u orden..." autocomplete="off"
                   class="w-full pl-9 pr-8 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </span>
        </div>
    </div>
    <div class="w-36">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
        <select name="estado"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            <option value="Emitida" {{ request('estado') === 'Emitida' ? 'selected' : '' }}>Emitida</option>
            <option value="Anulada" {{ request('estado') === 'Anulada' ? 'selected' : '' }}>Anulada</option>
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
    @if (request()->hasAny(['search', 'estado', 'fecha_desde', 'fecha_hasta']))
        <a href="{{ route('facturas.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p id="listCount" class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $facturas->total() }}</span>
            facturas encontradas
        </p>
    </div>

    @if ($facturas->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-receipt-cutoff text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron facturas.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">N° Factura</th>
                        <th class="px-6 py-3 text-left">Orden</th>
                        <th class="px-6 py-3 text-left">Cliente</th>
                        <th class="px-6 py-3 text-right">Subtotal</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-left">Fecha</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach ($facturas as $factura)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-3.5 font-mono text-sm font-semibold text-gray-100">{{ $factura->numero }}</td>
                        <td class="px-6 py-3.5">
                            <a href="{{ route('ordenes.show', $factura->orden) }}"
                               class="text-sm font-medium hover:underline" style="color:#D71920;">
                                {{ $factura->orden->numero }}
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-300">{{ $factura->orden->vehiculo->cliente->persona->nombre }}</td>
                        <td class="px-6 py-3.5 text-right text-sm text-gray-400">Bs {{ number_format($factura->subtotal, 2) }}</td>
                        <td class="px-6 py-3.5 text-right text-sm font-semibold text-gray-100">Bs {{ number_format($factura->total, 2) }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $factura->estado === 'Emitida'
                                    ? 'bg-green-900/40 text-green-400 border border-green-800'
                                    : 'bg-red-900/40 text-red-400 border border-red-800' }}">
                                {{ $factura->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ $factura->fecha_emision?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('facturas.show', $factura) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                                    <i class="bi bi-eye" style="font-size:11px;"></i> Ver
                                </a>
                                @if(Route::has('facturas.pdf'))
                                <a href="{{ route('facturas.pdf', $factura) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-300 bg-red-900/20 hover:bg-red-900/40 rounded-lg transition-colors border border-red-900/50">
                                    <i class="bi bi-file-earmark-pdf" style="font-size:11px;"></i> PDF
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div id="listPag" class="px-6 py-4 border-t border-gray-700 {{ $facturas->hasPages() ? '' : 'hidden' }}">
        {{ $facturas->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const input   = document.getElementById('searchInput');
    const spinner = document.getElementById('searchSpinner');
    const baseUrl = '{{ route('facturas.index') }}';
    let controller = null, timer = null;

    async function buscar() {
        if (controller) controller.abort();
        controller = new AbortController();
        spinner.classList.remove('hidden');
        const params = new URLSearchParams(new FormData(document.getElementById('filtroForm')));
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
})();
</script>
@endpush
