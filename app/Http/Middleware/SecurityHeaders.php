<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Nonce criptográfico por request — permite scripts inline sin 'unsafe-inline'.
        $nonce = base64_encode(random_bytes(16));

        // Registrar para @vite (añade nonce a sus <script> automáticamente)
        // y para acceso en Blade con la directiva @nonce.
        app(Vite::class)->useCspNonce($nonce);
        app()->instance('csp-nonce', $nonce);

        $response = $next($request);

        // No inyectar headers en respuestas binarias (PDF, imágenes, JSON…).
        $ct = $response->headers->get('Content-Type', '');
        if (! str_contains($ct, 'text/html')) {
            return $response;
        }

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Content-Security-Policy', $this->buildCsp($nonce));

        return $response;
    }

    private function buildCsp(string $nonce): string
    {
        $directives = [
            // Origen seguro por defecto para cualquier recurso no listado.
            "default-src 'self'",

            // Scripts: origen propio + nonce para inline + CDN + Google Maps.
            // 'unsafe-eval' es requerido por Alpine.js v3 — evalúa expresiones x-data/x-on
            // con new AsyncFunction() internamente. Sin esto Alpine no puede funcionar.
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' https://cdn.jsdelivr.net https://maps.googleapis.com",

            // Estilos: 'unsafe-inline' para los bindings :style de Alpine.js y bloques <style>.
            // IMPORTANTE: NO incluir nonce aquí — según la spec W3C CSP Level 2+, cuando un nonce
            // está presente en style-src, el browser ignora 'unsafe-inline', bloqueando Alpine :style.
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",

            // Fuentes: jsDelivr (Bootstrap Icons) + Bunny Fonts + Google Fonts (gstatic).
            "font-src 'self' https://cdn.jsdelivr.net https://fonts.bunny.net https://fonts.gstatic.com",

            // Imágenes: self + data URIs (QR, previews) + blob + tiles de Google Maps.
            "img-src 'self' data: blob: https://maps.gstatic.com https://*.googleapis.com",

            // Fetch/XHR: self + CDN (source maps en DevTools) + Google Maps API.
            "connect-src 'self' https://cdn.jsdelivr.net https://maps.googleapis.com",

            // Prohibir plugins Flash/Java/etc.
            "object-src 'none'",

            // Evitar que <base> redirija recursos a dominios externos.
            "base-uri 'self'",

            // Formularios solo al propio servidor.
            "form-action 'self'",

            // Nadie puede embeber esta app en un iframe (equivale a X-Frame-Options: DENY).
            "frame-ancestors 'none'",
        ];

        return implode('; ', $directives);
    }

    // Helper estático para acceder al nonce desde código PHP si hiciera falta.
    public static function nonce(): string
    {
        return app()->bound('csp-nonce') ? (string) app('csp-nonce') : '';
    }
}
