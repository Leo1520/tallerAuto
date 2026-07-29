<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller Pro — Portal Cliente</title>
    <style>
        body { margin:0; background:#0F172A; color:#F8FAFC; font-family:system-ui,sans-serif;
               display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .card { text-align:center; padding:48px 40px; background:#1E293B; border-radius:16px;
                border:1px solid #334155; max-width:420px; }
        .icon { font-size:48px; margin-bottom:16px; }
        h1 { font-size:22px; font-weight:700; margin:0 0 8px; }
        p  { font-size:14px; color:#94A3B8; margin:0 0 24px; line-height:1.6; }
        a  { display:inline-flex; align-items:center; gap:6px; padding:10px 20px;
             background:#D71920; color:#fff; border-radius:8px; text-decoration:none;
             font-size:13px; font-weight:600; }
        a:hover { background:#b81218; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">🔧</div>
    <h1>Portal del Cliente</h1>
    <p>
        Bienvenido, <strong>{{ auth()->user()->persona?->nombre }}</strong>.<br>
        El portal de clientes está en construcción.<br>
        Pronto podrás agendar citas, ver el estado de tu vehículo y más.
    </p>
    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button type="submit" style="background:#334155;border:none;color:#F8FAFC;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
            Cerrar sesión
        </button>
    </form>
</div>
</body>
</html>
