@extends('layouts.public')
@section('title', 'Taller Pro — Taller Automotriz Profesional')
@section('description', 'Mecánica general, mantenimiento, diagnóstico electrónico, chapa y pintura. Santa Cruz, Bolivia.')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap">
<style>
/* ── TOKEN OVERRIDES ── */
:root {
    --pub-bg:      #000;
    --pub-surface: #0D0D0D;
    --pub-card:    #111;
    --pub-border:  #1c1c1c;
    --pub-muted:   #5a5a5a;
    --pub-text:    #fff;
}

/* ── HERO ── */
.tp-hero {
    position: relative;
    min-height: 100vh;
    overflow: hidden;
    cursor: crosshair;
}

/* Image layers — full bleed */
.hero-img-base,
.hero-img-top {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
}
.hero-img-base {
    z-index: 0;
    background-image: url('/images/car-exploded.jpg');
}
.hero-img-top {
    z-index: 1;
    background-image: url('/images/car-built.jpg');
    will-change: -webkit-mask-image, mask-image;
}

/* Gradient veil so text stays readable on any image */
.hero-vignette {
    position: absolute;
    inset: 0;
    z-index: 2;
    background:
        linear-gradient(to right,  rgba(0,0,0,.88) 0%, rgba(0,0,0,.55) 55%, rgba(0,0,0,.1) 100%),
        linear-gradient(to top,    rgba(0,0,0,.6)  0%, transparent 50%);
    pointer-events: none;
}

/* Red left stripe */
.hero-stripe {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: var(--accent);
    z-index: 3;
}

/* Reveal hint */
.hero-hint {
    position: absolute;
    bottom: 36px;
    right: 48px;
    z-index: 4;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgba(255,255,255,.3);
    pointer-events: none;
    transition: opacity .3s;
}
.hero-hint i { font-size: 14px; animation: cursor-blink 1.6s infinite; }
@keyframes cursor-blink { 0%,100%{opacity:1} 50%{opacity:.2} }
.tp-hero.revealing .hero-hint { opacity: 0; }

/* Text content — overlaid on z-index 3 */
.hero-content {
    position: relative;
    z-index: 4;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 100px 72px 72px;
    max-width: 680px;
    pointer-events: none; /* let mouse events pass to hero for reveal */
}
.hero-content a,
.hero-content button { pointer-events: auto; }

@media (max-width: 800px) {
    .hero-content { padding: 100px 24px 60px; max-width: 100%; }
    .hero-hint { right: 24px; }
    .tp-cta-row { flex-wrap: wrap; }
}

/* Eyebrow */
.tp-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 14px;
}
.tp-eyebrow .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--accent);
    animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
    0%,100% { opacity:1; transform:scale(1); }
    50% { opacity:.3; transform:scale(1.4); }
}

/* Main headline */
.tp-headline {
    font-family: 'Bebas Neue', 'Impact', 'Arial Black', sans-serif;
    line-height: .90;
    letter-spacing: .015em;
    margin-bottom: 28px;
}
.tp-headline .line-taller {
    display: block;
    font-size: clamp(72px, 11vw, 148px);
    color: #fff;
}
.tp-headline .line-pro {
    display: block;
    font-size: clamp(92px, 14vw, 190px);
    color: var(--accent);
    margin-left: -3px;
    text-shadow: 0 0 100px rgba(215,25,32,.12);
}

/* Sub */
.tp-sub {
    font-size: 15px;
    color: var(--pub-muted);
    line-height: 1.75;
    max-width: 400px;
    margin-bottom: 28px;
}
@media (max-width: 800px) { .tp-sub { margin: 0 auto 24px; } }

/* Info items */
.tp-info-col {
    display: flex;
    flex-direction: column;
    gap: 9px;
    margin-bottom: 32px;
}
.tp-info-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 13px;
    color: #666;
}
.tp-info-item i { color: var(--accent); margin-top: 2px; flex-shrink: 0; }

