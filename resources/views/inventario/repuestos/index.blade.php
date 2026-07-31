@extends('layouts.app')
@section('title', 'Repuestos')
@section('page-title', 'Repuestos')

@section('header-actions')
    <a href="{{ route('repuestos.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
       style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
        <span class="relative inline-flex items-center" style="font-size:15px;">
                <i class="bi bi-box-seam-fill"></i>
                <i class="bi bi-plus-lg" style="font-size:9px; font-weight:900; position:absolute; top:-4px; right:-5px;"></i>
            </span> Nuevo repuesto
    </a>
@endsection

@section('content')

<form id="filtroForm" method="GET" action="{{ route('repuestos.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nombre, código o referencia..." autocomplete="off"
                   class="w-full pl-9 pr-8 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </span>
        </div>
    </div>
    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Proveedor</label>
        <select name="proveedor_id"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            @foreach ($proveedores as $p)
                <option value="{{ $p->id }}" {{ request('proveedor_id') == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-36">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
        <select name="activo"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
            <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
        </select>
    </div>
    <div class="flex items-center gap-2 self-end pb-2">
        <input type="checkbox" name="bajo_stock" value="1" id="bajoStock"
               {{ request('bajo_stock') ? 'checked' : '' }}
               class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-red-600 focus:ring-red-600 cursor-pointer">
        <label for="bajoStock" class="text-xs font-medium text-gray-400 cursor-pointer whitespace-nowrap">Stock bajo</label>
    </div>
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
    @if (request()->hasAny(['search', 'proveedor_id', 'activo', 'bajo_stock']))
        <a href="{{ route('repuestos.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p id="listCount" class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $repuestos->total() }}</span>
            repuestos encontrados
        </p>
    </div>

    @if ($repuestos->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-box-seam text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron repuestos.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Repuesto</th>
                        <th class="px-6 py-3 text-left">Proveedor</th>
                        <th class="px-6 py-3 text-right">Precio Compra</th>
                        <th class="px-6 py-3 text-right">Precio Venta</th>
                        <th class="px-6 py-3 text-center">Stock total</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach ($repuestos as $repuesto)
                    @php
                        $stockTotal = $repuesto->inventarios->sum('stock');
                        $bajoPorAlguna = $repuesto->inventarios->contains(fn($i) => $i->stock <= $i->stock_minimo);
                    @endphp
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <p class="text-sm font-semibold text-gray-100 truncate" style="max-width:220px;" title="{{ $repuesto->nombre }}">{{ $repuesto->nombre }}</p>
                            @if($repuesto->codigo)
                            <p class="text-xs font-mono text-gray-500">{{ $repuesto->codigo }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ $repuesto->proveedor->nombre ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-right text-sm text-gray-300">Bs {{ number_format($repuesto->precio_compra, 2) }}</td>
                        <td class="px-6 py-3.5 text-right text-sm font-medium text-gray-100">Bs {{ number_format($repuesto->precio_venta, 2) }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                                {{ $bajoPorAlguna ? 'bg-orange-900/40 text-orange-400 border border-orange-800' : 'bg-gray-700 text-gray-300 border border-gray-600' }}">
                                {{ $stockTotal }}
                                @if($bajoPorAlguna) <i class="bi bi-exclamation-triangle-fill" style="font-size:10px;"></i> @endif
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $repuesto->activo
                                    ? 'bg-green-900/40 text-green-400 border border-green-800'
                                    : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                                {{ $repuesto->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('repuestos.show', $repuesto) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                                    <i class="bi bi-eye" style="font-size:11px;"></i> Ver
                                </a>
                                <a href="{{ route('repuestos.edit', $repuesto) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                                    <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                                </a>
                                <form method="POST" action="{{ route('repuestos.destroy', $repuesto) }}"
                                      data-confirm="¿Eliminar el repuesto {{ $repuesto->nombre }}? Solo es posible si no tiene stock disponible.">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="tpOpen(this.form)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-400 bg-red-900/20 hover:bg-red-900/40 rounded-lg transition-colors border border-red-900/50">
                                        <i class="bi bi-trash" style="font-size:11px;"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div id="listPag" class="px-6 py-4 border-t border-gray-700 {{ $repuestos->hasPages() ? '' : 'hidden' }}">
        {{ $repuestos->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script @nonce>
(function () {
    const input   = document.getElementById('searchInput');
    const spinner = document.getElementById('searchSpinner');
    const baseUrl = '{{ route('repuestos.index') }}';
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
    document.getElementById('bajoStock').addEventListener('change', buscar);
})();
</script>
@endpush
