<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi cuenta') — Taller Automotrices SC-BOL</title>
    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --c-bg:      #0B1120;
            --c-surface: #111827;
            --c-card:    #1a2236;
            --c-border:  #1e2d45;
            --c-text:    #F1F5F9;
            --c-muted:   #64748B;
            --c-accent:  #D71920;
            --c-accent-h:#b81218;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--c-bg);
            color: var(--c-text);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Top bar ── */
        .c-topbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(11,17,32,.95);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--c-border);
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px; gap: 16px;
        }
        .c-topbar-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .c-topbar-logo-icon {
            width: 34px; height: 34px; background: var(--c-accent); border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 3px 10px rgba(215,25,32,.35);
        }
        .c-topbar-title { font-size: 16px; font-weight: 800; color: var(--c-text); }
        .c-topbar-title span { font-weight: 400; color: var(--c-muted); font-size: 13px; }

        .c-topbar-nav {
            display: flex; align-items: center; gap: 2px;
        }
        @media (max-width: 640px) { .c-topbar-nav { display: none; } }
        .c-nav-link {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 8px;
            font-size: 13px; font-weight: 500; color: #94A3B8;
            text-decoration: none; transition: .15s;
        }
        .c-nav-link:hover, .c-nav-link.active {
            color: var(--c-text); background: rgba(255,255,255,.06);
        }
        .c-nav-link.active { color: var(--c-accent); }

        .c-topbar-right { display: flex; align-items: center; gap: 10px; }
        .c-user-chip {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 10px 5px 6px;
            background: var(--c-card); border: 1px solid var(--c-border);
            border-radius: 100px; cursor: pointer; position: relative;
        }
        .c-user-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--c-accent); display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .c-user-name { font-size: 13px; font-weight: 600; color: var(--c-text); }
        .c-dropdown {
            position: absolute; right: 0; top: calc(100% + 8px);
            background: var(--c-card); border: 1px solid var(--c-border);
            border-radius: 12px; min-width: 180px; padding: 6px;
            box-shadow: 0 16px 48px rgba(0,0,0,.4);
            display: none;
        }
        .c-user-chip:focus-within .c-dropdown { display: block; }
        .c-dropdown-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px; font-size: 13px;
            color: var(--c-text); text-decoration: none; transition: .15s;
        }
        .c-dropdown-item:hover { background: rgba(255,255,255,.06); }
        .c-dropdown-item.danger { color: #f87171; }
        .c-dropdown-divider { height: 1px; background: var(--c-border); margin: 4px 0; }

        /* ── Main ── */
        .c-main { flex: 1; max-width: 1100px; width: 100%; margin: 0 auto; padding: 32px 20px 60px; }

        /* ── Cards ── */
        .c-card {
            background: var(--c-card); border: 1px solid var(--c-border);
            border-radius: 14px; padding: 24px;
        }
        .c-card-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 16px;
            border-bottom: 1px solid var(--c-border);
        }
        .c-card-title { font-size: 15px; font-weight: 700; color: var(--c-text); }

        /* ── Buttons ── */
        .btn-red {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; border-radius: 9px; font-size: 13px; font-weight: 700;
            background: var(--c-accent); color: #fff; text-decoration: none;
            border: none; cursor: pointer; transition: .15s;
        }
        .btn-red:hover { background: var(--c-accent-h); }
        .btn-outline {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 9px; font-size: 13px; font-weight: 600;
            border: 1px solid var(--c-border); color: var(--c-text);
            text-decoration: none; background: transparent; cursor: pointer; transition: .15s;
        }
        .btn-outline:hover { background: rgba(255,255,255,.05); }

        /* ── Badge ── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 100px; font-size: 11px; font-weight: 700;
        }
        .badge-yellow { background: rgba(234,179,8,.12); color: #FCD34D; }
        .badge-green  { background: rgba(16,185,129,.12); color: #34D399; }
        .badge-red    { background: rgba(215,25,32,.12); color: #f87171; }
        .badge-blue   { background: rgba(59,130,246,.12); color: #93C5FD; }
        .badge-gray   { background: rgba(100,116,139,.15); color: #94A3B8; }

        /* ── Mobile nav ── */
        .c-mob-nav {
            display: none; position: fixed; bottom: 0; left: 0; right: 0; z-index: 50;
            background: rgba(17,24,39,.97); border-top: 1px solid var(--c-border);
            padding: 8px 0 max(8px, env(safe-area-inset-bottom));
        }
        @media (max-width: 640px) { .c-mob-nav { display: flex; } .c-main { padding-bottom: 80px; } }
        .c-mob-nav-inner { display: flex; width: 100%; }
        .c-mob-nav-item {
            flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px;
            padding: 4px; text-decoration: none; color: var(--c-muted); font-size: 10px;
            font-weight: 600; transition: color .15s;
        }
        .c-mob-nav-item.active, .c-mob-nav-item:hover { color: var(--c-accent); }
        .c-mob-nav-item i { font-size: 20px; }

        /* ── Form ── */
        .c-label { display: block; font-size: 12px; font-weight: 600; color: var(--c-muted); margin-bottom: 6px; letter-spacing: .04em; text-transform: uppercase; }
        .c-input {
            width: 100%; padding: 10px 14px; border-radius: 9px;
            background: rgba(255,255,255,.04); border: 1px solid var(--c-border);
            color: var(--c-text); font-size: 14px; transition: border-color .15s;
        }
        .c-input:focus { outline: none; border-color: var(--c-accent); }
        .c-select {
            width: 100%; padding: 10px 14px; border-radius: 9px;
            background: var(--c-bg); border: 1px solid var(--c-border);
            color: var(--c-text); font-size: 14px; appearance: none;
        }
        .c-select:focus { outline: none; border-color: var(--c-accent); }
        .c-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 600px) { .c-form-grid { grid-template-columns: 1fr; } }
    </style>
    @stack('styles')