/* CTA buttons */
.tp-cta-row { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-tp-primary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 28px;
    background: var(--accent);
    color: #fff;
    font-size: 12px; font-weight: 700;
    text-decoration: none;
    letter-spacing: .06em;
    text-transform: uppercase;
    transition: background .15s, box-shadow .2s;
    clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
}
.btn-tp-primary:hover {
    background: #ff2128;
    box-shadow: 0 0 40px rgba(215,25,32,.45);
    color: #fff;
}
.btn-tp-outline {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 24px;
    background: transparent;
    color: #bbb;
    font-size: 12px; font-weight: 600;
    text-decoration: none;
    letter-spacing: .06em;
    text-transform: uppercase;
    border: 1px solid #2a2a2a;
    transition: border-color .15s, color .15s;
}
.btn-tp-outline:hover { border-color: #555; color: #fff; }
.btn-tp-wa {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 22px;
    background: #25D366;
    color: #fff;
    font-size: 12px; font-weight: 700;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: .06em;
    transition: background .15s;
}
.btn-tp-wa:hover { background: #1db954; color: #fff; }

/* (image reveal is handled by hero-img-base / hero-img-top above) */

/* ── STATS ── */
.tp-stats {
    background: #0D0D0D;
    border-top: 1px solid #1c1c1c;
    border-bottom: 1px solid #1c1c1c;
}
.tp-stats-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    background: #1c1c1c;
    gap: 1px;
}
@media (max-width: 600px) { .tp-stats-inner { grid-template-columns: repeat(2, 1fr); } }
.tp-stat {
    background: #0D0D0D;
    padding: 48px 32px;
    text-align: center;
}
.tp-stat-num {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: clamp(44px, 5.5vw, 68px);
    color: #fff;
    line-height: 1;
    letter-spacing: .02em;
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 2px;
}
.tp-stat-num .suffix { color: var(--accent); font-size: .85em; }
.tp-stat-num .counter { display: inline-block; }
.tp-stat-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #3a3a3a;
    margin-top: 10px;
}

/* ── SERVICES ── */
.tp-services {
    background: #000;
    padding: 100px 24px;
}
.tp-services-inner { max-width: 1200px; margin: 0 auto; }

.tp-section-eyebrow {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 16px;
}
.tp-section-title {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: clamp(48px, 7vw, 96px);
    color: #fff;
    line-height: .92;
    letter-spacing: .01em;
    margin-bottom: 56px;
}
.tp-section-title em { font-style: normal; color: var(--accent); }

.tp-services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    background: #1c1c1c;
    gap: 1px;
}
.tp-service-card {
    background: #000;
    padding: 30px 22px;
    position: relative;
    overflow: hidden;
    transition: background .2s;
    cursor: default;
}
.tp-service-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    background: var(--accent);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .35s cubic-bezier(.4,0,.2,1);
}
.tp-service-card:hover { background: #0a0a0a; }
.tp-service-card:hover::after { transform: scaleX(1); }
.tp-service-icon {
    font-size: 22px;
    color: var(--accent);
    margin-bottom: 12px;
    display: block;
    opacity: .85;
}
.tp-service-name {
    font-size: 13px;
    font-weight: 600;
    color: #ddd;
    line-height: 1.4;
}

/* ── WHY ── */
.tp-why {
    background: #0D0D0D;
    padding: 100px 24px;
    border-top: 1px solid #1c1c1c;
    border-bottom: 1px solid #1c1c1c;
}
.tp-why-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: start;
}
@media (max-width: 900px) { .tp-why-inner { grid-template-columns: 1fr; gap: 48px; } }

