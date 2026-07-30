@extends('layouts.cliente')

@section('title', 'Inicio')

@section('content')
@php
    $nombre = auth()->user()->persona?->nombre ?? auth()->user()->email;
    $hora   = now()->hour;
    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
@endphp

{{-- Header --}}
<div style="margin-bottom:28px;display:flex;align-items:center;gap:14px;">
    <div style="width:46px;height:46px;border-radius:12px;background:rgba(215,25,32,.1);border:1px solid rgba(215,25,32,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi bi-person-fill" style="color:var(--c-accent);font-size:21px;"></i>
    </div>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text);margin-bottom:2px;">
            {{ $saludo }}, {{ Str::words($nombre, 1, '') }}
        </h1>
        <p style="font-size:13px;color:var(--c-muted);">Resumen de tu cuenta en Taller Pro.</p>
    </div>
</div>

@if(!$cliente)
{{-- Sin perfil de cliente --}}
<div style="background:rgba(215,25,32,.06);border:1px solid rgba(215,25,32,.2);border-radius:14px;padding:24px;margin-bottom:28px;display:flex;align-items:flex-start;gap:16px;">
    <i class="bi bi-info-circle-fill" style="color:var(--c-accent);font-size:20px;flex-shrink:0;margin-top:2px;"></i>
    <div>
        <p style="font-size:14px;font-weight:700;color:var(--c-text);margin-bottom:4px;">Completa tu perfil</p>
        <p style="font-size:13px;color:var(--c-muted);">Tu cuenta fue creada pero aún no tienes un perfil de cliente activo. Contacta con el taller para activarlo.</p>
    </div>
</div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:28px;">
    <div class="c-card" style="display:flex;align-items:center;gap:14px;padding:18px 20px;">
        <div style="width:42px;height:42px;border-radius:11px;background:rgba(215,25,32,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-calendar-check" style="color:var(--c-accent);font-size:19px;"></i>
        </div>
        <div>
            <p style="font-size:24px;font-weight:900;color:var(--c-text);line-height:1;">{{ $totalCitas }}</p>
            <p style="font-size:12px;color:var(--c-muted);">Citas totales</p>
        </div>
    </div>
    <div class="c-card" style="display:flex;align-items:center;gap:14px;padding:18px 20px;">
        <div style="width:42px;height:42px;border-radius:11px;background:rgba(59,130,246,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-clipboard2-check" style="color:#60A5FA;font-size:19px;"></i>
        </div>
        <div>
            <p style="font-size:24px;font-weight:900;color:var(--c-text);line-height:1;">{{ $totalOrdenes }}</p>
            <p style="font-size:12px;color:var(--c-muted);">Órdenes de servicio</p>
        </div>
    </div>
    <div class="c-card" style="display:flex;align-items:center;gap:14px;padding:18px 20px;">
        <div style="width:42px;height:42px;border-radius:11px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-car-front" style="color:#34D399;font-size:19px;"></i>
        </div>
        <div>
            <p style="font-size:24px;font-weight:900;color:var(--c-text);line-height:1;">{{ $vehiculos->count() }}</p>
            <p style="font-size:12px;color:var(--c-muted);">Vehículo(s) registrado(s)</p>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

{{-- Próxima cita --}}
<div>
    <div class="c-card" style="height:100%;">
        <div class="c-card-header">
            <span class="c-card-title"><i class="bi bi-calendar-event" style="color:var(--c-accent);margin-right:6px;"></i>Próxima cita</span>
            <a href="{{ route('cliente.citas.create') }}" class="btn-red" style="padding:6px 12px;font-size:12px;">
                <i class="bi bi-plus"></i> Nueva
            </a>
        </div>
        @if($proximaCita)
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="background:rgba(215,25,32,.06);border:1px solid rgba(215,25,32,.15);border-radius:10px;padding:16px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                    <p style="font-size:16px;font-weight:800;color:var(--c-text);">
                        {{ $proximaCita->fecha->translatedFormat('D d M') }}
                    </p>
                    <span class="badge badge-{{ $proximaCita->estado === 'confirmada' ? 'green' : 'yellow' }}">
                        {{ ucfirst($proximaCita->estado) }}
                    </span>
                </div>
                <p style="font-size:15px;font-weight:700;color:var(--c-accent);margin-bottom:6px;">
                    {{ substr($proximaCita->hora, 0, 5) }} hs
                </p>
                <p style="font-size:13px;color:var(--c-muted);margin-bottom:4px;">
                    <i class="bi bi-geo-alt" style="margin-right:4px;"></i>{{ $proximaCita->sucursal->nombre }}
                </p>
                @if($proximaCita->servicio)
                <p style="font-size:13px;color:var(--c-muted);">
                    <i class="bi bi-wrench" style="margin-right:4px;"></i>{{ $proximaCita->servicio->nombre }}
                </p>
                @endif
                @if($proximaCita->vehiculo)
                <p style="font-size:12px;color:var(--c-muted);margin-top:6px;">
                    <i class="bi bi-car-front" style="margin-right:4px;"></i>
                    {{ $proximaCita->vehiculo->modelo->marca->nombre ?? '' }} {{ $proximaCita->vehiculo->modelo->nombre ?? '' }}
                    · {{ $proximaCita->vehiculo->placa }}
                </p>
                @endif
            </div>
            <a href="{{ route('cliente.citas.index') }}" style="font-size:13px;color:var(--c-accent);text-decoration:none;text-align:center;">
                Ver todas las citas →
            </a>
        </div>
        @else
        <div style="text-align:center;padding:24px 0;">
            <i class="bi bi-calendar-x" style="font-size:32px;color:var(--c-muted);opacity:.5;"></i>
            <p style="font-size:13px;color:var(--c-muted);margin-top:10px;">No tienes citas próximas</p>
            <a href="{{ route('cliente.citas.create') }}" class="btn-red" style="margin-top:14px;display:inline-flex;">
                Agendar cita
            </a>
        </div>
        @endif
    </div>
