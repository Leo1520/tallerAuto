@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.app')
@section('title', 'Solicitud #' . $consultaRepuesto->id)
@section('page-title', 'Detalle de Solicitud')

@section('header-actions')
<a href="{{ route('consultas.index') }}"
   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
    <i class="bi bi-arrow-left"></i> Volver
</a>
@endsection

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <p class="text-xs text-gray-500 mb-1">Solicitud #{{ $consultaRepuesto->id }}</p>
            <h2 class="text-lg font-bold text-gray-100">{{ $consultaRepuesto->nombre }}</h2>
            <p class="text-sm text-gray-400 mt-1">
                <i class="bi bi-clock me-1"></i>
                {{ $consultaRepuesto->created_at?->format('d/m/Y H:i') ?? '—' }}
            </p>
        </div>
        @php
            $badge = match($consultaRepuesto->estado) {
                'Pendiente' => ['bg' => 'rgba(250,204,21,.15)', 'color' => '#facc15'],
                'Atendida'  => ['bg' => 'rgba(52,211,153,.15)','color' => '#34d399'],
                'Cancelada' => ['bg' => 'rgba(248,113,113,.15)','color' => '#f87171'],
                default     => ['bg' => 'rgba(156,163,175,.15)','color' => '#9ca3af'],
            };
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-semibold"
              style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
            {{ $consultaRepuesto->estado }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {{-- Contacto --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Datos de contacto</p>
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2 text-gray-300">
                    <i class="bi bi-person text-gray-500"></i>
                    {{ $consultaRepuesto->nombre }}
                </div>
                @if($consultaRepuesto->telefono)
                <div class="flex items-center gap-2 text-gray-300">
                    <i class="bi bi-telephone text-gray-500"></i>
                    <a href="tel:{{ $consultaRepuesto->telefono }}" class="hover:text-blue-400">
                        {{ $consultaRepuesto->telefono }}
                    </a>
                </div>
                @endif
                @if($consultaRepuesto->email)
                <div class="flex items-center gap-2 text-gray-300">
                    <i class="bi bi-envelope text-gray-500"></i>
                    <a href="mailto:{{ $consultaRepuesto->email }}" class="hover:text-blue-400">
                        {{ $consultaRepuesto->email }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Repuesto --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Repuesto solicitado</p>
            @if($consultaRepuesto->repuesto)
                <p class="text-sm font-medium text-gray-200">{{ $consultaRepuesto->repuesto->nombre }}</p>
                @if($consultaRepuesto->repuesto->codigo)
                    <p class="text-xs font-mono text-gray-400">Código: {{ $consultaRepuesto->repuesto->codigo }}</p>
                @endif
                <p class="text-sm text-gray-300">
                    Cantidad: <span class="font-bold text-white">{{ $consultaRepuesto->cantidad }}</span> unidad(es)
                </p>
                <p class="text-sm text-gray-400">
                    Precio unitario: <span class="font-semibold text-gray-200">Bs {{ number_format($consultaRepuesto->repuesto->precio_venta, 2) }}</span>
                </p>
            @else
                <p class="text-sm text-gray-500">Repuesto no encontrado.</p>
            @endif
        </div>
    </div>

    {{-- Stock actual --}}
    @if($consultaRepuesto->repuesto?->inventarios?->count())
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Stock disponible por sucursal</p>
        <div class="space-y-2">
            @foreach($consultaRepuesto->repuesto->inventarios as $inv)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">{{ $inv->sucursal?->nombre ?? 'Sucursal' }}</span>
                <span class="font-bold {{ $inv->stock <= $inv->stock_minimo ? 'text-red-400' : 'text-green-400' }}">
                    {{ $inv->stock }} uds
                    @if($inv->stock <= $inv->stock_minimo)
                        <span class="text-xs text-red-400">(stock bajo)</span>
                    @endif
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Notas --}}
    @if($consultaRepuesto->notas)
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Notas del cliente</p>
        <p class="text-sm text-gray-300 leading-relaxed">{{ $consultaRepuesto->notas }}</p>
    </div>
    @endif

    {{-- Comprobante de pago --}}
    @if($consultaRepuesto->comprobante_path)
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Comprobante de pago</p>

        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
            <div class="flex items-center gap-3">
                @php
                    $ext = pathinfo($consultaRepuesto->comprobante_path, PATHINFO_EXTENSION);
                    $esPdf = strtolower($ext) === 'pdf';
                @endphp
                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                     style="background:rgba(96,165,250,.12);border:1px solid rgba(96,165,250,.2);">
                    <i class="bi {{ $esPdf ? 'bi-file-pdf' : 'bi-image' }}" style="color:#60a5fa;font-size:18px;"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-200">Comprobante adjunto</p>
                    @php
                        $pagoEstadoBadge = match($consultaRepuesto->pago_estado) {
                            'En revisión' => ['color' => '#facc15', 'label' => 'En revisión'],
                            'Confirmado'  => ['color' => '#34d399', 'label' => 'Confirmado'],
                            'Rechazado'   => ['color' => '#f87171', 'label' => 'Rechazado'],
                            default       => ['color' => '#9ca3af', 'label' => 'Pendiente'],
                        };
                    @endphp
                    <span class="text-xs font-semibold" style="color:{{ $pagoEstadoBadge['color'] }};">
                        {{ $pagoEstadoBadge['label'] }}
                    </span>
                </div>
            </div>
            <a href="{{ Storage::url($consultaRepuesto->comprobante_path) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
                <i class="bi bi-box-arrow-up-right"></i> Ver archivo
            </a>
        </div>

        @if($consultaRepuesto->pago_notas)
        <div class="p-3 rounded-lg text-sm text-gray-400" style="background:rgba(255,255,255,.04);">
            <i class="bi bi-chat-left-text me-1"></i> {{ $consultaRepuesto->pago_notas }}
        </div>
        @endif

        @if($consultaRepuesto->pago_estado === 'En revisión')
        <div class="mt-4 pt-4 border-t border-gray-700 flex flex-wrap gap-3">
            {{-- Confirmar pago --}}
            <form method="POST" action="{{ route('consultas.pago.confirmar', $consultaRepuesto) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                        style="background:#059669;">
                    <i class="bi bi-check-circle-fill"></i> Confirmar pago
                </button>
            </form>

            {{-- Rechazar pago --}}
            <form method="POST" action="{{ route('consultas.pago.rechazar', $consultaRepuesto) }}"
                  x-data="{ motivo: '' }" @submit.prevent="if(motivo.trim()) $el.submit()">
                @csrf
                <div class="flex gap-2 items-start flex-wrap">
                    <input type="text" name="notas" x-model="motivo"
                           placeholder="Motivo del rechazo (requerido)"
                           class="px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600"
                           style="min-width:220px;">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                            style="background:#D71920;"
                            :disabled="!motivo.trim()">
                        <i class="bi bi-x-circle-fill"></i> Rechazar
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
    @endif

    {{-- Gestionar solicitud --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5" x-data="{ panel: '' }">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Gestionar solicitud</p>

        {{-- Botones de acción --}}
        <div class="flex flex-wrap gap-3">
            @if($consultaRepuesto->estado !== 'Atendida')
            <button type="button" @click="panel = panel === 'atender' ? '' : 'atender'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
                    style="background:#059669;">
                <i class="bi bi-check-circle"></i> Marcar atendida (envía QR)
            </button>
            @endif

            @if($consultaRepuesto->estado !== 'Cancelada')
            <form method="POST" action="{{ route('consultas.update', $consultaRepuesto) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="estado" value="Cancelada">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                        style="background:#D71920;">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
            </form>
            @endif

            @if($consultaRepuesto->estado !== 'Pendiente')
            <form method="POST" action="{{ route('consultas.update', $consultaRepuesto) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="estado" value="Pendiente">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                        style="background:#6b7280;">
                    <i class="bi bi-arrow-counterclockwise"></i> Volver a pendiente
                </button>
            </form>
            @endif
        </div>

        {{-- Panel: Atender + subir QR --}}
        <div x-show="panel === 'atender'" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mt-4 pt-4 border-t border-gray-700">

            <form method="POST" action="{{ route('consultas.update', $consultaRepuesto) }}"
                  enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="estado" value="Atendida">

                {{-- Preview del QR actual --}}
                @if($consultaRepuesto->qr_path && Storage::disk('public')->exists($consultaRepuesto->qr_path))
                <div class="flex items-center gap-3 p-3 rounded-lg" style="background:rgba(255,255,255,.04);">
                    <img src="{{ Storage::url($consultaRepuesto->qr_path) }}"
                         alt="QR actual" class="w-16 h-16 rounded-lg object-contain">
                    <div>
                        <p class="text-xs font-medium text-gray-300">QR actual guardado</p>
                        <p class="text-xs text-gray-500">Sube uno nuevo para reemplazarlo</p>
                    </div>
                </div>
                @endif

                {{-- Upload QR --}}
                <div x-data="{ nombre: '' }">
                    <label class="block text-xs font-medium text-gray-400 mb-2">
                        Imagen QR de pago
                        <span class="text-gray-600 font-normal">(opcional — se adjuntará al correo del cliente)</span>
                    </label>
                    <label for="qr_upload"
                           class="flex flex-col items-center justify-center gap-2 cursor-pointer rounded-xl transition-colors"
                           style="min-height:110px;border:2px dashed rgba(255,255,255,.12);background:rgba(255,255,255,.03);padding:16px;"
                           onmouseover="this.style.borderColor='rgba(5,150,105,.5)'"
                           onmouseout="this.style.borderColor='rgba(255,255,255,.12)'">
                        <i class="bi bi-qr-code-scan" style="font-size:30px;color:#34d399;opacity:.7;"></i>
                        <span class="text-sm text-gray-400" x-text="nombre || 'Subir imagen del QR'"></span>
                        <span class="text-xs text-gray-600">JPG, PNG, WEBP — máx. 4 MB</span>
                    </label>
                    <input type="file" id="qr_upload" name="qr"
                           accept=".jpg,.jpeg,.png,.webp"
                           style="display:none;"
                           @change="nombre = $event.target.files[0]?.name ?? ''">
                    @error('qr')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white"
                            style="background:#059669;">
                        <i class="bi bi-send-fill"></i> Confirmar y enviar al cliente
                    </button>
                    <button type="button" @click="panel = ''"
                            class="px-4 py-2.5 rounded-lg text-sm font-medium text-gray-400 bg-gray-700 hover:bg-gray-600 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        {{-- Link de pago (si ya fue atendida) --}}
        @if($consultaRepuesto->token && $consultaRepuesto->estado === 'Atendida')
        <div class="mt-4 pt-4 border-t border-gray-700">
            <p class="text-xs text-gray-500 mb-1">Link de pago enviado al cliente:</p>
            <code class="text-xs text-blue-400 break-all">{{ url('/cliente/consultas/pagar/' . $consultaRepuesto->token) }}</code>
        </div>
        @endif
    </div>

</div>

@endsection
