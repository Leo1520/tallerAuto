@extends('layouts.cliente')

@section('title', 'Agendar Cita')

@push('styles')
<style>
#citaMap { height: 260px; border-radius: 12px; overflow: hidden; }

/* Tarjetas radio de sucursal */
input[type=radio].sucursal-radio:checked + .suc-option {
    border-color: var(--c-accent) !important;
    background: rgba(215,25,32,.07);
}
.suc-option:hover { border-color: rgba(215,25,32,.35) !important; }

/* Tarjetas de vehículo existente */
input[type=radio].veh-radio:checked + .veh-card {
    border-color: var(--c-accent) !important;
    background: rgba(215,25,32,.07);
}
.veh-card { cursor:pointer; transition: border-color .15s, background .15s; }
.veh-card:hover { border-color: rgba(215,25,32,.35) !important; }

/* Toggle modo vehículo */
.veh-toggle-btn {
    flex:1; padding:9px 16px; font-size:13px; font-weight:600;
    border-radius:9px; border: 1.5px solid var(--c-border);
    background: transparent; color: var(--c-muted); cursor:pointer; transition:.15s;
}
.veh-toggle-btn.active {
    border-color: var(--c-accent); color: var(--c-accent);
    background: rgba(215,25,32,.07);
}

/* Campos de formulario */
.f-input {
    width:100%; padding:10px 14px; border-radius:9px;
    background: rgba(255,255,255,.04); border:1.5px solid var(--c-border);
    color: var(--c-text); font-size:14px; transition: border-color .15s;
    box-sizing: border-box;
}
.f-input:focus { outline:none; border-color: var(--c-accent); }
.f-input.error { border-color: #ef4444; }
.f-select {
    width:100%; padding:10px 14px; border-radius:9px;
    background: var(--c-bg); border:1.5px solid var(--c-border);
    color: var(--c-text); font-size:14px; appearance:none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748B' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 14px center;
    padding-right: 36px; transition: border-color .15s;
}
.f-select:focus { outline:none; border-color: var(--c-accent); }
.f-select.error { border-color: #ef4444; }
.f-label { display:block; font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--c-muted); margin-bottom:6px; }
.f-error { font-size:12px; color:#f87171; margin-top:5px; display:flex; align-items:center; gap:4px; }
.f-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:520px) { .f-grid-2 { grid-template-columns:1fr; } }
.f-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
@media(max-width:620px) { .f-grid-3 { grid-template-columns:1fr 1fr; } }
@media(max-width:400px) { .f-grid-3 { grid-template-columns:1fr; } }

/* Sección card */
.form-card {
    background: var(--c-card); border:1px solid var(--c-border);
    border-radius:14px; padding:22px 24px; margin-bottom:16px;
}
.form-card-title {
    font-size:14px; font-weight:700; color:var(--c-text);
    margin-bottom:18px; display:flex; align-items:center; gap:8px;
}
.form-card-title i { color:var(--c-accent); font-size:16px; }

/* InfoWindow Google Maps */
.gm-style .gm-style-iw-c { background:#1a2236 !important; border-radius:10px !important; }
.gm-style .gm-style-iw-d { overflow:hidden !important; }
.gm-style-iw-tc::after { background:#1a2236 !important; }
.gm-ui-hover-effect > span { background-color:#94A3B8 !important; }
</style>
@endpush

@section('content')

<div style="margin-bottom:22px;">
    <a href="{{ route('cliente.citas.index') }}" style="font-size:13px;color:var(--c-muted);text-decoration:none;">
        <i class="bi bi-arrow-left" style="margin-right:4px;"></i> Mis citas
    </a>
    <h1 style="font-size:20px;font-weight:800;color:var(--c-text);margin-top:8px;">Nueva cita</h1>
    <p style="font-size:13px;color:var(--c-muted);">Registra tu vehículo, elige sucursal, servicio, fecha y hora.</p>
</div>

{{-- Errores globales --}}
@if($errors->any())
<div class="alert alert-danger d-flex align-items-start gap-2 mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
    <div>
        <strong>Revisa los siguientes campos:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $err)
                <li style="font-size:13px;">{{ $err }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div style="max-width:760px;">
<form method="POST" action="{{ route('cliente.citas.store') }}">
    @csrf

    {{-- ══════════════════════════════════════════════════════════
         1. VEHÍCULO
    ══════════════════════════════════════════════════════════ --}}
    <div class="form-card"
         x-data="vehiculoForm()"
         x-init="init()">

        <p class="form-card-title">
            <i class="bi bi-car-front-fill"></i>
            Vehículo *
        </p>

        {{-- Toggle: solo si hay vehículos existentes --}}
        @if($vehiculos->isNotEmpty())
        <div style="display:flex;gap:8px;margin-bottom:18px;">
            <button type="button" class="veh-toggle-btn" :class="modo==='existente' ? 'active' : ''"
                    @click="modo='existente'">
                <i class="bi bi-car-front" style="margin-right:5px;"></i> Mis vehículos
            </button>
            <button type="button" class="veh-toggle-btn" :class="modo==='nuevo' ? 'active' : ''"
                    @click="modo='nuevo'">
                <i class="bi bi-plus-circle" style="margin-right:5px;"></i> Registrar nuevo
            </button>
        </div>
        @endif

        <input type="hidden" name="nuevo_vehiculo" :value="modo === 'nuevo' ? '1' : '0'">

        {{-- ── Vehículos existentes ── --}}
        @if($vehiculos->isNotEmpty())
        <div x-show="modo === 'existente'" x-transition>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:10px;">
                @foreach($vehiculos as $v)
                <label style="cursor:pointer;">
                    <input type="radio" name="vehiculo_id" value="{{ $v->id }}"
                           class="veh-radio"
                           style="display:none;"
                           {{ old('vehiculo_id', request('vehiculo')) == $v->id ? 'checked' : '' }}>
                    <div class="veh-card" style="border:1.5px solid var(--c-border);border-radius:11px;padding:14px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                            <div style="width:38px;height:38px;border-radius:9px;background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-car-front-fill" style="color:#34D399;font-size:16px;"></i>
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:700;color:var(--c-text);line-height:1.2;">
                                    {{ $v->modelo->marca->nombre ?? '—' }} {{ $v->modelo->nombre ?? '' }}
                                </p>
                                <p style="font-size:11px;color:var(--c-muted);">{{ $v->placa }}</p>
                            </div>
                        </div>
                        <div style="display:flex;gap:10px;font-size:11px;color:var(--c-muted);">
                            @if($v->ano)<span><i class="bi bi-calendar3" style="margin-right:2px;"></i>{{ $v->ano }}</span>@endif
                            @if($v->color)<span><i class="bi bi-palette" style="margin-right:2px;"></i>{{ $v->color }}</span>@endif
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('vehiculo_id')
                <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
            @enderror
        </div>
        @endif

        {{-- ── Nuevo vehículo ── --}}
        <div x-show="modo === 'nuevo'" x-transition>
            <div style="display:grid;gap:14px;">

                {{-- Marca + Modelo en cascada --}}
                <div class="f-grid-2">
                    <div>
                        <label class="f-label">Marca *</label>
                        <select name="marca_id"
                                class="f-select {{ $errors->has('marca_id') ? 'error' : '' }}"
                                x-model="marcaId"
                                @change="cargarModelos()">
                            <option value="">— Selecciona marca —</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                    {{ $marca->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('marca_id')
                            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="f-label">Modelo *</label>
                        <select name="modelo_id"
                                class="f-select {{ $errors->has('modelo_id') ? 'error' : '' }}"
                                x-model="modeloId">
                            <option value="">— Selecciona modelo —</option>
                            <template x-for="m in modelos" :key="m.id">
                                <option :value="m.id" :selected="m.id == {{ old('modelo_id', 0) }}" x-text="m.nombre"></option>
                            </template>
                        </select>
                        @error('modelo_id')
                            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Placa + Año --}}
                <div class="f-grid-2">
                    <div>
                        <label class="f-label">Placa *</label>
                        <input type="text" name="placa"
                               value="{{ old('placa') }}"
                               placeholder="Ej: ABC-123"
                               class="f-input {{ $errors->has('placa') ? 'error' : '' }}"
                               style="text-transform:uppercase;">
                        @error('placa')
                            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="f-label">Año</label>
                        <input type="number" name="ano"
                               value="{{ old('ano') }}"
                               placeholder="{{ date('Y') }}"
                               min="1960" max="{{ date('Y') + 1 }}"
                               class="f-input {{ $errors->has('ano') ? 'error' : '' }}">
                        @error('ano')
                            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Color + Combustible --}}
                <div class="f-grid-2">
                    <div>
                        <label class="f-label">Color</label>
                        <input type="text" name="color"
                               value="{{ old('color') }}"
                               placeholder="Ej: Blanco"
                               class="f-input {{ $errors->has('color') ? 'error' : '' }}">
                        @error('color')
                            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="f-label">Tipo de combustible</label>
                        <select name="combustible"
                                class="f-select {{ $errors->has('combustible') ? 'error' : '' }}">
                            <option value="">— Selecciona —</option>
                            @foreach(['Gasolina','Diesel','Gas Natural','Eléctrico','Híbrido'] as $c)
                                <option value="{{ $c }}" {{ old('combustible') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                        @error('combustible')
                            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Kilometraje + VIN --}}
                <div class="f-grid-2">
                    <div>
                        <label class="f-label">Kilometraje</label>
                        <input type="number" name="kilometraje"
                               value="{{ old('kilometraje') }}"
                               placeholder="0"
                               min="0"
                               class="f-input {{ $errors->has('kilometraje') ? 'error' : '' }}">
                        @error('kilometraje')
                            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="f-label">VIN <span style="font-size:10px;color:var(--c-muted);text-transform:none;">(opcional)</span></label>
                        <input type="text" name="vin"
                               value="{{ old('vin') }}"
                               placeholder="17 caracteres"
                               maxlength="17"
                               class="f-input {{ $errors->has('vin') ? 'error' : '' }}"
                               style="text-transform:uppercase;font-family:monospace;">
                        @error('vin')
                            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         2. SUCURSAL
    ══════════════════════════════════════════════════════════ --}}
    <div class="form-card">
        <p class="form-card-title">
            <i class="bi bi-geo-alt-fill"></i>
            Sucursal *
        </p>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:10px;margin-bottom:16px;">
            @foreach($sucursales as $suc)
            <label style="cursor:pointer;">
                <input type="radio" name="sucursal_id" value="{{ $suc->id }}" required
                       {{ old('sucursal_id') == $suc->id ? 'checked' : '' }}
                       style="display:none;" class="sucursal-radio"
                       data-lat="{{ $suc->latitud }}" data-lng="{{ $suc->longitud }}">
                <div class="suc-option" id="suc-card-{{ $suc->id }}"
                     style="border:1.5px solid var(--c-border);border-radius:11px;padding:13px 15px;">
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

        <div id="citaMap"></div>
        <p style="font-size:11px;color:var(--c-muted);margin-top:8px;">
            <i class="bi bi-info-circle" style="margin-right:4px;"></i>Haz clic en un marcador del mapa para seleccionar la sucursal.
        </p>
        @error('sucursal_id')
            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
        @enderror
    </div>

    {{-- ══════════════════════════════════════════════════════════
         3. SERVICIO
    ══════════════════════════════════════════════════════════ --}}
    <div class="form-card">
        <p class="form-card-title">
            <i class="bi bi-tools"></i>
            Servicio
        </p>
        <label class="f-label">¿Qué tipo de servicio necesitas? <span style="font-size:10px;color:var(--c-muted);text-transform:none;">(opcional)</span></label>
        <select name="servicio_id" class="f-select">
            <option value="">— Sin especificar / Solo quiero reservar turno —</option>
            @foreach($servicios as $s)
            <option value="{{ $s->id }}"
                {{ old('servicio_id', $servicioId) == $s->id ? 'selected' : '' }}>
                {{ $s->nombre }}{{ $s->precio ? ' — Bs ' . number_format($s->precio, 2) : '' }}
            </option>
            @endforeach
        </select>
        <p style="font-size:12px;color:var(--c-muted);margin-top:8px;">
            <i class="bi bi-info-circle" style="margin-right:4px;"></i>Si no sabes el servicio exacto, descríbelo en las notas.
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         4. FECHA Y HORA
    ══════════════════════════════════════════════════════════ --}}
    <div class="form-card">
        <p class="form-card-title">
            <i class="bi bi-clock-fill"></i>
            Fecha y Hora *
        </p>
        <div class="f-grid-2">
            <div>
                <label class="f-label">Fecha *</label>
                <input type="date" name="fecha"
                       value="{{ old('fecha') }}"
                       min="{{ today()->toDateString() }}"
                       class="f-input {{ $errors->has('fecha') ? 'error' : '' }}"
                       required>
                @error('fecha')
                    <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="f-label">Hora *</label>
                <select name="hora" class="f-select {{ $errors->has('hora') ? 'error' : '' }}" required>
                    <option value="">— Selecciona —</option>
                    @foreach(['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30'] as $h)
                    <option value="{{ $h }}:00" {{ old('hora') === "{$h}:00" ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
                @error('hora')
                    <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         5. NOTAS
    ══════════════════════════════════════════════════════════ --}}
    <div class="form-card">
        <p class="form-card-title">
            <i class="bi bi-chat-left-text"></i>
            Notas adicionales
        </p>
        <label class="f-label">Describe el problema o lo que necesitas <span style="font-size:10px;color:var(--c-muted);text-transform:none;">(opcional)</span></label>
        <textarea name="notas" rows="3"
                  placeholder="Ej: El auto hace un ruido extraño al frenar, necesito cambio de aceite y filtros..."
                  class="f-input {{ $errors->has('notas') ? 'error' : '' }}"
                  style="resize:vertical;">{{ old('notas') }}</textarea>
        @error('notas')
            <p class="f-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
        @enderror
    </div>

    {{-- Botones --}}
    <div style="display:flex;gap:10px;margin-top:4px;">
        <button type="submit" class="btn-red" style="padding:11px 28px;font-size:14px;">
            <i class="bi bi-calendar-check"></i> Confirmar cita
        </button>
        <a href="{{ route('cliente.citas.index') }}" class="btn-outline" style="padding:11px 20px;font-size:14px;">
            Cancelar
        </a>
    </div>

