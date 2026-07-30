@extends('layouts.public')

@section('title', 'Tienda — Taller Pro')
@section('description', 'Servicios automotrices y repuestos disponibles en Taller Pro.')

@push('styles')
<style>
.tienda-hero {
    padding: 60px 24px 40px;
    background: radial-gradient(ellipse 80% 50% at 50% 0%, rgba(215,25,32,.07) 0%, transparent 70%);
    border-bottom: 1px solid var(--pub-border);
    text-align: center;
}
.tab-bar {
    display: flex; gap: 4px;
    background: var(--pub-surface); border: 1px solid var(--pub-border);
    border-radius: 12px; padding: 4px; width: fit-content; margin: 0 auto;
}
.tab-btn {
    display: flex; align-items: center; gap: 7px;
    padding: 9px 20px; border-radius: 9px; font-size: 13.5px; font-weight: 600;
    border: none; cursor: pointer; text-decoration: none; transition: .15s;
    color: var(--pub-muted); background: transparent;
}
.tab-btn.active {
    background: var(--accent); color: #fff;
    box-shadow: 0 4px 14px rgba(215,25,32,.3);
}
.tab-btn:not(.active):hover { background: rgba(255,255,255,.05); color: var(--pub-text); }

.search-bar {
    display: flex; align-items: center; gap: 10px;
    background: var(--pub-card); border: 1px solid var(--pub-border);
    border-radius: 10px; padding: 10px 16px; max-width: 420px; margin: 20px auto 0;
}
.search-bar input {
    background: none; border: none; outline: none; flex: 1;
    color: var(--pub-text); font-size: 14px;
}
.search-bar input::placeholder { color: var(--pub-muted); }
.search-bar i { color: var(--pub-muted); font-size: 16px; }

.filter-chips { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin-top: 16px; }
.chip {
    padding: 5px 14px; border-radius: 100px; font-size: 12px; font-weight: 600;
    border: 1px solid var(--pub-border); color: var(--pub-muted);
    text-decoration: none; transition: .15s; background: transparent;
}
.chip:hover, .chip.active { border-color: var(--accent); color: var(--accent); background: rgba(215,25,32,.06); }

