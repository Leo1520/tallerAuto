@extends('layouts.public')

@section('title', 'Taller Pro — Taller Automotriz Profesional')
@section('description', 'Mecánica general, mantenimiento, diagnóstico electrónico, chapa y pintura. Tu vehículo en las mejores manos.')

@push('styles')
<style>
/* ── Hero ── */
.hero {
    min-height: calc(100vh - 64px);
    display: flex; align-items: center;
    position: relative; overflow: hidden;
    padding: 80px 24px 60px;
}
.hero-bg {
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 60% 50%, rgba(215,25,32,.08) 0%, transparent 70%),
        radial-gradient(ellipse 50% 80% at 10% 80%, rgba(215,25,32,.05) 0%, transparent 60%);
}
.hero-grid {
    position: absolute; inset: 0; opacity: .03;
    background-image: linear-gradient(var(--pub-border) 1px, transparent 1px),
                      linear-gradient(90deg, var(--pub-border) 1px, transparent 1px);
    background-size: 48px 48px;
}
.hero-inner {
    max-width: 1200px; margin: 0 auto; position: relative; z-index: 1;
    display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
}
@media (max-width: 900px) { .hero-inner { grid-template-columns: 1fr; gap: 40px; } }
.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(215,25,32,.12); border: 1px solid rgba(215,25,32,.25);
    color: #f87171; padding: 6px 14px; border-radius: 100px;
    font-size: 12px; font-weight: 600; margin-bottom: 20px; letter-spacing: .04em;
}
.hero-badge span { width: 6px; height: 6px; background: var(--accent); border-radius: 50%;
    display: inline-block; animation: pulse 2s infinite; }
@keyframes pulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.4; transform:scale(1.4); }
}
.hero h1 {
    font-size: clamp(32px, 5vw, 58px); font-weight: 900; line-height: 1.1;
    letter-spacing: -.03em; color: var(--pub-text); margin-bottom: 16px;
}
.hero h1 em { font-style: normal; color: var(--accent); }
.hero-sub {
    font-size: 16px; color: var(--pub-muted); line-height: 1.7; margin-bottom: 28px; max-width: 480px;
}
.hero-info { display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px; }
.hero-info-item { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #94A3B8; }
.hero-info-item i { color: var(--accent); font-size: 15px; }
.hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-hero-primary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 13px 26px; border-radius: 10px; font-size: 14px; font-weight: 700;
    background: var(--accent); color: #fff; text-decoration: none;
    transition: background .15s, box-shadow .15s; border: none; cursor: pointer;
}
.btn-hero-primary:hover { background: var(--accent-h); box-shadow: 0 6px 24px rgba(215,25,32,.4); }
.btn-hero-outline {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;
    border: 1px solid var(--pub-border); color: var(--pub-text); text-decoration: none;
    transition: .15s; background: transparent;
}
.btn-hero-outline:hover { background: rgba(255,255,255,.05); border-color: #475569; }
.btn-whatsapp {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 22px; border-radius: 10px; font-size: 14px; font-weight: 600;
    background: #25D366; color: #fff; text-decoration: none; transition: .15s;
}
.btn-whatsapp:hover { background: #1ebe59; }

/* ── Hero visual ── */
.hero-visual {
    display: flex; align-items: center; justify-content: center;
}
.hero-car-card {
    background: var(--pub-card); border: 1px solid var(--pub-border);
    border-radius: 20px; padding: 32px; position: relative; overflow: hidden;
    box-shadow: 0 24px 80px rgba(0,0,0,.4);
}
.hero-car-card::before {
    content: ''; position: absolute; top: -40px; right: -40px;
    width: 200px; height: 200px; background: radial-gradient(circle, rgba(215,25,32,.12) 0%, transparent 70%);
}
.stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 24px; }
.stat-item {
    background: rgba(255,255,255,.04); border: 1px solid var(--pub-border);
    border-radius: 12px; padding: 16px; text-align: center;
}
.stat-num { font-size: 28px; font-weight: 900; color: var(--pub-text); line-height: 1; }
.stat-num span { color: var(--accent); }
.stat-label { font-size: 11px; color: var(--pub-muted); margin-top: 4px; font-weight: 500; }

/* ── Services ── */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px; margin-top: 48px;
}
.service-card {
    background: var(--pub-card); border: 1px solid var(--pub-border);
    border-radius: 14px; padding: 24px 20px;
    transition: transform .2s, border-color .2s, box-shadow .2s;
    cursor: default;
}
.service-card:hover {
    transform: translateY(-4px);
    border-color: rgba(215,25,32,.35);
    box-shadow: 0 12px 40px rgba(0,0,0,.35), 0 0 0 1px rgba(215,25,32,.1);
}
.service-icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: rgba(215,25,32,.1); border: 1px solid rgba(215,25,32,.2);
    display: flex; align-items: center; justify-content: center;
    color: var(--accent); font-size: 22px; margin-bottom: 14px;
}
.service-name {
    font-size: 14px; font-weight: 600; color: var(--pub-text); line-height: 1.4;
}

