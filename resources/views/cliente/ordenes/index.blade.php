@extends('layouts.cliente')

@section('title', 'Mis Órdenes')

@section('content')

<div style="margin-bottom:24px;">
    <h1 style="font-size:20px;font-weight:800;color:var(--c-text);">Mis Órdenes de Servicio</h1>
    <p style="font-size:13px;color:var(--c-muted);">Seguimiento en tiempo real del estado de tu vehículo</p>
</div>

@if($ordenes instanceof \Illuminate\Pagination\LengthAwarePaginator ? $ordenes->isNotEmpty() : $ordenes->isNotEmpty())
<div style="display:flex;flex-direction:column;gap:12px;">
    @foreach($ordenes as $orden)
    @php
        $badgeClass = match($orden->estado) {
            'En proceso'           => 'badge-blue',
            'Diagnóstico'          => 'badge-yellow',
            'Esperando repuestos'  => 'badge-red',
            'Listo'                => 'badge-green',
            'Entregado'            => 'badge-gray',
            default                => 'badge-gray',
        };
        $progress = match($orden->estado) {
            'Recibido'             => 10,
            'Diagnóstico'          => 30,
            'En proceso'           => 60,
            'Esperando repuestos'  => 50,
            'Listo'                => 90,
            'Entregado'            => 100,
            default                => 0,
        };
    @endphp
    <a href="{{ route('cliente.ordenes.show', $orden) }}" style="text-decoration:none;">
        <div class="c-card" style="transition:.15s;" onmouseover="this.style.borderColor='rgba(215,25,32,.3)'" onmouseout="this.style.borderColor='var(--c-border)'">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(59,130,246,.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-clipboard2-check-fill" style="color:#60A5FA;font-size:17px;"></i>
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:700;color:var(--c-text);margin-bottom:4px;">{{ $orden->numero }}</p>
                        <p style="font-size:12px;color:var(--c-muted);margin-bottom:2px;">
                            <i class="bi bi-car-front" style="margin-right:4px;"></i>
                            {{ $orden->vehiculo->modelo->marca->nombre ?? '' }} {{ $orden->vehiculo->modelo->nombre ?? '' }} · {{ $orden->vehiculo->placa }}
                        </p>
                        <p style="font-size:12px;color:var(--c-muted);">
                            <i class="bi bi-geo-alt" style="margin-right:4px;"></i>{{ $orden->sucursal->nombre ?? '—' }}
                            @if($orden->mecanico)
                            · <i class="bi bi-person" style="margin-right:2px;"></i>{{ $orden->mecanico->persona->nombre ?? '' }}
                            @endif
                        </p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <span class="badge {{ $badgeClass }}" style="margin-bottom:6px;display:inline-flex;">{{ $orden->estado }}</span>
                    <p style="font-size:12px;color:var(--c-muted);">{{ $orden->fecha_ingreso->format('d M Y') }}</p>
                    <p style="font-size:14px;font-weight:800;color:var(--c-text);margin-top:4px;">Bs {{ number_format($orden->total, 2) }}</p>
                </div>
            </div>
            {{-- Progress bar --}}
            <div style="margin-top:12px;">
                <div style="height:4px;background:rgba(255,255,255,.06);border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:{{ $progress }}%;background:{{ $progress >= 90 ? '#34D399' : ($progress >= 50 ? '#60A5FA' : 'var(--c-accent)') }};border-radius:4px;transition:width .3s;"></div>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>
@if($ordenes instanceof \Illuminate\Pagination\LengthAwarePaginator && $ordenes->hasPages())
<div style="margin-top:20px;">{{ $ordenes->links() }}</div>
@endif
@else
<div class="c-card" style="text-align:center;padding:48px 24px;">
    <i class="bi bi-clipboard2-x" style="font-size:40px;color:var(--c-muted);opacity:.4;"></i>
    <p style="font-size:15px;font-weight:700;color:var(--c-text);margin-top:14px;">No tienes órdenes de servicio</p>
    <p style="font-size:13px;color:var(--c-muted);margin:6px 0 20px;">Cuando traigas tu vehículo se creará una orden de servicio</p>
    <a href="{{ route('cliente.citas.create') }}" class="btn-red">
        <i class="bi bi-calendar-plus"></i> Agendar cita
    </a>
</div>
@endif

@endsection
