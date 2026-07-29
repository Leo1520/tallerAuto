@extends('layouts.cliente')

@section('title', 'Agendar Cita')

@push('styles')
<style>
#citaMap { height: 280px; border-radius: 12px; overflow: hidden; }

input[type=radio].sucursal-radio:checked + .suc-option {
    border-color: var(--c-accent) !important;
    background: rgba(215,25,32,.06);
}
.suc-option:hover { border-color: rgba(215,25,32,.35) !important; }

/* InfoWindow override */
.gm-style .gm-style-iw-c { background: #1a2236 !important; border-radius: 10px !important; }
.gm-style .gm-style-iw-d { overflow: hidden !important; }
.gm-style .gm-style-iw-t::after { background: #1a2236 !important; }
.gm-style-iw-tc::after { background: #1a2236 !important; }
.gm-ui-hover-effect > span { background-color: #94A3B8 !important; }
</style>
@endpush

@section('content')

<div style="margin-bottom:24px;">
    <a href="{{ route('cliente.citas.index') }}" style="font-size:13px;color:var(--c-muted);text-decoration:none;">
        <i class="bi bi-arrow-left" style="margin-right:4px;"></i> Mis citas
    </a>
    <h1 style="font-size:20px;font-weight:800;color:var(--c-text);margin-top:8px;">Nueva cita</h1>
    <p style="font-size:13px;color:var(--c-muted);">Elige sucursal, servicio, fecha y hora.</p>
</div>

<div style="max-width:720px;">
    <form method="POST" action="{{ route('cliente.citas.store') }}">
        @csrf

        {{-- ── Sucursal + Mapa ── --}}
        <div class="c-card" style="margin-bottom:16px;">
            <p style="font-size:14px;font-weight:700;color:var(--c-text);margin-bottom:14px;">
                <i class="bi bi-geo-alt-fill" style="color:var(--c-accent);margin-right:6px;"></i>Sucursal *
            </p>

            {{-- Tarjetas radio --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:10px;margin-bottom:16px;">
                @foreach($sucursales as $suc)
                <label style="cursor:pointer;">
                    <input type="radio" name="sucursal_id" value="{{ $suc->id }}" required
                           {{ old('sucursal_id') == $suc->id ? 'checked' : '' }}
                           style="display:none;" class="sucursal-radio"
                           data-lat="{{ $suc->latitud }}" data-lng="{{ $suc->longitud }}"
                           data-idx="{{ $loop->index }}">
                    <div class="suc-option" id="suc-card-{{ $suc->id }}"
                         style="border:1.5px solid var(--c-border);border-radius:10px;padding:12px 14px;transition:.15s;">
                        <p style="font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:3px;">{{ $suc->nombre }}</p>
                        <p style="font-size:11px;color:var(--c-muted);">{{ $suc->ciudad }}</p>
                        @if($suc->direccion)
                        <p style="font-size:11px;color:var(--c-muted);margin-top:1px;">{{ $suc->direccion }}</p>
                        @endif
                        @if($suc->telefono)
                        <p style="font-size:11px;color:var(--c-muted);margin-top:1px;">
                            <i class="bi bi-telephone" style="margin-right:3px;"></i>{{ $suc->telefono }}
                        </p>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            {{-- Mapa --}}
            <div id="citaMap"></div>
            <p style="font-size:11px;color:var(--c-muted);margin-top:8px;">
                <i class="bi bi-info-circle" style="margin-right:4px;"></i>Haz clic en un marcador para seleccionar la sucursal.
            </p>

            @error('sucursal_id')<p style="font-size:12px;color:#f87171;margin-top:8px;">{{ $message }}</p>@enderror
        </div>

        {{-- ── Servicio ── --}}
        <div class="c-card" style="margin-bottom:16px;">
            <label class="c-label">Servicio (opcional)</label>
            <select name="servicio_id" class="c-select">
                <option value="">— Sin especificar —</option>
                @foreach($servicios as $s)
                <option value="{{ $s->id }}"
                    {{ old('servicio_id', $servicioId) == $s->id ? 'selected' : '' }}>
                    {{ $s->nombre }}{{ $s->precio ? ' — Bs ' . number_format($s->precio, 2) : '' }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- ── Vehículo ── --}}
        @if($vehiculos->isNotEmpty())
        <div class="c-card" style="margin-bottom:16px;">
            <label class="c-label">Vehículo (opcional)</label>
            <select name="vehiculo_id" class="c-select">
                <option value="">— Sin especificar —</option>
                @foreach($vehiculos as $v)
                <option value="{{ $v->id }}" {{ old('vehiculo_id') == $v->id ? 'selected' : '' }}>
                    {{ $v->modelo->marca->nombre ?? '' }} {{ $v->modelo->nombre ?? '' }} · {{ $v->placa }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- ── Fecha y hora ── --}}
        <div class="c-card" style="margin-bottom:16px;">
            <p style="font-size:14px;font-weight:700;color:var(--c-text);margin-bottom:14px;">
                <i class="bi bi-clock" style="color:var(--c-accent);margin-right:6px;"></i>Fecha y hora
            </p>
            <div class="c-form-grid">
                <div>
                    <label class="c-label">Fecha *</label>
                    <input type="date" name="fecha" class="c-input"
                           value="{{ old('fecha') }}"
                           min="{{ today()->toDateString() }}" required>
                    @error('fecha')<p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="c-label">Hora *</label>
                    <select name="hora" class="c-select" required>
                        <option value="">— Selecciona —</option>
                        @foreach(['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30'] as $h)
                        <option value="{{ $h }}:00" {{ old('hora') === "{$h}:00" ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                    @error('hora')<p style="font-size:12px;color:#f87171;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ── Notas ── --}}
        <div class="c-card" style="margin-bottom:24px;">
            <label class="c-label">Notas adicionales</label>
            <textarea name="notas" class="c-input" rows="3"
                      placeholder="Describe brevemente el problema o lo que necesitas..."
                      style="resize:none;">{{ old('notas') }}</textarea>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn-red">
                <i class="bi bi-calendar-check"></i> Confirmar cita
            </button>
            <a href="{{ route('cliente.citas.index') }}" class="btn-outline">Cancelar</a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
const SUCURSALES = @json($sucursales);
const MAP_STYLES = [
    { elementType:'geometry', stylers:[{color:'#1d2433'}] },
    { elementType:'labels.text.fill', stylers:[{color:'#8ec3b9'}] },
    { elementType:'labels.text.stroke', stylers:[{color:'#1a3646'}] },
    { featureType:'road', elementType:'geometry', stylers:[{color:'#304a7d'}] },
    { featureType:'road', elementType:'labels.text.fill', stylers:[{color:'#98a5be'}] },
    { featureType:'water', elementType:'geometry', stylers:[{color:'#0e1626'}] },
    { featureType:'poi', elementType:'geometry', stylers:[{color:'#283d6a'}] },
    { featureType:'transit', elementType:'geometry', stylers:[{color:'#2f3948'}] },
    { featureType:'administrative', elementType:'geometry.stroke', stylers:[{color:'#4b6878'}] },
];

let map, markers = [], openIw = null;

function initCitaMap() {
    const withCoords = SUCURSALES.filter(s => s.latitud && s.longitud);
    const center = withCoords.length
        ? { lat: parseFloat(withCoords[0].latitud), lng: parseFloat(withCoords[0].longitud) }
        : { lat: -17.7833, lng: -63.1821 };

    map = new google.maps.Map(document.getElementById('citaMap'), {
        center, zoom: withCoords.length > 1 ? 12 : 14,
        mapTypeControl: false, streetViewControl: false, fullscreenControl: false,
        styles: MAP_STYLES,
    });

    const bounds = new google.maps.LatLngBounds();

    // Ubicación del usuario
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const p = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            new google.maps.Marker({
                map, position: p, title: 'Tu ubicación', zIndex: 0,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE, scale: 8,
                    fillColor: '#4285F4', fillOpacity: 1,
                    strokeColor: '#fff', strokeWeight: 2,
                },
            });
            bounds.extend(p);
            if (withCoords.length) map.fitBounds(bounds, { padding: 60 });
        }, () => {}, { timeout: 8000 });
    }

    withCoords.forEach((suc, i) => {
        const pos = { lat: parseFloat(suc.latitud), lng: parseFloat(suc.longitud) };
        bounds.extend(pos);

        const marker = new google.maps.Marker({
            map, position: pos, title: suc.nombre,
            animation: google.maps.Animation.DROP,
            icon: {
                path: google.maps.SymbolPath.CIRCLE, scale: 11,
                fillColor: '#D71920', fillOpacity: 1,
                strokeColor: '#fff', strokeWeight: 2.5,
            },
        });

        const iw = new google.maps.InfoWindow({
            content: `<div style="padding:12px 14px;background:#1a2236;color:#f1f5f9;border-radius:10px;min-width:180px;font-family:system-ui,sans-serif;">
                <p style="font-weight:700;font-size:13px;margin:0 0 4px;">${suc.nombre}</p>
                ${suc.ciudad ? `<p style="font-size:11px;color:#94a3b8;margin:0 0 2px;">${suc.ciudad}</p>` : ''}
                ${suc.direccion ? `<p style="font-size:11px;color:#94a3b8;margin:0 0 8px;">${suc.direccion}</p>` : ''}
                <button onclick="selectSucursal(${suc.id})"
                    style="padding:6px 12px;background:#D71920;color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;width:100%;">
                    <i class='bi bi-check2'></i> Seleccionar
                </button>
            </div>`,
        });

        marker.addListener('click', () => {
            if (openIw) openIw.close();
            iw.open(map, marker);
            openIw = iw;
            selectSucursal(suc.id);
        });

        markers.push({ marker, iw, id: suc.id });
    });

    if (withCoords.length > 1) map.fitBounds(bounds, { padding: 60 });
}

function selectSucursal(id) {
    // Marcar el radio correspondiente
    document.querySelectorAll('.sucursal-radio').forEach(r => {
        if (parseInt(r.value) === id) r.checked = true;
    });
    // Scroll suave a la tarjeta
    const card = document.getElementById('suc-card-' + id);
    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Al hacer clic en una tarjeta radio, centrar mapa en esa sucursal
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sucursal-radio').forEach(r => {
        r.addEventListener('change', () => {
            const lat = parseFloat(r.dataset.lat);
            const lng = parseFloat(r.dataset.lng);
            if (lat && lng && map) {
                map.panTo({ lat, lng });
                map.setZoom(15);
                const found = markers.find(m => m.id === parseInt(r.value));
                if (found) {
                    if (openIw) openIw.close();
                    found.iw.open(map, found.marker);
                    openIw = found.iw;
                }
            }
        });
    });
});
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initCitaMap" async defer></script>
@endpush
