@extends('layouts.cliente')
@section('title', 'Comprobante enviado — Taller Automotrices SC-BOL')

@section('content')
<div style="max-width:480px;margin:60px auto;padding:0 16px;text-align:center;">

    <div class="c-card" style="padding:40px 32px;">
        <div style="width:72px;height:72px;border-radius:20px;background:rgba(52,211,153,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <i class="bi bi-send-check-fill" style="font-size:32px;color:#34d399;"></i>
        </div>

        <h2 style="font-size:20px;font-weight:800;color:var(--c-text);margin-bottom:8px;">
            ¡Comprobante enviado!
        </h2>
        <p style="font-size:14px;color:var(--c-muted);line-height:1.6;margin-bottom:24px;">
            Recibimos tu comprobante para
            <strong style="color:var(--c-text);">{{ $consulta->repuesto?->nombre ?? 'el producto' }}</strong>.
            El equipo lo revisará y te notificaremos por correo cuando el pago sea confirmado.
        </p>

        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:16px;text-align:left;margin-bottom:24px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
                <span style="color:var(--c-muted);">Solicitud #</span>
                <span style="color:var(--c-text);font-weight:600;">{{ $consulta->id }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;">
                <span style="color:var(--c-muted);">Estado</span>
                <span style="color:#facc15;font-weight:600;">En revisión</span>
            </div>
        </div>

        <a href="{{ route('cliente.inicio') }}" class="btn-red" style="justify-content:center;width:100%;padding:13px;">
            Volver al inicio
        </a>
    </div>

</div>
@endsection