.tp-why-features { display: flex; flex-direction: column; gap: 0; }
.tp-feature {
    display: grid;
    grid-template-columns: 52px 1fr;
    gap: 0 20px;
    padding: 28px 0;
    border-bottom: 1px solid #1c1c1c;
    align-items: start;
}
.tp-feature:first-child { border-top: 1px solid #1c1c1c; }
.tp-feature-num {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: 28px;
    color: var(--accent);
    opacity: .5;
    line-height: 1;
    padding-top: 2px;
}
.tp-feature-title {
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 6px;
}
.tp-feature-desc {
    font-size: 13px;
    color: #555;
    line-height: 1.6;
}

/* ── STATEMENT ── */
.tp-statement {
    background: var(--accent);
    padding: 90px 24px;
    text-align: center;
}
.tp-statement-inner { max-width: 1000px; margin: 0 auto; }
.tp-statement-text {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: clamp(28px, 4.5vw, 60px);
    color: #fff;
    line-height: 1.1;
    letter-spacing: .02em;
}
.tp-statement-text .dim { opacity: .6; }
.tp-statement-sub {
    font-size: 14px;
    color: rgba(255,255,255,.6);
    margin-top: 20px;
    letter-spacing: .04em;
    text-transform: uppercase;
    font-weight: 600;
}

/* ── MAP ── */
.tp-map-section {
    background: #000;
    padding: 100px 0 0;
}
.tp-map-header {
    max-width: 1200px;
    margin: 0 auto 48px;
    padding: 0 24px;
}
#landingMap { height: 480px; width: 100%; }
.tp-branch-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    background: #1c1c1c;
    gap: 1px;
    margin-top: 1px;
}
.tp-branch-card {
    background: #000;
    padding: 22px 24px;
    cursor: pointer;
    transition: background .15s;
    display: flex;
    align-items: center;
    gap: 14px;
}
.tp-branch-card:hover { background: #0D0D0D; }
.tp-branch-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: rgba(215,25,32,.1);
    border: 1px solid rgba(215,25,32,.2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.tp-branch-icon i { color: var(--accent); font-size: 15px; }
.tp-branch-name { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 3px; }
.tp-branch-addr { font-size: 12px; color: #555; }

/* ── CTA ── */
.tp-cta-section {
    background: #0D0D0D;
    padding: 120px 24px;
    text-align: center;
    border-top: 1px solid #1c1c1c;
}
.tp-cta-title {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: clamp(52px, 8vw, 108px);
    color: #fff;
    line-height: .92;
    margin-bottom: 28px;
    letter-spacing: .01em;
}
.tp-cta-title em { font-style: normal; color: var(--accent); }
.tp-cta-sub {
    font-size: 15px;
    color: #555;
    margin-bottom: 40px;
    max-width: 440px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.7;
}
.tp-cta-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.tp-cta-contacts {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    margin-top: 56px;
    padding-top: 40px;
    border-top: 1px solid #1c1c1c;
}
.tp-contact-item { text-align: center; }
.tp-contact-item i { font-size: 20px; color: var(--accent); display: block; margin-bottom: 8px; }
.tp-contact-item p { font-size: 13px; color: #555; }

/* ── FOOTER ── */
.tp-footer {
    background: #000;
    border-top: 1px solid #1c1c1c;
    padding: 64px 24px 28px;
}
.tp-footer-inner { max-width: 1200px; margin: 0 auto; }
.tp-footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 48px;
    padding-bottom: 48px;
    border-bottom: 1px solid #1c1c1c;
    margin-bottom: 28px;
}
@media (max-width: 700px) { .tp-footer-grid { grid-template-columns: 1fr; gap: 32px; } }
.tp-footer-logo {
    display: flex; align-items: center; gap: 10px;
    text-decoration: none; margin-bottom: 14px;
}
.tp-footer-logo-icon {
    width: 36px; height: 36px;
    background: var(--accent);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
.tp-footer-logo-icon i { color: #fff; font-size: 15px; }
.tp-footer-logo-text {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: 22px;
    color: #fff;
    letter-spacing: .06em;
}
.tp-footer-tagline { font-size: 13px; color: #3a3a3a; line-height: 1.7; max-width: 260px; margin-bottom: 20px; }
.tp-footer-socials { display: flex; gap: 8px; }
.tp-footer-social {
    width: 34px; height: 34px;
    border-radius: 7px;
    background: #111;
    border: 1px solid #1c1c1c;
    display: flex; align-items: center; justify-content: center;
    color: #444;
    text-decoration: none;
    transition: color .15s, border-color .15s;
}
.tp-footer-social:hover { color: #fff; border-color: #333; }
.tp-footer-col-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: #2a2a2a;
    margin-bottom: 16px;
}
.tp-footer-link {
    display: block; color: #3a3a3a; text-decoration: none;
    font-size: 13px; padding: 4px 0; transition: color .15s;
}
.tp-footer-link:hover { color: #fff; }
.tp-footer-contact-item {
    display: flex; align-items: flex-start; gap: 8px;
    font-size: 13px; color: #3a3a3a; padding: 4px 0;
}
.tp-footer-contact-item i { color: var(--accent); margin-top: 2px; flex-shrink: 0; }

/* ── SCROLL REVEAL ── */
.reveal {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity .7s cubic-bezier(.4,0,.2,1), transform .7s cubic-bezier(.4,0,.2,1);
}
.reveal.visible { opacity: 1; transform: none; }
.reveal-d1 { transition-delay: .08s; }
.reveal-d2 { transition-delay: .16s; }
.reveal-d3 { transition-delay: .24s; }
.reveal-d4 { transition-delay: .32s; }
.reveal-d5 { transition-delay: .40s; }
.reveal-d6 { transition-delay: .48s; }

/* ── reduced motion ── */
@media (prefers-reduced-motion: reduce) {
    .reveal, .tp-feature, .car-layer { transition: none !important; }
}
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════
     HERO — full-screen cursor reveal
════════════════════════════════════════════ --}}
<section class="tp-hero" id="heroSection">

    {{-- Layer 1 (bottom): car-exploded — visible through the "scratch" --}}
    <div class="hero-img-base" id="heroBase"></div>

    {{-- Layer 2 (top): car-built — default visible; cursor cuts a hole --}}
    <div class="hero-img-top" id="heroTop"></div>

    {{-- Gradient veil for text readability --}}
    <div class="hero-vignette"></div>
    <div class="hero-stripe"></div>

    {{-- Hint badge --}}
    <div class="hero-hint" id="heroHint">
        <i class="bi bi-cursor-fill"></i> Pasa el cursor para descubrir
    </div>

    {{-- All text overlaid --}}
    <div class="hero-content">
        <div class="tp-eyebrow">
            <span class="dot"></span>
            Taller Automotriz — Santa Cruz, Bolivia
        </div>

        <div class="tp-headline">
            <span class="line-taller">TALLER</span>
            <span class="line-pro">PRO</span>
        </div>

        <p class="tp-sub">
            Mecánica general, diagnóstico electrónico y mantenimiento profesional.
            Tu vehículo en manos que conocen cada pieza.
        </p>

        <div class="tp-info-col">
            <div class="tp-info-item">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Calle Primavera esq. Lluvia de Oro — 5to anillo Radial 26</span>
            </div>
            <div class="tp-info-item">
                <i class="bi bi-telephone-fill"></i>
                <span>78559066 / 704-07035</span>
            </div>
            <div class="tp-info-item">
                <i class="bi bi-clock-fill"></i>
                <span>Lun — Sáb: 8:00 am – 6:00 pm</span>
            </div>
        </div>

        <div class="tp-cta-row">
            <a href="{{ auth()->check() ? route('cliente.inicio') : route('register') }}" class="btn-tp-primary">
                <i class="bi bi-calendar-check"></i> Solicitar cita
            </a>
            <a href="https://wa.me/59178559066" target="_blank" class="btn-tp-wa">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </a>
            <a href="#servicios" class="btn-tp-outline">
                Ver servicios <i class="bi bi-arrow-down"></i>
            </a>
        </div>
    </div>

</section>

{{-- ══════════════════════════════════════════
     STATS
════════════════════════════════════════════ --}}
<div class="tp-stats">
    <div class="tp-stats-inner">
        <div class="tp-stat">
            <div class="tp-stat-num">
                <span class="counter" data-target="10">0</span><span class="suffix">+</span>
            </div>
            <div class="tp-stat-label">Años de experiencia</div>
        </div>
        <div class="tp-stat">
            <div class="tp-stat-num">
                <span class="counter" data-target="5000">0</span><span class="suffix">+</span>
            </div>
            <div class="tp-stat-label">Vehículos atendidos</div>
        </div>
        <div class="tp-stat">
            <div class="tp-stat-num">
                <span class="counter" data-target="{{ $stats['mecanicos'] ?? 8 }}">0</span><span class="suffix">+</span>
            </div>
            <div class="tp-stat-label">Mecánicos expertos</div>
        </div>
        <div class="tp-stat">
            <div class="tp-stat-num">
                <span class="counter" data-target="98">0</span><span class="suffix">%</span>
            </div>
            <div class="tp-stat-label">Satisfacción del cliente</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     SERVICES
════════════════════════════════════════════ --}}
<section class="tp-services" id="servicios">
    <div class="tp-services-inner">
        <div class="tp-section-eyebrow reveal">Lo que hacemos</div>
        <div class="tp-section-title reveal">
            TODO LO QUE<br>
            TU AUTO <em>NECESITA</em>
        </div>

        <div class="tp-services-grid">
            @foreach([
                ['bi-wrench-adjustable',     'Mecánica General'],
                ['bi-calendar2-check',       'Mantenimiento Preventivo'],
                ['bi-droplet-fill',          'Cambio de Aceite'],
                ['bi-disc',                  'Suspensión y Frenos'],
                ['bi-cpu-fill',              'Diagnóstico Electrónico'],
                ['bi-thermometer-snow',      'Aire Acondicionado'],
                ['bi-brush-fill',            'Chapa y Pintura'],
                ['bi-stars',                 'Detailing Profesional'],
                ['bi-shield-shaded',         'Ceramic Coating'],
                ['bi-sun-fill',              'Láminas Protección Solar'],
                ['bi-wind',                  'Desinfección UV / Ozono'],
                ['bi-box-arrow-in-down',     'Importación de Autopartes'],
                ['bi-bicycle',               'Motos y ATVs'],
            ] as $i => [$icon, $name])
            <div class="tp-service-card reveal reveal-d{{ min($i % 6 + 1, 6) }}">
                <i class="bi {{ $icon }} tp-service-icon"></i>
                <div class="tp-service-name">{{ $name }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     WHY
════════════════════════════════════════════ --}}
<section class="tp-why" id="nosotros">
    <div class="tp-why-inner">
        <div>
            <div class="tp-section-eyebrow reveal">¿Por qué elegirnos?</div>
            <div class="tp-section-title reveal">
                EXPERTOS<br>
                EN CADA<br>
                <em>DETALLE</em>
            </div>
            <p class="reveal" style="font-size:14px; color:#555; line-height:1.8; max-width:380px;">
                En Taller Pro combinamos tecnología de diagnóstico de última generación con más de una década de experiencia
                para ofrecerte el servicio automotriz más completo de Santa Cruz.
            </p>
        </div>

        <div class="tp-why-features">
            @foreach([
                ['01', 'Técnicos certificados', 'Personal capacitado en las últimas tecnologías automotrices, actualizado constantemente.'],
                ['02', 'Diagnóstico de precisión', 'Equipos electrónicos de última generación para detección exacta de fallas.'],
                ['03', 'Garantía por escrito', 'Todos nuestros servicios incluyen garantía documentada en tiempo y forma.'],
                ['04', 'Transparencia total', 'Te explicamos qué se hará y por qué antes de comenzar cualquier trabajo.'],
            ] as [$num, $title, $desc])
            <div class="tp-feature reveal">
                <div class="tp-feature-num">{{ $num }}</div>
                <div>
                    <div class="tp-feature-title">{{ $title }}</div>
                    <div class="tp-feature-desc">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     STATEMENT
════════════════════════════════════════════ --}}
<div class="tp-statement">
    <div class="tp-statement-inner">
        <div class="tp-statement-text">
            <span class="dim">TU VEHÍCULO ES UNA INVERSIÓN.</span>
            CUÍDALA CON QUIENES LA ENTIENDEN.
        </div>
        <p class="tp-statement-sub">Más de 5000 vehículos atendidos — Santa Cruz de la Sierra</p>
    </div>