/* ── Stats bar ── */
.stats-bar {
    background: var(--pub-surface); border-top: 1px solid var(--pub-border);
    border-bottom: 1px solid var(--pub-border);
    padding: 40px 24px;
}
.stats-bar-inner {
    max-width: 1200px; margin: 0 auto;
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; text-align: center;
}
@media (max-width: 600px) { .stats-bar-inner { grid-template-columns: repeat(2,1fr); } }
.sbar-num { font-size: 36px; font-weight: 900; color: var(--pub-text); letter-spacing: -.02em; }
.sbar-num span { color: var(--accent); }
.sbar-label { font-size: 13px; color: var(--pub-muted); margin-top: 4px; }

/* ── Map ── */
#landingMap { height: 460px; width: 100%; border-radius: 16px; overflow: hidden; }

/* ── Footer ── */
.pub-footer {
    background: var(--pub-surface); border-top: 1px solid var(--pub-border);
    padding: 48px 24px 28px;
}
.pub-footer-inner { max-width: 1200px; margin: 0 auto; }
.footer-grid {
    display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 48px; margin-bottom: 40px;
}
@media (max-width: 700px) { .footer-grid { grid-template-columns: 1fr; gap: 32px; } }
.footer-link {
    display: block; color: var(--pub-muted); text-decoration: none; font-size: 13.5px;
    padding: 4px 0; transition: color .15s;
}
.footer-link:hover { color: var(--pub-text); }
.footer-contact { display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--pub-muted); padding: 5px 0; }
.footer-contact i { color: var(--accent); font-size: 15px; flex-shrink: 0; }

/* ── Divider ── */
.red-line { height: 3px; background: linear-gradient(90deg, var(--accent) 0%, transparent 100%); }
</style>
@endpush

@section('content')

