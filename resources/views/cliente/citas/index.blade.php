@extends('layouts.cliente')

@section('title', 'Mis Citas')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text);">Mis Citas</h1>
        <p style="font-size:13px;color:var(--c-muted);">Agenda y gestiona tus citas en el taller</p>
    </div>
    <a href="{{ route('cliente.citas.create') }}" class="btn-red">
        <i class="bi bi-calendar-plus"></i> Nueva cita
    </a>
</div>

@if($citas instanceof \Illuminate\Pagination\LengthAwarePaginator ? $citas->isNotEmpty() : $citas->isNotEmpty())
<div style="display:flex;flex-direction:column;gap:12px;">
    @foreach($citas as $cita)
    @php
        $esPasada = $cita->fecha->isPast() || $cita->estado === 'cancelada' || $cita->estado === 'completada';
        $badgeClass = match($cita->estado) {
            'confirmada' => 'badge-green',
            'cancelada'  => 'badge-red',
            'completada' => 'badge-blue',
            default      => 'badge-yellow',
        };
    @endphp
    <div class="c-card" style="{{ $esPasada ? 'opacity:.65;' : '' }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:flex-start;gap:16px;">
                <div style="width:52px;height:52px;border-radius:12px;background:rgba(215,25,32,.08);border:1px solid rgba(215,25,32,.15);display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="font-size:16px;font-weight:900;color:var(--c-text);line-height:1;">{{ $cita->fecha->format('d') }}</span>
                    <span style="font-size:10px;font-weight:700;color:var(--c-accent);letter-spacing:.06em;">{{ strtoupper($cita->fecha->translatedFormat('M')) }}</span>
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                        <p style="font-size:15px;font-weight:700;color:var(--c-text);">{{ substr($cita->hora, 0, 5) }} hs</p>
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($cita->estado) }}</span>
                    </div>
                    <p style="font-size:13px;color:var(--c-muted);margin-bottom:2px;">
                        <i class="bi bi-geo-alt" style="margin-right:4px;color:var(--c-accent);"></i>{{ $cita->sucursal->nombre }}
                    </p>
                    @if($cita->servicio)
                    <p style="font-size:13px;color:var(--c-muted);margin-bottom:2px;">
                        <i class="bi bi-wrench" style="margin-right:4px;color:var(--c-accent);"></i>{{ $cita->servicio->nombre }}
                    </p>
                    @endif
                    @if($cita->vehiculo)
                    <p style="font-size:12px;color:var(--c-muted);">
                        <i class="bi bi-car-front" style="margin-right:4px;"></i>
                        {{ $cita->vehiculo->modelo->marca->nombre ?? '' }} {{ $cita->vehiculo->modelo->nombre ?? '' }} · {{ $cita->vehiculo->placa }}
                    </p>
                    @endif
                    @if($cita->notas)
                    <p style="font-size:12px;color:var(--c-muted);margin-top:6px;font-style:italic;">
                        "{{ $cita->notas }}"
                    </p>
                    @endif
                </div>
            </div>
            @if(in_array($cita->estado, ['pendiente', 'confirmada']) && !$cita->fecha->isPast())
            <form method="POST" action="{{ route('cliente.citas.cancel', $cita) }}"
                  onsubmit="return confirm('¿Cancelar esta cita?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn-outline" style="font-size:12px;padding:6px 12px;color:#f87171;border-color:rgba(248,113,113,.3);">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@if($citas instanceof \Illuminate\Pagination\LengthAwarePaginator && $citas->hasPages())
<div style="margin-top:20px;">{{ $citas->links() }}</div>
@endif
@else
<div class="c-card" style="text-align:center;padding:48px 24px;">
    <i class="bi bi-calendar-x" style="font-size:40px;color:var(--c-muted);opacity:.4;"></i>
    <p style="font-size:15px;font-weight:700;color:var(--c-text);margin-top:14px;">No tienes citas registradas</p>
    <p style="font-size:13px;color:var(--c-muted);margin:6px 0 20px;">Agenda tu primera cita y nos encargaremos de tu vehículo</p>
    <a href="{{ route('cliente.citas.create') }}" class="btn-red">
        <i class="bi bi-calendar-plus"></i> Agendar ahora
    </a>
</div>
@endif

@endsection