</div>

{{-- ══════════════════════════════════════════
     SUCURSALES / MAPA
════════════════════════════════════════════ --}}
<section class="tp-map-section" id="mapa">
    <div class="tp-map-header">
        <div class="tp-section-eyebrow reveal">Ubicaciones</div>
        <div class="tp-section-title reveal" style="margin-bottom:8px;">
            CERCA DE <em>TI</em>
        </div>
        <p class="reveal" style="font-size:14px; color:#555; max-width:480px; line-height:1.7;">
            Múltiples sucursales en Santa Cruz. Haz click en una tarjeta para ver la dirección exacta en el mapa.
        </p>
    </div>

    <div id="landingMap"></div>

    @if($sucursales->isNotEmpty())
    <div class="tp-branch-grid">
        @foreach($sucursales as $suc)
        <div class="tp-branch-card"
             onclick="centerMapTo({{ $suc->latitud }}, {{ $suc->longitud }}, {{ $loop->index }})">
            <div class="tp-branch-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <div class="tp-branch-name">{{ $suc->nombre }}</div>
                <div class="tp-branch-addr">{{ $suc->ciudad }}{{ $suc->direccion ? ' — ' . Str::limit($suc->direccion, 40) : '' }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>

{{-- ══════════════════════════════════════════
     CTA
════════════════════════════════════════════ --}}
<section class="tp-cta-section" id="contacto">
    <div class="tp-cta-title reveal">
        AGENDA TU<br>
        <em>CITA HOY</em>
    </div>
    <p class="tp-cta-sub reveal">
        Regístrate, solicita tu cita en línea y haz seguimiento de tu vehículo desde cualquier dispositivo.
    </p>
    <div class="tp-cta-btns reveal">
        <a href="{{ auth()->check() ? route('cliente.inicio') : route('register') }}" class="btn-tp-primary">
            <i class="bi bi-calendar-plus"></i> Solicitar cita
        </a>
        <a href="{{ route('tienda') }}" class="btn-tp-outline">
            <i class="bi bi-shop-window"></i> Ver tienda
        </a>
        <a href="https://wa.me/59178559066" target="_blank" class="btn-tp-wa">
            <i class="bi bi-whatsapp"></i> Escribir por WhatsApp
        </a>
    </div>

    <div class="tp-cta-contacts">
        <div class="tp-contact-item">
            <i class="bi bi-telephone-fill"></i>
            <p>78559066 / 704-07035</p>
        </div>
        <div class="tp-contact-item">
            <i class="bi bi-envelope-fill"></i>
            <p>contacto@tallerpro.bo</p>
        </div>
        <div class="tp-contact-item">
            <i class="bi bi-clock-fill"></i>
            <p>Lun–Sáb: 8:00 – 18:00</p>
        </div>
        <div class="tp-contact-item">
            <i class="bi bi-geo-alt-fill"></i>
            <p>Santa Cruz de la Sierra, Bolivia</p>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     FOOTER
════════════════════════════════════════════ --}}
<footer class="tp-footer">
    <div class="tp-footer-inner">
        <div class="tp-footer-grid">

            <div>
                <a href="{{ url('/') }}" class="tp-footer-logo">
                    <div class="tp-footer-logo-icon">
                        <i class="bi bi-tools"></i>
                    </div>
                    <span class="tp-footer-logo-text">TALLER PRO</span>
                </a>
                <p class="tp-footer-tagline">
                    Tu taller automotriz de confianza en Santa Cruz de la Sierra, Bolivia. Más de 10 años cuidando tu inversión.
                </p>
                <div class="tp-footer-socials">
                    <a href="#" class="tp-footer-social"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="tp-footer-social"><i class="bi bi-instagram"></i></a>
                    <a href="https://wa.me/59178559066" target="_blank" class="tp-footer-social" style="color:#25D366;border-color:#1a3a1a;">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
            </div>

            <div>
                <div class="tp-footer-col-title">Servicios</div>
                @foreach(['Mecánica General','Mantenimiento','Diagnóstico Electrónico','Chapa y Pintura','Detailing','Aire Acondicionado'] as $s)
                <a href="#servicios" class="tp-footer-link">{{ $s }}</a>
                @endforeach
            </div>

            <div>
                <div class="tp-footer-col-title">Contacto</div>
                <div class="tp-footer-contact-item">
                    <i class="bi bi-geo-alt-fill"></i><span>Calle Primavera esq. Lluvia de Oro, SCZ</span>
                </div>
                <div class="tp-footer-contact-item">
                    <i class="bi bi-telephone-fill"></i><span>78559066 / 704-07035</span>
                </div>
                <div class="tp-footer-contact-item">
                    <i class="bi bi-envelope-fill"></i><span>contacto@tallerpro.bo</span>
                </div>
                <div class="tp-footer-contact-item">
                    <i class="bi bi-clock-fill"></i><span>Lun–Sáb: 8:00 – 18:00</span>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <p style="font-size:12px;color:#222;">&copy; {{ date('Y') }} Taller Pro — Todos los derechos reservados</p>
            <a href="{{ route('login') }}" style="font-size:12px;color:#222;text-decoration:none;">Panel administrativo</a>
        </div>
    </div>
