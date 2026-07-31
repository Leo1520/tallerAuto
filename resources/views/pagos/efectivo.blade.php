@extends('layouts.app')
@section('title', 'Cobro en efectivo')
@section('page-title', 'Cobro en efectivo')

@section('header-actions')
<a href="{{ route('pagos.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Pagos
</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}
</div>
@endif

<div class="row justify-content-center">
<div class="col-lg-6 col-xl-5">

<div class="card border-0" style="border-radius:14px;"
     x-data="{
         total: {{ $orden ? (float)$orden->total : 0 }},
         montoPagado: {{ $orden ? $orden->pagos->where('estado','Confirmado')->sum('monto') : 0 }},
         pendiente: {{ $orden ? max(0, (float)$orden->total - $orden->pagos->where('estado','Confirmado')->sum('monto')) : 0 }},
         monto: {{ $orden ? max(0, (float)$orden->total - $orden->pagos->where('estado','Confirmado')->sum('monto')) : 0 }},
         recibido: '',
         get cambio() {
             const c = parseFloat(this.recibido || 0) - parseFloat(this.monto || 0);
             return c >= 0 ? c : null;
         }
     }">

    <div class="card-header border-0 d-flex align-items-center gap-2 py-3 px-4">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(215,25,32,.1);display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-cash-stack" style="color:#D71920;font-size:18px;"></i>
        </div>
        <span class="fw-semibold fs-6">Registrar cobro en efectivo</span>
    </div>

    <div class="card-body px-4 pb-4">
        <form method="POST" action="{{ route('pagos.efectivo.store') }}">
            @csrf

            {{-- Búsqueda / selección de orden --}}
            <div class="mb-4">
                <label class="form-label small text-muted fw-semibold text-uppercase" style="letter-spacing:.05em;">Orden de servicio</label>

                @if($orden)
                    <input type="hidden" name="orden_id" value="{{ $orden->id }}">
                    <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <p class="mb-0 fw-bold">#{{ $orden->numero }}</p>
                            <p class="mb-0 text-muted" style="font-size:12px;">
                                {{ $orden->vehiculo?->cliente?->persona?->nombre ?? '—' }}
                            </p>
                        </div>
                        <div class="text-end">
                            <p class="mb-0 fw-bold" style="color:#D71920;">Bs {{ number_format($orden->total, 2) }}</p>
                            <p class="mb-0 text-muted" style="font-size:12px;">total</p>
                        </div>
                    </div>
                    <a href="{{ route('pagos.efectivo') }}" class="d-block mt-2 text-muted" style="font-size:12px;text-decoration:none;">
                        <i class="bi bi-search me-1"></i> Cambiar orden
                    </a>
                @else
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscarOrden"
                               class="form-control"
                               placeholder="Número de orden o nombre de cliente"
                               autocomplete="off">
                    </div>
                    <input type="hidden" name="orden_id" id="ordenIdInput" value="{{ old('orden_id') }}"
                           @if(old('orden_id')) required @endif>
                    <div id="resultadosOrden" style="position:relative;"></div>
                    @error('orden_id')
                        <div class="text-danger mt-1" style="font-size:12px;">Selecciona una orden.</div>
                    @enderror
                @endif
            </div>

            {{-- Monto a cobrar --}}
            <div class="mb-3">
                <label class="form-label small text-muted fw-semibold text-uppercase" style="letter-spacing:.05em;">Monto a cobrar (Bs)</label>
                <input type="number" name="monto" step="0.01" min="0.01"
                       class="form-control form-control-lg @error('monto') is-invalid @enderror"
                       x-model="monto"
                       value="{{ old('monto', $orden ? max(0, $orden->total - $orden->pagos->where('estado','Confirmado')->sum('monto')) : '') }}"
                       required
                       style="font-size:22px;font-weight:700;text-align:right;letter-spacing:-.01em;">
                @error('monto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Monto recibido --}}
            <div class="mb-3">
                <label class="form-label small text-muted fw-semibold text-uppercase" style="letter-spacing:.05em;">Monto recibido (Bs)</label>
                <input type="number" name="monto_recibido" step="0.01" min="0.01"
                       class="form-control form-control-lg @error('monto_recibido') is-invalid @enderror"
                       x-model="recibido"
                       value="{{ old('monto_recibido') }}"
                       required placeholder="0.00"
                       style="font-size:22px;font-weight:700;text-align:right;letter-spacing:-.01em;">
                @error('monto_recibido')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Cambio --}}
            <div class="mb-4 p-3 rounded-3"
                 :style="cambio === null ? 'background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);' : 'background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);'">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size:14px;">Cambio a entregar</span>
                    <span class="fw-bold fs-4"
                          :style="cambio === null ? 'color:#f87171;' : 'color:#4ade80;'"
                          x-text="cambio === null ? '— Insuficiente' : 'Bs ' + cambio.toFixed(2)">
                    </span>
                </div>
                <p class="mb-0 mt-1 text-muted" style="font-size:11px;" x-show="cambio !== null && cambio > 0">
                    Entregar el vuelto al cliente.
                </p>
                <p class="mb-0 mt-1 text-muted" style="font-size:11px;" x-show="cambio === 0">
                    Monto exacto. Sin vuelto.
                </p>
            </div>

            {{-- Botón submit --}}
            <button type="submit"
                    class="btn w-100 fw-bold"
                    style="background:#D71920;color:#fff;border:none;padding:14px;border-radius:10px;font-size:15px;"
                    :disabled="cambio === null">
                <i class="bi bi-cash me-2"></i> Registrar cobro y emitir factura
            </button>

        </form>
    </div>
</div>

</div>
</div>

@push('scripts')
<script @nonce>
    // Búsqueda de orden si no viene preseleccionada
    const input   = document.getElementById('buscarOrden');
    const hidden  = document.getElementById('ordenIdInput');
    const results = document.getElementById('resultadosOrden');

    if (input) {
        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 2) { results.innerHTML = ''; return; }
            timer = setTimeout(async () => {
                const res = await fetch(`/admin/ordenes?q=${encodeURIComponent(q)}&ajax=1`);
                if (!res.ok) return;
                const data = await res.json();
                if (!data.length) {
                    results.innerHTML = '<p class="text-muted small mt-2 ps-1">Sin resultados.</p>';
                    return;
                }
                results.innerHTML = '<div class="list-group mt-2">' +
                    data.map(o => `<button type="button" class="list-group-item list-group-item-action"
                        data-id="${o.id}" data-num="${o.numero}" data-pendiente="${o.pendiente}"
                        style="font-size:13px;">
                        <strong>#${o.numero}</strong> — ${o.cliente} — Bs ${o.pendiente} pendiente
                    </button>`).join('') + '</div>';
                results.querySelectorAll('button[data-id]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        hidden.value = btn.dataset.id;
                        input.value  = `#${btn.dataset.num}`;
                        results.innerHTML = '';
                        // Actualizar Alpine si está activo
                        if (window.Alpine) {
                            const component = document.querySelector('[x-data]').__x?.$data;
                            if (component) component.monto = parseFloat(btn.dataset.pendiente);
                        }
                    });
                });
            }, 300);
        });
    }
</script>
@endpush

@endsection