</div>

{{-- Órdenes activas --}}
<div>
    <div class="c-card" style="height:100%;">
        <div class="c-card-header">
            <span class="c-card-title"><i class="bi bi-wrench-adjustable" style="color:#60A5FA;margin-right:6px;"></i>Órdenes activas</span>
            <a href="{{ route('cliente.ordenes.index') }}" class="btn-outline" style="padding:6px 12px;font-size:12px;">Ver todas</a>
        </div>
        @forelse($ordenesActivas as $orden)
        @php
            $badgeClass = match($orden->estado) {
                'En proceso'           => 'badge-blue',
                'Diagnóstico'          => 'badge-yellow',
                'Esperando repuestos'  => 'badge-red',
                default                => 'badge-gray',
            };
        @endphp
        <a href="{{ route('cliente.ordenes.show', $orden) }}" style="display:block;text-decoration:none;margin-bottom:10px;">
            <div style="background:rgba(255,255,255,.03);border:1px solid var(--c-border);border-radius:10px;padding:14px;transition:.15s;"
                 onmouseover="this.style.borderColor='rgba(215,25,32,.3)'" onmouseout="this.style.borderColor='var(--c-border)'">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                    <p style="font-size:13px;font-weight:700;color:var(--c-text);">{{ $orden->numero }}</p>
                    <span class="badge {{ $badgeClass }}">{{ $orden->estado }}</span>
                </div>
                <p style="font-size:12px;color:var(--c-muted);">
                    {{ $orden->vehiculo->modelo->marca->nombre ?? '' }} {{ $orden->vehiculo->modelo->nombre ?? '' }}
                    · {{ $orden->sucursal->nombre ?? '—' }}
                </p>
            </div>
        </a>
        @empty
        <div style="text-align:center;padding:24px 0;">
            <i class="bi bi-clipboard2" style="font-size:32px;color:var(--c-muted);opacity:.5;"></i>
            <p style="font-size:13px;color:var(--c-muted);margin-top:10px;">No hay órdenes en proceso</p>
        </div>
        @endforelse
    </div>
</div>

</div>

{{-- Vehículos --}}
@if($vehiculos->isNotEmpty())
<div style="margin-top:20px;">
    <div class="c-card">
        <div class="c-card-header">
            <span class="c-card-title"><i class="bi bi-car-front" style="color:#34D399;margin-right:6px;"></i>Mis vehículos</span>
            <a href="{{ route('cliente.citas.create') }}" class="btn-outline" style="padding:6px 12px;font-size:12px;">Nueva cita</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
            @foreach($vehiculos as $v)
            <div style="background:rgba(255,255,255,.03);border:1px solid var(--c-border);border-radius:10px;padding:14px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                    <div style="width:36px;height:36px;border-radius:9px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-car-front-fill" style="color:#34D399;font-size:16px;"></i>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:700;color:var(--c-text);">{{ $v->modelo->marca->nombre ?? '—' }} {{ $v->modelo->nombre ?? '' }}</p>
                        <p style="font-size:11px;color:var(--c-muted);">{{ $v->placa }} · {{ $v->ano }}</p>
                    </div>
                </div>
                @if($v->color)
                <p style="font-size:11px;color:var(--c-muted);">
                    <i class="bi bi-palette" style="margin-right:4px;"></i>{{ $v->color }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