</head>
<body>

@php
    $user    = auth()->user();
    $initials = strtoupper(substr($user->persona?->nombre ?? $user->email, 0, 1));
    $nombre   = $user->persona?->nombre ?? $user->email;
    $currentRoute = request()->route()?->getName() ?? '';
@endphp

{{-- Top bar --}}
<header class="c-topbar">
    <a href="{{ route('landing') }}" class="c-topbar-logo">
        <div class="c-topbar-logo-icon">
            <i class="bi bi-tools" style="color:#fff; font-size:14px;"></i>
        </div>
        <div>
            <div class="c-topbar-title">Taller Automotrices SC-BOL <span>· Mi cuenta</span></div>
        </div>
    </a>

    <nav class="c-topbar-nav">
        <a href="{{ route('cliente.inicio') }}" class="c-nav-link {{ str_starts_with($currentRoute, 'cliente.inicio') ? 'active' : '' }}">
            <i class="bi bi-house"></i> Inicio
        </a>
        <a href="{{ route('cliente.citas.index') }}" class="c-nav-link {{ str_starts_with($currentRoute, 'cliente.citas') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Mis Citas
        </a>
        <a href="{{ route('cliente.ordenes.index') }}" class="c-nav-link {{ str_starts_with($currentRoute, 'cliente.ordenes') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-check"></i> Mis Órdenes
        </a>
    </nav>

    <div class="c-topbar-right">
        <div class="c-user-chip" tabindex="0">
            <div class="c-user-avatar">{{ $initials }}</div>
            <span class="c-user-name">{{ Str::words($nombre, 1, '') }}</span>
            <i class="bi bi-chevron-down" style="font-size:10px; color:var(--c-muted);"></i>
            <div class="c-dropdown">
                <div style="padding:10px 12px 8px;">
                    <p style="font-size:13px;font-weight:700;color:var(--c-text);">{{ $nombre }}</p>
                    <p style="font-size:11px;color:var(--c-muted);">{{ $user->email }}</p>
                </div>
                <div class="c-dropdown-divider"></div>
                <a href="{{ route('landing') }}" class="c-dropdown-item">
                    <i class="bi bi-globe" style="font-size:14px;color:var(--c-muted);"></i> Ver sitio web
                </a>
                <div class="c-dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="c-dropdown-item danger" style="width:100%;border:none;background:none;cursor:pointer;text-align:left;">
                        <i class="bi bi-box-arrow-right" style="font-size:14px;"></i> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- Flash messages --}}
@if(session('status') || session('success') || session('error'))
<div style="background:var(--c-surface);border-bottom:1px solid var(--c-border);padding:10px 20px;">
    <div style="max-width:1100px;margin:0 auto;">
        @if(session('status') || session('success'))
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#34D399;">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('status') ?? session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#f87171;">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ session('error') }}
        </div>
        @endif
    </div>
</div>
@endif

<main class="c-main">
    @yield('content')
</main>

{{-- Mobile bottom nav --}}
<nav class="c-mob-nav">
    <div class="c-mob-nav-inner">
        <a href="{{ route('cliente.inicio') }}" class="c-mob-nav-item {{ str_starts_with($currentRoute, 'cliente.inicio') ? 'active' : '' }}">
            <i class="bi bi-house-fill"></i> Inicio
        </a>
        <a href="{{ route('cliente.citas.index') }}" class="c-mob-nav-item {{ str_starts_with($currentRoute, 'cliente.citas') ? 'active' : '' }}">
            <i class="bi bi-calendar-check-fill"></i> Citas
        </a>
        <a href="{{ route('cliente.ordenes.index') }}" class="c-mob-nav-item {{ str_starts_with($currentRoute, 'cliente.ordenes') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-check-fill"></i> Órdenes
        </a>
    </div>
</nav>

@stack('scripts')
</body>
</html>
