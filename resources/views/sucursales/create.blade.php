@extends('layouts.app')

@section('title', 'Nueva Sucursal')
@section('page-title', 'Nueva Sucursal')

@section('header-actions')
    <a href="{{ route('sucursales.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-arrow-left" style="font-size:15px;"></i> Volver
    </a>
@endsection

@push('styles')
<style>
    #sucursalMap { height: 380px; border-radius: 0.5rem; }
    /* Estilo del input de búsqueda de Google Places */
    #mapSearch {
        width: 100%;
        padding: 9px 14px 9px 38px;
        background: #111827;
        border: 1px solid #374151;
        border-radius: 8px;
        color: #f1f5f9;
        font-size: 13px;
        outline: none;
        box-sizing: border-box;
    }
    #mapSearch:focus { border-color: #D71920; box-shadow: 0 0 0 2px rgba(215,25,32,.25); }
    #mapSearch::placeholder { color: #6b7280; }
    /* Dropdown de sugerencias de Google */
    .pac-container {
        background: #1f2937 !important;
        border: 1px solid #374151 !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,.5) !important;
        margin-top: 4px !important;
        font-family: inherit !important;
    }
    .pac-item {
        padding: 8px 14px !important;
        border-top: 1px solid #374151 !important;
        color: #d1d5db !important;
        cursor: pointer !important;
        font-size: 12px !important;
    }
    .pac-item:hover, .pac-item-selected { background: #374151 !important; }
    .pac-item-query { color: #f9fafb !important; font-size: 13px !important; }
    .pac-icon { filter: invert(1) opacity(.4); }
    .pac-matched { color: #f87171 !important; font-weight: 600 !important; }
</style>
@endpush

@section('content')

<div class="max-w-3xl mx-auto">
<form method="POST" action="{{ route('sucursales.store') }}" class="space-y-5">
    @csrf

    {{-- Información básica --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-gray-100 flex items-center gap-2">
                <i class="bi bi-building" style="color:#D71920;"></i>
                Información de la sucursal
            </h2>
        </div>
        <div class="p-6 space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nombre <span class="text-red-400">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500 @error('nombre') border-red-500 @enderror"
                       placeholder="Ej: Sucursal Centro">
                @error('nombre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Ciudad</label>
                    <input type="text" name="ciudad" id="inputCiudad" value="{{ old('ciudad') }}"
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                           placeholder="La Paz, Santa Cruz...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                           class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                           placeholder="+591 2 2XXXXXX">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Dirección</label>
                <input type="text" name="direccion" id="inputDireccion" value="{{ old('direccion') }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                       placeholder="Av. 16 de Julio N.º 1234">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-3 py-2.5 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                       placeholder="sucursal@tallerpro.com">
            </div>

            <div class="flex items-center gap-3 pt-1">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" id="activo" class="sr-only peer"
                           {{ old('activo', '1') == '1' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-600 peer-focus:ring-2 peer-focus:ring-red-600 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                </label>
                <label for="activo" class="text-sm font-medium text-gray-300 cursor-pointer">Sucursal activa</label>
            </div>

        </div>
    </div>

    {{-- Mapa de ubicación --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-gray-100 flex items-center gap-2">
                <i class="bi bi-geo-alt-fill" style="color:#D71920;"></i>
                Ubicación en el mapa
                <span class="text-xs font-normal text-gray-500 ml-1">— Haz clic o arrastra el marcador para seleccionar</span>
            </h2>
        </div>
        <div class="p-4 space-y-3">

            {{-- Barra de búsqueda Google Places --}}
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:13px; pointer-events:none; z-index:1;"></i>
                <input type="text" id="mapSearch" placeholder="Buscar dirección, negocio o lugar..." autocomplete="off">
            </div>

            {{-- Mapa Google --}}
            <div id="sucursalMap"></div>

            {{-- Coordenadas --}}
            <div class="grid grid-cols-2 gap-4 pt-1">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">
                        <i class="bi bi-crosshair me-1" style="color:#D71920;"></i> Latitud
                    </label>
                    <input type="number" name="latitud" id="inputLat" value="{{ old('latitud') }}"
                           step="any" min="-90" max="90"
                           class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                           placeholder="-17.783327">
                    @error('latitud') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">
                        <i class="bi bi-crosshair me-1" style="color:#D71920;"></i> Longitud
                    </label>
                    <input type="number" name="longitud" id="inputLng" value="{{ old('longitud') }}"
                           step="any" min="-180" max="180"
                           class="w-full px-3 py-2 bg-gray-900 border border-gray-600 text-gray-100 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-600 placeholder-gray-500"
                           placeholder="-63.182127">
                    @error('longitud') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="text-xs text-gray-600">Haz clic en el mapa, usa el buscador o arrastra el marcador para obtener coordenadas.</p>

        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('sucursales.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
            Cancelar
        </a>
        <button type="submit"
                class="px-6 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors"
                style="background:#D71920;" onmouseover="this.style.background='#b81218'" onmouseout="this.style.background='#D71920'">
            <i class="bi bi-check-lg me-1"></i> Crear sucursal
        </button>
    </div>

</form>
</div>

@endsection

@push('scripts')
<script @nonce>
const INIT_LAT = {{ old('latitud', -17.7833) }};
const INIT_LNG = {{ old('longitud', -63.1821) }};
const HAS_OLD  = {{ old('latitud') ? 'true' : 'false' }};

const MAP_STYLES = [
    { elementType: 'geometry', stylers: [{ color: '#1d2433' }] },
    { elementType: 'labels.text.fill', stylers: [{ color: '#8ec3b9' }] },
    { elementType: 'labels.text.stroke', stylers: [{ color: '#1a3646' }] },
    { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#304a7d' }] },
    { featureType: 'road', elementType: 'labels.text.fill', stylers: [{ color: '#98a5be' }] },
    { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#0e1626' }] },
    { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#283d6a' }] },
    { featureType: 'poi', elementType: 'labels.text.fill', stylers: [{ color: '#6f9ba5' }] },
    { featureType: 'transit', elementType: 'geometry', stylers: [{ color: '#2f3948' }] },
    { featureType: 'administrative', elementType: 'geometry.stroke', stylers: [{ color: '#4b6878' }] },
];

function initMap() {
    const center = { lat: INIT_LAT, lng: INIT_LNG };

    const map = new google.maps.Map(document.getElementById('sucursalMap'), {
        center,
        zoom: HAS_OLD ? 16 : 13,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        styles: MAP_STYLES,
    });

    const inputLat = document.getElementById('inputLat');
    const inputLng = document.getElementById('inputLng');

    const marker = new google.maps.Marker({
        map: HAS_OLD ? map : null,
        position: center,
        draggable: true,
        animation: google.maps.Animation.DROP,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: '#D71920',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 2.5,
        },
    });

    function setCoords(lat, lng) {
        const la = parseFloat(lat.toFixed(6));
        const lo = parseFloat(lng.toFixed(6));
        inputLat.value = la;
        inputLng.value = lo;
        marker.setPosition({ lat: la, lng: lo });
        marker.setMap(map);
    }

    map.addListener('click', (e) => setCoords(e.latLng.lat(), e.latLng.lng()));
    marker.addListener('dragend', () => {
        const pos = marker.getPosition();
        setCoords(pos.lat(), pos.lng());
    });

    function syncFromInputs() {
        const la = parseFloat(inputLat.value);
        const lo = parseFloat(inputLng.value);
        if (!isNaN(la) && !isNaN(lo)) {
            marker.setPosition({ lat: la, lng: lo });
            marker.setMap(map);
            map.setCenter({ lat: la, lng: lo });
        }
    }
    inputLat.addEventListener('change', syncFromInputs);
    inputLng.addEventListener('change', syncFromInputs);

    // ── Geolocalización: centrar en ubicación real del usuario ──────────
    if (!HAS_OLD && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const userLat = pos.coords.latitude;
                const userLng = pos.coords.longitude;
                const userPos = { lat: userLat, lng: userLng };
                map.setCenter(userPos);
                map.setZoom(15);

                // Punto azul: ubicación actual
                new google.maps.Marker({
                    map,
                    position: userPos,
                    title: 'Tu ubicación',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#4285F4',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                    },
                    zIndex: 0,
                });
            },
            () => { /* permiso denegado — se queda en Santa Cruz */ },
            { timeout: 8000 }
        );
    }

    // ── Google Places Autocomplete ──────────────────────────────────────
    const searchInput = document.getElementById('mapSearch');
    const autocomplete = new google.maps.places.Autocomplete(searchInput, {
        fields: ['geometry', 'formatted_address', 'address_components', 'name'],
    });
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) return;

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();
        setCoords(lat, lng);
        map.setCenter({ lat, lng });
        map.setZoom(17);

        const dirInput = document.getElementById('inputDireccion');
        if (!dirInput.value && place.formatted_address) {
            dirInput.value = place.formatted_address.split(',').slice(0, 2).join(',').trim();
        }

        const ciudadInput = document.getElementById('inputCiudad');
        if (!ciudadInput.value && place.address_components) {
            const locality = place.address_components.find(c =>
                c.types.includes('locality') || c.types.includes('administrative_area_level_2')
            );
            if (locality) ciudadInput.value = locality.long_name;
        }
    });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places&callback=initMap" async defer></script>
@endpush
