@extends('layouts.app')
@section('title', "Revisar pago #{$pago->id}")
@section('page-title', "Revisar pago #$pago->id")

@section('header-actions')
<a href="{{ route('pagos.revision.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver a la cola
</a>
@endsection

@section('content')

<div class="row g-4">

    {{-- Columna principal --}}
    <div class="col-lg-8">

        {{-- Datos del pago --}}
        <div class="card border-0 mb-4" style="border-radius:12px;">
            <div class="card-header border-0 d-flex align-items-center gap-2 py-3">
                <i class="bi bi-receipt" style="color:var(--c-accent,#D71920);font-size:18px;"></i>
                <span class="fw-semibold">Información del pago</span>
                <span class="ms-auto badge"
                      style="background:rgba(234,179,8,.15);color:#facc15;border-radius:20px;font-size:12px;padding:4px 12px;">
                    En revisión
                </span>
            </div>
            <div class="card-body px-4">
                <dl class="row gy-2 mb-0" style="font-size:14px;">
                    <dt class="col-sm-4 text-muted fw-normal">Orden</dt>
                    <dd class="col-sm-8 fw-semibold">
                        <a href="{{ route('ordenes.show', $pago->orden) }}" style="text-decoration:none;">
                            #{{ $pago->orden->numero }}
                        </a>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Cliente</dt>
                    <dd class="col-sm-8">{{ $pago->orden?->vehiculo?->cliente?->persona?->nombre ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Método</dt>
                    <dd class="col-sm-8">{{ $pago->metodoPago?->nombre ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Monto declarado</dt>
                    <dd class="col-sm-8 fw-bold fs-5">Bs {{ number_format($pago->monto, 2) }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Enviado</dt>
                    <dd class="col-sm-8">{{ $pago->created_at->format('d/m/Y H:i') }}</dd>

                    @if($pago->user)
                    <dt class="col-sm-4 text-muted fw-normal">Enviado por</dt>
                    <dd class="col-sm-8">{{ $pago->user->persona?->nombre ?? $pago->user->email }}</dd>
                    @endif

                    {{-- Bloque de confirmación — visible si ya fue confirmado --}}
                    @if($pago->estado === 'Confirmado' && $pago->fecha_confirmacion)
                    <dt class="col-sm-4 fw-semibold" style="color:#4ade80;">Confirmado</dt>
                    <dd class="col-sm-8">
                        <span class="badge" style="background:rgba(74,222,128,.15);color:#4ade80;border:1px solid rgba(74,222,128,.25);border-radius:6px;font-size:11px;padding:3px 8px;">
                            {{ $pago->fecha_confirmacion->format('d/m/Y H:i:s') }}
                        </span>
                    </dd>
                    @if($pago->confirmadoPor)
                    <dt class="col-sm-4 text-muted fw-normal">Confirmado por</dt>
                    <dd class="col-sm-8 fw-semibold">
                        {{ $pago->confirmadoPor->persona?->nombre ?? $pago->confirmadoPor->email }}
                    </dd>
                    @endif
                    @if($pago->confirmado_ip)
                    <dt class="col-sm-4 text-muted fw-normal">IP</dt>
                    <dd class="col-sm-8 font-monospace" style="font-size:12px;">{{ $pago->confirmado_ip }}</dd>
                    @endif
                    @if($pago->metodo_confirmacion)
                    <dt class="col-sm-4 text-muted fw-normal">Canal</dt>
                    <dd class="col-sm-8">{{ $pago->metodo_confirmacion }}</dd>
                    @endif
                    @endif
                </dl>
            </div>
        </div>

        {{-- Comprobante --}}
        <div class="card border-0 mb-4" style="border-radius:12px;">
            <div class="card-header border-0 d-flex align-items-center gap-2 py-3">
                <i class="bi bi-image" style="color:var(--c-accent,#D71920);font-size:18px;"></i>
                <span class="fw-semibold">Comprobante adjunto</span>
            </div>
            <div class="card-body px-4 pb-4">
                @if($pago->comprobante)
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:8px;background:rgba(96,165,250,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-{{ $pago->comprobante->esImagen() ? 'image' : 'file-earmark-pdf' }}"
                               style="color:#60a5fa;font-size:18px;"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-semibold" style="font-size:14px;">{{ $pago->comprobante->nombre_original }}</p>
                            <p class="mb-0 text-muted" style="font-size:12px;">{{ $pago->comprobante->tamanoLegible() }}</p>
                        </div>
                        <a href="{{ $pago->comprobante->url() }}" target="_blank"
                           class="btn btn-sm ms-auto"
                           style="background:rgba(96,165,250,.12);color:#60a5fa;border:none;">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Abrir
                        </a>
                    </div>

                    @if($pago->comprobante->esImagen())
                        <div style="border-radius:10px;overflow:hidden;max-height:400px;text-align:center;background:rgba(0,0,0,.2);">
                            <img src="{{ $pago->comprobante->url() }}"
                                 alt="Comprobante"
                                 style="max-width:100%;max-height:400px;object-fit:contain;">
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x" style="font-size:36px;opacity:.3;"></i>
                        <p class="text-muted mt-2 mb-0" style="font-size:13px;">El cliente no adjuntó comprobante.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Sidebar de acciones --}}
    <div class="col-lg-4">

        {{-- Confirmar --}}
        <div class="card border-0 mb-3" style="border-radius:12px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(34,197,94,.12);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-check-circle-fill" style="color:#4ade80;font-size:18px;"></i>
                    </div>
                    <p class="fw-semibold mb-0" style="font-size:15px;">Confirmar pago</p>
                </div>
                <p class="text-muted mb-3" style="font-size:13px;line-height:1.55;">
                    Al confirmar se marcará como pagado y se generará la factura si la orden está cubierta.
                    Se notificará al cliente por correo.
                </p>
                <form method="POST" action="{{ route('pagos.validar', $pago) }}"
                      onsubmit="return confirm('¿Confirmar el pago de Bs {{ number_format($pago->monto, 2) }} de la orden #{{ $pago->orden->numero }}?')">
                    @csrf
                    <button type="submit" class="btn w-100"
                            style="background:#16a34a;color:#fff;border:none;padding:12px;border-radius:8px;font-weight:600;">
                        <i class="bi bi-check-lg me-2"></i> Confirmar pago válido
                    </button>
                </form>
            </div>
        </div>

        {{-- Rechazar --}}
        <div class="card border-0" style="border-radius:12px;" x-data="{ abierto: false }">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-x-circle-fill" style="color:#f87171;font-size:18px;"></i>
                    </div>
                    <p class="fw-semibold mb-0" style="font-size:15px;">Rechazar pago</p>
                </div>

                <button type="button" class="btn w-100 mb-2"
                        style="background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.25);padding:10px;border-radius:8px;"
                        @click="abierto = !abierto">
                    <i class="bi bi-x-lg me-2"></i> Rechazar comprobante
                </button>

                <div x-show="abierto" x-cloak>
                    <form method="POST" action="{{ route('pagos.rechazar', $pago) }}"
                          onsubmit="return confirm('¿Rechazar este pago y notificar al cliente?')">
                        @csrf
                        <div class="mb-3 mt-2">
                            <label class="form-label small text-muted">Motivo del rechazo *</label>
                            <textarea name="motivo" rows="3"
                                      class="form-control @error('motivo') is-invalid @enderror"
                                      placeholder="Ej: El monto no coincide, la imagen no es legible…"
                                      required maxlength="500"
                                      style="font-size:13px;resize:none;">{{ old('motivo') }}</textarea>
                            @error('motivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn w-100"
                                style="background:#dc2626;color:#fff;border:none;padding:10px;border-radius:8px;font-weight:600;font-size:13px;">
                            <i class="bi bi-send me-1"></i> Enviar rechazo y notificar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Datos de la orden --}}
        <div class="card border-0 mt-3" style="border-radius:12px;">
            <div class="card-body px-4 py-3">
                <p class="text-muted fw-semibold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;">Resumen de orden</p>
                <dl class="row gy-1 mb-0" style="font-size:13px;">
                    <dt class="col-6 text-muted fw-normal">Total orden</dt>
                    <dd class="col-6 text-end fw-semibold">Bs {{ number_format($pago->orden->total, 2) }}</dd>

                    <dt class="col-6 text-muted fw-normal">Ya confirmado</dt>
                    <dd class="col-6 text-end" style="color:#4ade80;">
                        Bs {{ number_format($pago->orden->pagos->where('estado','Confirmado')->sum('monto'), 2) }}
                    </dd>

                    <dt class="col-6 text-muted fw-normal">Este pago</dt>
                    <dd class="col-6 text-end" style="color:#facc15;">
                        Bs {{ number_format($pago->monto, 2) }}
                    </dd>

                    @php
                        $cubierto = $pago->orden->pagos->where('estado','Confirmado')->sum('monto') + $pago->monto;
                        $pendiente = max(0, $pago->orden->total - $cubierto);
                    @endphp
                    <dt class="col-6 text-muted fw-normal border-top pt-2">Quedaría pendiente</dt>
                    <dd class="col-6 text-end border-top pt-2 fw-bold" style="color:{{ $pendiente > 0 ? '#f87171' : '#4ade80' }};">
                        Bs {{ number_format($pendiente, 2) }}
                    </dd>
                </dl>
            </div>
        </div>

    </div>
</div>

@endsection