{{-- ══════════ HERO ══════════ --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-inner">
        <div>
            <div class="hero-badge">
                <span></span> Taller Automotriz Profesional
            </div>
            <h1>
                Nos encargamos<br>
                del cuidado integral<br>
                de <em>tu vehículo</em>
            </h1>
            <p class="hero-sub">
                Servicio de calidad, diagnóstico preciso y atención personalizada. Tu auto en las mejores manos desde el primer día.
            </p>
            <div class="hero-info">
                <div class="hero-info-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Calle Primavera esq. Lluvia de Oro — 5to anillo Radial 26, Santa Cruz, Bolivia</span>
                </div>
                <div class="hero-info-item">
                    <i class="bi bi-telephone-fill"></i>
                    <span>78559066 / 704-07035</span>
                </div>
                <div class="hero-info-item">
                    <i class="bi bi-clock-fill"></i>
                    <span>Lun — Sáb: 8:00 am – 6:00 pm</span>
                </div>
            </div>
            <div class="hero-actions">
                <a href="{{ auth()->check() ? route('cliente.inicio') : route('register') }}" class="btn-hero-primary">
                    <i class="bi bi-calendar-check"></i> Solicitar cita
                </a>
                <a href="https://wa.me/59178559066" target="_blank" class="btn-whatsapp">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
                <a href="#servicios" class="btn-hero-outline">
                    Ver servicios <i class="bi bi-arrow-down"></i>
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-car-card" style="width:100%; max-width:360px;">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                    <div style="width:44px;height:44px;background:rgba(215,25,32,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-tools" style="color:var(--accent);font-size:20px;"></i>
                    </div>
                    <div>
                        <p style="font-size:15px;font-weight:700;color:var(--pub-text);">Taller Pro</p>
                        <p style="font-size:12px;color:var(--pub-muted);">Sistema automotriz</p>
                    </div>
                </div>
                <div class="red-line" style="margin:16px 0;"></div>
                <p style="font-size:12px;color:var(--pub-muted);margin-bottom:4px;">Estado del sistema</p>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:8px;height:8px;background:#10B981;border-radius:50%;display:inline-block;box-shadow:0 0 6px #10B981;"></span>
                    <span style="font-size:13px;font-weight:600;color:#10B981;">Operativo — Atendiendo ahora</span>
                </div>
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-num">{{ $stats['ordenes_activas'] ?? 0 }}<span>+</span></div>
                        <div class="stat-label">Órdenes activas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">{{ $stats['mecanicos'] ?? 0 }}<span>+</span></div>
                        <div class="stat-label">Mecánicos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">{{ $stats['clientes'] ?? 0 }}<span>+</span></div>
                        <div class="stat-label">Clientes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num">{{ $sucursales->count() }}<span>+</span></div>
                        <div class="stat-label">Sucursales</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════ STATS BAR ══════════ --}}
<div class="stats-bar">
    <div class="stats-bar-inner">
        <div>
            <div class="sbar-num">10<span>+</span></div>
            <div class="sbar-label">Años de experiencia</div>
        </div>
        <div>
            <div class="sbar-num">5000<span>+</span></div>
            <div class="sbar-label">Vehículos atendidos</div>
        </div>
        <div>
            <div class="sbar-num">13<span>+</span></div>
            <div class="sbar-label">Tipos de servicios</div>
        </div>
        <div>
            <div class="sbar-num">98<span>%</span></div>
            <div class="sbar-label">Satisfacción del cliente</div>
        </div>
    </div>
</div>

{{-- ══════════ SERVICIOS ══════════ --}}
<section class="pub-section" id="servicios">
    <div class="pub-container">
        <div style="text-align:center; max-width:600px; margin:0 auto 0;">
            <div class="section-label">Nuestros Servicios</div>
            <h2 class="section-title">Todo lo que tu vehículo necesita</h2>
            <p class="section-sub" style="margin:0 auto;">
                Contamos con técnicos especializados y equipos de diagnóstico de última generación para cada tipo de servicio.
            </p>
        </div>
        <div class="services-grid">
            @foreach([
                ['bi-wrench-adjustable',     'Mecánica General'],
                ['bi-calendar2-check',       'Mantenimiento Preventivo'],
                ['bi-droplet-fill',          'Cambio de Aceite'],
                ['bi-disc',                  'Suspensión y Frenos'],
                ['bi-cpu-fill',              'Diagnóstico Electrónico'],
                ['bi-thermometer-snow',      'Aire Acondicionado'],
                ['bi-brush-fill',            'Chapa y Pintura'],
                ['bi-stars',                 'Detailing'],
                ['bi-shield-shaded',         'Ceramic Coating'],
                ['bi-sun-fill',              'Láminas Protección Solar'],
                ['bi-wind',                  'Desinfección / Ozono UV'],
                ['bi-box-arrow-in-down',     'Importación de Autopartes'],
                ['bi-bicycle',               'Motos y ATVs'],
            ] as [$icon, $name])
            <div class="service-card">
                <div class="service-icon">
                    <i class="bi {{ $icon }}"></i>
                </div>
                <div class="service-name">{{ $name }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════ NOSOTROS ══════════ --}}
