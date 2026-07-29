@extends('layouts.app')
@section('title', 'Revisión de pagos QR')
@section('page-title', 'Pagos en revisión')

@section('header-actions')
<a href="{{ route('pagos.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-list-ul me-1"></i> Todos los pagos
</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('warning'))
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('warning') }}
</div>
@endif

{{-- Contador --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-auto">
        <div class="card border-0 h-100" style="background:var(--bs-body-bg,#1e293b);border-radius:12px;">
            <div class="card-body d-flex align-items-center gap-3 px-4 py-3">
                <div style="width:44px;height:44px;border-radius:10px;background:rgba(234,179,8,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-hourglass-split" style="color:#facc15;font-size:20px;"></i>
                </div>
                <div>
                    <p class="mb-0 small text-muted">En revisión</p>
                    <p class="mb-0 fw-bold fs-5">{{ $pagos->total() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if($pagos->isEmpty())
<div class="card border-0 text-center py-5" style="border-radius:12px;">
    <i class="bi bi-check-circle" style="font-size:40px;color:#4ade80;opacity:.5;"></i>
    <p class="mt-3 text-muted">No hay pagos pendientes de revisión.</p>
    <p class="small text-muted">Todo está al día.</p>
</div>
@else
<div class="card border-0" style="border-radius:12px;overflow:hidden;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;">
                <tr>
                    <th class="ps-4">Orden</th>
                    <th>Cliente</th>
                    <th>Método</th>
                    <th class="text-end">Monto</th>
                    <th>Comprobante</th>
                    <th>Enviado</th>
                    <th class="pe-4 text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                <tr>
                    <td class="ps-4">
                        <a href="{{ route('ordenes.show', $pago->orden) }}"
                           style="font-weight:600;text-decoration:none;color:inherit;">
                            #{{ $pago->orden->numero ?? '—' }}
                        </a>
                    </td>
                    <td>
                        <span style="font-size:14px;">
                            {{ $pago->orden?->vehiculo?->cliente?->persona?->nombre ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge rounded-pill" style="background:rgba(99,102,241,.18);color:#818cf8;font-size:11px;">
                            {{ $pago->metodoPago?->nombre ?? '—' }}
                        </span>
                    </td>
                    <td class="text-end fw-bold" style="font-size:15px;">
                        Bs {{ number_format($pago->monto, 2) }}
                    </td>
                    <td>
                        @if($pago->comprobante)
                            <a href="{{ $pago->comprobante->url() }}"
                               target="_blank"
                               title="{{ $pago->comprobante->nombre_original }}"
                               style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#60a5fa;text-decoration:none;">
                                <i class="bi bi-{{ $pago->comprobante->esImagen() ? 'image' : 'file-earmark-pdf' }}"></i>
                                Ver archivo
                            </a>
                        @else
                            <span class="text-muted" style="font-size:12px;">Sin comprobante</span>
                        @endif
                    </td>
                    <td class="text-muted" style="font-size:12px;">
                        {{ $pago->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('pagos.revision.show', $pago) }}"
                               class="btn btn-sm"
                               style="background:rgba(96,165,250,.12);color:#60a5fa;border:none;">
                                <i class="bi bi-eye me-1"></i> Revisar
                            </a>
                            {{-- Confirmación rápida --}}
                            <form method="POST" action="{{ route('pagos.validar', $pago) }}"
                                  onsubmit="return confirm('¿Confirmar este pago de Bs {{ number_format($pago->monto, 2) }}?')">
                                @csrf
                                <button type="submit" class="btn btn-sm"
                                        style="background:rgba(34,197,94,.12);color:#4ade80;border:none;">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $pagos->links() }}
</div>
@endif

@endsection
