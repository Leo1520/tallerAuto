@extends('layouts.app')
@section('title', "Pago #$pago->id")
@section('page-title', "Detalle de pago")

@section('header-actions')
<a href="{{ route('pagos.index') }}"
   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm text-gray-400 hover:text-gray-200 transition-colors border border-gray-700 hover:border-gray-500">
    <i class="bi bi-arrow-left" style="font-size:12px;"></i> Pagos
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Columna principal ──────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Cabecera del pago --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-1">Pago</p>
                    <p class="text-2xl font-bold text-gray-100 font-mono">#{{ str_pad($pago->id, 6, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-sm text-gray-400 mt-1">Registrado {{ $pago->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                @php
                    [$estadoColor, $estadoBg] = match($pago->estado) {
                        'Confirmado'  => ['#4ade80', 'rgba(74,222,128,.1)'],
                        'En revisión' => ['#60a5fa', 'rgba(96,165,250,.1)'],
                        'Rechazado'   => ['#f87171', 'rgba(248,113,113,.1)'],
                        'Anulado'     => ['#fb923c', 'rgba(251,146,60,.1)'],
                        default       => ['#94a3b8', 'rgba(148,163,184,.1)'],
                    };
                @endphp
                <span class="px-3 py-1.5 rounded-lg text-sm font-semibold"
                      style="background:{{ $estadoBg }};color:{{ $estadoColor }};border:1px solid {{ $estadoColor }}22;">
                    {{ $pago->estado }}
                </span>
            </div>

            <hr class="border-gray-700 my-4">

            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Orden</dt>
                    <dd class="font-semibold text-gray-100">
                        <a href="{{ route('ordenes.show', $pago->orden) }}" class="hover:underline" style="color:#D71920;">
                            #{{ $pago->orden->numero }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Cliente</dt>
                    <dd class="text-gray-200">{{ $pago->orden->vehiculo?->cliente?->persona?->nombre ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Método</dt>
                    <dd class="text-gray-200">{{ $pago->metodoPago?->nombre ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Monto</dt>
                    <dd class="text-xl font-bold text-gray-100">Bs {{ number_format($pago->monto, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Moneda</dt>
                    <dd class="text-gray-200">{{ $pago->moneda }}</dd>
                </div>
                @if($pago->referencia)
                <div>
                    <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Referencia</dt>
                    <dd class="font-mono text-gray-200">{{ $pago->referencia }}</dd>
                </div>
                @endif
                @if($pago->observaciones)
                <div class="col-span-full">
                    <dt class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Observaciones</dt>
                    <dd class="text-gray-300 text-sm">{{ $pago->observaciones }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- ── Bloque de auditoría de confirmación ────────────────────── --}}
        @if($pago->estado === 'Confirmado' && $pago->fecha_confirmacion)
        <div class="bg-gray-800 border border-green-900/50 rounded-xl overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-gray-700/60"
                 style="background:rgba(74,222,128,.05);">
                <i class="bi bi-shield-check" style="color:#4ade80;font-size:16px;"></i>
                <span class="text-sm font-semibold text-gray-200">Registro de confirmación</span>
                <span class="ms-auto text-xs text-green-400 font-mono">VERIFICADO</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Timestamp --}}
                <div class="flex items-start gap-3 bg-gray-900/40 rounded-lg p-3.5 border border-gray-700/50">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:rgba(96,165,250,.12);">
                        <i class="bi bi-clock-history" style="color:#60a5fa;font-size:14px;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Timestamp exacto</p>
                        <p class="text-sm font-semibold text-gray-100 font-mono">
                            {{ $pago->fecha_confirmacion->format('d/m/Y H:i:s') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $pago->fecha_confirmacion->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Confirmado por --}}
                <div class="flex items-start gap-3 bg-gray-900/40 rounded-lg p-3.5 border border-gray-700/50">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:rgba(167,139,250,.12);">
                        <i class="bi bi-person-check" style="color:#a78bfa;font-size:14px;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Confirmado por</p>
                        <p class="text-sm font-semibold text-gray-100">
                            {{ $pago->confirmadoPor?->persona?->nombre ?? $pago->confirmadoPor?->email ?? '—' }}
                        </p>
                        @if($pago->confirmadoPor)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $pago->confirmadoPor->email }}</p>
                        @endif
                    </div>
                </div>

                {{-- IP --}}
                <div class="flex items-start gap-3 bg-gray-900/40 rounded-lg p-3.5 border border-gray-700/50">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:rgba(251,191,36,.12);">
                        <i class="bi bi-geo-alt" style="color:#fbbf24;font-size:14px;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">IP de confirmación</p>
                        <p class="text-sm font-semibold text-gray-100 font-mono">
                            {{ $pago->confirmado_ip ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Método de confirmación --}}
                <div class="flex items-start gap-3 bg-gray-900/40 rounded-lg p-3.5 border border-gray-700/50">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:rgba(74,222,128,.12);">
                        <i class="bi bi-check2-circle" style="color:#4ade80;font-size:14px;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Canal de confirmación</p>
                        <p class="text-sm font-semibold text-gray-100">
                            {{ $pago->metodo_confirmacion ?? '—' }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- Movimiento de caja asociado --}}
            @if($pago->movimientoCaja)
            <div class="mx-5 mb-5 p-3.5 rounded-lg border border-gray-700/50"
                 style="background:rgba(0,0,0,.2);">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Movimiento de caja</p>
                <div class="flex items-center gap-4 text-sm flex-wrap">
                    <span class="text-gray-400">{{ $pago->movimientoCaja->concepto }}</span>
                    <span class="font-bold font-mono ml-auto" style="color:#4ade80;">
                        + Bs {{ number_format($pago->movimientoCaja->monto, 2) }}
                    </span>
                </div>
                <p class="text-xs text-gray-600 mt-1">
                    Registrado por {{ $pago->movimientoCaja->user?->persona?->nombre ?? '—' }}
                    · {{ $pago->movimientoCaja->created_at->format('H:i:s') }}
                </p>
            </div>
            @endif
        </div>
        @endif

        {{-- ── Comprobante ────────────────────────────────────────────── --}}
        @if($pago->comprobante)
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-gray-700/60">
                <i class="bi bi-paperclip" style="color:#60a5fa;font-size:16px;"></i>
                <span class="text-sm font-semibold text-gray-200">Comprobante adjunto</span>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <i class="bi bi-{{ $pago->comprobante->esImagen() ? 'image' : 'file-earmark-pdf' }}"
                       style="color:#60a5fa;font-size:22px;"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-100 truncate">{{ $pago->comprobante->nombre_original }}</p>
                        <p class="text-xs text-gray-500">{{ $pago->comprobante->tamanoLegible() }}</p>
                    </div>
                    <a href="{{ $pago->comprobante->url() }}" target="_blank"
                       class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors"
                       style="background:rgba(96,165,250,.12);color:#60a5fa;border:1px solid rgba(96,165,250,.2);">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Abrir
                    </a>
                </div>
                @if($pago->comprobante->esImagen())
                <div class="rounded-lg overflow-hidden" style="max-height:360px;text-align:center;background:rgba(0,0,0,.3);">
                    <img src="{{ $pago->comprobante->url() }}" alt="Comprobante"
                         style="max-width:100%;max-height:360px;object-fit:contain;">
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── Log de auditoría ────────────────────────────────────────── --}}
        @if($auditLog->isNotEmpty())
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-gray-700/60">
                <i class="bi bi-journal-text" style="color:#a78bfa;font-size:16px;"></i>
                <span class="text-sm font-semibold text-gray-200">Historial de auditoría</span>
                <span class="ms-auto text-xs text-gray-500">{{ $auditLog->count() }} {{ Str::plural('evento', $auditLog->count()) }}</span>
            </div>
            <div class="divide-y divide-gray-700/50">
                @foreach($auditLog as $entry)
                <div class="px-5 py-3.5 hover:bg-gray-700/20 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                             style="background:rgba(167,139,250,.12);">
                            <i class="bi bi-person" style="color:#a78bfa;font-size:12px;"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-gray-200">
                                    {{ $entry->user?->persona?->nombre ?? $entry->user?->email ?? 'Sistema' }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-mono"
                                      style="background:rgba(167,139,250,.1);color:#a78bfa;border:1px solid rgba(167,139,250,.2);">
                                    {{ $entry->tipo_operacion }}
                                </span>
                                <span class="ms-auto text-xs text-gray-500 font-mono">
                                    {{ $entry->created_at->format('d/m/Y H:i:s') }}
                                </span>
                            </div>
                            @if($entry->ip)
                            <p class="text-xs text-gray-500 mt-0.5 font-mono">IP: {{ $entry->ip }}</p>
                            @endif
                            @if($entry->cambios)
                            <div class="mt-2 p-2.5 rounded-lg text-xs font-mono overflow-x-auto"
                                 style="background:rgba(0,0,0,.3);color:#94a3b8;max-height:120px;">
                                <pre style="margin:0;white-space:pre-wrap;">{{ json_encode($entry->cambios, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ── Sidebar ────────────────────────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Acciones --}}
        @if($pago->estado === 'Pendiente')
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 space-y-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Acciones</p>
            <form method="POST" action="{{ route('pagos.confirmar', $pago) }}"
                  data-confirm="¿Confirmar el pago Bs {{ number_format($pago->monto, 2) }}?"
                  data-confirm-title="Confirmar pago">
                @csrf
                <button type="button" onclick="tpOpen(this.form)"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                        style="background:#16a34a;color:#fff;border:none;">
                    <i class="bi bi-check-lg"></i> Confirmar pago
                </button>
            </form>
            <form method="POST" action="{{ route('pagos.anular', $pago) }}"
                  data-confirm="¿Anular el pago #{{ $pago->id }}? Esta acción no se puede deshacer.">
                @csrf
                <button type="button" onclick="tpOpen(this.form)"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                        style="background:rgba(251,146,60,.1);color:#fb923c;border:1px solid rgba(251,146,60,.25);">
                    <i class="bi bi-x-circle"></i> Anular
                </button>
            </form>
        </div>
        @elseif($pago->estado === 'En revisión')
        <div class="bg-gray-800 border border-blue-900/50 rounded-xl p-4">
            <a href="{{ route('pagos.revision.show', $pago) }}"
               class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
               style="background:rgba(96,165,250,.12);color:#60a5fa;border:1px solid rgba(96,165,250,.25);">
                <i class="bi bi-eye"></i> Revisar comprobante
            </a>
        </div>
        @endif

        {{-- Resumen de la orden --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Orden #{{ $pago->orden->numero }}</p>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Total orden</dt>
                    <dd class="font-semibold text-gray-200">Bs {{ number_format($pago->orden->total, 2) }}</dd>
                </div>
                @php
                    $confirmado = $pago->orden->pagos->where('estado','Confirmado')->sum('monto');
                    $pendiente  = max(0, $pago->orden->total - $confirmado);
                @endphp
                <div class="flex justify-between">
                    <dt class="text-gray-500">Confirmado</dt>
                    <dd class="font-semibold" style="color:#4ade80;">Bs {{ number_format($confirmado, 2) }}</dd>
                </div>
                <div class="flex justify-between border-t border-gray-700 pt-2">
                    <dt class="text-gray-400 font-medium">Pendiente</dt>
                    <dd class="font-bold" style="color:{{ $pendiente > 0 ? '#f87171' : '#4ade80' }};">
                        Bs {{ number_format($pendiente, 2) }}
                    </dd>
                </div>
            </dl>
            <a href="{{ route('ordenes.show', $pago->orden) }}"
               class="mt-3 block text-center text-xs text-gray-400 hover:text-gray-200 transition-colors">
                Ver orden completa →
            </a>
        </div>

        {{-- Quién registró --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Registrado por</p>
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background:rgba(148,163,184,.1);">
                    <i class="bi bi-person" style="color:#94a3b8;font-size:14px;"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-200">
                        {{ $pago->user?->persona?->nombre ?? $pago->user?->email ?? 'Sistema' }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $pago->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
