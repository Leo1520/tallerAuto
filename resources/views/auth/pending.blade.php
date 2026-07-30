@extends('layouts.auth')
@section('title', 'Cuenta Pendiente — Taller Automotrices SC-BOL')

@section('content')

<div class="auth-card" style="padding:40px 32px; text-align:center;">

    <div style="width:64px;height:64px;margin:0 auto 20px;background:rgba(215,25,32,.12);border-radius:50%;display:flex;align-items:center;justify-content:center;">
        <i class="bi bi-hourglass-split" style="font-size:28px;color:#D71920;"></i>
    </div>

    <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">
        Cuenta pendiente de aprobación
    </h1>
    <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin-bottom:28px;">
        Tu cuenta fue creada correctamente.<br>
        Un administrador debe asignarte un rol de acceso antes de que puedas ingresar al sistema.
    </p>

    <div style="background:rgba(249,115,22,.08);border:1px solid rgba(249,115,22,.2);border-radius:10px;padding:14px 18px;text-align:left;margin-bottom:28px;">
        <div style="display:flex;align-items:flex-start;gap:10px;">
            <i class="bi bi-info-circle-fill" style="color:#fb923c;margin-top:2px;flex-shrink:0;"></i>
            <div style="font-size:12px;color:var(--text-muted);line-height:1.6;">
                <strong style="color:var(--text-secondary);">¿Qué pasa ahora?</strong><br>
                El administrador recibirá una notificación con tu solicitud. Una vez que te asignen un rol, podrás iniciar sesión normalmente.
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-primary" style="width:100%;">
            <i class="bi bi-box-arrow-left" style="margin-right:7px;"></i>
            Cerrar sesión
        </button>
    </form>

    <p style="font-size:11px;color:var(--text-dim);margin-top:16px;">
        ¿Tienes dudas? Contacta al administrador del sistema.
    </p>
</div>

<p style="text-align:center;font-size:11px;color:var(--text-dim);margin-top:24px;">
    &copy; {{ date('Y') }} Taller Automotrices SC-BOL — Sistema de gestión automotriz
</p>

@endsection
