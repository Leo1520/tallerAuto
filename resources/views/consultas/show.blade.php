@extends('layouts.app')
@section('title', 'Solicitud #' . $consultaRepuesto->id)
@section('page-title', 'Detalle de Solicitud')

@section('header-actions')
<a href="{{ route('consultas.index') }}"
   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
    <i class="bi bi-arrow-left"></i> Volver
</a>
@endsection

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <p class="text-xs text-gray-500 mb-1">Solicitud #{{ $consultaRepuesto->id }}</p>
            <h2 class="text-lg font-bold text-gray-100">{{ $consultaRepuesto->nombre }}</h2>
            <p class="text-sm text-gray-400 mt-1">
                <i class="bi bi-clock me-1"></i>
                {{ $consultaRepuesto->created_at?->format('d/m/Y H:i') ?? '—' }}
            </p>
        </div>
        @php
            $badge = match($consultaRepuesto->estado) {
                'Pendiente' => ['bg' => 'rgba(250,204,21,.15)', 'color' => '#facc15'],
                'Atendida'  => ['bg' => 'rgba(52,211,153,.15)','color' => '#34d399'],
                'Cancelada' => ['bg' => 'rgba(248,113,113,.15)','color' => '#f87171'],
                default     => ['bg' => 'rgba(156,163,175,.15)','color' => '#9ca3af'],
            };
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-semibold"
              style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
            {{ $consultaRepuesto->estado }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {{-- Contacto --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Datos de contacto</p>
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2 text-gray-300">
                    <i class="bi bi-person text-gray-500"></i>
                    {{ $consultaRepuesto->nombre }}
                </div>
                @if($consultaRepuesto->telefono)
                <div class="flex items-center gap-2 text-gray-300">
                    <i class="bi bi-telephone text-gray-500"></i>
                    <a href="tel:{{ $consultaRepuesto->telefono }}" class="hover:text-blue-400">
                        {{ $consultaRepuesto->telefono }}
                    </a>
                </div>
                @endif
                @if($consultaRepuesto->email)
                <div class="flex items-center gap-2 text-gray-300">
                    <i class="bi bi-envelope text-gray-500"></i>
                    <a href="mailto:{{ $consultaRepuesto->email }}" class="hover:text-blue-400">
                        {{ $consultaRepuesto->email }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Repuesto --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Repuesto solicitado</p>
            @if($consultaRepuesto->repuesto)
                <p class="text-sm font-medium text-gray-200">{{ $consultaRepuesto->repuesto->nombre }}</p>
                @if($consultaRepuesto->repuesto->codigo)
                    <p class="text-xs font-mono text-gray-400">Código: {{ $consultaRepuesto->repuesto->codigo }}</p>
                @endif
                <p class="text-sm text-gray-300">
                    Cantidad: <span class="font-bold text-white">{{ $consultaRepuesto->cantidad }}</span> unidad(es)
                </p>
                <p class="text-sm text-gray-400">
                    Precio unitario: <span class="font-semibold text-gray-200">Bs {{ number_format($consultaRepuesto->repuesto->precio_venta, 2) }}</span>
                </p>
            @else
                <p class="text-sm text-gray-500">Repuesto no encontrado.</p>
            @endif
        </div>
    </div>

    {{-- Stock actual --}}
    @if($consultaRepuesto->repuesto?->inventarios?->count())
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Stock disponible por sucursal</p>
        <div class="space-y-2">
            @foreach($consultaRepuesto->repuesto->inventarios as $inv)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">{{ $inv->sucursal?->nombre ?? 'Sucursal' }}</span>
                <span class="font-bold {{ $inv->stock <= $inv->stock_minimo ? 'text-red-400' : 'text-green-400' }}">
                    {{ $inv->stock }} uds
                    @if($inv->stock <= $inv->stock_minimo)
                        <span class="text-xs text-red-400">(stock bajo)</span>
                    @endif
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Notas --}}
    @if($consultaRepuesto->notas)
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Notas del cliente</p>
        <p class="text-sm text-gray-300 leading-relaxed">{{ $consultaRepuesto->notas }}</p>
    </div>
    @endif

    {{-- Cambiar estado --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Gestionar solicitud</p>
        <form method="POST" action="{{ route('consultas.update', $consultaRepuesto) }}" class="flex flex-wrap gap-3">
            @csrf @method('PATCH')
            @foreach(['Atendida' => ['color' => '#059669', 'icon' => 'bi-check-circle', 'label' => 'Marcar atendida'],
                       'Cancelada' => ['color' => '#D71920', 'icon' => 'bi-x-circle',    'label' => 'Cancelar'],
                       'Pendiente' => ['color' => '#6b7280', 'icon' => 'bi-arrow-counterclockwise', 'label' => 'Volver a pendiente']] as $estado => $btn)
                @if($consultaRepuesto->estado !== $estado)
                <button type="submit" name="estado" value="{{ $estado }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
                        style="background:{{ $btn['color'] }};">
                    <i class="bi {{ $btn['icon'] }}"></i> {{ $btn['label'] }}
                </button>
                @endif
            @endforeach
        </form>
    </div>

</div>

@endsection
