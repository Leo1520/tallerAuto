@extends('layouts.auth')
@section('title', 'Nueva contraseña — Taller Automotrices SC-BOL')

@section('content')

<div class="auth-card" style="padding:36px 32px;">

    <div style="margin-bottom:28px;">
        <div style="width:48px;height:48px;background:rgba(249,115,22,.12);border:1px solid rgba(249,115,22,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
            <i class="bi bi-shield-lock" style="color:var(--accent);font-size:22px;"></i>
        </div>
        <h1 style="font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">Nueva contrasena</h1>
        <p style="font-size:13px;color:var(--text-muted);">Elige una contrasena segura para tu cuenta.</p>
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

    <form method="POST" action="{{ route('password.update') }}" style="display:flex; flex-direction:column; gap:20px;">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        {{-- Email (readonly display) --}}
        <div>
            <label class="field-label">Correo electronico</label>
            <div class="input-group" style="opacity:.7; pointer-events:none;">
                <span class="input-icon"><i class="bi bi-envelope-at"></i></span>
                <input type="email" value="{{ $email }}" disabled style="color:var(--text-muted);">
            </div>
        </div>

        {{-- Nueva contraseña --}}
        <div>
            <label for="password" class="field-label">Nueva contrasena</label>
            <div class="input-group {{ $errors->has('password') ? 'is-error' : '' }}">
                <span class="input-icon"><i class="bi bi-lock"></i></span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="new-password"
                    placeholder="Minimo 8 caracteres"
                >
                <button type="button" class="input-toggle" data-target="password" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        {{-- Confirmar --}}
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
                    placeholder="Repite la nueva contrasena"
                >
                <button type="button" class="input-toggle" data-target="password_confirmation" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">
            <i class="bi bi-check2-square" style="margin-right:7px;"></i>
            Restablecer contrasena
        </button>
    </form>

    <p style="text-align:center; font-size:13px; color:var(--text-muted); margin-top:20px;">
        <a href="{{ route('login') }}" class="link-muted">
            <i class="bi bi-arrow-left" style="margin-right:3px;"></i>Volver al inicio de sesion
        </a>
    </p>

</div>

<p style="text-align:center; font-size:11px; color:var(--text-dim); margin-top:24px;">
    &copy; {{ date('Y') }} Taller Automotrices SC-BOL — Todos los derechos reservados
</p>
@endsection