.grid-3 {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px; margin-top: 32px;
}
.product-card {
    background: var(--pub-card); border: 1px solid var(--pub-border);
    border-radius: 14px; overflow: hidden;
    transition: transform .2s, border-color .2s, box-shadow .2s;
    display: flex; flex-direction: column;
}
.product-card:hover {
    transform: translateY(-3px);
    border-color: rgba(215,25,32,.3);
    box-shadow: 0 10px 36px rgba(0,0,0,.3);
}
.product-thumb {
    height: 130px; display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.02); border-bottom: 1px solid var(--pub-border);
}
.product-body { padding: 16px; flex: 1; display: flex; flex-direction: column; }
.product-tag {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
    color: var(--accent); margin-bottom: 8px;
}
.product-name { font-size: 14px; font-weight: 700; color: var(--pub-text); margin-bottom: 6px; line-height: 1.4; }
.product-desc { font-size: 12px; color: var(--pub-muted); line-height: 1.6; flex: 1; }
.product-footer {
    padding: 12px 16px; border-top: 1px solid var(--pub-border);
    display: flex; align-items: center; justify-content: space-between;
}
.price { font-size: 16px; font-weight: 900; color: var(--pub-text); }
.price span { font-size: 11px; font-weight: 500; color: var(--pub-muted); }
.btn-agendar {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 700;
    background: var(--accent); color: #fff; text-decoration: none;
    border: none; cursor: pointer; transition: .15s;
}
.btn-agendar:hover { background: var(--accent-h); }
.stock-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 100px;
}
.in-stock  { background: rgba(16,185,129,.12); color: #34D399; }
.no-stock  { background: rgba(100,116,139,.12); color: #94A3B8; }
.empty-state {
    text-align: center; padding: 64px 24px;
    color: var(--pub-muted);
}
.empty-state i { font-size: 44px; opacity: .3; margin-bottom: 14px; display: block; }
</style>
@endpush

@section('content')

{{-- Hero / Tabs --}}
<div class="tienda-hero">
    <div class="section-label" style="justify-content:center;">Taller Pro</div>
    <h1 class="section-title" style="margin-bottom:6px;">Tienda</h1>
    <p class="section-sub" style="margin:0 auto 24px;">Servicios y productos disponibles en nuestras sucursales.</p>

    {{-- Tabs --}}
    <div class="tab-bar">
        <a href="{{ route('tienda', ['tab' => 'servicios'] + ($buscar ? ['q' => $buscar] : [])) }}"
           class="tab-btn {{ $tab === 'servicios' ? 'active' : '' }}">
            <i class="bi bi-wrench-adjustable"></i> Servicios
            <span style="background:rgba(255,255,255,.2);border-radius:100px;padding:1px 7px;font-size:11px;">{{ $servicios->count() }}</span>
        </a>
        <a href="{{ route('tienda', ['tab' => 'productos'] + ($buscar ? ['q' => $buscar] : [])) }}"
           class="tab-btn {{ $tab === 'productos' ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Productos
            <span style="background:rgba(255,255,255,.2);border-radius:100px;padding:1px 7px;font-size:11px;">{{ $repuestos->count() }}</span>
        </a>
    </div>

    {{-- Búsqueda --}}
    <form method="GET" action="{{ route('tienda') }}">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="search-bar">
            <i class="bi bi-search"></i>
            <input type="text" name="q" value="{{ $buscar }}"
                   placeholder="{{ $tab === 'servicios' ? 'Buscar servicio...' : 'Buscar producto o código...' }}">
            @if($buscar)
            <a href="{{ route('tienda', ['tab' => $tab]) }}" style="color:var(--pub-muted);text-decoration:none;font-size:14px;">
                <i class="bi bi-x-lg"></i>
            </a>
            @endif
        </div>
    </form>

    {{-- Filtros por tipo (solo en servicios) --}}
    @if($tab === 'servicios' && $tiposServicio->isNotEmpty())
    <div class="filter-chips">
        <a href="{{ route('tienda', array_filter(['tab' => 'servicios', 'q' => $buscar])) }}"
           class="chip {{ !request('tipo') ? 'active' : '' }}">Todos</a>
        @foreach($tiposServicio as $tipo)
        <a href="{{ route('tienda', array_filter(['tab' => 'servicios', 'tipo' => $tipo->id, 'q' => $buscar])) }}"
           class="chip {{ request('tipo') == $tipo->id ? 'active' : '' }}">{{ $tipo->nombre }}</a>
        @endforeach
    </div>
    @endif
</div>

{{-- Contenido --}}
<div class="pub-section" style="padding-top:40px;">
    <div class="pub-container">

        @if($tab === 'servicios')
        {{-- ── Servicios ── --}}
        @if($servicios->isNotEmpty())
        <div class="grid-3">
            @foreach($servicios as $srv)
            <div class="product-card">
                <div class="product-thumb">
                    @php
                        $icon = match(strtolower($srv->tipoServicio?->nombre ?? '')) {
                            'diagnóstico', 'diagnostico' => 'bi-cpu-fill',
                            'mantenimiento'              => 'bi-calendar2-check',
                            'pintura', 'chapa y pintura' => 'bi-brush-fill',
                            'detailing'                  => 'bi-stars',
                            'suspensión', 'suspension'   => 'bi-disc',
                            'aire acondicionado'         => 'bi-thermometer-snow',
                            default                      => 'bi-wrench-adjustable',
                        };
                    @endphp
                    <i class="bi {{ $icon }}" style="font-size:48px;color:var(--accent);opacity:.6;"></i>
                </div>
                <div class="product-body">
                    @if($srv->tipoServicio)
                    <div class="product-tag">
                        <i class="bi bi-tag-fill"></i> {{ $srv->tipoServicio->nombre }}
                    </div>
                    @endif
                    <p class="product-name">{{ $srv->nombre }}</p>
                    @if($srv->descripcion)
                    <p class="product-desc">{{ Str::limit($srv->descripcion, 80) }}</p>
                    @endif
                    @if($srv->tiempo_estimado)
                    <p style="font-size:11px;color:var(--pub-muted);margin-top:8px;">
                        <i class="bi bi-clock" style="margin-right:3px;"></i>{{ $srv->tiempo_estimado }} min aprox.
                    </p>
                    @endif
                </div>
                <div class="product-footer">
                    <div>
                        <div class="price">Bs {{ number_format($srv->precio, 2) }}</div>
                    </div>
                    <a href="{{ auth()->check() ? route('cliente.citas.create') . '?servicio_id=' . $srv->id : route('register') }}"
                       class="btn-agendar">
                        <i class="bi bi-calendar-plus"></i> Agendar
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-wrench"></i>
            <p style="font-size:15px;font-weight:700;color:var(--pub-text);margin-bottom:6px;">
                {{ $buscar ? 'Sin resultados para "' . $buscar . '"' : 'No hay servicios disponibles' }}
            </p>
            <p style="font-size:13px;">Pronto agregaremos más servicios.</p>
        </div>
        @endif

        @else
        {{-- ── Productos / Repuestos ── --}}
        @if($repuestos->isNotEmpty())
        <div class="grid-3">
            @foreach($repuestos as $rep)
            @php
                $stockTotal = $rep->inventarios->sum('stock');
            @endphp
            <div class="product-card">
                <div class="product-thumb">
                    <i class="bi bi-box-seam-fill" style="font-size:48px;color:#A78BFA;opacity:.5;"></i>
                </div>
                <div class="product-body">
                    @if($rep->codigo)
                    <div class="product-tag" style="color:#A78BFA;">
                        <i class="bi bi-upc"></i> {{ $rep->codigo }}
                    </div>
                    @endif
                    <p class="product-name">{{ $rep->nombre }}</p>
                    @if($rep->descripcion)
                    <p class="product-desc">{{ Str::limit($rep->descripcion, 80) }}</p>
                    @endif
                    <div style="margin-top:10px;">
                        <span class="stock-badge {{ $stockTotal > 0 ? 'in-stock' : 'no-stock' }}">
                            <i class="bi bi-circle-fill" style="font-size:6px;"></i>
                            {{ $stockTotal > 0 ? "En stock ({$stockTotal})" : 'Sin stock' }}
                        </span>
                    </div>
                </div>
                <div class="product-footer">
                    <div>
                        <div class="price">
                            Bs {{ number_format($rep->precio_venta, 2) }}
                            <span>/ unid.</span>
                        </div>
                    </div>
                    @if($stockTotal > 0)
                    <a href="{{ auth()->check() ? route('cliente.consultas.repuesto', $rep) : route('login', ['redirect' => route('cliente.consultas.repuesto', $rep)]) }}"
                       class="btn-agendar" style="background:#7C3AED;">
                        <i class="bi bi-cart-plus"></i> Comprar
                    </a>
                    @else
                    <span style="font-size:12px;color:var(--pub-muted);">No disponible</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-box-seam"></i>
            <p style="font-size:15px;font-weight:700;color:var(--pub-text);margin-bottom:6px;">
                {{ $buscar ? 'Sin resultados para "' . $buscar . '"' : 'No hay productos disponibles' }}
            </p>
            <p style="font-size:13px;">El catálogo de productos se irá actualizando.</p>
        </div>
        @endif
        @endif

        {{-- CTA --}}
        <div style="margin-top:48px;text-align:center;padding:32px;background:var(--pub-surface);border:1px solid var(--pub-border);border-radius:16px;">
            <i class="bi bi-headset" style="font-size:28px;color:var(--accent);margin-bottom:10px;display:block;"></i>
            <p style="font-size:15px;font-weight:700;color:var(--pub-text);margin-bottom:6px;">¿No encuentras lo que buscas?</p>
            <p style="font-size:13px;color:var(--pub-muted);margin-bottom:18px;">Contáctanos y te asesoramos sin compromiso.</p>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <a href="https://wa.me/59178559066" target="_blank"
                   style="display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:9px;background:#25D366;color:#fff;text-decoration:none;font-size:13px;font-weight:700;">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
                <a href="{{ auth()->check() ? route('cliente.citas.create') : route('register') }}"
                   style="display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:9px;background:var(--accent);color:#fff;text-decoration:none;font-size:13px;font-weight:700;">
                    <i class="bi bi-calendar-plus"></i> Agendar cita
                </a>
            </div>
        </div>

    </div>
</div>

@endsection
