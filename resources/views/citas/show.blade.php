@extends('layouts.app')
@section('title', 'Cita #' . $cita->id)
@section('page-title', 'Detalle de Cita')

@section('header-actions')
<a href="{{ route('citas.index') }}"
   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
    <i class="bi bi-arrow-left"></i> Volver
</a>
@endsection

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header card --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs text-gray-500 mb-1">Cita #{{ $cita->id }}</p>
                <h2 class="text-lg font-bold text-gray-100">{{ $cita->cliente?->persona?->nombre ?? 'Cliente sin nombre' }}</h2>
                <p class="text-sm text-gray-400 mt-1">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $cita->fecha?->format('d/m/Y') }} a las {{ substr($cita->hora, 0, 5) }}
                    — {{ $cita->sucursal?->nombre ?? 'Sin sucursal' }}
                </p>
            </div>
            @php
                $badge = match($cita->estado) {
                    'pendiente'  => ['bg' => 'rgba(250,204,21,.15)',  'color' => '#facc15',  'label' => 'Pendiente'],
                    'confirmada' => ['bg' => 'rgba(52,211,153,.15)', 'color' => '#34d399',  'label' => 'Confirmada'],
                    'cancelada'  => ['bg' => 'rgba(248,113,113,.15)','color' => '#f87171',  'label' => 'Cancelada'],
                    'completada' => ['bg' => 'rgba(96,165,250,.15)', 'color' => '#60a5fa',  'label' => 'Completada'],
                    default      => ['bg' => 'rgba(156,163,175,.15)','color' => '#9ca3af',  'label' => $cita->estado],
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-semibold"
                  style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                {{ $badge['label'] }}
            </span>
        </div>
    </div>

    {{-- Info --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehículo</p>
            @if($cita->vehiculo)
                <p class="text-sm text-gray-200 font-medium">
                    {{ $cita->vehiculo->modelo?->marca?->nombre }} {{ $cita->vehiculo->modelo?->nombre }}
                    {{ $cita->vehiculo->ano }}
                </p>
                <p class="text-xs text-gray-500 font-mono">{{ $cita->vehiculo->placa }}</p>
                @if($cita->vehiculo->color)
                    <p class="text-xs text-gray-400">Color: {{ $cita->vehiculo->color }}</p>
                @endif
            @else
                <p class="text-sm text-gray-500">No se especificó vehículo.</p>
            @endif
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Servicio solicitado</p>
            @if($cita->servicio)
                <p class="text-sm text-gray-200 font-medium">{{ $cita->servicio->nombre }}</p>
                @if($cita->servicio->descripcion)
                    <p class="text-xs text-gray-400">{{ $cita->servicio->descripcion }}</p>
                @endif
            @else
                <p class="text-sm text-gray-500">No se especificó servicio.</p>
            @endif
        </div>
    </div>

    @if($cita->notas)
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Notas del cliente</p>
        <p class="text-sm text-gray-300 leading-relaxed">{{ $cita->notas }}</p>
    </div>
    @endif

    {{-- Cambiar estado --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Gestionar cita</p>
        <form method="POST" action="{{ route('citas.update', $cita) }}" class="flex flex-wrap gap-3">
            @csrf @method('PATCH')
            @foreach(['confirmada' => ['color' => '#059669', 'icon' => 'bi-check-circle', 'label' => 'Confirmar'],
                       'cancelada'  => ['color' => '#D71920', 'icon' => 'bi-x-circle',     'label' => 'Cancelar'],
                       'completada' => ['color' => '#2563eb', 'icon' => 'bi-flag-fill',    'label' => 'Completada'],
                       'pendiente'  => ['color' => '#6b7280', 'icon' => 'bi-arrow-counterclockwise', 'label' => 'Pendiente']] as $estado => $btn)
                @if($cita->estado !== $estado)
                <button type="submit" name="estado" value="{{ $estado }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
                        style="background:{{ $btn['color'] }};">
                    <i class="bi {{ $btn['icon'] }}"></i> {{ $btn['label'] }}
                </button>
                @endif
            @endforeach
        </form>
    </div>

    <p class="text-xs text-gray-600 text-center">
        Registrada: {{ $cita->created_at?->format('d/m/Y H:i') ?? '—' }}
    </p>
</div>

@endsection
