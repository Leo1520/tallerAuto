@extends('layouts.app')
@section('title', 'Historial de Auditoría')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('reportes.index') }}"
           class="p-2 rounded-lg text-gray-400 hover:text-gray-200 hover:bg-gray-700 transition-colors">
            <i class="bi bi-arrow-left" style="font-size:16px;"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Historial de Auditoría</h1>
            <p class="text-sm text-gray-400">Trazabilidad completa de operaciones sobre pagos</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $desde }}"
                   class="px-3 py-2 text-sm bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $hasta }}"
                   class="px-3 py-2 text-sm bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">Operación</label>
            <select name="operacion" class="px-3 py-2 text-sm bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                <option value="">Todas</option>
                @foreach($operaciones as $op)
                <option value="{{ $op }}" @selected($operacion === $op)>{{ $op }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-400 mb-1.5">Usuario</label>
            <select name="user_id" class="px-3 py-2 text-sm bg-gray-900 border border-gray-600 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600">
                <option value="">Todos</option>
                @foreach($usuarios as $u)
                <option value="{{ $u->id }}" @selected($userId == $u->id)>
                    {{ $u->persona?->nombre ?? $u->email }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
            <i class="bi bi-search" style="font-size:12px;"></i> Filtrar
        </button>
        @if(request()->hasAny(['fecha_desde','fecha_hasta','operacion','user_id']))
        <a href="{{ route('reportes.auditoria') }}" class="px-3 py-2 text-sm text-gray-400 hover:text-gray-200 flex items-center gap-1">
            <i class="bi bi-x-lg" style="font-size:11px;"></i> Limpiar
        </a>
        @endif
    </form>

    {{-- Timeline de eventos --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
            <p class="text-sm text-gray-400">
                <span class="font-semibold text-gray-200">{{ $logs->total() }}</span>
                eventos encontrados
                <span class="text-gray-600 ml-1">· tabla: pagos</span>
            </p>
            <span class="text-xs text-gray-500">{{ $desde }} — {{ $hasta }}</span>
        </div>

        @if($logs->isEmpty())
        <div class="py-20 text-center">
            <i class="bi bi-journal-x text-gray-600" style="font-size:48px;"></i>
            <p class="mt-3 text-sm text-gray-500">Sin eventos en el período seleccionado.</p>
        </div>
        @else
        <div class="divide-y divide-gray-700/50">
            @foreach($logs as $log)
            @php
                $opColor = match($log->tipo_operacion ?? '') {
                    'confirmar_pago'  => ['#4ade80', 'rgba(74,222,128,.1)', 'bi-check-circle'],
                    'rechazar_pago'   => ['#f87171', 'rgba(248,113,113,.1)', 'bi-x-circle'],
                    'anular_pago'     => ['#fb923c', 'rgba(251,146,60,.1)',  'bi-slash-circle'],
                    'registrar_pago'  => ['#60a5fa', 'rgba(96,165,250,.1)',  'bi-plus-circle'],
                    default           => ['#94a3b8', 'rgba(148,163,184,.1)', 'bi-circle'],
                };
            @endphp
            <div class="px-5 py-4 hover:bg-gray-700/20 transition-colors"
                 x-data="{ open: false }">
                <div class="flex items-start gap-4">

                    {{-- Icono de operación --}}
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                         style="background:{{ $opColor[1] }};border:1px solid {{ $opColor[0] }}22;">
                        <i class="bi {{ $opColor[2] }}" style="color:{{ $opColor[0] }};font-size:14px;"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="text-sm font-semibold text-gray-100">
                                {{ $log->user?->persona?->nombre ?? $log->user?->email ?? 'Sistema' }}
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-mono font-medium"
                                  style="background:{{ $opColor[1] }};color:{{ $opColor[0] }};border:1px solid {{ $opColor[0] }}33;">
                                {{ $log->tipo_operacion ?? 'operación' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                sobre pago
                                <a href="{{ route('pagos.show', $log->registro_id) }}"
                                   class="hover:underline" style="color:#60a5fa;">#{{ $log->registro_id }}</a>
                            </span>
                            <span class="ms-auto text-xs text-gray-500 font-mono whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 mt-1 text-xs text-gray-500 flex-wrap">
                            @if($log->ip)
                            <span class="font-mono flex items-center gap-1">
                                <i class="bi bi-geo-alt" style="font-size:11px;"></i>{{ $log->ip }}
                            </span>
                            @endif
                            @if($log->user_agent)
                            <span class="truncate max-w-xs" title="{{ $log->user_agent }}">
                                <i class="bi bi-browser-chrome me-1" style="font-size:11px;"></i>
                                {{ Str::limit($log->user_agent, 60) }}
                            </span>
                            @endif
                        </div>

                        {{-- Cambios colapsables --}}
                        @if($log->cambios)
                        <button @click="open = !open"
                                class="mt-2 text-xs flex items-center gap-1 hover:text-gray-300 transition-colors"
                                style="color:#60a5fa;">
                            <i class="bi bi-code-slash" style="font-size:11px;"></i>
                            <span x-text="open ? 'Ocultar cambios' : 'Ver cambios'">Ver cambios</span>
                        </button>
                        <div x-show="open" x-cloak class="mt-2">
                            <div class="p-3 rounded-lg text-xs font-mono overflow-x-auto"
                                 style="background:rgba(0,0,0,.35);color:#94a3b8;max-height:180px;">
                                <pre style="margin:0;white-space:pre-wrap;">{{ json_encode($log->cambios, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="px-5 py-4 border-t border-gray-700 {{ $logs->hasPages() ? '' : 'hidden' }}">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
