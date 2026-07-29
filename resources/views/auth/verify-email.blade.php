@extends('layouts.auth')
@section('title', 'Verifica tu correo — Taller Pro')

@section('content')

<div class="auth-card" style="padding:40px 32px; text-align:center;">

    <div style="width:64px;height:64px;margin:0 auto 20px;background:rgba(215,25,32,.12);border-radius:50%;display:flex;align-items:center;justify-content:center;">
        <i class="bi bi-envelope-check-fill" style="font-size:28px;color:#D71920;"></i>
    </div>

    <h1 style="font-size:20px;font-weight:700;color:var(--a-text);margin-bottom:8px;">
        Verifica tu correo electrónico
    </h1>
    <p style="font-size:13px;color:var(--a-muted);line-height:1.6;margin-bottom:6px;">
        Te enviamos un enlace de activación a:
    </p>
    <p style="font-size:14px;font-weight:600;color:var(--a-text);margin-bottom:24px;">
        {{ $email }}
    </p>

    <div style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:14px 18px;text-align:left;margin-bottom:24px;">
        <div style="display:flex;align-items:flex-start;gap:10px;">
            <i class="bi bi-info-circle-fill" style="color:#4ade80;margin-top:2px;flex-shrink:0;"></i>
            <div style="font-size:12px;color:var(--a-muted);line-height:1.6;">
                <strong style="color:var(--a-secondary);">¿No ves el correo?</strong><br>
                Revisa tu carpeta de <strong>Spam</strong> o <strong>Correo no deseado</strong>. El enlace expira en 60 minutos.
            </div>
        </div>
    </div>

    @if(session('resent'))
    <div class="alert-success" style="margin-bottom:20px; display:flex; align-items:center; gap:8px; text-align:left;">
        <i class="bi bi-check-circle-fill" style="flex-shrink:0;"></i>
        <span>¡Correo reenviado! Revisa tu bandeja de entrada.</span>
    </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}" style="margin-bottom:16px;">
        @csrf
        <button type="submit" class="btn-primary" style="width:100%;">
            <i class="bi bi-send" style="margin-right:7px;"></i>
            Reenviar correo de verificación
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="width:100%;background:none;border:1px solid var(--a-border);border-radius:10px;padding:11px;font-size:13px;color:var(--a-muted);cursor:pointer;transition:.15s;">
            <i class="bi bi-box-arrow-left" style="margin-right:6px;"></i>
            Cerrar sesión
        </button>
    </form>

</div>

<p style="text-align:center;font-size:11px;color:var(--a-dim);margin-top:24px;">
    &copy; {{ date('Y') }} Taller Pro — Sistema de gestión automotriz
</p>

@endsection
