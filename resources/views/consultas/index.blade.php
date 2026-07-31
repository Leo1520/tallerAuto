@extends('layouts.app')
@section('title', 'Solicitudes de Repuesto')
@section('page-title', 'Solicitudes de Repuesto')

@section('content')

{{-- Stats: 4 KPIs en fila --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px;">
    <div class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 flex-shrink-0 rounded-lg flex items-center justify-center" style="background:rgba(250,204,21,.12);border:1px solid rgba(250,204,21,.2);">
            <i class="bi bi-hourglass-split" style="color:#facc15;font-size:16px;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-gray-300 truncate">Pendientes</p>
            <p class="text-lg font-bold text-gray-100 leading-tight">{{ $stats['pendientes'] }}</p>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 flex-shrink-0 rounded-lg flex items-center justify-center" style="background:rgba(251,146,60,.12);border:1px solid rgba(251,146,60,.2);">
            <i class="bi bi-credit-card" style="color:#fb923c;font-size:16px;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-gray-300 truncate">En revisión</p>
            <p class="text-lg font-bold text-gray-100 leading-tight">{{ $stats['en_revision'] }}</p>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 flex-shrink-0 rounded-lg flex items-center justify-center" style="background:rgba(52,211,153,.12);border:1px solid rgba(52,211,153,.2);">
            <i class="bi bi-check2-all" style="color:#34d399;font-size:16px;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-gray-300 truncate">Atendidas</p>
            <p class="text-lg font-bold text-gray-100 leading-tight">{{ $stats['atendidas'] }}</p>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 flex-shrink-0 rounded-lg flex items-center justify-center" style="background:rgba(96,165,250,.12);border:1px solid rgba(96,165,250,.2);">
            <i class="bi bi-box-seam" style="color:#60a5fa;font-size:16px;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-gray-300 truncate">Total</p>
            <p class="text-lg font-bold text-gray-100 leading-tight">{{ $stats['total'] }}</p>
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
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
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
    <div class="px-6 py-3 border-b border-gray-700">
        <p class="text-sm text-gray-400">
            <span class="font-semibold text-gray-200">{{ $consultas->total() }}</span>
            {{ $consultas->total() == 1 ? 'solicitud' : 'solicitudes' }} encontradas
        </p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-gray-300" style="table-layout:fixed;font-size:13px;">
            <colgroup>
                <col style="width:36px;">       {{-- # --}}
                <col style="width:140px;">      {{-- Cliente --}}
                <col style="width:150px;">      {{-- Repuesto (4 líneas máx) --}}
                <col style="width:44px;">       {{-- Cant --}}
                <col style="width:170px;">      {{-- Contacto --}}
                <col style="width:82px;">       {{-- Fecha --}}
                <col style="width:110px;">      {{-- Estado --}}
                <col style="width:110px;">      {{-- Acciones --}}
            </colgroup>
            <thead>
                <tr class="border-b border-gray-700 text-xs text-gray-500 uppercase tracking-wider">
                    <th class="px-3 py-2.5 text-left">#</th>
                    <th class="px-3 py-2.5 text-left">Cliente</th>
                    <th class="px-3 py-2.5 text-left">Repuesto</th>
                    <th class="px-3 py-2.5 text-center">Cant.</th>
                    <th class="px-3 py-2.5 text-left">Contacto</th>
                    <th class="px-3 py-2.5 text-left">Fecha</th>
                    <th class="px-3 py-2.5 text-left">Estado</th>
                    <th class="px-3 py-2.5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($consultas as $consulta)
                <tr class="hover:bg-gray-700/30 transition-colors">
                    <td class="px-3 py-2.5 text-gray-500 font-mono" style="font-size:12px;">{{ $consulta->id }}</td>

                    {{-- Cliente --}}
                    <td class="px-3 py-2.5">
                        <p class="font-semibold text-gray-200 truncate" title="{{ $consulta->nombre }}">{{ $consulta->nombre }}</p>
                        @if($consulta->cliente)
                            <p class="text-gray-600 mt-0.5" style="font-size:11px;">Registrado</p>
                        @endif
                    </td>

                    {{-- Repuesto — máx 2 líneas --}}
                    <td class="px-3 py-2.5">
                        @if($consulta->repuesto)
                            <p class="text-gray-200" title="{{ $consulta->repuesto->nombre }}"
                               style="display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;line-height:1.4;font-size:12px;">
                                {{ $consulta->repuesto->nombre }}
                            </p>
                            @if($consulta->repuesto->codigo)
                                <p class="text-gray-500 font-mono mt-0.5" style="font-size:11px;">{{ $consulta->repuesto->codigo }}</p>
                            @endif
                        @else
                            <span class="text-gray-600 italic">Sin repuesto</span>
                        @endif
                    </td>

                    {{-- Cantidad --}}
                    <td class="px-3 py-2.5 text-center font-bold text-gray-200">{{ $consulta->cantidad }}</td>

                    {{-- Contacto --}}
                    <td class="px-3 py-2.5">
                        <div class="space-y-1">
                            @if($consulta->telefono)
                                <div class="flex items-center gap-1.5" style="color:#86efac;">
                                    <i class="bi bi-telephone-fill" style="font-size:11px; flex-shrink:0;"></i>
                                    <span class="text-gray-300">{{ $consulta->telefono }}</span>
                                </div>
                            @endif
                            @if($consulta->email)
                                <div class="flex items-center gap-1.5" style="color:#93c5fd;">
                                    <i class="bi bi-envelope-fill" style="font-size:11px; flex-shrink:0;"></i>
                                    <span class="text-gray-400 truncate" title="{{ $consulta->email }}" style="max-width:140px; display:block; font-size:12px;">{{ $consulta->email }}</span>
                                </div>
                            @endif
                            @if(!$consulta->telefono && !$consulta->email)
                                <span class="text-gray-600 italic">—</span>
                            @endif
                        </div>
                    </td>

                    {{-- Fecha --}}
                    <td class="px-3 py-2.5 text-gray-400" style="white-space:nowrap;">
                        {{ $consulta->created_at?->format('d/m/Y') ?? '—' }}<br>
                        <span class="text-gray-600" style="font-size:11px;">{{ $consulta->created_at?->format('H:i') }}</span>
                    </td>

                    {{-- Estado + badge pago --}}
                    <td class="px-3 py-2.5">
                        @php
                            $badge = match($consulta->estado) {
                                'Pendiente' => ['bg' => 'rgba(250,204,21,.15)', 'color' => '#facc15'],
                                'Atendida'  => ['bg' => 'rgba(52,211,153,.15)','color' => '#34d399'],
                                'Cancelada' => ['bg' => 'rgba(248,113,113,.15)','color' => '#f87171'],
                                default     => ['bg' => 'rgba(156,163,175,.15)','color' => '#9ca3af'],
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full font-semibold" style="font-size:11px;background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                            {{ $consulta->estado }}
                        </span>
                        @if($consulta->pago_estado === 'En revisión')
                            <span class="mt-1 block px-2 py-0.5 rounded-full font-semibold" style="font-size:10px;background:rgba(251,146,60,.15);color:#fb923c;">
                                <i class="bi bi-clock"></i> Revisión
                            </span>
                        @elseif($consulta->pago_estado === 'Confirmado')
                            <span class="mt-1 block px-2 py-0.5 rounded-full font-semibold" style="font-size:10px;background:rgba(52,211,153,.10);color:#34d399;">
                                <i class="bi bi-check2"></i> Pagado
                            </span>
                        @endif
                    </td>

                    {{-- Acciones --}}
                    <td class="px-3 py-2.5">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Acción rápida: marcar atendida (solo si está pendiente) --}}
                            @if($consulta->estado === 'Pendiente')
                                <form method="POST" action="{{ route('consultas.update', $consulta) }}"
                                      onsubmit="return confirm('¿Marcar como Atendida y notificar al cliente?')">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado" value="Atendida">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-2 py-1 font-medium rounded-lg transition-colors"
                                            style="font-size:11px;color:#86efac;background:rgba(22,101,52,.25);border:1px solid rgba(22,101,52,.4);"
                                            title="Marcar como atendida">
                                        <i class="bi bi-check-lg"></i> Atender
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('consultas.show', $consulta) }}"
                               class="inline-flex items-center gap-1 px-2 py-1 font-medium rounded-lg transition-colors"
                               style="font-size:11px;color:#d1d5db;background:#374151;border:1px solid #4b5563;">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </div>
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
