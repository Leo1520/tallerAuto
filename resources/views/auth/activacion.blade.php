@extends('layouts.auth')
@section('title', 'Activa tu cuenta — Taller Automotrices SC-BOL')

@section('content')

<div class="auth-card" style="padding:40px 32px; text-align:center;">

    <div style="width:64px;height:64px;border-radius:16px;background:rgba(215,25,32,.1);border:1px solid rgba(215,25,32,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
        <i class="bi bi-envelope-check" style="font-size:28px;color:var(--accent);"></i>
    </div>

    <h1 style="font-size:21px;font-weight:800;color:var(--a-text);margin-bottom:8px;">
        Revisa tu correo
    </h1>
    <p style="font-size:13px;color:var(--a-muted);line-height:1.6;margin-bottom:6px;">
        Te enviamos un enlace de activación a:
    </p>
    @if($email)
    <p style="font-size:14px;font-weight:700;color:var(--a-text);margin-bottom:20px;">
        {{ $email }}
    </p>
    @endif

    <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:16px 18px;text-align:left;margin-bottom:24px;">
        <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
            <i class="bi bi-1-circle-fill" style="color:var(--accent);font-size:16px;flex-shrink:0;margin-top:1px;"></i>
            <p style="font-size:13px;color:var(--a-muted);">Abre tu bandeja de entrada y busca el correo de <strong style="color:var(--a-text);">Taller Automotrices SC-BOL</strong>.</p>
        </div>
        <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
            <i class="bi bi-2-circle-fill" style="color:var(--accent);font-size:16px;flex-shrink:0;margin-top:1px;"></i>
            <p style="font-size:13px;color:var(--a-muted);">Haz clic en el botón <strong style="color:var(--a-text);">"Activar mi cuenta"</strong>.</p>
        </div>
        <div style="display:flex;align-items:flex-start;gap:10px;">
            <i class="bi bi-3-circle-fill" style="color:var(--accent);font-size:16px;flex-shrink:0;margin-top:1px;"></i>
            <p style="font-size:13px;color:var(--a-muted);">Serás redirigido al inicio de sesión con tu correo ya cargado.</p>
        </div>
    </div>

    <a href="{{ route('login') }}" class="btn-primary" style="display:flex;align-items:center;justify-content:center;gap:8px;">
        <i class="bi bi-box-arrow-in-right"></i> Ir al inicio de sesión
    </a>

    <p style="font-size:11px;color:var(--a-dim);margin-top:18px;line-height:1.6;">
        Si no ves el correo, revisa tu carpeta de spam.<br>El enlace expira en 60 minutos.
    </p>

</div>

<p style="text-align:center; font-size:11px; color:var(--a-dim); margin-top:22px;">
    &copy; {{ date('Y') }} Taller Automotrices SC-BOL — Todos los derechos reservados
</p>
@endsection
