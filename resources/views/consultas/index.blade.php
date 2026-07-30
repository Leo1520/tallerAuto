@extends('layouts.app')
@section('title', 'Solicitudes de Repuesto')
@section('page-title', 'Solicitudes de Repuesto')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(250,204,21,.12);border:1px solid rgba(250,204,21,.2);">
            <i class="bi bi-hourglass-split" style="color:#facc15;font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Pendientes</p>
            <p class="text-xl font-bold text-gray-100">{{ $stats['pendientes'] }}</p>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(52,211,153,.12);border:1px solid rgba(52,211,153,.2);">
            <i class="bi bi-check2-all" style="color:#34d399;font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Atendidas</p>
            <p class="text-xl font-bold text-gray-100">{{ $stats['atendidas'] }}</p>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(96,165,250,.12);border:1px solid rgba(96,165,250,.2);">
            <i class="bi bi-box-seam" style="color:#60a5fa;font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Total solicitudes</p>
            <p class="text-xl font-bold text-gray-100">{{ $stats['total'] }}</p>
        </div>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('consultas.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
        <select name="estado" class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            @foreach(['Pendiente','Atendida','Cancelada'] as $e)
                <option value="{{ $e }}" {{ request('estado') == $e ? 'selected' : '' }}>{{ $e }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex-1 min-w-52">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cliente o repuesto..."
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
    </div>
    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#D71920;">
        <i class="bi bi-search me-1"></i> Filtrar
    </button>
    @if(request()->hasAny(['estado','search']))
        <a href="{{ route('consultas.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-400 bg-gray-700 hover:bg-gray-600">Limpiar</a>
    @endif
</form>

{{-- Tabla --}}
<div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-gray-300">
            <thead>
                <tr class="border-b border-gray-700 text-xs text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Cliente</th>
                    <th class="px-4 py-3 text-left">Repuesto</th>
                    <th class="px-4 py-3 text-center">Cantidad</th>
                    <th class="px-4 py-3 text-left">Contacto</th>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($consultas as $consulta)
                <tr class="hover:bg-gray-700/30 transition-colors">
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $consulta->id }}</td>
                    <td class="px-4 py-3 font-medium text-gray-200">{{ $consulta->nombre }}</td>
                    <td class="px-4 py-3">
                        <span class="text-gray-200">{{ $consulta->repuesto?->nombre ?? '—' }}</span>
                        @if($consulta->repuesto?->codigo)
                            <br><span class="text-xs text-gray-500 font-mono">{{ $consulta->repuesto->codigo }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-gray-200">{{ $consulta->cantidad }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400">
                        @if($consulta->telefono)<div><i class="bi bi-telephone me-1"></i>{{ $consulta->telefono }}</div>@endif
                        @if($consulta->email)<div><i class="bi bi-envelope me-1"></i>{{ $consulta->email }}</div>@endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">
                        {{ $consulta->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $badge = match($consulta->estado) {
                                'Pendiente' => ['bg' => 'rgba(250,204,21,.15)', 'color' => '#facc15'],
                                'Atendida'  => ['bg' => 'rgba(52,211,153,.15)','color' => '#34d399'],
                                'Cancelada' => ['bg' => 'rgba(248,113,113,.15)','color' => '#f87171'],
                                default     => ['bg' => 'rgba(156,163,175,.15)','color' => '#9ca3af'],
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                              style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                            {{ $consulta->estado }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('consultas.show', $consulta) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                        <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4;"></i>
                        No hay solicitudes con estos filtros.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($consultas->hasPages())
    <div class="px-4 py-3 border-t border-gray-700">
        {{ $consultas->links() }}
    </div>
    @endif
</div>

@endsection
