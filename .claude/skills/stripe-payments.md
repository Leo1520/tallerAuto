# Skill: stripe-payments

Integración Stripe segura para Taller Pro.

## Flujo de pago

1. Frontend solicita `PaymentIntent` → Controller llama Stripe API
2. Stripe devuelve `client_secret` → Frontend confirma con Stripe.js
3. Stripe envía webhook → `StripeWebhookController` verifica firma
4. Al confirmar `payment_intent.succeeded` → actualizar `pagos.estado = Confirmado`

## Seguridad de webhooks

```php
// app/Http/Controllers/StripeWebhookController.php
public function handle(Request $request): Response
{
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    
    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, config('services.stripe.webhook_secret')
        );
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return response('Invalid signature', 400);
    }
    
    // Idempotencia: verificar si ya procesamos este evento
    $pago = Pago::where('transaccion_externa', $event->data->object->id)->first();
    if ($pago?->webhook_verificado) {
        return response('Already processed', 200);
    }
    
    // Procesar según tipo de evento
    match ($event->type) {
        'payment_intent.succeeded' => $this->handleSuccess($event->data->object),
        'payment_intent.payment_failed' => $this->handleFailure($event->data->object),
        default => null,
    };
    
    return response('OK', 200);
}
```

## Variables de entorno (.env)

```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

## Ruta del webhook (excluir CSRF)

```php
// bootstrap/app.php o Middleware
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->middleware('stripe.webhook'); // middleware propio, no csrf
```

## Modelo Pago — campos clave

- `transaccion_externa`: ID del PaymentIntent de Stripe (UNIQUE)
- `webhook_verificado`: true solo tras verificar firma
- `estado`: Pendiente → Confirmado/Rechazado
- `referencia`: número de referencia interno
