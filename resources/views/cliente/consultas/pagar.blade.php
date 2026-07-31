@extends('layouts.cliente')
@section('title', 'Pagar solicitud — Taller Automotrices SC-BOL')

@section('content')
<div style="max-width:560px;margin:0 auto;padding:24px 16px 100px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;font-size:13px;color:var(--c-muted);">
        <a href="{{ route('cliente.inicio') }}" style="color:var(--c-muted);text-decoration:none;">Inicio</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span style="color:var(--c-text);">Pagar solicitud</span>
    </div>

    {{-- Estado del pago --}}
    @if($consulta->pago_estado === 'En revisión')
    <div class="c-card" style="text-align:center;padding:28px;margin-bottom:16px;border-color:rgba(250,204,21,.3);background:rgba(250,204,21,.06);">
        <i class="bi bi-hourglass-split" style="font-size:36px;color:#facc15;display:block;margin-bottom:12px;"></i>
        <p style="font-size:16px;font-weight:700;color:#facc15;margin-bottom:6px;">Comprobante en revisión</p>
        <p style="font-size:13px;color:var(--c-muted);">Ya enviaste tu comprobante. El equipo lo está revisando y te notificaremos por correo.</p>
    </div>
    @elseif($consulta->pago_estado === 'Rechazado')
    <div class="c-card" style="padding:16px 20px;margin-bottom:16px;border-color:rgba(248,113,113,.3);background:rgba(248,113,113,.06);">
        <div style="display:flex;align-items:center;gap:10px;">
            <i class="bi bi-exclamation-triangle-fill" style="color:#f87171;font-size:18px;flex-shrink:0;"></i>
            <div>
                <p style="font-size:13px;font-weight:700;color:#f87171;margin-bottom:2px;">Comprobante rechazado</p>
                <p style="font-size:12px;color:var(--c-muted);">{{ $consulta->pago_notas ?? 'El comprobante no pudo verificarse.' }} Sube uno nuevo.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Resumen del pedido --}}
    <div class="c-card" style="padding:24px;margin-bottom:16px;">
        <p style="font-size:12px;color:var(--c-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px;">Resumen del pedido</p>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;">
            <div>
                <p style="font-size:15px;font-weight:700;color:var(--c-text);">{{ $consulta->repuesto?->nombre ?? '—' }}</p>
                @if($consulta->repuesto?->codigo)
                    <p style="font-size:12px;color:var(--c-muted);font-family:monospace;">{{ $consulta->repuesto->codigo }}</p>
                @endif
                <p style="font-size:13px;color:var(--c-muted);margin-top:6px;">Cantidad: <strong style="color:var(--c-text);">{{ $consulta->cantidad }}</strong> uds.</p>
            </div>
            <div style="text-align:right;">
                <p style="font-size:12px;color:var(--c-muted);">Total a pagar</p>
                <p style="font-size:32px;font-weight:800;color:var(--c-accent);letter-spacing:-.02em;line-height:1.1;">
                    Bs {{ number_format(($consulta->repuesto?->precio_venta ?? 0) * $consulta->cantidad, 2) }}
                </p>
            </div>
        </div>
    </div>

    {{-- QR Banco Ganadero --}}
    @if(! in_array($consulta->pago_estado, ['En revisión', 'Confirmado']))
    <div class="c-card" style="text-align:center;padding:28px 24px;">

        <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:20px;">
            <div style="width:38px;height:38px;border-radius:10px;background:rgba(215,25,32,.12);display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-qr-code" style="color:var(--c-accent);font-size:20px;"></i>
            </div>
            <div style="text-align:left;">
                <p style="font-size:15px;font-weight:700;color:var(--c-text);line-height:1.2;">QR Banco Ganadero</p>
                <p style="font-size:12px;color:var(--c-muted);">Escanea con tu app bancaria</p>
            </div>
        </div>

        {{-- Imagen QR — prioridad: QR subido por admin → QR estático global --}}
        @php
            $qrUrl = null;
            if ($consulta->qr_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($consulta->qr_path)) {
                $qrUrl = \Illuminate\Support\Facades\Storage::url($consulta->qr_path);
            } elseif (file_exists(public_path('images/qr_banco_ganadero.png'))) {
                $qrUrl = asset('images/qr_banco_ganadero.png');
            }
        @endphp
        <div style="display:inline-flex;align-items:center;justify-content:center;width:220px;height:220px;border-radius:16px;border:2px dashed rgba(255,255,255,.15);background:rgba(255,255,255,.04);margin-bottom:20px;">
            @if($qrUrl)
                <img src="{{ $qrUrl }}" alt="QR de pago"
                     style="width:200px;height:200px;border-radius:12px;object-fit:contain;">
            @else
                <div style="text-align:center;padding:16px;">
                    <i class="bi bi-qr-code-scan" style="font-size:56px;color:var(--c-muted);opacity:.4;"></i>
                    <p style="font-size:12px;color:var(--c-muted);margin-top:8px;opacity:.7;">
                        El equipo te enviará el QR por correo.<br>Escanéalo con tu app bancaria.
                    </p>
                </div>
            @endif
        </div>

        {{-- Instrucciones --}}
        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:14px 16px;text-align:left;margin-bottom:24px;">
            @foreach([
                '1' => 'Abre tu app del banco y escanea el código QR.',
                '2' => 'Ingresa el monto exacto: <strong style="color:var(--c-text);">Bs ' . number_format(($consulta->repuesto?->precio_venta ?? 0) * $consulta->cantidad, 2) . '</strong>',
                '3' => 'En referencia escribe: <strong style="color:var(--c-text);">Solicitud #' . $consulta->id . '</strong>',
                '4' => 'Confirma y vuelve aquí para subir tu comprobante.',
            ] as $num => $text)
            <div style="display:flex;align-items:flex-start;gap:10px;{{ !$loop->last ? 'margin-bottom:8px;' : '' }}">
                <i class="bi bi-{{ $num }}-circle-fill" style="color:var(--c-accent);font-size:15px;flex-shrink:0;margin-top:1px;"></i>
                <p style="font-size:13px;color:var(--c-muted);">{!! $text !!}</p>
            </div>
            @endforeach
        </div>

        {{-- Formulario subir comprobante --}}
        <form method="POST"
              action="{{ route('cliente.consultas.pago.store', $consulta->token) }}"
              enctype="multipart/form-data"
              id="formPago">
            @csrf

            <div x-data="{ mostrarUpload: false }">

                <p style="font-size:13px;color:var(--c-muted);margin-bottom:12px;">¿Ya realizaste el pago?</p>

                <button type="button"
                        @click="mostrarUpload = true"
                        x-show="!mostrarUpload"
                        class="btn-red"
                        style="width:100%;justify-content:center;padding:14px;">
                    <i class="bi bi-check-circle-fill" style="margin-right:8px;"></i>
                    Ya realicé el pago
                </button>

                <div x-show="mostrarUpload" x-cloak style="text-align:left;">
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--c-text);margin-bottom:6px;">
                            Comprobante de pago <span style="color:#f87171;">*</span>
                        </label>
                        <label for="comprobante"
                               style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;min-height:80px;border:2px dashed rgba(255,255,255,.15);border-radius:10px;cursor:pointer;padding:16px;transition:border-color .2s;"
                               onmouseover="this.style.borderColor='rgba(215,25,32,.5)'"
                               onmouseout="this.style.borderColor='rgba(255,255,255,.15)'">
                            <i class="bi bi-upload" style="font-size:22px;color:var(--c-muted);"></i>
                            <span style="font-size:13px;color:var(--c-muted);" id="lblArchivo">Toca para adjuntar imagen o PDF</span>
                            <span style="font-size:11px;color:var(--c-muted);opacity:.7;">JPG, PNG, PDF — máx. 5 MB</span>
                        </label>
                        <input type="file" id="comprobante" name="comprobante"
                               accept=".jpg,.jpeg,.png,.pdf"
                               style="display:none;"
                               onchange="document.getElementById('lblArchivo').textContent = this.files[0]?.name ?? 'Toca para adjuntar'">
                        @error('comprobante')
                            <p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-red" style="width:100%;justify-content:center;padding:14px;">
                        <i class="bi bi-send-fill" style="margin-right:8px;"></i>
                        Enviar comprobante
                    </button>

                    <button type="button" @click="mostrarUpload = false" class="btn-outline"
                            style="width:100%;justify-content:center;margin-top:10px;">
                        Cancelar
                    </button>
                </div>

            </div>
        </form>

    </div>
    @endif

    <p style="text-align:center;font-size:12px;color:var(--c-muted);margin-top:16px;line-height:1.6;opacity:.7;">
        Tu comprobante quedará en revisión hasta que el equipo lo valide.<br>
        Recibirás un correo de confirmación.
    </p>

</div>
@endsection
