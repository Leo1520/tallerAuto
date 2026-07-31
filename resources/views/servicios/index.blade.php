@extends('layouts.app')
@section('title', 'Servicios')
@section('page-title', 'Servicios')

@section('header-actions')
    <a href="{{ route('servicios.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
       style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
        <i class="bi bi-wrench-adjustable" style="font-size:14px;"></i> Nuevo servicio
    </a>
@endsection

@section('content')

<form id="filtroForm" method="GET" action="{{ route('servicios.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nombre o descripción..." autocomplete="off"
                   class="w-full pl-9 pr-8 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </span>
        </div>
    </div>
    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Tipo</label>
        <select name="tipo_servicio_id"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            @foreach($tipos as $tipo)
                <option value="{{ $tipo->id }}" {{ request('tipo_servicio_id') == $tipo->id ? 'selected' : '' }}>
                    {{ $tipo->nombre }}
                </option>
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
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
    @if(request()->hasAny(['search','tipo_servicio_id','activo']))
        <a href="{{ route('servicios.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p id="listCount" class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $servicios->total() }}</span> servicios encontrados
        </p>
    </div>

    @if($servicios->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-wrench-adjustable text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron servicios.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Servicio</th>
                        <th class="px-6 py-3 text-left">Tipo</th>
                        <th class="px-6 py-3 text-right">Precio</th>
                        <th class="px-6 py-3 text-center">Tiempo est.</th>
                        <th class="px-6 py-3 text-center">Repuestos</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach($servicios as $srv)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <p class="text-sm font-semibold text-gray-100">{{ $srv->nombre }}</p>
                            @if($srv->descripcion)
                            <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($srv->descripcion, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-3.5">
                            @if($srv->tipoServicio)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-900/30 text-blue-300 border border-blue-800/50">
                                {{ $srv->tipoServicio->nombre }}
                            </span>
                            @else
                            <span class="text-xs text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right text-sm font-bold text-gray-100">
                            Bs {{ number_format($srv->precio, 2) }}
                        </td>
                        <td class="px-6 py-3.5 text-center text-sm text-gray-400">
                            {{ $srv->tiempo_estimado ? $srv->tiempo_estimado . ' min' : '—' }}
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            @if($srv->requiere_repuestos)
                            <i class="bi bi-check-circle-fill text-green-400" style="font-size:15px;"></i>
                            @else
                            <i class="bi bi-dash text-gray-600" style="font-size:15px;"></i>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $srv->activo
                                    ? 'bg-green-900/40 text-green-400 border border-green-800'
                                    : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                                {{ $srv->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('servicios.edit', $srv) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                                    <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                                </a>
                                <form method="POST" action="{{ route('servicios.destroy', $srv) }}"
                                      data-confirm="¿Eliminar el servicio {{ $srv->nombre }}?">
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

    <div id="listPag" class="px-6 py-4 border-t border-gray-700 {{ $servicios->hasPages() ? '' : 'hidden' }}">
        {{ $servicios->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const input   = document.getElementById('searchInput');
    const spinner = document.getElementById('searchSpinner');
    const baseUrl = '{{ route('servicios.index') }}';
    let controller = null, timer = null;

    async function buscar() {
        if (controller) controller.abort();
        controller = new AbortController();
        spinner.classList.remove('hidden');
        const params = new URLSearchParams(new FormData(document.getElementById('filtroForm')));
        for (const [k, v] of [...params.entries()]) { if (!v) params.delete(k); }
        try {
            const res = await fetch(baseUrl + (params.toString() ? '?' + params : ''), {
                signal: controller.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
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
})();
</script>
@endpush
