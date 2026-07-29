@extends('layouts.cliente')

@section('title', 'Orden ' . $orden->numero)

@section('content')

<div style="margin-bottom:24px;">
    <a href="{{ route('cliente.ordenes.index') }}" style="font-size:13px;color:var(--c-muted);text-decoration:none;">
        <i class="bi bi-arrow-left" style="margin-right:4px;"></i> Mis órdenes
    </a>
    <div style="display:flex;align-items:center;gap:14px;margin-top:10px;flex-wrap:wrap;">
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text);">{{ $orden->numero }}</h1>
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
        <span class="badge {{ $badgeClass }} " style="font-size:12px;padding:5px 12px;">{{ $orden->estado }}</span>
    </div>
</div>

{{-- Progress steps --}}
<div class="c-card" style="margin-bottom:20px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        @foreach(['Recibido','Diagnóstico','En proceso','Listo','Entregado'] as $step)
        @php $done = $progress >= match($step) { 'Recibido' => 10, 'Diagnóstico' => 30, 'En proceso' => 60, 'Listo' => 90, 'Entregado' => 100, default => 0 }; @endphp
        <div style="display:flex;align-items:center;gap:4px;font-size:11px;font-weight:600;{{ $done ? 'color:var(--c-accent)' : 'color:var(--c-muted)' }};">
            <i class="bi bi-{{ $done ? 'check-circle-fill' : 'circle' }}" style="font-size:15px;"></i>
            {{ $step }}
        </div>
        @if(!$loop->last)
        <div style="flex:1;height:1px;background:var(--c-border);min-width:12px;"></div>
        @endif
        @endforeach
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

{{-- Vehículo e info --}}
<div>
    <div class="c-card" style="margin-bottom:16px;">
        <p style="font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:12px;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-car-front-fill" style="color:#34D399;"></i> Vehículo
        </p>
        <p style="font-size:15px;font-weight:700;color:var(--c-text);margin-bottom:4px;">
            {{ $orden->vehiculo->modelo->marca->nombre ?? '—' }} {{ $orden->vehiculo->modelo->nombre ?? '' }}
        </p>
        <p style="font-size:13px;color:var(--c-muted);">Placa: <strong style="color:var(--c-text);">{{ $orden->vehiculo->placa }}</strong></p>
        @if($orden->vehiculo->ano)
        <p style="font-size:13px;color:var(--c-muted);">Año: <strong style="color:var(--c-text);">{{ $orden->vehiculo->ano }}</strong></p>
        @endif
        @if($orden->vehiculo->color)
        <p style="font-size:13px;color:var(--c-muted);">Color: <strong style="color:var(--c-text);">{{ $orden->vehiculo->color }}</strong></p>
        @endif
    </div>

    <div class="c-card">
        <p style="font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:12px;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-info-circle" style="color:#60A5FA;"></i> Detalles
        </p>
        <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--c-muted);">Ingreso</span>
                <span style="color:var(--c-text);font-weight:600;">{{ $orden->fecha_ingreso->format('d M Y H:i') }}</span>
            </div>
            @if($orden->fecha_entrega_estimada)
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--c-muted);">Entrega estimada</span>
                <span style="color:var(--c-text);font-weight:600;">{{ $orden->fecha_entrega_estimada->format('d M Y') }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--c-muted);">Sucursal</span>
                <span style="color:var(--c-text);font-weight:600;">{{ $orden->sucursal->nombre ?? '—' }}</span>
            </div>
            @if($orden->mecanico)
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--c-muted);">Mecánico</span>
                <span style="color:var(--c-text);font-weight:600;">{{ $orden->mecanico->persona->nombre ?? '—' }}</span>
            </div>
            @endif
        </div>
        @if($orden->observaciones)
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--c-border);">
            <p style="font-size:12px;color:var(--c-muted);font-style:italic;">"{{ $orden->observaciones }}"</p>
        </div>
        @endif
    </div>
</div>

{{-- Servicios y costos --}}
<div>
    @if($orden->detallesServicios->isNotEmpty())
    <div class="c-card" style="margin-bottom:16px;">
        <p style="font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:12px;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-wrench" style="color:var(--c-accent);"></i> Servicios
        </p>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($orden->detallesServicios as $ds)
            <div style="display:flex;justify-content:space-between;align-items:flex-start;font-size:13px;">
                <div>
                    <p style="color:var(--c-text);font-weight:600;">{{ $ds->servicio->nombre }}</p>
                    @if($ds->cantidad > 1)<p style="color:var(--c-muted);">x{{ $ds->cantidad }}</p>@endif
                </div>
                <span style="color:var(--c-text);font-weight:700;">Bs {{ number_format($ds->subtotal ?? ($ds->precio_unitario * $ds->cantidad), 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($orden->detallesRepuestos->isNotEmpty())
    <div class="c-card" style="margin-bottom:16px;">
        <p style="font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:12px;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-box-seam" style="color:#A78BFA;"></i> Repuestos
        </p>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($orden->detallesRepuestos as $dr)
            <div style="display:flex;justify-content:space-between;align-items:flex-start;font-size:13px;">
                <div>
                    <p style="color:var(--c-text);font-weight:600;">{{ $dr->repuesto->nombre }}</p>
                    <p style="color:var(--c-muted);">x{{ $dr->cantidad }}</p>
                </div>
                <span style="color:var(--c-text);font-weight:700;">Bs {{ number_format($dr->precio_unitario * $dr->cantidad, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Total --}}
    <div class="c-card" style="background:rgba(215,25,32,.04);border-color:rgba(215,25,32,.15);">
        <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--c-muted);">Subtotal</span>
                <span style="color:var(--c-text);">Bs {{ number_format($orden->subtotal, 2) }}</span>
            </div>
            @if($orden->descuento > 0)
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--c-muted);">Descuento</span>
                <span style="color:#34D399;">— Bs {{ number_format($orden->descuento, 2) }}</span>
            </div>
            @endif
            @if($orden->impuestos > 0)
            <div style="display:flex;justify-content:space-between;">
                <span style="color:var(--c-muted);">Impuestos</span>
                <span style="color:var(--c-text);">Bs {{ number_format($orden->impuestos, 2) }}</span>
            </div>
            @endif
            <div style="height:1px;background:rgba(215,25,32,.2);margin:4px 0;"></div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:16px;font-weight:800;color:var(--c-text);">Total</span>
                <span style="font-size:18px;font-weight:900;color:var(--c-accent);">Bs {{ number_format($orden->total, 2) }}</span>
            </div>
        </div>
    </div>
</div>

</div>

@endsection
