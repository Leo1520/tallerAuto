# Skill: laravel-security

Aplica principios de seguridad Laravel al código del proyecto Taller Pro.

## Checklist de seguridad

### Autenticación y autorización
- [ ] Usar `auth` middleware en rutas protegidas
- [ ] Usar Policies para autorización de modelos (`php artisan make:policy`)
- [ ] Usar Gates para permisos globales
- [ ] Verificar `$this->authorize()` en controladores antes de cualquier acción

### Form Requests
- [ ] Toda entrada del usuario pasa por un FormRequest (`php artisan make:request`)
- [ ] Reglas de validación estrictas (`required`, `max`, `regex`, tipos)
- [ ] Sanitizar inputs de texto libre

### CSRF
- [ ] `@csrf` en todos los formularios Blade
- [ ] `VerifyCsrfToken` activo (no excluir rutas innecesariamente)

### XSS
- [ ] Usar `{{ }}` (escapado) en Blade, nunca `{!! !!}` con datos de usuario
- [ ] Sanitizar antes de guardar HTML si es necesario (DOMPurify o similar)

### SQL Injection
- [ ] Usar Eloquent o Query Builder con bindings, nunca `DB::statement()` con concatenación
- [ ] Revisar raw queries con `whereRaw()` — parametrizar siempre

### Subida de archivos
- [ ] Validar MIME type y extensión en FormRequest
- [ ] Guardar fuera de `public/` o usar disco privado S3
- [ ] Generar nombre único (`Str::uuid()`)

### Rate Limiting
- [ ] Aplicar `throttle:` middleware en rutas de login y APIs sensibles
- [ ] Configurar en `RouteServiceProvider` o `bootstrap/app.php`

### Webhooks (Stripe)
- [ ] Verificar firma con `\Stripe\Webhook::constructEvent()`
- [ ] Guardar `webhook_verificado = true` solo tras verificación exitosa
- [ ] Usar idempotencia por `transaccion_externa`

### Producción
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS forzado (`$app->forceHttps()` o config Nginx)
- [ ] Headers de seguridad (CSP, X-Frame-Options, HSTS)

## Comandos útiles

```bash
php artisan make:policy ModeloPolicy --model=Modelo
php artisan make:request StoreModeloRequest
php artisan make:middleware VerifyStripeWebhook
```
