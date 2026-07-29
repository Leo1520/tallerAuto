@extends('layouts.cliente')

@section('title', 'Mis Vehículos')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:var(--c-text);">Mis Vehículos</h1>
        <p style="font-size:13px;color:var(--c-muted);">Vehículos registrados en tu cuenta</p>
    </div>
</div>

@if($vehiculos->isNotEmpty())
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
    @foreach($vehiculos as $v)
    <div class="c-card">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--c-border);">
            <div style="width:46px;height:46px;border-radius:12px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-car-front-fill" style="color:#34D399;font-size:20px;"></i>
            </div>
            <div>
                <p style="font-size:15px;font-weight:700;color:var(--c-text);">{{ $v->modelo->marca->nombre ?? '—' }} {{ $v->modelo->nombre ?? '' }}</p>
                <p style="font-size:12px;color:var(--c-muted);">{{ $v->placa }}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div>
                <p style="font-size:11px;color:var(--c-muted);font-weight:600;letter-spacing:.05em;text-transform:uppercase;">Año</p>
                <p style="font-size:14px;font-weight:700;color:var(--c-text);">{{ $v->ano ?? '—' }}</p>
            </div>
            <div>
                <p style="font-size:11px;color:var(--c-muted);font-weight:600;letter-spacing:.05em;text-transform:uppercase;">Color</p>
                <p style="font-size:14px;font-weight:700;color:var(--c-text);">{{ $v->color ?? '—' }}</p>
            </div>
            @if($v->kilometraje)
            <div>
                <p style="font-size:11px;color:var(--c-muted);font-weight:600;letter-spacing:.05em;text-transform:uppercase;">Km</p>
                <p style="font-size:14px;font-weight:700;color:var(--c-text);">{{ number_format($v->kilometraje) }}</p>
            </div>
            @endif
            @if($v->vin)
            <div style="grid-column:1/-1;">
                <p style="font-size:11px;color:var(--c-muted);font-weight:600;letter-spacing:.05em;text-transform:uppercase;">VIN</p>
                <p style="font-size:12px;font-weight:600;color:var(--c-text);font-family:monospace;">{{ $v->vin }}</p>
            </div>
            @endif
        </div>
        <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--c-border);display:flex;justify-content:space-between;align-items:center;">
            <span class="badge {{ $v->activo ? 'badge-green' : 'badge-gray' }}">
                {{ $v->activo ? 'Activo' : 'Inactivo' }}
            </span>
            <a href="{{ route('cliente.citas.create') }}?vehiculo={{ $v->id }}" class="btn-outline" style="font-size:12px;padding:5px 12px;">
                <i class="bi bi-calendar-plus"></i> Agendar
            </a>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="c-card" style="text-align:center;padding:48px 24px;">
    <i class="bi bi-car-front" style="font-size:40px;color:var(--c-muted);opacity:.4;"></i>
    <p style="font-size:15px;font-weight:700;color:var(--c-text);margin-top:14px;">No hay vehículos registrados</p>
    <p style="font-size:13px;color:var(--c-muted);margin:6px 0 20px;">
        Cuando traigas tu vehículo al taller, el recepcionista lo registrará en el sistema.
    </p>
    <a href="{{ route('cliente.citas.create') }}" class="btn-red">
        <i class="bi bi-calendar-plus"></i> Agendar primera cita
    </a>
</div>
@endif

@endsection
