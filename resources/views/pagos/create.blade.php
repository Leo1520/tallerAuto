@extends('layouts.app')
@section('title', 'Registrar Pago')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="pagoForm(@js($metodos), '{{ route('pagos.intent') }}', '{{ csrf_token() }}')">

    <div class="flex items-center gap-4">
        @if(isset($orden))
        <a href="{{ route('ordenes.show', $orden) }}"
        @else
        <a href="{{ route('pagos.index') }}"
        @endif
           class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Registrar Pago</h1>
            @isset($orden)
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Orden {{ $orden->numero }} — {{ $orden->vehiculo->cliente->persona->nombre ?? '' }}
            </p>
            @endisset
        </div>
    </div>

    @isset($orden)
    {{-- Resumen de la orden --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Resumen de la orden</h2>
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400">Total orden</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">Bs {{ number_format($orden->total, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Ya pagado</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400">
                    Bs {{ number_format($orden->total - $pendiente, 2) }}
                </p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400">Pendiente</p>
                <p class="text-xl font-bold text-red-600 dark:text-red-400">Bs {{ number_format($pendiente, 2) }}</p>
            </div>
        </div>
    </div>
    @endisset

    {{-- Formulario --}}
    <form method="POST" action="{{ route('pagos.store') }}" id="pagoForm" class="space-y-6" @submit.prevent="submitPago">
        @csrf

        @isset($orden)
        <input type="hidden" name="orden_id" value="{{ $orden->id }}">
        @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de orden *</label>
            <input type="text" id="buscarOrden" placeholder="Buscar por número de orden..."
                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="hidden" name="orden_id" :value="ordenId">
        </div>
        @endisset

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Datos del pago</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Método de pago *</label>
                    <select name="metodo_pago_id" x-model="metodoId" @change="onMetodoChange"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('metodo_pago_id') border-red-500 @enderror">
                        <option value="">Seleccionar método</option>
                        @foreach($metodos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                    @error('metodo_pago_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto (Bs) *</label>
                    <input type="number" name="monto" x-model="monto" min="0.01" step="0.01"
                           value="{{ old('monto', isset($pendiente) ? $pendiente : '') }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('monto') border-red-500 @enderror">
                    @error('monto')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Referencia (para métodos que la requieren) --}}
            <div x-show="requiereReferencia" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Referencia / Nro. transacción</label>
                <input type="text" name="referencia" value="{{ old('referencia') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="2"
                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('observaciones') }}</textarea>
            </div>
        </div>

        {{-- Panel Stripe --}}
        <div x-show="esStripe" x-cloak
             class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-indigo-200 dark:border-indigo-700 p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h16v2H4V6zm0 4h16v8H4v-8zm2 2v2h4v-2H6zm6 0v2h4v-2h-4z"/>
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Pago con tarjeta</h2>
                <span class="text-xs text-gray-400">Powered by Stripe</span>
            </div>

            <div id="card-element"
                 class="p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 min-h-[40px]">
            </div>
            <div id="card-errors" class="text-xs text-red-500" x-text="stripeError"></div>

            <input type="hidden" name="payment_intent" x-bind:value="paymentIntentId">
        </div>

        <div class="flex justify-end gap-3">
            @isset($orden)
            <a href="{{ route('ordenes.show', $orden) }}"
            @else
            <a href="{{ route('pagos.index') }}"
            @endif
               class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    :disabled="procesando"
                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed rounded-lg transition-colors flex items-center gap-2">
                <span x-show="procesando">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                <span x-text="esStripe ? 'Pagar con tarjeta' : 'Registrar Pago'"></span>
            </button>
        </div>
    </form>
</div>

{{-- Stripe.js --}}
<script src="https://js.stripe.com/v3/"></script>
<script>
function pagoForm(metodos, intentUrl, csrfToken) {
    return {
        metodoId:        '',
        monto:           {{ isset($pendiente) ? $pendiente : 0 }},
        esStripe:        false,
        requiereReferencia: false,
        procesando:      false,
        stripeError:     '',
        paymentIntentId: '',
        stripe:          null,
        cardElement:     null,

        init() {
            this.stripe = Stripe('{{ config('services.stripe.key') }}');
            const elements = this.stripe.elements();
            this.cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '14px',
                        color: '#374151',
                        '::placeholder': { color: '#9CA3AF' },
                    },
                },
            });
        },

        onMetodoChange() {
            const m = metodos.find(m => m.id == this.metodoId);
            if (!m) return;

            this.requiereReferencia = m.requiere_referencia;
            const esS = m.nombre.toLowerCase().includes('stripe') || m.nombre.toLowerCase().includes('tarjeta');

            if (esS && !this.esStripe) {
                this.$nextTick(() => this.cardElement.mount('#card-element'));
            } else if (!esS && this.esStripe) {
                this.cardElement.unmount();
            }
            this.esStripe = esS;
        },

        async submitPago() {
            this.procesando = true;
            this.stripeError = '';

            if (this.esStripe) {
                try {
                    // Crear PaymentIntent
                    const res = await fetch(intentUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({
                            orden_id: document.querySelector('[name=orden_id]').value,
                            monto: this.monto
                        }),
                    });
                    const { client_secret, error } = await res.json();
                    if (error) { this.stripeError = error; this.procesando = false; return; }

                    // Confirmar pago en Stripe
                    const { paymentIntent, error: stripeErr } = await this.stripe.confirmCardPayment(client_secret, {
                        payment_method: { card: this.cardElement },
                    });

                    if (stripeErr) {
                        this.stripeError = stripeErr.message;
                        this.procesando = false;
                        return;
                    }

                    this.paymentIntentId = paymentIntent.id;
                } catch (e) {
                    this.stripeError = 'Error de conexión. Intenta de nuevo.';
                    this.procesando = false;
                    return;
                }
            }

            document.getElementById('pagoForm').submit();
        }
    }
}
</script>
@endsection
