@extends('layouts.cliente')
@section('title', 'Solicitar producto — Taller Pro')

@section('content')
<div style="max-width:560px;margin:0 auto;padding:24px 16px 100px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;font-size:13px;color:var(--c-muted);">
        <a href="{{ route('tienda', ['tab' => 'productos']) }}" style="color:var(--c-muted);text-decoration:none;">Tienda</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span style="color:var(--c-text);">Solicitar producto</span>
    </div>

    {{-- Tarjeta del producto --}}
    <div class="c-card" style="display:flex;align-items:center;gap:16px;padding:18px 20px;margin-bottom:20px;">
        <div style="width:56px;height:56px;border-radius:12px;background:rgba(124,58,237,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
            @if($repuesto->imagen)
                <img src="{{ asset('storage/' . $repuesto->imagen) }}" alt="{{ $repuesto->nombre }}"
                     style="width:100%;height:100%;object-fit:cover;">
            @else
                <i class="bi bi-box-seam-fill" style="font-size:26px;color:#A78BFA;"></i>
            @endif
        </div>
        <div style="flex:1;min-width:0;">
            @if($repuesto->codigo)
            <p style="font-size:11px;font-weight:700;color:#A78BFA;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">
                <i class="bi bi-upc" style="margin-right:3px;"></i>{{ $repuesto->codigo }}
            </p>
            @endif
            <p style="font-size:15px;font-weight:700;color:var(--c-text);margin-bottom:4px;line-height:1.3;">{{ $repuesto->nombre }}</p>
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:17px;font-weight:800;color:var(--c-accent);">Bs {{ number_format($repuesto->precio_venta, 2) }}</span>
                <span style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:100px;
                    {{ $stockTotal > 0 ? 'background:rgba(16,185,129,.12);color:#34D399;' : 'background:rgba(100,116,139,.12);color:#94A3B8;' }}">
                    <i class="bi bi-circle-fill" style="font-size:6px;margin-right:3px;"></i>
                    {{ $stockTotal > 0 ? "En stock ({$stockTotal})" : 'Sin stock' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Sin stock: bloquear formulario --}}
    @if($stockTotal <= 0)
    <div class="c-card" style="padding:28px;text-align:center;">
        <div style="width:60px;height:60px;border-radius:50%;background:rgba(100,116,139,.12);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i class="bi bi-archive" style="font-size:28px;color:#64748B;"></i>
        </div>
        <p style="font-size:16px;font-weight:700;color:var(--c-text);margin-bottom:8px;">Producto sin stock</p>
        <p style="font-size:13px;color:var(--c-muted);margin-bottom:20px;line-height:1.5;">
            Este producto no está disponible actualmente. Puedes consultarnos por WhatsApp para saber cuándo volverá a estar en stock.
        </p>
        <a href="https://wa.me/59178559066?text={{ urlencode('Hola, me interesa el producto: ' . $repuesto->nombre . '. ¿Cuándo estará disponible?') }}"
           target="_blank"
           style="display:inline-flex;align-items:center;gap:8px;background:#25D366;color:#fff;text-decoration:none;padding:11px 22px;border-radius:100px;font-size:14px;font-weight:600;">
            <i class="bi bi-whatsapp"></i> Consultar por WhatsApp
        </a>
        <div style="margin-top:16px;">
            <a href="{{ route('tienda', ['tab' => 'productos']) }}" style="font-size:13px;color:var(--c-muted);text-decoration:none;">
                <i class="bi bi-arrow-left" style="margin-right:4px;"></i> Volver a la tienda
            </a>
        </div>
    </div>
    @else

    {{-- Alerta de stock bajo --}}
    @if($stockTotal <= 5)
    <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:11px 14px;margin-bottom:16px;display:flex;align-items:center;gap:10px;font-size:13px;color:#FCD34D;">
        <i class="bi bi-exclamation-triangle-fill" style="flex-shrink:0;"></i>
        <span><strong>¡Quedan solo {{ $stockTotal }} unidad{{ $stockTotal > 1 ? 'es' : '' }}!</strong> Tu solicitud quedará sujeta a disponibilidad al momento de procesarla.</span>
    </div>
    @endif

    {{-- Formulario --}}
    <div class="c-card" style="padding:24px;">

        <p style="font-size:15px;font-weight:700;color:var(--c-text);margin-bottom:4px;">Datos de tu solicitud</p>
        <p style="font-size:13px;color:var(--c-muted);margin-bottom:20px;line-height:1.5;">
            Te contactaremos para confirmar disponibilidad y coordinar la entrega o retiro en sucursal.
        </p>

        @if($errors->any())
        <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#f87171;">
            <i class="bi bi-exclamation-circle-fill" style="margin-right:6px;"></i>{{ $errors->first() }}
        </div>
        @endif

        @if(session('error'))
        <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#f87171;">
            <i class="bi bi-exclamation-circle-fill" style="margin-right:6px;"></i>{{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('cliente.consultas.repuesto.store', $repuesto) }}">
            @csrf

            {{-- Datos del cliente (pre-llenados, solo lectura) --}}
            <div class="c-form-grid" style="margin-bottom:16px;">
                <div>
                    <label class="c-label">Tu nombre</label>
                    <div class="c-input" style="opacity:.7;cursor:not-allowed;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-person" style="color:var(--c-muted);"></i>
                        <span style="font-size:14px;">{{ $persona->nombre }}</span>
                    </div>
                </div>
                <div>
                    <label class="c-label">Tu correo</label>
                    <div class="c-input" style="opacity:.7;cursor:not-allowed;display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-envelope" style="color:var(--c-muted);"></i>
                        <span style="font-size:14px;">{{ $persona->email }}</span>
                    </div>
                </div>
            </div>

            {{-- Teléfono --}}
            <div style="margin-bottom:16px;">
                <label for="telefono" class="c-label">Teléfono / celular <span style="color:var(--c-muted);font-weight:400;">(opcional)</span></label>
                <div style="display:flex;align-items:center;gap:8px;background:var(--c-input-bg,rgba(255,255,255,.06));border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:11px 14px;">
                    <i class="bi bi-phone" style="color:var(--c-muted);"></i>
                    <input type="tel" id="telefono" name="telefono"
                           value="{{ old('telefono', $persona->telefono) }}"
                           placeholder="Ej: 70012345"
                           style="background:none;border:none;outline:none;flex:1;font-size:14px;color:var(--c-text);">
                </div>
            </div>

            {{-- Cantidad --}}
            <div style="margin-bottom:16px;">
                <label for="cantidad" class="c-label">
                    Cantidad
                    <span style="font-weight:400;color:var(--c-muted);font-size:12px;margin-left:6px;">(máx. {{ $stockTotal }})</span>
                </label>
                <div style="display:flex;align-items:center;gap:12px;">
                    <button type="button" onclick="cambiarCantidad(-1)"
                            style="width:40px;height:40px;border-radius:10px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:var(--c-text);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" id="cantidad" name="cantidad"
                           value="{{ old('cantidad', 1) }}" min="1" max="{{ $stockTotal }}" required
                           style="width:70px;text-align:center;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:10px;padding:10px;font-size:18px;font-weight:700;color:var(--c-text);outline:none;">
                    <button type="button" onclick="cambiarCantidad(1)"
                            style="width:40px;height:40px;border-radius:10px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:var(--c-text);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-plus"></i>
                    </button>
                    <div>
                        <p style="font-size:11px;color:var(--c-muted);">Precio estimado</p>
                        <p id="precioTotal" style="font-size:17px;font-weight:800;color:var(--c-accent);">
                            Bs {{ number_format($repuesto->precio_venta, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Notas --}}
            <div style="margin-bottom:24px;">
                <label for="notas" class="c-label">Notas adicionales <span style="color:var(--c-muted);font-weight:400;">(opcional)</span></label>
                <textarea id="notas" name="notas" rows="3" maxlength="500"
                          placeholder="Ej: Es para un Nissan Sentra 2018, necesito saber si es original..."
                          style="width:100%;box-sizing:border-box;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:11px 14px;font-size:14px;color:var(--c-text);outline:none;resize:none;">{{ old('notas') }}</textarea>
            </div>

            <button type="submit" class="btn-red" style="width:100%;justify-content:center;padding:14px;font-size:15px;">
                <i class="bi bi-send-fill" style="margin-right:8px;"></i> Enviar solicitud
            </button>
        </form>

        <p style="font-size:12px;color:var(--c-muted);text-align:center;margin-top:14px;line-height:1.6;opacity:.8;">
            También puedes contactarnos por
            <a href="https://wa.me/59178559066?text={{ urlencode('Hola, me interesa el producto: ' . $repuesto->nombre . ' (Bs ' . number_format($repuesto->precio_venta, 2) . '). ¿Está disponible?') }}"
               target="_blank" style="color:#25D366;text-decoration:none;">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </a>
        </p>
    </div>
    @endif

</div>

@if($stockTotal > 0)
<script>
const precioUnit  = {{ (float) $repuesto->precio_venta }};
const stockMaximo = {{ (int) $stockTotal }};
function cambiarCantidad(delta) {
    const input = document.getElementById('cantidad');
    let v = parseInt(input.value || 1) + delta;
    if (v < 1)           v = 1;
    if (v > stockMaximo) v = stockMaximo;
    input.value = v;
    actualizarPrecio(v);
}
function actualizarPrecio(v) {
    document.getElementById('precioTotal').textContent =
        'Bs ' + (precioUnit * v).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
document.getElementById('cantidad').addEventListener('input', function() {
    let v = parseInt(this.value) || 1;
    if (v < 1)           v = 1;
    if (v > stockMaximo) { v = stockMaximo; this.value = v; }
    actualizarPrecio(v);
});
</script>
@endif
@endsection
