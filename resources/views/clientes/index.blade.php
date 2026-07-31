@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('header-actions')
    @can('create', App\Models\Cliente::class)
        <a href="{{ route('clientes.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
           style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
            <i class="bi bi-person-plus-fill" style="font-size:15px;"></i>
            Nuevo cliente
        </a>
    @endcan
@endsection

@section('content')

{{-- Filtros --}}
<form id="filtroForm" method="GET" action="{{ route('clientes.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nombre, apellido, documento, teléfono o ciudad..."
                   autocomplete="off"
                   class="w-full pl-9 pr-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            {{-- Spinner de búsqueda --}}
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </span>
        </div>
    </div>

    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Ciudad</label>
        <select id="ciudadSelect" name="ciudad"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 cursor-pointer">
            <option value="">Todas</option>
            @foreach ($ciudades as $ciudad)
                <option value="{{ $ciudad }}" {{ request('ciudad') === $ciudad ? 'selected' : '' }}>{{ $ciudad }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>

    @if (request()->hasAny(['search', 'ciudad']))
        <a href="{{ route('clientes.index') }}"
           class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
        </a>
    @endif
</form>

{{-- Tabla --}}
<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">

    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
        <p id="clienteCount" class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $clientes->total() }}</span>
            {{ Str::plural('cliente', $clientes->total()) }} encontrados
        </p>
    </div>

    @if ($clientes->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-people text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">No se encontraron clientes.</p>
            @can('create', App\Models\Cliente::class)
            <a href="{{ route('clientes.create') }}"
               class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white"
               style="background:#D71920;">
                <i class="bi bi-plus-lg"></i> Crear cliente
            </a>
            @endcan
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-900/50">
                    <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Nombre</th>
                        <th class="px-6 py-3 text-left">Documento</th>
                        <th class="px-6 py-3 text-left">Teléfono</th>
                        <th class="px-6 py-3 text-left">Ciudad</th>
                        <th class="px-6 py-3 text-center">Vehículos</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @foreach ($clientes as $cliente)
                    <tr class="hover:bg-gray-700/30 transition-colors">

                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                     style="background:#D71920;">
                                    {{ strtoupper(substr($cliente->persona->nombre, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-100">{{ $cliente->persona->nombre }}</p>
                                    <p class="text-xs text-gray-500">{{ $cliente->persona->email ?? '—' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-3.5">
                            @if ($cliente->numero_documento)
                                @php
                                    $tipo = $cliente->tipo_documento ?? '';
                                    $num  = $cliente->numero_documento;
                                    // Normalizar: quitar prefijo si ya lo trae el número guardado
                                    if ($tipo && preg_match('/^' . preg_quote($tipo, '/') . '-?/i', $num)) {
                                        $num = preg_replace('/^' . preg_quote($tipo, '/') . '-?/i', '', $num);
                                    }
                                    $docDisplay = $tipo ? "{$tipo}-{$num}" : $num;
                                @endphp
                                <p class="text-sm font-mono text-gray-300">{{ $docDisplay }}</p>
                            @else
                                <span class="text-gray-600">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-3.5 text-sm text-gray-300">{{ $cliente->persona->telefono ?? '—' }}</td>
                        <td class="px-6 py-3.5">
                            @if($cliente->ciudad)
                            <button type="button"
                                    onclick="filtrarCiudad('{{ addslashes($cliente->ciudad) }}')"
                                    title="Filtrar por {{ $cliente->ciudad }}"
                                    class="text-sm text-gray-300 hover:text-red-400 underline decoration-dotted underline-offset-2 transition-colors cursor-pointer">
                                {{ $cliente->ciudad }}
                            </button>
                            @else
                            <span class="text-gray-600">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-gray-700 text-gray-300">
                                {{ $cliente->vehiculos_count ?? $cliente->vehiculos->count() }}
                            </span>
                        </td>

                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('clientes.show', $cliente) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors border border-gray-600">
                                    <i class="bi bi-eye" style="font-size:11px;"></i> Ver
                                </a>
                                @can('update', $cliente)
                                <a href="{{ route('clientes.edit', $cliente) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                                    <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                                </a>
                                @endcan
                                @can('delete', $cliente)
                                <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                                      data-confirm="¿Eliminar al cliente {{ addslashes($cliente->persona->nombre) }}? Esta acción no se puede deshacer.">
                                    @csrf @method('DELETE')
                                    <button type="submit"
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

        <div id="paginacion" class="px-6 py-4 border-t border-gray-700 {{ $clientes->hasPages() ? '' : 'hidden' }}">
            {{ $clientes->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function () {
    const input   = document.getElementById('searchInput');
    const select  = document.getElementById('ciudadSelect');
    const spinner = document.getElementById('searchSpinner');
    const baseUrl = '{{ route('clientes.index') }}';

    let controller = null;
    let timer      = null;

    async function buscar() {
        // Cancelar petición anterior si todavía está en vuelo
        if (controller) controller.abort();
        controller = new AbortController();

        spinner.classList.remove('hidden');

        const params = new URLSearchParams();
        const q      = input.value.trim();
        const ciudad = select.value;
        if (q)      params.set('search', q);
        if (ciudad) params.set('ciudad', ciudad);

        try {
            const res  = await fetch(baseUrl + (params.toString() ? '?' + params : ''), {
                signal:  controller.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!res.ok) return;

            const html = await res.text();
            const doc  = new DOMParser().parseFromString(html, 'text/html');

            // Reemplazar tbody
            const newTbody = doc.querySelector('tbody');
            const curTbody = document.querySelector('tbody');
            if (newTbody && curTbody) curTbody.innerHTML = newTbody.innerHTML;

            // Reemplazar contador
            const newCount = doc.getElementById('clienteCount');
            const curCount = document.getElementById('clienteCount');
            if (newCount && curCount) curCount.innerHTML = newCount.innerHTML;

            // Reemplazar paginación
            const newPag = doc.getElementById('paginacion');
            const curPag = document.getElementById('paginacion');
            if (newPag && curPag) {
                curPag.innerHTML = newPag.innerHTML;
                curPag.classList.toggle('hidden', !newPag.innerHTML.trim());
            }

            // Actualizar la URL en el navegador (sin recargar)
            const newUrl = baseUrl + (params.toString() ? '?' + params : '');
            history.replaceState(null, '', newUrl);

        } catch (e) {
            if (e.name !== 'AbortError') console.error(e);
        } finally {
            spinner.classList.add('hidden');
            controller = null;
        }
    }

    // Mientras se escribe — sin debounce forzado, se cancela la anterior con AbortController
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(buscar, 250);   // 250 ms: muy corto, apenas perceptible
    });

    // Al cambiar ciudad → busca inmediatamente
    select.addEventListener('change', buscar);

    // Mantener la función global para clic en ciudad desde la tabla
    window.filtrarCiudad = function (ciudad) {
        select.value = ciudad;
        input.value  = '';
        buscar();
    };
})();
</script>
@endpush
