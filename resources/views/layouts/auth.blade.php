<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taller Pro')</title>
    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --a-bg:       #0F172A;
            --a-panel:    #111827;
            --a-card:     #1E293B;
            --a-surface:  #141e2e;
            --a-border:   #334155;
            --a-input:    #0f1726;
            --accent:     #D71920;
            --accent-h:   #b81218;
            --a-text:     #F8FAFC;
            --a-muted:    #64748B;
            --a-dim:      #2d3748;
        }

        body { background: var(--a-bg); font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif; }

        /* ── Left decorative panel ── */
        .auth-panel {
            background: var(--a-panel);
            position: relative;
            overflow: hidden;
        }
        .auth-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(
                -52deg,
                transparent,
                transparent 40px,
                rgba(215,25,32,.03) 40px,
                rgba(215,25,32,.03) 41px
            );
        }
        .auth-panel::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 45%;
            background: linear-gradient(to top, rgba(215,25,32,.1) 0%, transparent 100%);
            pointer-events: none;
        }
        .panel-glow {
            position: absolute;
            bottom: -80px; left: -80px;
            width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(215,25,32,.15) 0%, transparent 70%);
        }
        .panel-accent-bar {
            position: absolute;
            top: 0; left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(to bottom, #D71920 0%, transparent 60%);
        }

        /* ── Auth card ── */
        .auth-card {
            background: var(--a-card);
            border: 1px solid var(--a-border);
            border-radius: 16px;
            box-shadow: 0 24px 64px rgba(0,0,0,.55), 0 0 0 1px rgba(215,25,32,.06);
        }

        /* ── Input group ── */
        .input-group {
            display: flex;
            align-items: center;
            background: var(--a-input);
            border: 1px solid var(--a-border);
            border-radius: 10px;
            overflow: hidden;
            transition: border-color .15s, box-shadow .15s;
        }
        .input-group:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(215,25,32,.14);
        }
        .input-group.is-error { border-color: #ef4444; }
        .input-icon { padding: 0 14px; color: var(--a-muted); font-size: 15px; flex-shrink: 0; }
        .input-group input {
            flex: 1; background: transparent; border: none; outline: none;
            padding: 11px 12px 11px 0; font-size: 14px; color: var(--a-text); min-width: 0;
        }
        .input-group input::placeholder { color: var(--a-muted); }
        .input-toggle {
            padding: 0 14px; color: var(--a-muted); cursor: pointer;
            font-size: 15px; flex-shrink: 0; background: none; border: none; line-height: 1;
        }
        .input-toggle:hover { color: var(--a-text); }

        /* ── Buttons ── */
        .btn-primary {
            width: 100%; background: var(--accent); color: #fff;
            border: none; border-radius: 10px; padding: 12px 20px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            transition: background .15s, box-shadow .15s;
            letter-spacing: .02em;
        }
        .btn-primary:hover {
            background: var(--accent-h);
            box-shadow: 0 6px 20px rgba(215,25,32,.4);
        }
        .btn-secondary {
            width: 100%; background: var(--a-surface); color: var(--a-text);
            border: 1px solid var(--a-border); border-radius: 10px;
            padding: 11px 20px; font-size: 14px; font-weight: 500;
            cursor: pointer; text-decoration: none; display: block;
            text-align: center; transition: background .15s, border-color .15s;
        }
        .btn-secondary:hover { background: var(--a-card); border-color: #475569; color: var(--a-text); }

        /* ── Labels ── */
        .field-label {
            display: block; font-size: 11px; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase;
            color: var(--a-muted); margin-bottom: 6px;
        }

        /* ── Alerts ── */
        .alert-error {
            background: rgba(215,25,32,.1); border: 1px solid rgba(215,25,32,.3);
            color: #f87171; border-radius: 8px; padding: 10px 14px; font-size: 13px;
        }
        .alert-success {
            background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25);
            color: #34d399; border-radius: 8px; padding: 10px 14px; font-size: 13px;
        }

        /* ── Checkbox ── */
        input[type="checkbox"].auth-check { accent-color: var(--accent); width: 15px; height: 15px; cursor: pointer; }

        /* ── Feature items ── */
        .feature-item { display: flex; align-items: flex-start; gap: 12px; padding: 13px 0; }
        .feature-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: rgba(215,25,32,.1); border: 1px solid rgba(215,25,32,.18);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; color: var(--accent); font-size: 16px;
        }

        /* ── Links ── */
        .link-accent { color: var(--accent); text-decoration: none; font-weight: 500; }
        .link-accent:hover { color: #ef4444; text-decoration: underline; }
        .link-muted { color: var(--a-muted); text-decoration: none; font-size: 13px; }
        .link-muted:hover { color: var(--a-text); }

        /* ── Divider ── */
        .divider { border: none; border-top: 1px solid var(--a-border); margin: 20px 0; }
    </style>
</head>
<body>
<div style="display:flex; min-height:100vh;">

    {{-- Left decorative panel --}}
    <div class="auth-panel" id="auth-left-panel"
         style="display:none; flex-direction:column; justify-content:space-between; padding:48px 40px; position:relative; flex-shrink:0;">

        <div class="panel-glow"></div>
        <div class="panel-accent-bar"></div>

        {{-- Brand --}}
        <div style="position:relative; z-index:1;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:52px;">
                <div style="width:46px;height:46px;background:#D71920;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 6px 20px rgba(215,25,32,.4);">
                    <i class="bi bi-tools" style="color:#fff;font-size:20px;"></i>
                </div>
                <div>
                    <p style="font-size:20px;font-weight:800;color:var(--a-text);letter-spacing:-.02em;line-height:1.1;">Taller Pro</p>
                    <p style="font-size:11px;color:var(--a-muted);letter-spacing:.04em;text-transform:uppercase;">Sistema automotriz</p>
                </div>
            </div>

            <h2 style="font-size:26px;font-weight:700;color:var(--a-text);line-height:1.3;text-wrap:balance;margin-bottom:10px;">
                Gestiona tu taller<br>con precision total
            </h2>
            <p style="font-size:14px;color:var(--a-muted);line-height:1.7;max-width:270px;">
                Plataforma profesional para talleres automotrices. Ordenes, inventario, pagos y reportes en un solo lugar.
            </p>
        </div>

        {{-- Features --}}
        <div style="position:relative; z-index:1; border-top: 1px solid var(--a-border); padding-top:20px;">
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:var(--a-text);margin-bottom:2px;">Ordenes en tiempo real</p>
                    <p style="font-size:12px;color:var(--a-muted);line-height:1.5;">Seguimiento completo de cada vehiculo en taller</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-box-seam-fill"></i></div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:var(--a-text);margin-bottom:2px;">Inventario inteligente</p>
                    <p style="font-size:12px;color:var(--a-muted);line-height:1.5;">Alertas de stock minimo por sucursal</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-bar-chart-line-fill"></i></div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:var(--a-text);margin-bottom:2px;">Reportes y metricas</p>
                    <p style="font-size:12px;color:var(--a-muted);line-height:1.5;">Ventas, mecanicos y repuestos con filtros</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="position:relative; z-index:1;">
            <p style="font-size:11px;color:var(--a-dim);">&copy; {{ date('Y') }} Taller Pro — Todos los derechos reservados</p>
        </div>
    </div>

    {{-- Right form area --}}
    <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:32px 20px; background:var(--a-bg);">
        <div style="width:100%; max-width:420px;">
            @yield('content')
        </div>
    </div>

</div>

<script>
    function checkPanel() {
        var el = document.getElementById('auth-left-panel');
        if (!el) return;
        if (window.innerWidth >= 1024) {
            el.style.display = 'flex';
            el.style.width = '44%';
        } else {
            el.style.display = 'none';
        }
    }
    checkPanel();
    window.addEventListener('resize', checkPanel);

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.input-toggle[data-target]');
        if (!btn) return;
        var input = document.getElementById(btn.dataset.target);
        if (!input) return;
        var isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        btn.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
</script>
</body>
</html>
