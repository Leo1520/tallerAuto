@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush

@section('content')

@php
$badgeEstado = [
    'Recibido'            => 'badge-info',
    'En diagnóstico'      => 'badge-warning',
    'En reparación'       => 'badge-warning',
    'Esperando repuestos' => 'badge-gray',
    'Listo'               => 'badge-success',
    'Entregado'           => 'badge-success',
    'Cancelado'           => 'badge-danger',
];
@endphp

{{-- ── KPIs ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <div class="kpi-card bg-white rounded-xl p-5 border border-gray-200 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(215,25,32,.12);">
            <i class="bi bi-people-fill" style="font-size:22px; color:#D71920;"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Clientes</p>
            <p class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['clientes']) }}</p>
        </div>
    </div>

    <div class="kpi-card bg-white rounded-xl p-5 border border-gray-200 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(59,130,246,.1);">
            <i class="bi bi-car-front-fill" style="font-size:22px; color:#60a5fa;"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Vehículos</p>
            <p class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['vehiculos']) }}</p>
        </div>
    </div>

    <div class="kpi-card bg-white rounded-xl p-5 border border-gray-200 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(249,115,22,.1);">
            <i class="bi bi-clipboard2-pulse-fill" style="font-size:22px; color:#fb923c;"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ordenes activas</p>
            <p class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($stats['ordenes_activas']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Excluye entregadas y canceladas</p>
        </div>
    </div>

    <div class="kpi-card bg-white rounded-xl p-5 border border-gray-200 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(16,185,129,.1);">
            <i class="bi bi-wallet-fill" style="font-size:22px; color:#34d399;"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ingresos este mes</p>
            <p class="text-2xl font-bold text-gray-900 leading-tight">Bs {{ number_format($stats['ingresos_mes'], 0) }}</p>
        </div>
    </div>
</div>

{{-- ── Gráficas ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    {{-- Línea: Ingresos 30 días --}}
    <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm font-semibold text-gray-200">Ingresos — últimos 30 días</p>
                <p class="text-xs text-gray-400">Pagos confirmados acumulados por día</p>
            </div>
            <i class="bi bi-graph-up-arrow text-gray-500" style="font-size:20px;"></i>
        </div>
        <div style="height:200px; position:relative;">
            <canvas id="chartIngresos"></canvas>
        </div>
    </div>

    {{-- Donut: Estados --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-gray-200">Ordenes por estado</p>
            <i class="bi bi-pie-chart text-gray-500" style="font-size:18px;"></i>
        </div>
        <div style="height:170px; position:relative;">
            <canvas id="chartEstados"></canvas>
        </div>
        <div class="mt-3 space-y-1">
            @foreach(['Recibido','En reparación','Listo','Entregado','Cancelado'] as $idx => $e)
            @php $n = $donutData[array_search($e, $donutLabels)] ?? 0; @endphp
            @if($n > 0)
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>{{ $e }}</span>
                <span class="font-semibold text-gray-300">{{ $n }}</span>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

{{-- ── Fila inferior ─────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Órdenes recientes --}}
    <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-200">Ordenes recientes</p>
            <a href="{{ route('ordenes.index') }}" class="text-xs text-gray-400 hover:text-gray-200 transition-colors">
                Ver todas <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @if ($ordenes_recientes->isEmpty())
            <div class="py-12 text-center text-gray-500 text-sm">
                <i class="bi bi-clipboard2 text-gray-600" style="font-size:36px;"></i>
                <p class="mt-2">No hay ordenes de servicio aun.</p>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach ($ordenes_recientes as $orden)
                <a href="{{ route('ordenes.show', $orden) }}"
                   class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors group">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-gray-300">{{ $orden->numero }}</span>
                            <span class="badge {{ $badgeEstado[$orden->estado] ?? 'badge-gray' }} text-xs">{{ $orden->estado }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-200 truncate mt-0.5 group-hover:text-white transition-colors">
                            {{ $orden->vehiculo->cliente->persona->nombre ?? '—' }}
                            <span class="text-gray-500 font-normal">· {{ $orden->vehiculo->placa ?? '' }}</span>
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-500">{{ $orden->fecha_ingreso->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $orden->mecanico?->persona->nombre ?? 'Sin asignar' }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Panel derecho: Top mecánicos + alertas stock --}}
    <div class="space-y-4">

        {{-- Top mecánicos --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-200">Top mecanicos</p>
                <span class="text-xs text-gray-500">Este mes</span>
            </div>
            @forelse($topMecanicos as $i => $mec)
            <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                     style="{{ $i === 0 ? 'background:#D71920; color:#fff;' : 'background:#233044; color:#64748B;' }}">
                    {{ $i + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-200 truncate">{{ $mec->persona->nombre }}</p>
                    <p class="text-xs text-gray-500">{{ $mec->activas }} activa{{ $mec->activas != 1 ? 's' : '' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-200">{{ $mec->entregadas_mes }}</p>
                    <p class="text-xs text-gray-500">entregadas</p>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-500 py-2">Sin datos este mes.</p>
            @endforelse
        </div>

        {{-- Alertas bajo stock --}}
        @if($alertasStock->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-2 mb-3">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:15px;"></i>
                <p class="text-sm font-semibold text-gray-200">Bajo stock</p>
            </div>
            <div class="space-y-2">
                @foreach($alertasStock as $inv)
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-300 truncate">{{ $inv->repuesto->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $inv->sucursal->nombre }}</p>
                    </div>
                    <span class="badge badge-danger ml-2 flex-shrink-0">{{ $inv->stock }} / {{ $inv->stock_minimo }}</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('inventario.index') }}"
               class="block mt-3 text-xs text-center text-gray-400 hover:text-gray-200 transition-colors">
                Ver inventario completo <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        @endif

    </div>
</div>

{{-- ── Charts JS ────────────────────────────────────────── --}}
<script @nonce>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.color = '#64748B';
    Chart.defaults.borderColor = '#1e3352';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

    // Gráfica de ingresos (línea)
    new Chart(document.getElementById('chartIngresos'), {
        type: 'line',
        data: {
            labels: @json($labels30),
            datasets: [{
                label: 'Ingresos (Bs)',
                data:  @json($data30),
                borderColor: '#D71920',
                backgroundColor: 'rgba(215,25,32,.08)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#D71920',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#D71920',
                pointHoverBorderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: ctx => ctx.parsed.y > 0
                            ? ' Bs ' + ctx.parsed.y.toLocaleString('es-BO', { minimumFractionDigits: 2 })
                            : ' Sin ingresos',
                    },
                },
            },
            scales: {
                x: {
                    grid: { color: '#1e3352' },
                    ticks: { maxTicksLimit: 8, font: { size: 11 }, color: '#94a3b8' },
                },
                y: {
                    grid: { color: '#1e3352' },
                    beginAtZero: true,
                    ticks: {
                        font: { size: 11 },
                        color: '#94a3b8',
                        callback: v => 'Bs ' + v.toLocaleString(),
                    },
                },
            },
        },
    });

    // Donut de estados
    new Chart(document.getElementById('chartEstados'), {
        type: 'doughnut',
        data: {
            labels: @json($donutLabels),
            datasets: [{
                data: @json($donutData),
                backgroundColor: [
                    '#3B82F6','#F97316','#F97316',
                    '#64748B','#10B981','#334155','#D71920',
                ],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + ctx.parsed,
                    },
                },
            },
        },
    });
});
</script>

@endsection
