@extends('layouts.auth')
@section('title', 'Iniciar sesión — Taller Pro')

@section('content')

{{-- Mobile brand (shown only on small screens, panel handles lg+) --}}
<div style="display:flex; align-items:center; gap:10px; margin-bottom:32px;" id="mobile-brand">
    <div style="width:38px;height:38px;background:var(--accent);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi bi-tools" style="color:#fff;font-size:17px;"></i>
    </div>
    <span style="font-size:19px;font-weight:700;color:var(--text-primary);">Taller Pro</span>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var brand = document.getElementById('mobile-brand');
        if (brand) brand.style.display = window.innerWidth >= 1024 ? 'none' : 'flex';
        window.addEventListener('resize', function() {
            if (brand) brand.style.display = window.innerWidth >= 1024 ? 'none' : 'flex';
        });
    });
</script>

<div class="auth-card" style="padding:36px 32px;">

    <div style="margin-bottom:28px;">
        <h1 style="font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">Bienvenido de vuelta</h1>
        <p style="font-size:13px;color:var(--text-muted);">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    @if($errors->any())
    <div class="alert-error" style="margin-bottom:20px; display:flex; align-items:flex-start; gap:8px;">
        <i class="bi bi-exclamation-circle-fill" style="margin-top:1px; flex-shrink:0;"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    @if(session('status'))
    <div class="alert-success" style="margin-bottom:20px; display:flex; align-items:flex-start; gap:8px;">
        <i class="bi bi-check-circle-fill" style="margin-top:1px; flex-shrink:0;"></i>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" style="display:flex; flex-direction:column; gap:20px;">
        @csrf

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
                    autofocus
                    autocomplete="email"
                    placeholder="usuario@tallerpro.bo"
                >
            </div>
        </div>

        {{-- Contraseña --}}
        <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                <label for="password" class="field-label" style="margin-bottom:0;">Contrasena</label>
                <a href="{{ route('password.request') }}" class="link-muted">
                    <i class="bi bi-question-circle" style="margin-right:3px;"></i>Olvidaste la contrasena?
                </a>
            </div>
            <div class="input-group">
                <span class="input-icon"><i class="bi bi-lock"></i></span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                >
                <button type="button" class="input-toggle" data-target="password" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        {{-- Recuerdame --}}
        <div style="display:flex; align-items:center; gap:8px;">
            <input id="remember" name="remember" type="checkbox" class="auth-check">
            <label for="remember" style="font-size:13px; color:var(--text-muted); cursor:pointer; user-select:none;">
                Mantener sesion iniciada
            </label>
        </div>

        <button type="submit" class="btn-primary">
            <i class="bi bi-box-arrow-in-right" style="margin-right:7px;"></i>
            Ingresar al sistema
        </button>
    </form>

    <hr class="divider">

    <div style="text-align:center;">
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">
            No tienes una cuenta?
        </p>
        <a href="{{ route('register') }}" class="btn-secondary">
            <i class="bi bi-person-plus" style="margin-right:7px;"></i>
            Crear cuenta nueva
        </a>
    </div>

</div>

<p style="text-align:center; font-size:11px; color:var(--text-dim); margin-top:24px;">
    &copy; {{ date('Y') }} Taller Pro — Todos los derechos reservados
</p>
@endsection
