<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taller Automotrices SC-BOL') — Taller Automotrices SC-BOL</title>
    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Tailwind + assets propios (carga después para que sus utilidades tengan prioridad) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-900 min-h-screen" x-data="{ sidebarOpen: true, userMenu: false }">

{{-- ═══════════════════════════════════════════════════════ --}}
{{--  SIDEBAR                                               --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<aside
    class="fixed top-0 left-0 h-full bg-gray-800 border-r border-gray-600 z-30 flex flex-col transition-all duration-200 overflow-hidden"
    :style="sidebarOpen ? 'width:260px' : 'width:64px'"
>
    {{-- ── Brand ── --}}
    <div class="flex flex-col items-center border-b border-gray-600 flex-shrink-0 overflow-hidden"
         style="padding: 10px 12px 8px;">
        <img src="{{ asset('images/logo.png') }}" alt="SC-BOL"
             style="width:150px;height:150px;object-fit:contain;flex-shrink:0;">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-150"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             style="text-align:center;margin-top:4px;">
            <p class="font-bold text-gray-100 leading-tight tracking-tight" style="font-size:12px;">Taller Automotrices SC-BOL</p>
            <p class="text-gray-400" style="font-size:10px;margin-top:1px;">Sistema automotriz</p>
        </div>
    </div>

    {{-- ── Navigation ── --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

        @php
        $isActive = fn(string $pattern) => request()->routeIs($pattern);

        $groups = [
            null => [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'pattern' => 'dashboard'],
            ],
            'OPERACIONES' => [
                ['route' => 'clientes.index',  'label' => 'Clientes',        'icon' => 'bi-people',            'pattern' => 'clientes*'],
                ['route' => 'vehiculos.index', 'label' => 'Vehiculos',       'icon' => 'bi-car-front',         'pattern' => 'vehiculos*'],
                ['route' => 'ordenes.index',   'label' => 'Ordenes',         'icon' => 'bi-clipboard2-check',  'pattern' => 'ordenes*'],
                ['route' => 'citas.index',     'label' => 'Citas',           'icon' => 'bi-calendar2-check',   'pattern' => 'citas*'],
                ['route' => 'consultas.index', 'label' => 'Solicitudes',     'icon' => 'bi-box-seam',          'pattern' => 'consultas*'],
            ],
            'INVENTARIO' => [
                ['route' => 'repuestos.index',   'label' => 'Repuestos',   'icon' => 'bi-box-seam',         'pattern' => 'repuestos*'],
                ['route' => 'proveedores.index', 'label' => 'Proveedores', 'icon' => 'bi-building',         'pattern' => 'proveedores*'],
                ['route' => 'inventario.index',  'label' => 'Inventario',  'icon' => 'bi-archive',          'pattern' => 'inventario*'],
            ],
            'FINANZAS' => [
                ['route' => 'pagos.index',    'label' => 'Pagos',    'icon' => 'bi-credit-card-2-front', 'pattern' => 'pagos*'],
                ['route' => 'facturas.index', 'label' => 'Facturas', 'icon' => 'bi-receipt',             'pattern' => 'facturas*'],
            ],
            'ANALISIS' => [
                ['route' => 'reportes.index', 'label' => 'Reportes', 'icon' => 'bi-bar-chart-line', 'pattern' => 'reportes*'],
            ],
            'ADMINISTRACION' => [
                ['route' => 'mecanicos.index',  'label' => 'Mecanicos',  'icon' => 'bi-wrench-adjustable', 'pattern' => 'mecanicos*'],
                ['route' => 'sucursales.index', 'label' => 'Sucursales', 'icon' => 'bi-geo-alt',           'pattern' => 'sucursales*'],
                ['route' => 'usuarios.index',   'label' => 'Usuarios',   'icon' => 'bi-shield-person',     'pattern' => 'usuarios*'],
                ['route' => 'roles.index',      'label' => 'Roles',      'icon' => 'bi-shield-lock',       'pattern' => 'roles*'],
            ],
        ];
        @endphp

        @foreach($groups as $groupLabel => $items)
            @if($groupLabel)
                <div class="nav-group-label" x-show="sidebarOpen">{{ $groupLabel }}</div>
                <div x-show="!sidebarOpen" style="height:8px;"></div>
            @endif

            @foreach($items as $item)
                @if(Route::has($item['route']))
                <a href="{{ route($item['route']) }}"
                   class="nav-item {{ $isActive($item['pattern']) ? 'active' : '' }}"
                   title="{{ $item['label'] }}">
                    <i class="bi {{ $item['icon'] }} nav-icon"></i>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity duration-100"
                          x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        {{ $item['label'] }}
                    </span>
                </a>
                @endif
            @endforeach
        @endforeach

    </nav>

    {{-- ── User profile ── --}}
    <div class="border-t border-gray-600 p-3 flex-shrink-0">
        <div class="flex items-center gap-3 rounded-lg p-2 hover:bg-gray-750 transition-colors cursor-default">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white"
                 style="background:#D71920;">
                {{ strtoupper(substr(auth()->user()->nombre, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0" x-show="sidebarOpen">
                <p class="text-xs font-semibold text-gray-200 truncate leading-tight">{{ auth()->user()->nombre }}</p>
                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->roles->first()?->nombre ?? 'Sin rol' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen"
              x-transition:enter="transition-opacity duration-100"
              x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
              class="mt-1">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-gray-400 hover:text-red-400 hover:bg-gray-750 transition-colors">
                <i class="bi bi-box-arrow-left" style="font-size:13px;"></i>
                Cerrar sesion
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MAIN WRAPPER                                          --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="flex flex-col min-h-screen transition-all duration-200"
     :style="sidebarOpen ? 'margin-left:260px' : 'margin-left:64px'">

    {{-- ── Topbar ── --}}
    <header class="sticky top-0 z-20 flex items-center gap-4 px-5 bg-gray-800 border-b border-gray-600"
            style="height:60px;">

        {{-- Sidebar toggle --}}
        <button @click="sidebarOpen = !sidebarOpen"
                class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-200 hover:bg-gray-700 transition-colors flex-shrink-0">
            <i class="bi bi-layout-sidebar-inset" style="font-size:17px;"></i>
        </button>

        {{-- Breadcrumb / page title --}}
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-400">Taller Automotrices SC-BOL</span>
            <i class="bi bi-chevron-right text-gray-600" style="font-size:10px;"></i>
            <span class="text-gray-200 font-medium">@yield('page-title', 'Dashboard')</span>
        </div>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Actions slot --}}
        @yield('header-actions')

        {{-- Notificaciones --}}
        <div class="dropdown" id="notifDropdown">
            <button id="notifBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-200 hover:bg-gray-700 transition-colors relative"
                    style="border:none;background:transparent;">
                <i class="bi bi-bell" style="font-size:16px;"></i>
                <span id="notifBadge"
                      class="absolute -top-1 -right-1 text-white text-xs font-bold rounded-full flex items-center justify-center"
                      style="display:none!important;min-width:18px;height:18px;font-size:10px;padding:0 4px;background:#D71920;line-height:1;">
                    0
                </span>
            </button>

            <div class="dropdown-menu dropdown-menu-end shadow-xl border-0 p-0 overflow-hidden"
                 style="width:340px;background:#1f2937;border:1px solid #374151;border-radius:12px;margin-top:8px;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color:#374151;">
                    <span class="text-sm font-semibold text-gray-200">Notificaciones</span>
                    <span id="notifTotal" class="text-xs text-gray-500"></span>
                </div>

                {{-- Lista --}}
                <div id="notifList" style="max-height:320px;overflow-y:auto;">
                    <div class="px-4 py-8 text-center text-gray-500 text-sm">
                        <i class="bi bi-arrow-clockwise" style="font-size:20px;display:block;margin-bottom:8px;opacity:.5;"></i>
                        Cargando...
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-4 py-2.5 border-t text-center" style="border-color:#374151;">
                    <span class="text-xs text-gray-600">Actualización automática cada 60s</span>
                </div>
            </div>
        </div>

        {{-- User chip --}}
        <div class="flex items-center gap-2 pl-2 border-l border-gray-600">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                 style="background:#D71920;">
                {{ strtoupper(substr(auth()->user()->nombre, 0, 2)) }}
            </div>
            <span class="text-xs font-medium text-gray-300 hidden sm:block">{{ auth()->user()->nombre }}</span>
        </div>
    </header>

    {{-- ── Page content ── --}}
    <main class="flex-1 p-6">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mb-5 flex items-start gap-3 p-4 rounded-xl border text-sm"
             style="background:rgba(16,185,129,.1); border-color:rgba(16,185,129,.25); color:#34d399;"
             x-data x-init="setTimeout(() => $el.remove(), 5000)">
            <i class="bi bi-check-circle-fill mt-0.5 flex-shrink-0" style="font-size:15px;"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-5 flex items-start gap-3 p-4 rounded-xl border text-sm"
             style="background:rgba(215,25,32,.1); border-color:rgba(215,25,32,.25); color:#f87171;"
             x-data x-init="setTimeout(() => $el.remove(), 7000)">
            <i class="bi bi-exclamation-circle-fill mt-0.5 flex-shrink-0" style="font-size:15px;"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="px-6 py-3 border-t border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Taller Automotrices SC-BOL — Sistema de gestion automotriz</p>
        <p class="text-xs text-gray-600">v1.0</p>
    </footer>
</div>

@stack('scripts')

{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MODAL DE CONFIRMACIÓN GLOBAL                          --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="tpConfirmModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.6);">
    <div id="tpConfirmPanel" style="background:#1E293B;border:1px solid #334155;border-radius:1rem;width:100%;max-width:400px;box-shadow:0 25px 50px rgba(0,0,0,.5);">
        <div style="padding:1.5rem;display:flex;flex-direction:column;align-items:center;text-align:center;gap:1rem;">
            <div id="tpConfirmIcon" style="width:64px;height:64px;border-radius:1rem;display:flex;align-items:center;justify-content:center;background:rgba(215,25,32,.15);">
                <i id="tpConfirmIconEl" class="bi bi-trash3" style="color:#D71920;font-size:28px;"></i>
            </div>
            <div>
                <p id="tpConfirmTitle" style="font-size:1rem;font-weight:700;color:#F1F5F9;margin:0 0 .25rem;">Confirmar eliminación</p>
                <p id="tpConfirmMsg"   style="font-size:.875rem;color:#94a3b8;margin:0;line-height:1.5;"></p>
            </div>
        </div>
        <div style="padding:0 1.5rem 1.5rem;display:flex;gap:.75rem;">
            <button id="tpConfirmCancel"
                    style="flex:1;padding:.625rem 1rem;font-size:.875rem;font-weight:600;color:#e2e8f0;background:#334155;border:none;border-radius:.75rem;cursor:pointer;"
                    onmouseover="this.style.background='#475569'" onmouseout="this.style.background='#334155'">
                Cancelar
            </button>
            <button id="tpConfirmOk"
                    style="flex:1;padding:.625rem 1rem;font-size:.875rem;font-weight:600;color:#fff;background:#D71920;border:none;border-radius:.75rem;cursor:pointer;"
                    onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
                Eliminar
            </button>
        </div>
    </div>
</div>

<script @nonce>
(function () {
    var pendingForm = null;

    var modal   = document.getElementById('tpConfirmModal');
    var msgEl   = document.getElementById('tpConfirmMsg');
    var titleEl = document.getElementById('tpConfirmTitle');
    var iconEl  = document.getElementById('tpConfirmIconEl');
    var iconBox = document.getElementById('tpConfirmIcon');
    var btnOk   = document.getElementById('tpConfirmOk');
    var btnCancel = document.getElementById('tpConfirmCancel');

    function openModal(form) {
        if (!modal || !form) return;
        pendingForm = form;

        msgEl.textContent   = form.dataset.confirm || '';
        titleEl.textContent = form.dataset.confirmTitle || 'Confirmar eliminación';

        var type = form.dataset.confirmType || 'delete';
        if (type === 'warning') {
            iconEl.className           = 'bi bi-exclamation-triangle';
            iconEl.style.color         = '#F97316';
            iconBox.style.background   = 'rgba(249,115,22,.15)';
            btnOk.style.background     = '#F97316';
            btnOk.onmouseover = function() { btnOk.style.background = '#ea580c'; };
            btnOk.onmouseout  = function() { btnOk.style.background = '#F97316'; };
            btnOk.textContent = form.dataset.confirmOk || 'Confirmar';
        } else {
            iconEl.className           = 'bi bi-trash3';
            iconEl.style.color         = '#D71920';
            iconBox.style.background   = 'rgba(215,25,32,.15)';
            btnOk.style.background     = '#D71920';
            btnOk.onmouseover = function() { btnOk.style.background = '#b81218'; };
            btnOk.onmouseout  = function() { btnOk.style.background = '#D71920'; };
            btnOk.textContent = form.dataset.confirmOk || 'Eliminar';
        }

        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
        pendingForm = null;
    }

    btnOk.addEventListener('click', function () {
        if (pendingForm) {
            pendingForm.removeAttribute('data-confirm');
            pendingForm.submit();
        }
        closeModal();
    });

    btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    window.tpOpen = function (form) { openModal(form); };
    window.tpInit = function () {};
})();
</script>
{{-- Bootstrap 5 JS (modals, dropdowns, tooltips) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script @nonce>
(function () {
    const badge    = document.getElementById('notifBadge');
    const list     = document.getElementById('notifList');
    const totalEl  = document.getElementById('notifTotal');
    const endpoint = '{{ route("notificaciones.resumen") }}';

    const iconMap = {
        'cita':     'bi-calendar2-check',
        'consulta': 'bi-box-seam',
        'stock':    'bi-exclamation-triangle-fill',
        'pago':     'bi-credit-card-2-front',
    };

    function renderItems(data) {
        if (!data.items || data.items.length === 0) {
            list.innerHTML = `
                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                    <i class="bi bi-check-circle" style="font-size:24px;display:block;margin-bottom:8px;color:#34d399;opacity:.7;"></i>
                    Todo al día, sin pendientes.
                </div>`;
            return;
        }

        list.innerHTML = data.items.map(item => `
            <a href="${item.url}"
               class="flex items-start gap-3 px-4 py-3 border-b text-decoration-none hover:bg-gray-700/40 transition-colors"
               style="border-color:#374151;">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                     style="background:${item.color}20;">
                    <i class="bi ${item.icono}" style="color:${item.color};font-size:15px;"></i>
                </div>
                <span class="text-sm text-gray-300 leading-snug">${item.mensaje}</span>
            </a>
        `).join('');
    }

    function updateBadge(total) {
        if (total > 0) {
            badge.textContent = total > 99 ? '99+' : total;
            badge.style.removeProperty('display');
        } else {
            badge.style.setProperty('display', 'none', 'important');
        }
        totalEl.textContent = total > 0 ? `${total} pendiente(s)` : 'Sin pendientes';
    }

    async function fetchNotifs() {
        try {
            const res  = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            updateBadge(data.total);
            renderItems(data);
        } catch (e) {
            list.innerHTML = `<div class="px-4 py-4 text-center text-gray-600 text-xs">Error al cargar.</div>`;
        }
    }

    // Cargar al abrir el dropdown
    const btn = document.getElementById('notifBtn');
    if (btn) {
        btn.addEventListener('show.bs.dropdown', fetchNotifs);
    }

    // Carga inicial del badge (sin abrir dropdown)
    fetchNotifs();

    // Polling cada 60s
    setInterval(fetchNotifs, 60000);
})();
</script>
</body>
</html>
