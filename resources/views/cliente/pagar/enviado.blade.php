@extends('layouts.cliente')
@section('title', 'Pago enviado — Taller Pro')

@section('content')
<div style="max-width:480px;margin:0 auto;padding:48px 16px 100px;text-align:center;">

    {{-- Ícono de éxito --}}
    <div style="width:80px;height:80px;border-radius:20px;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
        <i class="bi bi-hourglass-split" style="font-size:36px;color:#4ade80;"></i>
    </div>

    <h1 style="font-size:22px;font-weight:800;color:var(--c-text);margin-bottom:10px;">
        Pago enviado para revisión
    </h1>
    <p style="font-size:14px;color:var(--c-muted);line-height:1.65;margin-bottom:28px;">
        Tu comprobante fue recibido correctamente.<br>
        El cajero lo revisará y lo confirmará a la brevedad.
    </p>

    {{-- Estado --}}
    <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:20px 24px;margin-bottom:28px;text-align:left;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(34,197,94,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-check" style="color:#4ade80;font-size:14px;"></i>
            </div>
            <p style="font-size:14px;color:var(--c-text);font-weight:600;">Comprobante recibido</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(234,179,8,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-clock" style="color:#facc15;font-size:14px;"></i>
            </div>
            <p style="font-size:14px;color:var(--c-text);font-weight:600;">En revisión de caja</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-envelope-check" style="color:var(--c-muted);font-size:14px;"></i>
            </div>
            <p style="font-size:14px;color:var(--c-muted);">Confirmación por correo</p>
        </div>
    </div>

    @if($orden)
    <div style="margin-bottom:20px;">
        <p style="font-size:13px;color:var(--c-muted);margin-bottom:12px;">
            Orden <strong style="color:var(--c-text);">#{{ $orden->numero }}</strong>
        </p>
        <a href="{{ route('cliente.ordenes.show', $orden) }}" class="btn-outline" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;">
            <i class="bi bi-eye"></i> Ver mi orden
        </a>
    </div>
    @endif

    <a href="{{ route('cliente.inicio') }}" style="font-size:13px;color:var(--c-muted);text-decoration:none;">
        <i class="bi bi-house" style="margin-right:4px;"></i> Ir al inicio
    </a>

</div>
@endsection
