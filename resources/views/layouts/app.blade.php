<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taller Pro') — Taller Pro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-600 flex-shrink-0" style="min-height:68px;">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
             style="background:#D71920; box-shadow:0 4px 12px rgba(215,25,32,.35);">
            <i class="bi bi-tools text-white" style="font-size:16px;"></i>
        </div>
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-150"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <p class="font-bold text-gray-100 text-base leading-tight tracking-tight">Taller Pro</p>
            <p class="text-gray-400 text-xs">Sistema automotriz</p>
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
                ['route' => 'clientes.index',  'label' => 'Clientes',   'icon' => 'bi-people',         'pattern' => 'clientes*'],
                ['route' => 'vehiculos.index', 'label' => 'Vehiculos',  'icon' => 'bi-car-front',      'pattern' => 'vehiculos*'],
                ['route' => 'ordenes.index',   'label' => 'Ordenes',    'icon' => 'bi-clipboard2-check','pattern' => 'ordenes*'],
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
            <span class="text-gray-400">Taller Pro</span>
            <i class="bi bi-chevron-right text-gray-600" style="font-size:10px;"></i>
            <span class="text-gray-200 font-medium">@yield('page-title', 'Dashboard')</span>
        </div>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Actions slot --}}
        @yield('header-actions')

        {{-- Notifications (placeholder) --}}
        <button class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-200 hover:bg-gray-700 transition-colors relative">
            <i class="bi bi-bell" style="font-size:16px;"></i>
        </button>

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
        <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Taller Pro — Sistema de gestion automotriz</p>
        <p class="text-xs text-gray-600">v1.0</p>
    </footer>
</div>

@stack('scripts')
</body>
</html>