<section class="pub-section" id="nosotros" style="background:var(--pub-surface); padding-top:80px; padding-bottom:80px;">
    <div class="pub-container">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:center;">
            <div>
                <div class="section-label">¿Por qué elegirnos?</div>
                <h2 class="section-title">Expertos que cuidan tu inversión</h2>
                <p style="font-size:15px; color:var(--pub-muted); line-height:1.8; margin-bottom:28px;">
                    En Taller Pro combinamos tecnología de diagnóstico con años de experiencia para ofrecerte el mejor servicio automotriz de Santa Cruz.
                </p>
                <div style="display:flex;flex-direction:column;gap:16px;">
                    @foreach([
                        ['bi-patch-check-fill', 'Técnicos certificados', 'Personal capacitado en las últimas tecnologías automotrices.'],
                        ['bi-lightning-charge-fill', 'Diagnóstico rápido', 'Equipos electrónicos de última generación para detección precisa.'],
                        ['bi-shield-check', 'Garantía en trabajos', 'Todos nuestros servicios cuentan con garantía por escrito.'],
                        ['bi-geo-alt-fill', 'Múltiples sucursales', 'Encuentra el punto más cercano a ti en Santa Cruz.'],
                    ] as [$icon, $title, $desc])
                    <div style="display:flex;align-items:flex-start;gap:14px;">
                        <div style="width:40px;height:40px;border-radius:10px;background:rgba(215,25,32,.1);border:1px solid rgba(215,25,32,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi {{ $icon }}" style="color:var(--accent);font-size:16px;"></i>
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;color:var(--pub-text);margin-bottom:3px;">{{ $title }}</p>
                            <p style="font-size:13px;color:var(--pub-muted);line-height:1.5;">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                @for($i = 0; $i < 4; $i++)
                <div style="aspect-ratio:1;border-radius:14px;background:var(--pub-card);border:1px solid var(--pub-border);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;color:var(--pub-muted);">
                    <i class="bi bi-image" style="font-size:28px;opacity:.3;"></i>
                    <span style="font-size:11px;opacity:.5;">Foto del taller</span>
                </div>
                @endfor
            </div>
        </div>
    </div>
</section>

{{-- ══════════ MAPA DE SUCURSALES ══════════ --}}
<section class="pub-section" id="mapa">
    <div class="pub-container">
        <div style="text-align:center; max-width:580px; margin:0 auto 40px;">
            <div class="section-label">Sucursales</div>
            <h2 class="section-title">Encuéntranos cerca de ti</h2>
            <p class="section-sub" style="margin:0 auto;">
                Tenemos múltiples puntos de atención en Santa Cruz. El mapa muestra tu ubicación y las sucursales activas.
            </p>
        </div>

        <div id="landingMap"></div>

        @if($sucursales->isNotEmpty())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;margin-top:20px;">
            @foreach($sucursales as $suc)
            <div onclick="centerMapTo({{ $suc->latitud }}, {{ $suc->longitud }}, {{ $loop->index }})"
                 style="background:var(--pub-card);border:1px solid var(--pub-border);border-radius:12px;padding:16px 18px;cursor:pointer;transition:.15s;"
                 onmouseover="this.style.borderColor='rgba(215,25,32,.4)'" onmouseout="this.style.borderColor='var(--pub-border)'">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:36px;height:36px;border-radius:9px;background:rgba(215,25,32,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-geo-alt-fill" style="color:var(--accent);"></i>
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:700;color:var(--pub-text);margin-bottom:2px;">{{ $suc->nombre }}</p>
                        <p style="font-size:12px;color:var(--pub-muted);">{{ $suc->ciudad }}</p>
                    </div>
                </div>
                @if($suc->direccion)
                <p style="font-size:12px;color:var(--pub-muted);margin-top:10px;padding-top:10px;border-top:1px solid var(--pub-border);">
                    <i class="bi bi-pin-map" style="margin-right:4px;color:var(--accent);"></i>{{ $suc->direccion }}
                </p>
                @endif
                @if($suc->telefono)
                <p style="font-size:12px;color:var(--pub-muted);margin-top:6px;">
                    <i class="bi bi-telephone" style="margin-right:4px;color:var(--accent);"></i>{{ $suc->telefono }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ══════════ CONTACTO / CTA ══════════ --}}