</footer>

@endsection

@push('scripts')
<script>
/* ─── HERO CURSOR REVEAL (scratch-card effect) ─── */
(function() {
    const hero    = document.getElementById('heroSection');
    const topLayer = document.getElementById('heroTop');
    const hint    = document.getElementById('heroHint');
    if (!hero || !topLayer) return;

    const RADIUS = 200; // px — hole size

    function applyMask(x, y, r) {
        const mask = `radial-gradient(circle ${r}px at ${x}px ${y}px, transparent 0%, transparent 70%, black 100%)`;
        topLayer.style.webkitMaskImage = mask;
        topLayer.style.maskImage = mask;
    }

    function clearMask() {
        topLayer.style.webkitMaskImage = 'none';
        topLayer.style.maskImage = 'none';
    }

    /* ── Desktop: follow cursor ── */
    const isTouch = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;

    if (!isTouch) {
        hero.addEventListener('mousemove', (e) => {
            const rect = hero.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            hero.classList.add('revealing');
            applyMask(x, y, RADIUS);
        });

        hero.addEventListener('mouseleave', () => {
            hero.classList.remove('revealing');
            clearMask();
        });
    }

    /* ── Mobile / no cursor: animate the reveal circle automatically ── */
    if (isTouch) {
        hint.innerHTML = '<i class="bi bi-hand-index-fill"></i> Toca para descubrir';

        let raf;
        let t = 0;
        const W = () => hero.offsetWidth;
        const H = () => hero.offsetHeight;

        function autoAnimate() {
            t += 0.007;
            // Lemniscate-style path so it sweeps the whole image
            const x = W() * (0.5 + 0.38 * Math.cos(t));
            const y = H() * (0.5 + 0.30 * Math.sin(t * 1.3));
            applyMask(x, y, RADIUS * 1.2);
            raf = requestAnimationFrame(autoAnimate);
        }

        autoAnimate();

        /* Pause on touch and follow finger */
        hero.addEventListener('touchmove', (e) => {
            cancelAnimationFrame(raf);
            hero.classList.add('revealing');
            const rect = hero.getBoundingClientRect();
            const touch = e.touches[0];
            applyMask(touch.clientX - rect.left, touch.clientY - rect.top, RADIUS * 1.4);
        }, { passive: true });

        hero.addEventListener('touchend', () => {
            hero.classList.remove('revealing');
            autoAnimate();
        });
    }
})();

