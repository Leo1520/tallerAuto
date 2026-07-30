<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taller Automotrices SC-BOL — Taller Automotriz')</title>
    <meta name="description" content="@yield('description', 'Taller automotriz profesional. Mecánica general, mantenimiento, diagnóstico y más.')">
    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --pub-bg:      #0B1120;
            --pub-surface: #111827;
            --pub-card:    #1a2236;
            --pub-border:  #1e2d45;
            --accent:      #D71920;
            --accent-h:    #b81218;
            --pub-text:    #F1F5F9;
            --pub-muted:   #64748B;
            --pub-dim:     #334155;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--pub-bg);
            color: var(--pub-text);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            line-height: 1.6;
        }

        /* ── Navbar ── */
        .pub-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(11,17,32,.92);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--pub-border);
            transition: background .3s;
        }
        .pub-nav-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 24px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between; gap: 24px;
        }
        .pub-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; }
        .pub-logo-icon {
            width: 38px; height: 38px; background: var(--accent); border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(215,25,32,.35);
        }
        .pub-logo-text { font-size: 18px; font-weight: 800; color: var(--pub-text); letter-spacing: -.01em; }
        .pub-nav-links {
            display: flex; align-items: center; gap: 6px; list-style: none;
        }
        .pub-nav-links a {
            padding: 6px 12px; border-radius: 7px; font-size: 13.5px; font-weight: 500;
            color: #94A3B8; text-decoration: none; transition: color .15s, background .15s;
        }
        .pub-nav-links a:hover { color: var(--pub-text); background: rgba(255,255,255,.06); }
        .pub-nav-actions { display: flex; align-items: center; gap: 10px; }
        .btn-nav-login {
            padding: 7px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
            color: var(--pub-text); border: 1px solid var(--pub-border);
            background: transparent; text-decoration: none; transition: .15s;
        }
        .btn-nav-login:hover { background: rgba(255,255,255,.06); border-color: #475569; }
        .btn-nav-tienda {
            padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 700;
            color: #fff; background: var(--accent); text-decoration: none;
            transition: background .15s, box-shadow .15s;
            display: flex; align-items: center; gap: 6px;
        }
        .btn-nav-tienda:hover {
            background: var(--accent-h);
            box-shadow: 0 4px 16px rgba(215,25,32,.4);
        }
        .mob-menu-btn {
            display: none; background: none; border: none; color: #94A3B8;
            font-size: 22px; cursor: pointer; padding: 4px;
        }
        @media (max-width: 768px) {
            .pub-nav-links { display: none; }
            .btn-nav-login { display: none; }
            .mob-menu-btn { display: block; }
        }

        /* ── Sections ── */
        .pub-section { padding: 90px 24px; }
        .pub-container { max-width: 1200px; margin: 0 auto; }
        .section-label {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            color: var(--accent); margin-bottom: 14px;
        }
        .section-label::before, .section-label::after {
            content: ''; display: block; height: 1px; width: 24px; background: var(--accent);
        }
        .section-title {
            font-size: clamp(26px, 4vw, 40px); font-weight: 800; line-height: 1.2;
            color: var(--pub-text); margin-bottom: 14px; letter-spacing: -.02em;
        }
        .section-sub {
            font-size: 15px; color: var(--pub-muted); max-width: 560px; line-height: 1.7;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="pub-nav" id="pubNav">
    <div class="pub-nav-inner">
        <a href="{{ url('/') }}" class="pub-logo">
            <img src="{{ asset('images/logo.png') }}" alt="SC-BOL"
                 style="height:42px;width:auto;object-fit:contain;">
        </a>

        <ul class="pub-nav-links">
            <li><a href="#servicios">Servicios</a></li>
            <li><a href="#nosotros">Nosotros</a></li>
            <li><a href="#mapa">Sucursales</a></li>
            <li><a href="#contacto">Contacto</a></li>
        </ul>

        <div class="pub-nav-actions">
            @auth
                <a href="{{ route('cliente.inicio') }}" class="btn-nav-login">
                    <i class="bi bi-person-circle" style="font-size:14px;"></i> Mi cuenta
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-nav-login">Iniciar sesión</a>
            @endauth
            <a href="{{ route('tienda') }}" class="btn-nav-tienda">
                <i class="bi bi-shop-window" style="font-size:14px;"></i>
                Tienda
            </a>
        </div>

        <button class="mob-menu-btn" onclick="toggleMobMenu()">
            <i class="bi bi-list" id="mobMenuIcon"></i>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div id="mobMenu" style="display:none; background:rgba(11,17,32,.98); border-top:1px solid var(--pub-border); padding:12px 24px 20px;">
        <ul style="list-style:none; display:flex; flex-direction:column; gap:2px; margin-bottom:16px;">
            <li><a href="#servicios" onclick="closeMobMenu()" style="display:block; padding:10px 8px; color:#94A3B8; text-decoration:none; font-size:14px; border-radius:8px;">Servicios</a></li>
            <li><a href="#nosotros" onclick="closeMobMenu()" style="display:block; padding:10px 8px; color:#94A3B8; text-decoration:none; font-size:14px; border-radius:8px;">Nosotros</a></li>
            <li><a href="#mapa" onclick="closeMobMenu()" style="display:block; padding:10px 8px; color:#94A3B8; text-decoration:none; font-size:14px; border-radius:8px;">Sucursales</a></li>
            <li><a href="#contacto" onclick="closeMobMenu()" style="display:block; padding:10px 8px; color:#94A3B8; text-decoration:none; font-size:14px; border-radius:8px;">Contacto</a></li>
        </ul>
        <div style="display:flex; gap:10px;">
            @auth
                <a href="{{ route('cliente.inicio') }}" style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid var(--pub-border); color:var(--pub-text); text-decoration:none; font-size:13px; font-weight:600;">Mi cuenta</a>
            @else
                <a href="{{ route('login') }}" style="flex:1; text-align:center; padding:10px; border-radius:8px; border:1px solid var(--pub-border); color:var(--pub-text); text-decoration:none; font-size:13px; font-weight:600;">Iniciar sesión</a>
            @endauth
            <a href="{{ route('tienda') }}" style="flex:1; text-align:center; padding:10px; border-radius:8px; background:var(--accent); color:#fff; text-decoration:none; font-size:13px; font-weight:700;">Tienda</a>
        </div>
    </div>
</nav>

<div style="padding-top:64px;">
    @yield('content')
</div>

<script>
function toggleMobMenu() {
    var m = document.getElementById('mobMenu');
    var open = m.style.display === 'block';
    m.style.display = open ? 'none' : 'block';
    document.getElementById('mobMenuIcon').className = open ? 'bi bi-list' : 'bi bi-x-lg';
}
function closeMobMenu() {
    document.getElementById('mobMenu').style.display = 'none';
    document.getElementById('mobMenuIcon').className = 'bi bi-list';
}
window.addEventListener('scroll', function() {
    document.getElementById('pubNav').style.background =
        window.scrollY > 20 ? 'rgba(11,17,32,.98)' : 'rgba(11,17,32,.92)';
});
</script>
@stack('scripts')
</body>
</html>