</form>
</div>

@endsection

@push('scripts')
<script>
// ─── Datos para Alpine ────────────────────────────────────────────────────────
const MARCAS_DATA = {!! $marcasJson !!};

const OLD_MARCA_ID  = '{{ old('marca_id', '') }}';
const OLD_MODELO_ID = '{{ old('modelo_id', '') }}';
const TIENE_VEHICULOS = {{ $vehiculos->isNotEmpty() ? 'true' : 'false' }};
const NUEVO_VEHICULO_OLD = '{{ old('nuevo_vehiculo', '0') }}';

// ─── Alpine: Formulario de vehículo ──────────────────────────────────────────
function vehiculoForm() {
    return {
        modo: TIENE_VEHICULOS && NUEVO_VEHICULO_OLD !== '1' ? 'existente' : 'nuevo',
        marcaId: OLD_MARCA_ID,
        modeloId: OLD_MODELO_ID,
        modelos: [],

        init() {
            if (this.marcaId) this.cargarModelos();
        },

        cargarModelos() {
            const marca = MARCAS_DATA.find(m => String(m.id) === String(this.marcaId));
            this.modelos = marca ? marca.modelos : [];
            // Si el modelo_id guardado sigue válido en la nueva lista, mantenerlo
            if (this.modeloId && !this.modelos.find(mo => String(mo.id) === String(this.modeloId))) {
                this.modeloId = '';
            }
        },
    };
}