<section class="pub-section" id="contacto" style="background:var(--pub-surface);padding-top:80px;padding-bottom:80px;">
    <div class="pub-container" style="text-align:center; max-width:640px; margin:0 auto;">
        <div class="section-label">Contacto</div>
        <h2 class="section-title">¿Listo para agendar tu cita?</h2>
        <p class="section-sub" style="margin:0 auto 32px;">
            Regístrate para agendar tu cita en línea, hacer seguimiento de tu vehículo y más. O contáctanos directamente.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ auth()->check() ? route('cliente.inicio') : route('register') }}" class="btn-hero-primary">
                <i class="bi bi-calendar-plus"></i> Agendar cita
            </a>
            <a href="https://wa.me/59178559066" target="_blank" class="btn-whatsapp">
                <i class="bi bi-whatsapp"></i> Escribir por WhatsApp
            </a>
        </div>
        <div style="margin-top:40px;display:flex;justify-content:center;gap:32px;flex-wrap:wrap;">
            <div style="text-align:center;">
                <i class="bi bi-telephone-fill" style="font-size:20px;color:var(--accent);"></i>
                <p style="font-size:13px;color:var(--pub-muted);margin-top:6px;">78559066 / 704-07035</p>
            </div>
            <div style="text-align:center;">
                <i class="bi bi-envelope-fill" style="font-size:20px;color:var(--accent);"></i>
                <p style="font-size:13px;color:var(--pub-muted);margin-top:6px;">contacto@tallerpro.bo</p>
            </div>
            <div style="text-align:center;">
                <i class="bi bi-clock-fill" style="font-size:20px;color:var(--accent);"></i>
                <p style="font-size:13px;color:var(--pub-muted);margin-top:6px;">Lun–Sáb: 8:00 – 18:00</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════ FOOTER ══════════ --}}
<footer class="pub-footer">
    <div class="pub-footer-inner">
        <div class="footer-grid">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:36px;height:36px;background:var(--accent);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-tools" style="color:#fff;font-size:15px;"></i>
                    </div>
                    <span style="font-size:17px;font-weight:800;color:var(--pub-text);">Taller Pro</span>
                </div>
                <p style="font-size:13px;color:var(--pub-muted);line-height:1.7;max-width:280px;">
                    Tu taller automotriz de confianza en Santa Cruz de la Sierra, Bolivia.
                </p>
                <div style="display:flex;gap:10px;margin-top:16px;">
                    <a href="#" style="width:34px;height:34px;border-radius:8px;background:var(--pub-card);border:1px solid var(--pub-border);display:flex;align-items:center;justify-content:center;color:var(--pub-muted);text-decoration:none;transition:.15s;" onmouseover="this.style.color='var(--pub-text)'" onmouseout="this.style.color='var(--pub-muted)'">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" style="width:34px;height:34px;border-radius:8px;background:var(--pub-card);border:1px solid var(--pub-border);display:flex;align-items:center;justify-content:center;color:var(--pub-muted);text-decoration:none;transition:.15s;" onmouseover="this.style.color='var(--pub-text)'" onmouseout="this.style.color='var(--pub-muted)'">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://wa.me/59178559066" target="_blank" style="width:34px;height:34px;border-radius:8px;background:var(--pub-card);border:1px solid var(--pub-border);display:flex;align-items:center;justify-content:center;color:var(--pub-muted);text-decoration:none;transition:.15s;" onmouseover="this.style.color='#25D366'" onmouseout="this.style.color='var(--pub-muted)'">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
            </div>
            <div>
                <p style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--pub-muted);margin-bottom:14px;">Servicios</p>
                @foreach(['Mecánica General','Mantenimiento','Diagnóstico Electrónico','Chapa y Pintura','Detailing','Aire Acondicionado'] as $s)
                <a href="#servicios" class="footer-link">{{ $s }}</a>
                @endforeach
            </div>
            <div>
                <p style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--pub-muted);margin-bottom:14px;">Contacto</p>
                <div class="footer-contact"><i class="bi bi-geo-alt-fill"></i>Calle Primavera esq. Lluvia de Oro, SCZ</div>
                <div class="footer-contact"><i class="bi bi-telephone-fill"></i>78559066 / 704-07035</div>
                <div class="footer-contact"><i class="bi bi-envelope-fill"></i>contacto@tallerpro.bo</div>
                <div class="footer-contact"><i class="bi bi-clock-fill"></i>Lun–Sáb: 8:00 – 18:00</div>
            </div>
        </div>
        <div style="border-top:1px solid var(--pub-border);padding-top:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <p style="font-size:12px;color:var(--pub-muted);">&copy; {{ date('Y') }} Taller Pro — Todos los derechos reservados</p>
            <div style="display:flex;gap:16px;">
                <a href="{{ route('login') }}" style="font-size:12px;color:var(--pub-muted);text-decoration:none;">Panel administrativo</a>
            </div>
        </div>
    </div>