/* ─── SCROLL REVEAL ─── */
(function() {
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
})();

/* ─── STATS COUNTERS ─── */
(function() {
    const counters = document.querySelectorAll('.counter[data-target]');
    if (!counters.length) return;

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el     = e.target;
            const target = parseInt(el.dataset.target, 10);
            const duration = 1200;
            const start  = performance.now();
            obs.unobserve(el);

            function tick(now) {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 3); // ease-out cubic
                el.textContent = Math.round(ease * target).toLocaleString('es-BO');
                if (progress < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        });
    }, { threshold: 0.5 });

    counters.forEach(c => obs.observe(c));
})();

/* ─── GOOGLE MAPS ─── */
const SUCURSALES = @json($sucursales);
const MAP_STYLES = [
    { elementType:'geometry',            stylers:[{color:'#0a0a0a'}] },
    { elementType:'labels.text.fill',    stylers:[{color:'#555'}] },
    { elementType:'labels.text.stroke',  stylers:[{color:'#000'}] },
    { featureType:'road', elementType:'geometry',         stylers:[{color:'#1a1a1a'}] },
    { featureType:'road', elementType:'labels.text.fill', stylers:[{color:'#444'}] },
    { featureType:'water',       elementType:'geometry',  stylers:[{color:'#000'}] },
    { featureType:'poi',         elementType:'geometry',  stylers:[{color:'#111'}] },
    { featureType:'poi',         elementType:'labels.text.fill', stylers:[{color:'#333'}] },
    { featureType:'transit',     elementType:'geometry',  stylers:[{color:'#111'}] },
    { featureType:'administrative', elementType:'geometry.stroke', stylers:[{color:'#222'}] },
];

