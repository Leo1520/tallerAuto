@extends('layouts.cliente')
@section('title', 'Solicitud enviada — Taller Pro')

@section('content')
<div style="max-width:480px;margin:0 auto;padding:48px 16px 100px;text-align:center;">

    <div style="width:80px;height:80px;border-radius:20px;background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
        <i class="bi bi-bag-check-fill" style="font-size:36px;color:#A78BFA;"></i>
    </div>

    <h1 style="font-size:22px;font-weight:800;color:var(--c-text);margin-bottom:10px;">
        Solicitud enviada
    </h1>
    <p style="font-size:14px;color:var(--c-muted);line-height:1.65;margin-bottom:28px;">
        Recibimos tu solicitud del producto.<br>
        Nos comunicaremos contigo a la brevedad para confirmar disponibilidad y coordinar la entrega.
    </p>

    @if($repuesto)
    <div class="c-card" style="display:flex;align-items:center;gap:14px;padding:16px 18px;margin-bottom:24px;text-align:left;">
        <div style="width:44px;height:44px;border-radius:10px;background:rgba(124,58,237,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-box-seam-fill" style="font-size:20px;color:#A78BFA;"></i>
        </div>
        <div>
            <p style="font-size:14px;font-weight:700;color:var(--c-text);margin-bottom:2px;">{{ $repuesto->nombre }}</p>
            <p style="font-size:13px;color:var(--c-accent);font-weight:700;">Bs {{ number_format($repuesto->precio_venta, 2) }} / unid.</p>
        </div>
    </div>
    @endif

    {{-- Pasos --}}
    <div class="c-card" style="padding:16px 18px;margin-bottom:24px;text-align:left;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div style="width:26px;height:26px;border-radius:50%;background:rgba(34,197,94,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-check" style="color:#4ade80;font-size:13px;"></i>
            </div>
            <p style="font-size:13px;color:var(--c-text);">Solicitud registrada</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div style="width:26px;height:26px;border-radius:50%;background:rgba(234,179,8,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-clock" style="color:#facc15;font-size:12px;"></i>
            </div>
            <p style="font-size:13px;color:var(--c-muted);">Un asesor te contactará por correo o teléfono</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-shop" style="color:var(--c-muted);font-size:12px;"></i>
            </div>
            <p style="font-size:13px;color:var(--c-muted);">Retiro en sucursal o coordinación de entrega</p>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;align-items:center;">
        <a href="{{ route('tienda', ['tab' => 'productos']) }}" class="btn-red" style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;">
            <i class="bi bi-arrow-left"></i> Ver más productos
        </a>
        <a href="{{ route('cliente.inicio') }}" style="font-size:13px;color:var(--c-muted);text-decoration:none;">
            <i class="bi bi-house" style="margin-right:4px;"></i> Ir al inicio
        </a>
    </div>

</div>
@endsection
