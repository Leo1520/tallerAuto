@extends('layouts.app')
@section('title', 'Proveedores')
@section('page-title', 'Proveedores')

@section('header-actions')
    <a href="{{ route('proveedores.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors btn-taller-red">
        <i class="bi bi-building-add" style="font-size:15px;"></i> Nuevo proveedor
    </a>
@endsection

@section('content')

<form id="filtroForm" method="GET" action="{{ route('proveedores.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nombre, NIT o teléfono..." autocomplete="off"
                   class="w-full pl-9 pr-8 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </span>
        </div>
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
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
    @if (request()->hasAny(['search', 'activo']))
        <a href="{{ route('proveedores.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p id="listCount" class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $proveedores->total() }}</span>
            proveedores encontrados
        </p>
    </div>

    @if ($proveedores->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-building text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron proveedores.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Proveedor</th>
                        <th class="px-6 py-3 text-left">NIT</th>
                        <th class="px-6 py-3 text-left">Teléfono</th>
                        <th class="px-6 py-3 text-left">Ciudad</th>
                        <th class="px-6 py-3 text-center">Repuestos</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach ($proveedores as $proveedor)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <p class="text-sm font-semibold text-gray-100">{{ $proveedor->nombre }}</p>
                            @if($proveedor->email)
                            <p class="text-xs text-gray-500">{{ $proveedor->email }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-sm font-mono text-gray-300">{{ $proveedor->nit ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-300">{{ $proveedor->telefono ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-400">{{ $proveedor->ciudad ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-gray-700 text-gray-300 border border-gray-600">
                                {{ $proveedor->repuestos_count ?? $proveedor->repuestos->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $proveedor->activo
                                    ? 'bg-green-900/40 text-green-400 border border-green-800'
                                    : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                                {{ $proveedor->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('proveedores.edit', $proveedor) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                                    <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                                </a>
                                <form method="POST" action="{{ route('proveedores.destroy', $proveedor) }}"
                                      data-confirm="¿Eliminar el proveedor {{ $proveedor->nombre }}?">
                                    @csrf @method('DELETE')
                                    <button type="button" data-confirm-open
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

    <div id="listPag" class="px-6 py-4 border-t border-gray-700 {{ $proveedores->hasPages() ? '' : 'hidden' }}">
        {{ $proveedores->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script @nonce>
(function () {
    const input   = document.getElementById('searchInput');
    const spinner = document.getElementById('searchSpinner');
    const baseUrl = '{{ route('proveedores.index') }}';
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
                if (n && c) {
                    c.innerHTML = n.innerHTML;
                    if (sel === 'tbody' && window.tpInit) window.tpInit(c);
                }
            });
            history.replaceState(null, '', baseUrl + (params.toString() ? '?' + params : ''));
        } catch(e) { if (e.name !== 'AbortError') console.error(e); }
        finally { spinner.classList.add('hidden'); controller = null; }
    }

    input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(buscar, 250); });
    document.querySelectorAll('#filtroForm select').forEach(s => s.addEventListener('change', buscar));

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-confirm-open]');
        if (btn) { e.preventDefault(); tpOpen(btn.closest('form')); }
    });
})();
</script>
@endpush

@push('styles')
<style>.btn-taller-red{background:#D71920}.btn-taller-red:hover{background:#b81218}</style>
@endpush
