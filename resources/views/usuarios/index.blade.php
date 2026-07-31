@extends('layouts.app')
@section('title', 'Usuarios')
@section('page-title', 'Usuarios')

@section('header-actions')
    <a href="{{ route('usuarios.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
       class="btn-taller-red"
        <i class="bi bi-person-plus-fill" style="font-size:15px;"></i> Nuevo usuario
    </a>
@endsection

@section('content')

<form id="filtroForm" method="GET" action="{{ route('usuarios.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none;"></i>
            <input id="searchInput" type="text" name="search" value="{{ $search }}"
                   placeholder="Nombre o correo..." autocomplete="off"
                   class="w-full pl-9 pr-8 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500">
            <span id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </span>
        </div>
    </div>
    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Rol</label>
        <select name="rol_id"
                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos los roles</option>
            @foreach($roles as $rol)
                <option value="{{ $rol->id }}" @selected($rolId == $rol->id)>{{ $rol->nombre }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
        <i class="bi bi-search" style="font-size:13px;"></i> Filtrar
    </button>
    @if($search || $rolId)
    <a href="{{ route('usuarios.index') }}"
       class="px-4 py-2 text-sm text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
        <i class="bi bi-x-lg" style="font-size:12px;"></i> Limpiar
    </a>
    @endif
</form>

<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-700">
        <p id="listCount" class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $usuarios->total() }}</span>
            usuarios encontrados
            @if($mecanicosSinCuenta->isNotEmpty() && !request('rol_id'))
                · <span class="text-yellow-500">{{ $mecanicosSinCuenta->count() }} mecánico(s) sin cuenta</span>
            @endif
        </p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900/50">
                <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">Usuario</th>
                    <th class="px-6 py-3 text-left">Correo</th>
                    <th class="px-6 py-3 text-left">Rol</th>
                    <th class="px-6 py-3 text-center">Último acceso</th>
                    <th class="px-6 py-3 text-center">Registrado</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($usuarios as $usuario)
                <tr class="hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                 style="background:#D71920;">
                                {{ strtoupper(substr($usuario->persona->nombre, 0, 2)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-100">{{ $usuario->persona->nombre }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-sm text-gray-400">{{ $usuario->email }}</td>
                    <td class="px-6 py-3.5">
                        @foreach($usuario->roles as $rol)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ in_array($rol->nombre, ['Admin','Administrador'])
                                    ? 'bg-red-900/40 text-red-400 border border-red-800'
                                    : 'bg-blue-900/30 text-blue-400 border border-blue-800/50' }}">
                                {{ $rol->nombre }}
                            </span>
                        @endforeach
                        @if($usuario->roles->isEmpty())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-900/30 text-yellow-400 border border-yellow-700/50">
                                <i class="bi bi-clock-history" style="font-size:10px;"></i> Pendiente
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3.5 text-center text-xs text-gray-500">
                        {{ $usuario->ultimo_acceso?->diffForHumans() ?? 'Nunca' }}
                    </td>
                    <td class="px-6 py-3.5 text-center text-xs text-gray-500">
                        {{ $usuario->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('usuarios.edit', $usuario) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-300 bg-blue-900/30 hover:bg-blue-900/50 rounded-lg transition-colors border border-blue-800/50">
                                <i class="bi bi-pencil" style="font-size:11px;"></i> Editar
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                @if($mecanicosSinCuenta->isEmpty())
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-500">No se encontraron usuarios.</td>
                </tr>
                @endif
                @endforelse

                {{-- Mecánicos sin cuenta de usuario --}}
                @foreach($mecanicosSinCuenta as $mec)
                <tr class="hover:bg-gray-700/30 transition-colors opacity-80">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                 style="background:#374151;">
                                {{ strtoupper(substr($mec->persona->nombre, 0, 2)) }}
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-300">{{ $mec->persona->nombre }}</span>
                                <span class="ml-2 text-xs text-gray-600">sin cuenta</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-sm text-gray-500">{{ $mec->persona->email ?? '—' }}</td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-700 text-gray-400 border border-gray-600">
                            Mecánico
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-center text-xs text-gray-600">—</td>
                    <td class="px-6 py-3.5 text-center text-xs text-gray-600">—</td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('usuarios.create', ['persona_id' => $mec->persona_id, 'nombre' => $mec->persona->nombre, 'email' => $mec->persona->email]) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-green-300 bg-green-900/30 hover:bg-green-900/50 rounded-lg transition-colors border border-green-800/50">
                                <i class="bi bi-person-plus" style="font-size:11px;"></i> Crear cuenta
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="listPag" class="px-6 py-4 border-t border-gray-700 {{ $usuarios->hasPages() ? '' : 'hidden' }}">
        {{ $usuarios->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script @nonce>
(function () {
    const input   = document.getElementById('searchInput');
    const spinner = document.getElementById('searchSpinner');
    const baseUrl = '{{ route('usuarios.index') }}';
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

@push('styles')
<style>.btn-taller-red{background:#D71920}.btn-taller-red:hover{background:#b81218}</style>
@endpush