// ─── Google Maps ──────────────────────────────────────────────────────────────
const SUCURSALES = @json($sucursales);
const MAP_STYLES = [
    { elementType:'geometry',    stylers:[{color:'#1d2433'}] },
    { elementType:'labels.text.fill',   stylers:[{color:'#8ec3b9'}] },
    { elementType:'labels.text.stroke', stylers:[{color:'#1a3646'}] },
    { featureType:'road',        elementType:'geometry',           stylers:[{color:'#304a7d'}] },
    { featureType:'road',        elementType:'labels.text.fill',   stylers:[{color:'#98a5be'}] },
    { featureType:'water',       elementType:'geometry',           stylers:[{color:'#0e1626'}] },
    { featureType:'poi',         elementType:'geometry',           stylers:[{color:'#283d6a'}] },
    { featureType:'transit',     elementType:'geometry',           stylers:[{color:'#2f3948'}] },
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
        mapTypeControl:false, streetViewControl:false, fullscreenControl:false,
        styles: MAP_STYLES,
    });

    const bounds = new google.maps.LatLngBounds();

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const p = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            new google.maps.Marker({
                map, position: p, title:'Tu ubicación', zIndex:0,
                icon:{ path:google.maps.SymbolPath.CIRCLE, scale:8,
                       fillColor:'#4285F4', fillOpacity:1, strokeColor:'#fff', strokeWeight:2 },
            });
            bounds.extend(p);
            if (withCoords.length) map.fitBounds(bounds, {padding:60});
        }, ()=>{}, {timeout:8000});
    }

    withCoords.forEach(suc => {
        const pos = { lat:parseFloat(suc.latitud), lng:parseFloat(suc.longitud) };
        bounds.extend(pos);

        const marker = new google.maps.Marker({
            map, position:pos, title:suc.nombre,
            animation:google.maps.Animation.DROP,
            icon:{ path:google.maps.SymbolPath.CIRCLE, scale:11,
                   fillColor:'#D71920', fillOpacity:1, strokeColor:'#fff', strokeWeight:2.5 },
        });

        const iw = new google.maps.InfoWindow({
            content:`<div style="padding:12px 14px;background:#1a2236;color:#f1f5f9;border-radius:10px;min-width:180px;font-family:system-ui,sans-serif;">
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

    if (withCoords.length > 1) map.fitBounds(bounds, {padding:60});
}

function selectSucursal(id) {
    document.querySelectorAll('.sucursal-radio').forEach(r => {
        if (parseInt(r.value) === id) r.checked = true;
    });
    const card = document.getElementById('suc-card-' + id);
    if (card) card.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sucursal-radio').forEach(r => {
        r.addEventListener('change', () => {
            const lat = parseFloat(r.dataset.lat);
            const lng = parseFloat(r.dataset.lng);
            if (lat && lng && map) {
                map.panTo({lat, lng});
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
