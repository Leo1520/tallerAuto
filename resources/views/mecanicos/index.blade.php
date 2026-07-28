@extends('layouts.app')
@section('title', 'Mecánicos')
@section('page-title', 'Mecánicos')

@section('header-actions')
    @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('mecanicos.crear'))
    <a href="{{ route('mecanicos.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
       style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
        <i class="bi bi-plus-lg" style="font-size:15px;"></i> Nuevo mecánico
    </a>
    @endif
@endsection

@section('content')

<form id="filtroForm" method="GET" action="{{ route('mecanicos.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ $search }}"
                   placeholder="Nombre o cédula..." autocomplete="off"
                   class="w-full pl-9 pr-8 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </span>
        </div>
    </div>
    <div class="w-40">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Sucursal</label>
        <select name="sucursal_id"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todas</option>
            @foreach($sucursales as $s)
                <option value="{{ $s->id }}" @selected($sucursalId == $s->id)>{{ $s->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-40">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Especialidad</label>
        <select name="especialidad_id"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todas</option>
            @foreach($especialidades as $e)
                <option value="{{ $e->id }}" @selected($especialidadId == $e->id)>{{ $e->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-32">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
        <select name="activo"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="" @selected($activo === '')>Todos</option>
            <option value="1" @selected($activo === '1')>Activos</option>
            <option value="0" @selected($activo === '0')>Inactivos</option>
        </select>
    </div>
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
    @if($search || $sucursalId || $especialidadId || $activo !== '')
    <a href="{{ route('mecanicos.index') }}"
       class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
        <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
    </a>
    @endif
</form>

@if($mecanicos->isEmpty())
<div class="bg-gray-800 border border-gray-700 rounded-xl py-20 text-center">
    <i class="bi bi-wrench-adjustable text-gray-600" style="font-size:48px;"></i>
    <p class="mt-3 text-sm text-gray-500">No se encontraron mecánicos.</p>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('mecanicos.create') }}"
       class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white"
       style="background:#D71920;">
        <i class="bi bi-plus-lg"></i> Agregar mecánico
    </a>
    @endif
</div>
@else
<div id="mecanicosGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($mecanicos as $mecanico)
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex flex-col hover:border-gray-600 transition-colors">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold text-white"
                     style="background:#D71920;">
                    {{ strtoupper(substr($mecanico->persona->nombre, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-100 text-sm">{{ $mecanico->persona->nombre }}</p>
                    <p class="text-xs text-gray-500">CI: {{ $mecanico->cedula }}</p>
                </div>
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                {{ $mecanico->activo
                    ? 'bg-green-900/40 text-green-400 border border-green-800'
                    : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                {{ $mecanico->activo ? 'Activo' : 'Inactivo' }}
            </span>
        </div>

        <div class="space-y-1.5 flex-1">
            <div class="flex items-center gap-2 text-xs text-gray-400">
                <i class="bi bi-building flex-shrink-0" style="font-size:12px;"></i>
                {{ $mecanico->sucursal->nombre }}
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-400">
                <i class="bi bi-gear flex-shrink-0" style="font-size:12px;"></i>
                {{ $mecanico->especialidad->nombre }}
            </div>
            @if($mecanico->persona->telefono)
            <div class="flex items-center gap-2 text-xs text-gray-400">
                <i class="bi bi-telephone flex-shrink-0" style="font-size:12px;"></i>
                {{ $mecanico->persona->telefono }}
            </div>
            @endif
        </div>

        <div class="mt-4 pt-4 border-t border-gray-700 flex items-center justify-between">
            <span class="text-xs text-gray-500">
                Desde {{ $mecanico->fecha_ingreso?->format('d/m/Y') ?? '—' }}
            </span>
            <div class="flex gap-1.5">
                <a href="{{ route('mecanicos.show', $mecanico) }}"
                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                    <i class="bi bi-eye" style="font-size:11px;"></i> Ver
                </a>
                @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('mecanicos.editar'))
                <a href="{{ route('mecanicos.edit', $mecanico) }}"
                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                    <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($mecanicos->hasPages())
<div class="mt-4">{{ $mecanicos->links() }}</div>
@endif
@endif

@endsection

@push('scripts')
<script>
(function () {
    const input   = document.getElementById('searchInput');
    const spinner = document.getElementById('searchSpinner');
    const baseUrl = '{{ route('mecanicos.index') }}';
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
            const ng = doc.getElementById('mecanicosGrid'), cg = document.getElementById('mecanicosGrid');
            if (ng && cg) cg.innerHTML = ng.innerHTML;
            history.replaceState(null, '', baseUrl + (params.toString() ? '?' + params : ''));
        } catch(e) { if (e.name !== 'AbortError') console.error(e); }
        finally { spinner.classList.add('hidden'); controller = null; }
    }

    input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(buscar, 250); });
    document.querySelectorAll('#filtroForm select').forEach(s => s.addEventListener('change', buscar));
})();
</script>
@endpush
