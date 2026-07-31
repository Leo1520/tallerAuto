@extends('layouts.auth')
@section('title', 'Crear cuenta — Taller Automotrices SC-BOL')

@section('content')

<div style="display:flex; align-items:center; gap:10px; margin-bottom:32px;" id="mobile-brand-reg">
    <div style="width:38px;height:38px;background:var(--accent);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi bi-tools" style="color:#fff;font-size:17px;"></i>
    </div>
    <span style="font-size:19px;font-weight:700;color:var(--text-primary);">Taller Automotrices SC-BOL</span>
</div>
<script @nonce>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('mobile-brand-reg');
        if (el) el.style.display = window.innerWidth >= 1024 ? 'none' : 'flex';
        window.addEventListener('resize', function() {
            if (el) el.style.display = window.innerWidth >= 1024 ? 'none' : 'flex';
        });
    });
</script>

<div class="auth-card" style="padding:36px 32px;">

    <div style="margin-bottom:28px;">
        <a href="{{ route('login') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:var(--text-muted); text-decoration:none; margin-bottom:16px;">
            <i class="bi bi-arrow-left"></i> Volver al inicio de sesion
        </a>
        <h1 style="font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">Crear cuenta</h1>
        <p style="font-size:13px;color:var(--text-muted);">Completa tus datos para solicitar acceso al sistema</p>
    </div>

    @if($errors->any())
    <div class="alert-error" style="margin-bottom:20px; display:flex; align-items:flex-start; gap:8px;">
        <i class="bi bi-exclamation-circle-fill" style="margin-top:1px; flex-shrink:0;"></i>
        <div>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" style="display:flex; flex-direction:column; gap:20px;">
        @csrf

        {{-- Nombre --}}
        <div>
            <label for="nombre" class="field-label">Nombre completo</label>
            <div class="input-group {{ $errors->has('nombre') ? 'is-error' : '' }}">
                <span class="input-icon"><i class="bi bi-person"></i></span>
                <input
                    id="nombre"
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Nombre completo"
                >
            </div>
        </div>

        {{-- Email --}}
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
                    autocomplete="email"
                    placeholder="correo@ejemplo.com"
                >
            </div>
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="field-label">Contrasena</label>
            <div class="input-group {{ $errors->has('password') ? 'is-error' : '' }}">
                <span class="input-icon"><i class="bi bi-lock"></i></span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimo 8 caracteres"
                >
                <button type="button" class="input-toggle" data-target="password" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        {{-- Confirmar contraseña --}}
        <div>
            <label for="password_confirmation" class="field-label">Confirmar contrasena</label>
            <div class="input-group">
                <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repite tu contrasena"
                >
                <button type="button" class="input-toggle" data-target="password_confirmation" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        {{-- Aviso --}}
        <div style="background:rgba(249,115,22,.08); border:1px solid rgba(249,115,22,.2); border-radius:8px; padding:10px 14px; display:flex; align-items:flex-start; gap:8px;">
            <i class="bi bi-info-circle" style="color:var(--accent); margin-top:2px; flex-shrink:0;"></i>
            <p style="font-size:12px; color:var(--text-muted); line-height:1.5; margin:0;">
                Al crear tu cuenta recibirás un <strong style="color:var(--text-secondary);">correo de activación</strong>. Haz clic en el enlace para verificar tu dirección y acceder al sistema.
            </p>
        </div>

        <button type="submit" class="btn-primary">
            <i class="bi bi-person-check" style="margin-right:7px;"></i>
            Crear mi cuenta
        </button>
    </form>

    <p style="text-align:center; font-size:13px; color:var(--text-muted); margin-top:20px;">
        Ya tienes cuenta?
        <a href="{{ route('login') }}" class="link-accent">Inicia sesion</a>
    </p>

</div>

<p style="text-align:center; font-size:11px; color:var(--text-dim); margin-top:24px;">
    &copy; {{ date('Y') }} Taller Automotrices SC-BOL — Todos los derechos reservados
</p>
@endsection