let map, markers = [], openIW = null;

function initLandingMap() {
    const defaultCenter = SUCURSALES.length
        ? { lat: parseFloat(SUCURSALES[0].latitud), lng: parseFloat(SUCURSALES[0].longitud) }
        : { lat: -17.7833, lng: -63.1821 };

    map = new google.maps.Map(document.getElementById('landingMap'), {
        center: defaultCenter,
        zoom: SUCURSALES.length > 1 ? 12 : 14,
        mapTypeControl: false, streetViewControl: false, fullscreenControl: false,
        styles: MAP_STYLES,
    });

    const bounds = new google.maps.LatLngBounds();

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const p = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            new google.maps.Marker({
                map, position: p, title: 'Tu ubicación',
                icon: { path: google.maps.SymbolPath.CIRCLE, scale: 9, fillColor:'#4285F4', fillOpacity:1, strokeColor:'#fff', strokeWeight:2 },
                zIndex: 0,
            });
            bounds.extend(p);
            if (SUCURSALES.length) map.fitBounds(bounds, { padding: 80 });
        }, () => {}, { timeout: 8000 });
    }

    SUCURSALES.forEach((suc, i) => {
        const pos = { lat: parseFloat(suc.latitud), lng: parseFloat(suc.longitud) };
        bounds.extend(pos);

        const marker = new google.maps.Marker({
            map, position: pos, title: suc.nombre,
            animation: google.maps.Animation.DROP,
            icon: { path: google.maps.SymbolPath.CIRCLE, scale: 12, fillColor:'#D71920', fillOpacity:1, strokeColor:'#fff', strokeWeight:2.5 },
        });

        const iw = new google.maps.InfoWindow({
            content: `<div style="min-width:200px;padding:14px 16px;font-family:system-ui,sans-serif;background:#111;color:#f1f5f9;border-radius:8px;">
                <p style="font-weight:700;font-size:14px;margin:0 0 4px;">${suc.nombre}</p>
                ${suc.ciudad ? `<p style="font-size:12px;color:#555;margin:0 0 8px;">${suc.ciudad}</p>` : ''}
                ${suc.direccion ? `<p style="font-size:12px;color:#555;margin:0 0 12px;">${suc.direccion}</p>` : ''}
                <a href="https://www.google.com/maps/dir/?api=1&destination=${suc.latitud},${suc.longitud}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#D71920;color:#fff;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;">
                   Cómo llegar
                </a></div>`,
        });

        marker.addListener('click', () => {
            if (openIW) openIW.close();
            iw.open(map, marker);
            openIW = iw;
        });
        markers.push({ marker, iw });
    });

    if (SUCURSALES.length > 1) map.fitBounds(bounds, { padding: 80 });
}

function centerMapTo(lat, lng, idx) {
    map.panTo({ lat: parseFloat(lat), lng: parseFloat(lng) });
    map.setZoom(16);
    if (openIW) openIW.close();
    if (markers[idx]) { markers[idx].iw.open(map, markers[idx].marker); openIW = markers[idx].iw; }
    document.getElementById('mapa').scrollIntoView({ behavior: 'smooth' });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initLandingMap" async defer></script>
@endpush
