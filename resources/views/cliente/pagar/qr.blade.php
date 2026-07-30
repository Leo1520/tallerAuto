@extends('layouts.cliente')
@section('title', 'Pagar con QR — Taller Automotrices SC-BOL')

@section('content')
<div style="max-width:560px;margin:0 auto;padding:24px 16px 100px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;font-size:13px;color:var(--c-muted);">
        <a href="{{ route('cliente.ordenes.index') }}" style="color:var(--c-muted);text-decoration:none;">Mis órdenes</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <a href="{{ route('cliente.ordenes.show', $orden) }}" style="color:var(--c-muted);text-decoration:none;">Orden #{{ $orden->numero }}</a>
        <i class="bi bi-chevron-right" style="font-size:11px;"></i>
        <span style="color:var(--c-text);">Pagar con QR</span>
    </div>

    {{-- Monto a pagar --}}
    <div class="c-card" style="text-align:center;padding:28px 24px 20px;margin-bottom:16px;">
        <p style="font-size:13px;color:var(--c-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em;">Total a pagar</p>
        <p style="font-size:42px;font-weight:800;color:var(--c-accent);letter-spacing:-.02em;line-height:1.1;">
            Bs {{ number_format($pendiente, 2) }}
        </p>
        <p style="font-size:13px;color:var(--c-muted);margin-top:6px;">Orden #{{ $orden->numero }}</p>
    </div>

    {{-- QR Banco Ganadero --}}
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

        {{-- Imagen QR — reemplazar con el QR real en public/images/qr_banco_ganadero.png --}}
        <div style="display:inline-flex;align-items:center;justify-content:center;width:220px;height:220px;border-radius:16px;border:2px dashed rgba(255,255,255,.15);background:rgba(255,255,255,.04);margin-bottom:20px;">
            @if(file_exists(public_path('images/qr_banco_ganadero.png')))
                <img src="{{ asset('images/qr_banco_ganadero.png') }}"
                     alt="QR Banco Ganadero"
                     style="width:200px;height:200px;border-radius:12px;object-fit:contain;">
            @else
                <div style="text-align:center;padding:16px;">
                    <i class="bi bi-qr-code-scan" style="font-size:56px;color:var(--c-muted);opacity:.4;"></i>
                    <p style="font-size:11px;color:var(--c-muted);margin-top:8px;opacity:.7;">
                        Coloca el QR de Banco<br>Ganadero en:<br>
                        <code style="font-size:10px;">public/images/qr_banco_ganadero.png</code>
                    </p>
                </div>
            @endif
        </div>

        {{-- Instrucciones --}}
        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:14px 16px;text-align:left;margin-bottom:24px;">
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:8px;">
                <i class="bi bi-1-circle-fill" style="color:var(--c-accent);font-size:15px;flex-shrink:0;margin-top:1px;"></i>
                <p style="font-size:13px;color:var(--c-muted);">Abre tu app del banco y escanea el código QR.</p>
            </div>
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:8px;">
                <i class="bi bi-2-circle-fill" style="color:var(--c-accent);font-size:15px;flex-shrink:0;margin-top:1px;"></i>
                <p style="font-size:13px;color:var(--c-muted);">
                    Ingresa el monto exacto:
                    <strong style="color:var(--c-text);">Bs {{ number_format($pendiente, 2) }}</strong>
                </p>
            </div>
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:8px;">
                <i class="bi bi-3-circle-fill" style="color:var(--c-accent);font-size:15px;flex-shrink:0;margin-top:1px;"></i>
                <p style="font-size:13px;color:var(--c-muted);">
                    En la referencia/concepto escribe: <strong style="color:var(--c-text);">{{ $orden->numero }}</strong>
                </p>
            </div>
            <div style="display:flex;align-items:flex-start;gap:10px;">
                <i class="bi bi-4-circle-fill" style="color:var(--c-accent);font-size:15px;flex-shrink:0;margin-top:1px;"></i>
                <p style="font-size:13px;color:var(--c-muted);">Confirma el pago y regresa aquí para notificarnos.</p>
            </div>
        </div>

        {{-- Formulario de confirmación --}}
        <form method="POST"
              action="{{ route('cliente.ordenes.pagar.confirmar', $orden) }}"
              enctype="multipart/form-data"
              id="formConfirmar">
            @csrf

            <div x-data="{ mostrarUpload: false }">

                <p style="font-size:13px;color:var(--c-muted);margin-bottom:12px;">
                    ¿Ya realizaste el pago?
                </p>

                {{-- Botón principal --}}
                <button type="button"
                        @click="mostrarUpload = true"
                        x-show="!mostrarUpload"
                        class="btn-red"
                        style="width:100%;justify-content:center;padding:14px;">
                    <i class="bi bi-check-circle-fill" style="margin-right:8px;"></i>
                    Ya realicé el pago
                </button>

                {{-- Panel de confirmación --}}
                <div x-show="mostrarUpload" x-cloak style="text-align:left;">

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--c-text);margin-bottom:6px;">
                            Comprobante de pago
                            <span style="font-weight:400;color:var(--c-muted);font-size:12px;">(opcional)</span>
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
                               class="c-input" style="display:none;"
                               onchange="document.getElementById('lblArchivo').textContent = this.files[0]?.name ?? 'Toca para adjuntar imagen o PDF'">
                        @error('comprobante')
                            <p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="btn-red"
                            style="width:100%;justify-content:center;padding:14px;">
                        <i class="bi bi-send-fill" style="margin-right:8px;"></i>
                        Enviar confirmación de pago
                    </button>

                    <button type="button"
                            @click="mostrarUpload = false"
                            class="btn-outline"
                            style="width:100%;justify-content:center;margin-top:10px;">
                        Cancelar
                    </button>
                </div>

            </div>{{-- /x-data --}}
        </form>

    </div>{{-- /c-card --}}

    <p style="text-align:center;font-size:12px;color:var(--c-muted);margin-top:16px;line-height:1.6;opacity:.7;">
        Tu pago quedará en estado "En revisión" hasta que el cajero lo valide.<br>
        Recibirás un correo de confirmación.
    </p>

</div>
@endsection
