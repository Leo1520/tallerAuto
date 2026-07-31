@extends('layouts.app')
@section('title', 'Registrar Pago')
@section('page-title', 'Registrar Pago')

@section('header-actions')
    @isset($orden)
    <a href="{{ route('ordenes.show', $orden) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver a la orden
    </a>
    @else
    <a href="{{ route('pagos.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:14px;"></i> Volver
    </a>
    @endisset
@endsection

@section('content')
<div class="max-w-xl mx-auto space-y-4" x-data="pagoForm({{ $metodos->toJson() }})">

    @isset($orden)
    {{-- Resumen de la orden --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl">
        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30 rounded-t-xl">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-receipt" style="color:#D71920;"></i> Orden {{ $orden->numero }}
            </p>
        </div>
        <div class="p-5 grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-xs text-gray-500 mb-1">Total orden</p>
                <p class="text-lg font-bold text-gray-100">Bs {{ number_format($orden->total, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Ya pagado</p>
                <p class="text-lg font-bold text-green-400">Bs {{ number_format($orden->total - $pendiente, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Pendiente</p>
                <p class="text-lg font-bold text-red-400">Bs {{ number_format($pendiente, 2) }}</p>
            </div>
        </div>
    </div>
    @endisset

    {{-- Formulario de pago --}}
    <form method="POST" action="{{ route('pagos.store') }}"
          class="bg-gray-800 border border-gray-700 rounded-xl">
        @csrf

        @isset($orden)
        <input type="hidden" name="orden_id" value="{{ $orden->id }}">
        @endisset

        <div class="px-6 py-3 border-b border-gray-700 bg-gray-900/30 rounded-t-xl">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-cash-coin" style="color:#D71920;"></i> Datos del pago
            </p>
        </div>

        <div class="p-6 space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">
                        Método de pago <span class="text-red-400">*</span>
                    </label>
                    <select name="metodo_pago_id" x-model="metodoId" @change="onMetodoChange()"
                            class="w-full px-3 py-2.5 bg-gray-900 border text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 {{ $errors->has('metodo_pago_id') ? 'border-red-500' : 'border-gray-600' }}" required>
                        <option value="" style="background:#111827;">Seleccionar...</option>
                        @foreach($metodos as $m)
                            <option value="{{ $m->id }}" style="background:#111827;">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                    @error('metodo_pago_id')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">
                        Monto (Bs)
                    </label>
                    <p class="w-full px-3 py-2.5 bg-gray-800/60 border border-gray-700 text-gray-100 rounded-lg text-sm font-semibold">
                        Bs {{ number_format($pendiente, 2) }}
                    </p>
                    <input type="hidden" name="monto" value="{{ $pendiente }}">
                </div>
            </div>

            {{-- Referencia — visible solo si el método la requiere --}}
            <div x-show="requiereReferencia" x-cloak>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">
                    Referencia / N° transacción
                </label>
                <input type="text" name="referencia" value="{{ old('referencia') }}"
                       placeholder="Ej: comprobante, N° transferencia..."
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Observaciones</label>
                <textarea name="observaciones" rows="2"
                          placeholder="Notas adicionales sobre el pago..."
                          class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-400 resize-none">{{ old('observaciones') }}</textarea>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-gray-700 flex items-center justify-end gap-3">
            @isset($orden)
            <a href="{{ route('ordenes.show', $orden) }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                Cancelar
            </a>
            @endisset
            <button type="submit" id="btnRegistrarPago"
                    class="px-6 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors btn-taller-red">
                <i class="bi bi-check-lg me-1"></i> Registrar pago
            </button>
        </div>
    </form>

</div>

<script @nonce>
function pagoForm(metodos) {
    return {
        metodoId: '',
        requiereReferencia: false,
        onMetodoChange() {
            const m = metodos.find(m => String(m.id) === String(this.metodoId));
            this.requiereReferencia = m ? !!m.requiere_referencia : false;
        }
    };
}
</script>
@endsection

@push('styles')
<style>.btn-taller-red{background:#D71920}.btn-taller-red:hover{background:#b81218}</style>
@endpush
