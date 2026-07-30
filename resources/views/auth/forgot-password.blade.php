@extends('layouts.auth')
@section('title', 'Recuperar contraseña — Taller Automotrices SC-BOL')

@section('content')

<div class="auth-card" style="padding:36px 32px;">

    <div style="margin-bottom:28px;">
        <a href="{{ route('login') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:var(--text-muted); text-decoration:none; margin-bottom:16px;">
            <i class="bi bi-arrow-left"></i> Volver al inicio de sesion
        </a>
        <div style="width:48px;height:48px;background:rgba(249,115,22,.12);border:1px solid rgba(249,115,22,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
            <i class="bi bi-envelope-open" style="color:var(--accent);font-size:22px;"></i>
        </div>
        <h1 style="font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">Recuperar acceso</h1>
        <p style="font-size:13px;color:var(--text-muted);line-height:1.6;">
            Ingresa tu correo registrado. Te enviaremos un enlace seguro para restablecer tu contrasena.
        </p>
    </div>

    @if($errors->any())
    <div class="alert-error" style="margin-bottom:20px; display:flex; align-items:flex-start; gap:8px;">
        <i class="bi bi-exclamation-circle-fill" style="margin-top:1px; flex-shrink:0;"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    @if(session('status'))
    <div class="alert-success" style="margin-bottom:20px; display:flex; align-items:flex-start; gap:8px;">
        <i class="bi bi-check2-circle" style="margin-top:1px; flex-shrink:0;"></i>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" style="display:flex; flex-direction:column; gap:20px;">
        @csrf

        <div>
            <label for="email" class="field-label">Correo electronico</label>
            <div class="input-group {{ $errors->has('email') ? 'is-error' : '' }}">
                <span class="input-icon"><i class="bi bi-envelope-at"></i></span>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="usuario@tallerpro.bo"
                >
            </div>
        </div>

        <button type="submit" class="btn-primary">
            <i class="bi bi-send" style="margin-right:7px;"></i>
            Enviar enlace de recuperacion
        </button>
    </form>

    <p style="text-align:center; font-size:13px; color:var(--text-muted); margin-top:20px;">
        Recuerdas tu contrasena?
        <a href="{{ route('login') }}" class="link-accent">Inicia sesion</a>
    </p>

</div>

<p style="text-align:center; font-size:11px; color:var(--text-dim); margin-top:24px;">
    &copy; {{ date('Y') }} Taller Automotrices SC-BOL — Todos los derechos reservados
</p>
@endsection