</footer>

@endsection

@push('scripts')
<script>
const SUCURSALES = @json($sucursales);
const GMAPS_KEY  = '{{ config('services.google.maps_key') }}';
const MAP_STYLES = [
    { elementType:'geometry', stylers:[{color:'#1d2433'}] },
    { elementType:'labels.text.fill', stylers:[{color:'#8ec3b9'}] },
    { elementType:'labels.text.stroke', stylers:[{color:'#1a3646'}] },
    { featureType:'road', elementType:'geometry', stylers:[{color:'#304a7d'}] },
    { featureType:'road', elementType:'labels.text.fill', stylers:[{color:'#98a5be'}] },
    { featureType:'water', elementType:'geometry', stylers:[{color:'#0e1626'}] },
    { featureType:'poi', elementType:'geometry', stylers:[{color:'#283d6a'}] },
    { featureType:'poi', elementType:'labels.text.fill', stylers:[{color:'#6f9ba5'}] },
    { featureType:'transit', elementType:'geometry', stylers:[{color:'#2f3948'}] },
    { featureType:'administrative', elementType:'geometry.stroke', stylers:[{color:'#4b6878'}] },
];

let map, markers = [], openInfoWindow = null;

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

    // Geolocalización del usuario
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const userPos = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            new google.maps.Marker({
                map, position: userPos, title: 'Tu ubicación',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE, scale: 9,
                    fillColor: '#4285F4', fillOpacity: 1,
                    strokeColor: '#fff', strokeWeight: 2,
                },
                zIndex: 0,
            });
            bounds.extend(userPos);
            if (SUCURSALES.length > 0) map.fitBounds(bounds, { padding: 60 });
        }, () => {}, { timeout: 8000 });
    }

    SUCURSALES.forEach((suc, i) => {
        const pos = { lat: parseFloat(suc.latitud), lng: parseFloat(suc.longitud) };
        bounds.extend(pos);

        const marker = new google.maps.Marker({
            map, position: pos, title: suc.nombre,
            animation: google.maps.Animation.DROP,
            icon: {
                path: google.maps.SymbolPath.CIRCLE, scale: 11,
                fillColor: '#D71920', fillOpacity: 1,
                strokeColor: '#ffffff', strokeWeight: 2.5,
            },
        });

        const iwContent = `
            <div style="min-width:200px;padding:14px 16px;font-family:system-ui,sans-serif;background:#1a2236;color:#f1f5f9;border-radius:10px;">
                <p style="font-weight:700;font-size:14px;margin:0 0 4px;">${suc.nombre}</p>
                ${suc.ciudad ? `<p style="font-size:12px;color:#94a3b8;margin:0 0 4px;">${suc.ciudad}</p>` : ''}
                ${suc.direccion ? `<p style="font-size:12px;color:#94a3b8;margin:0 0 10px;">${suc.direccion}</p>` : ''}
                <a href="https://www.google.com/maps/dir/?api=1&destination=${suc.latitud},${suc.longitud}"
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#D71920;color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600;">
                    Cómo llegar
                </a>
            </div>`;

        const iw = new google.maps.InfoWindow({ content: iwContent });
        marker.addListener('click', () => {
            if (openInfoWindow) openInfoWindow.close();
            iw.open(map, marker);
            openInfoWindow = iw;
        });
        markers.push({ marker, iw });
    });

    if (SUCURSALES.length > 1) map.fitBounds(bounds, { padding: 60 });
}

function centerMapTo(lat, lng, idx) {
    map.panTo({ lat: parseFloat(lat), lng: parseFloat(lng) });
    map.setZoom(16);
    if (openInfoWindow) openInfoWindow.close();
    if (markers[idx]) {
        markers[idx].iw.open(map, markers[idx].marker);
        openInfoWindow = markers[idx].iw;
    }
    document.getElementById('mapa').scrollIntoView({ behavior: 'smooth' });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initLandingMap" async defer></script>
@endpush
