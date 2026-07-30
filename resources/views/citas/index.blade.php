@extends('layouts.app')
@section('title', 'Citas')
@section('page-title', 'Citas')

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
            <i class="bi bi-calendar2-check" style="color:#34d399;font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Confirmadas (próximas)</p>
            <p class="text-xl font-bold text-gray-100">{{ $stats['confirmadas'] }}</p>
        </div>
    </div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:rgba(96,165,250,.12);border:1px solid rgba(96,165,250,.2);">
            <i class="bi bi-calendar-day" style="color:#60a5fa;font-size:18px;"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Citas hoy</p>
            <p class="text-xl font-bold text-gray-100">{{ $stats['hoy'] }}</p>
        </div>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('citas.index') }}"
      class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="w-48">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Estado</label>
        <select name="estado" class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Todos</option>
            @foreach(['pendiente','confirmada','cancelada','completada'] as $e)
                <option value="{{ $e }}" {{ request('estado') == $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-40">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Desde</label>
        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
    </div>
    <div class="w-40">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Hasta</label>
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
    </div>
    <div class="flex-1 min-w-44">
        <label class="block text-xs font-medium text-gray-400 mb-1.5">Cliente</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre..."
               class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
    </div>
    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#D71920;">
        <i class="bi bi-search me-1"></i> Filtrar
    </button>
    @if(request()->hasAny(['estado','fecha_desde','fecha_hasta','search']))
        <a href="{{ route('citas.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-400 bg-gray-700 hover:bg-gray-600">Limpiar</a>
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
                    <th class="px-4 py-3 text-left">Vehículo</th>
                    <th class="px-4 py-3 text-left">Servicio</th>
                    <th class="px-4 py-3 text-left">Fecha / Hora</th>
                    <th class="px-4 py-3 text-left">Sucursal</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($citas as $cita)
                <tr class="hover:bg-gray-700/30 transition-colors">
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $cita->id }}</td>
                    <td class="px-4 py-3 font-medium text-gray-200">
                        {{ $cita->cliente?->persona?->nombre ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        @if($cita->vehiculo)
                            {{ $cita->vehiculo->modelo?->marca?->nombre }} {{ $cita->vehiculo->modelo?->nombre }}<br>
                            <span class="font-mono">{{ $cita->vehiculo->placa }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $cita->servicio?->nombre ?? 'No especificado' }}</td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-gray-200">{{ $cita->fecha?->format('d/m/Y') }}</span><br>
                        <span class="text-xs text-gray-500">{{ substr($cita->hora, 0, 5) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $cita->sucursal?->nombre ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $badge = match($cita->estado) {
                                'pendiente'  => ['bg' => 'rgba(250,204,21,.15)',  'color' => '#facc15',  'label' => 'Pendiente'],
                                'confirmada' => ['bg' => 'rgba(52,211,153,.15)', 'color' => '#34d399',  'label' => 'Confirmada'],
                                'cancelada'  => ['bg' => 'rgba(248,113,113,.15)','color' => '#f87171',  'label' => 'Cancelada'],
                                'completada' => ['bg' => 'rgba(96,165,250,.15)', 'color' => '#60a5fa',  'label' => 'Completada'],
                                default      => ['bg' => 'rgba(156,163,175,.15)','color' => '#9ca3af',  'label' => $cita->estado],
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                              style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('citas.show', $cita) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                        <i class="bi bi-calendar-x" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4;"></i>
                        No hay citas con estos filtros.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($citas->hasPages())
    <div class="px-4 py-3 border-t border-gray-700">
        {{ $citas->links() }}
    </div>
    @endif
</div>

@endsection
