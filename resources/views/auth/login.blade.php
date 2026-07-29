@extends('layouts.auth')
@section('title', 'Iniciar sesion — Taller Pro')

@section('content')

{{-- Mobile brand --}}
<div id="mob-brand" style="display:flex; align-items:center; gap:10px; margin-bottom:28px;">
    <div style="width:40px;height:40px;background:#D71920;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(215,25,32,.4);">
        <i class="bi bi-tools" style="color:#fff;font-size:18px;"></i>
    </div>
    <div>
        <p style="font-size:18px;font-weight:800;color:var(--a-text);letter-spacing:-.01em;line-height:1.1;">Taller Pro</p>
        <p style="font-size:11px;color:var(--a-muted);text-transform:uppercase;letter-spacing:.04em;">Sistema automotriz</p>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('mob-brand');
        if (el) el.style.display = window.innerWidth >= 1024 ? 'none' : 'flex';
        window.addEventListener('resize', function() {
            if (el) el.style.display = window.innerWidth >= 1024 ? 'none' : 'flex';
        });
    });
</script>

<div class="auth-card" style="padding:36px 32px;">

    <div style="margin-bottom:28px;">
        <h1 style="font-size:22px;font-weight:700;color:var(--a-text);margin-bottom:6px;">Bienvenido de vuelta</h1>
        <p style="font-size:13px;color:var(--a-muted);">Ingresa tus credenciales para acceder al sistema</p>
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

    @if(session('error'))
    <div class="alert-error" style="margin-bottom:20px; display:flex; align-items:flex-start; gap:8px;">
        <i class="bi bi-exclamation-circle-fill" style="margin-top:1px; flex-shrink:0;"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" style="display:flex; flex-direction:column; gap:18px;">
        @csrf

        <div>
            <label for="email" class="field-label">Correo electronico</label>
            <div class="input-group {{ $errors->has('email') ? 'is-error' : '' }}">
                <span class="input-icon"><i class="bi bi-envelope-at"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email', request('email')) }}"
                       required autofocus autocomplete="email"
                       placeholder="correo@ejemplo.com">
            </div>
        </div>

        <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                <label for="password" class="field-label" style="margin-bottom:0;">Contrasena</label>
                <a href="{{ route('password.request') }}" class="link-muted">
                    <i class="bi bi-question-circle" style="margin-right:3px;font-size:12px;"></i>Olvidaste la contrasena?
                </a>
            </div>
            <div class="input-group">
                <span class="input-icon"><i class="bi bi-lock"></i></span>
                <input id="password" type="password" name="password"
                       required autocomplete="current-password"
                       placeholder="••••••••">
                <button type="button" class="input-toggle" data-target="password" aria-label="Mostrar">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
            <input id="remember" name="remember" type="checkbox" class="auth-check">
            <label for="remember" style="font-size:13px; color:var(--a-muted); cursor:pointer; user-select:none;">
                Mantener sesion iniciada
            </label>
        </div>

        <button type="submit" class="btn-primary" style="margin-top:2px;">
            <i class="bi bi-box-arrow-in-right" style="margin-right:7px;"></i>
            Ingresar al sistema
        </button>
    </form>

    <hr class="divider">

    <div style="text-align:center;">
        <p style="font-size:13px; color:var(--a-muted); margin-bottom:12px;">No tienes una cuenta?</p>
        <a href="{{ route('register') }}" class="btn-secondary">
            <i class="bi bi-person-plus" style="margin-right:7px;"></i>
            Crear cuenta nueva
        </a>
    </div>

</div>

<p style="text-align:center; font-size:11px; color:var(--a-dim); margin-top:22px;">
    &copy; {{ date('Y') }} Taller Pro — Todos los derechos reservados
</p>
@endsection
