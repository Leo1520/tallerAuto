<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taller Pro')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --auth-bg:       #0b0f1a;
            --auth-panel:    #0f1520;
            --auth-surface:  #151d2e;
            --auth-card:     #1a2236;
            --auth-border:   #253047;
            --auth-input:    #101726;
            --accent:        #f97316;
            --accent-dark:   #c2540a;
            --text-primary:  #dde4f0;
            --text-muted:    #64748b;
            --text-dim:      #3d4d63;
        }

        body { background: var(--auth-bg); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif; }

        /* Left decorative panel */
        .auth-panel {
            background: var(--auth-panel);
            position: relative;
            overflow: hidden;
        }
        .auth-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                repeating-linear-gradient(
                    -52deg,
                    transparent,
                    transparent 38px,
                    rgba(249,115,22,.035) 38px,
                    rgba(249,115,22,.035) 39px
                );
        }
        .auth-panel::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 40%;
            background: linear-gradient(to top, rgba(249,115,22,.12) 0%, transparent 100%);
            pointer-events: none;
        }
        .panel-glow {
            position: absolute;
            bottom: -60px; left: -60px;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(249,115,22,.18) 0%, transparent 70%);
        }

        /* Auth card */
        .auth-card {
            background: var(--auth-card);
            border: 1px solid var(--auth-border);
            border-radius: 16px;
            box-shadow: 0 24px 64px rgba(0,0,0,.5);
        }

        /* Input group */
        .input-group {
            display: flex;
            align-items: center;
            background: var(--auth-input);
            border: 1px solid var(--auth-border);
            border-radius: 10px;
            overflow: hidden;
            transition: border-color .15s;
        }
        .input-group:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(249,115,22,.12);
        }
        .input-group.is-error { border-color: #ef4444; }
        .input-icon {
            padding: 0 14px;
            color: var(--text-muted);
            font-size: 15px;
            flex-shrink: 0;
        }
        .input-group input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            padding: 11px 12px 11px 0;
            font-size: 14px;
            color: var(--text-primary);
            min-width: 0;
        }
        .input-group input::placeholder { color: var(--text-muted); }
        .input-toggle {
            padding: 0 14px;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 15px;
            flex-shrink: 0;
            background: none;
            border: none;
            line-height: 1;
        }
        .input-toggle:hover { color: var(--text-primary); }

        /* Buttons */
        .btn-primary {
            width: 100%;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, transform .1s;
            letter-spacing: .02em;
        }
        .btn-primary:hover  { background: #ea6c0a; }
        .btn-primary:active { transform: scale(.98); }
        .btn-secondary {
            width: 100%;
            background: var(--auth-surface);
            color: var(--text-primary);
            border: 1px solid var(--auth-border);
            border-radius: 10px;
            padding: 11px 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, border-color .15s;
            letter-spacing: .01em;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .btn-secondary:hover {
            background: var(--auth-card);
            border-color: #364560;
            color: var(--text-primary);
        }

        /* Labels */
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        /* Alerts */
        .alert-error {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.3);
            color: #f87171;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
        }
        .alert-success {
            background: rgba(16,185,129,.1);
            border: 1px solid rgba(16,185,129,.25);
            color: #34d399;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
        }

        /* Remember checkbox */
        input[type="checkbox"].auth-check {
            accent-color: var(--accent);
            width: 15px; height: 15px;
            cursor: pointer;
        }

        /* Feature items (left panel) */
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 0;
        }
        .feature-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: rgba(249,115,22,.12);
            border: 1px solid rgba(249,115,22,.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            color: var(--accent);
            font-size: 16px;
        }
        .divider {
            border: none;
            border-top: 1px solid var(--auth-border);
            margin: 20px 0;
        }
        .link-accent {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }
        .link-accent:hover { text-decoration: underline; color: #fb923c; }
        .link-muted {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
        }
        .link-muted:hover { color: var(--text-primary); }
    </style>
</head>
<body>
<div style="display:flex; min-height:100vh;">

    {{-- Left decorative panel --}}
    <div class="auth-panel" style="display:none; flex-direction:column; justify-content:space-between; padding:48px 40px; position:relative;" id="auth-left-panel">
        <div class="panel-glow"></div>

        {{-- Brand --}}
        <div style="position:relative; z-index:1;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:48px;">
                <div style="width:44px;height:44px;background:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-tools" style="color:#fff;font-size:20px;"></i>
                </div>
                <span style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.01em;">Taller Pro</span>
            </div>

            <h2 style="font-size:28px;font-weight:700;color:var(--text-primary);line-height:1.25;text-wrap:balance;margin-bottom:12px;">
                Gestiona tu taller<br>con precisión
            </h2>
            <p style="font-size:15px;color:var(--text-muted);line-height:1.6;max-width:280px;">
                Plataforma profesional para talleres automotrices. Órdenes, inventario, pagos y más en un solo lugar.
            </p>
        </div>

        {{-- Feature list --}}
        <div style="position:relative; z-index:1; border-top: 1px solid var(--auth-border); padding-top:24px;">
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-clipboard2-check"></i></div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:2px;">Órdenes de servicio</p>
                    <p style="font-size:12px;color:var(--text-muted);">Seguimiento en tiempo real de cada vehículo</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-box-seam"></i></div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:2px;">Control de inventario</p>
                    <p style="font-size:12px;color:var(--text-muted);">Stock de repuestos por sucursal y alertas</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-credit-card-2-front"></i></div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:2px;">Pagos y facturación</p>
                    <p style="font-size:12px;color:var(--text-muted);">Stripe, transferencia y factura PDF con IVA</p>
                </div>
            </div>
        </div>

        {{-- Bottom tag --}}
        <div style="position:relative; z-index:1;">
            <p style="font-size:12px;color:var(--text-dim);">
                &copy; {{ date('Y') }} Taller Pro — Sistema de gestion automotriz
            </p>
        </div>
    </div>

    {{-- Right form area --}}
    <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:32px 20px;">
        <div style="width:100%; max-width:420px;">
            @yield('content')
        </div>
    </div>

</div>

<script>
    // Show left panel only on lg+ (>= 1024px)
    function checkPanel() {
        var el = document.getElementById('auth-left-panel');
        if (window.innerWidth >= 1024) {
            el.style.display = 'flex';
            el.style.width = '42%';
        } else {
            el.style.display = 'none';
        }
    }
    checkPanel();
    window.addEventListener('resize', checkPanel);

    // Toggle password visibility
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.input-toggle[data-target]');
        if (!btn) return;
        var input = document.getElementById(btn.dataset.target);
        if (!input) return;
        var isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.innerHTML = isPassword
            ? '<i class="bi bi-eye-slash"></i>'
            : '<i class="bi bi-eye"></i>';
    });
</script>
</body>
</html>
