@extends('layouts.app')
@section('title', 'Vehículos')
@section('page-title', 'Vehículos')

@section('header-actions')
    @can('create', App\Models\Vehiculo::class)
        <a href="{{ route('vehiculos.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
           style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
            <span class="relative inline-flex items-center" style="font-size:15px;">
                <i class="bi bi-car-front-fill"></i>
                <i class="bi bi-plus-lg" style="font-size:9px; font-weight:900; position:absolute; top:-4px; right:-5px;"></i>
            </span> Nuevo vehículo
        </a>
    @endcan
@endsection

@section('content')

<form id="filtroForm" method="GET" action="{{ route('vehiculos.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                   placeholder="Placa, VIN o nombre del cliente..." autocomplete="off"
                   class="w-full pl-9 pr-8 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </span>
        </div>
    </div>
    <div class="w-40">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Marca</label>
        <select id="marcaSelect" name="marca_id"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todas</option>
            @foreach ($marcas as $marca)
                <option value="{{ $marca->id }}" {{ request('marca_id') == $marca->id ? 'selected' : '' }}>{{ $marca->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-36">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
        <select id="activoSelect" name="activo"
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
    @if (request()->hasAny(['search', 'marca_id', 'activo']))
        <a href="{{ route('vehiculos.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p id="listCount" class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $vehiculos->total() }}</span>
            {{ Str::plural('vehículo', $vehiculos->total()) }} encontrados
        </p>
    </div>

    @if ($vehiculos->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-car-front text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron vehículos.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Vehículo</th>
                        <th class="px-6 py-3 text-left">Placa / VIN</th>
                        <th class="px-6 py-3 text-left">Cliente</th>
                        <th class="px-6 py-3 text-left">Km</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach ($vehiculos as $vehiculo)
                    <tr class="hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <p class="text-sm font-semibold text-gray-100">{{ $vehiculo->modelo->marca->nombre }} {{ $vehiculo->modelo->nombre }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ $vehiculo->ano }}{{ $vehiculo->color ? ' · ' . $vehiculo->color : '' }}</p>
                        </td>
                        <td class="px-6 py-3.5">
                            <p class="text-sm font-mono font-semibold text-gray-100">{{ $vehiculo->placa }}</p>
                            @if($vehiculo->vin)
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <p class="text-xs text-gray-500 font-mono">{{ $vehiculo->vin }}</p>
                                <button type="button" title="Copiar VIN"
                                        onclick="copiarVin(this, '{{ $vehiculo->vin }}')"
                                        class="text-gray-600 hover:text-blue-400 transition-colors flex-shrink-0">
                                    <i class="bi bi-clipboard" style="font-size:11px;"></i>
                                </button>
                            </div>
                            @else
                            <p class="text-xs text-gray-600 italic mt-0.5">Sin VIN</p>
                            @endif
                        </td>
                        <td class="px-6 py-3.5">
                            <a href="{{ route('clientes.show', $vehiculo->cliente) }}"
                               class="text-sm font-medium text-blue-400 hover:text-blue-300 hover:underline transition-colors">
                                {{ $vehiculo->cliente->persona->nombre }}
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-300">{{ number_format($vehiculo->kilometraje) }} km</td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $vehiculo->activo
                                    ? 'bg-green-900/40 text-green-400 border border-green-800'
                                    : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                                {{ $vehiculo->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('vehiculos.show', $vehiculo) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                                    <i class="bi bi-eye" style="font-size:11px;"></i> Ver
                                </a>
                                @can('update', $vehiculo)
                                <a href="{{ route('vehiculos.edit', $vehiculo) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                                    <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                                </a>
                                @endcan
                                @can('delete', $vehiculo)
                                <form method="POST" action="{{ route('vehiculos.destroy', $vehiculo) }}"
                                      data-confirm="¿Eliminar el vehículo {{ $vehiculo->placa }}? Esta acción no se puede deshacer.">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="tpOpen(this.form)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-400 bg-red-900/20 hover:bg-red-900/40 rounded-lg transition-colors border border-red-900/50">
                                        <i class="bi bi-trash" style="font-size:11px;"></i> Eliminar
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div id="listPag" class="px-6 py-4 border-t border-gray-700 {{ $vehiculos->hasPages() ? '' : 'hidden' }}">
        {{ $vehiculos->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script @nonce>
(function () {
    const input   = document.getElementById('searchInput');
    const spinner = document.getElementById('searchSpinner');
    const selects = document.querySelectorAll('#marcaSelect, #activoSelect');
    const baseUrl = '{{ route('vehiculos.index') }}';
    let controller = null, timer = null;

    async function buscar() {
        if (controller) controller.abort();
        controller = new AbortController();
        spinner.classList.remove('hidden');
        const params = new URLSearchParams();
        if (input.value.trim()) params.set('search', input.value.trim());
        document.querySelectorAll('#filtroForm select').forEach(s => { if (s.value) params.set(s.name, s.value); });
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
    selects.forEach(s => s.addEventListener('change', buscar));
})();

window.copiarVin = function(btn, vin) {
    navigator.clipboard.writeText(vin).then(() => {
        const icon = btn.querySelector('i');
        icon.className = 'bi bi-clipboard-check';
        btn.classList.add('text-green-400');
        setTimeout(() => {
            icon.className = 'bi bi-clipboard';
            btn.classList.remove('text-green-400');
        }, 1500);
    });
};
</script>
@endpush
