@extends('layouts.app')

@section('title', 'Mapa de Sucursales')
@section('page-title', 'Mapa de Sucursales')

@section('header-actions')
    <a href="{{ route('sucursales.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 transition-colors">
        <i class="bi bi-list-ul" style="font-size:15px;"></i>
        Ver lista
    </a>
@endsection

@push('styles')
<style>
    #mapa-sucursales { height: 500px; width: 100%; }

    /* Estilos del InfoWindow de Google Maps */
    .gm-infowindow-inner {
        padding: 0 !important;
    }
    .iw-body {
        min-width: 200px;
        padding: 14px 16px;
        font-family: inherit;
        background: #1f2937;
        border-radius: 10px;
        color: #f1f5f9;
    }
    .iw-title {
        font-weight: 700;
        font-size: 14px;
        margin: 0 0 4px;
        color: #f9fafb;
    }
    .iw-sub {
        font-size: 12px;
        color: #9ca3af;
        margin: 0 0 3px;
    }
    .iw-stats {
        display: flex;
        gap: 14px;
        font-size: 12px;
        color: #d1d5db;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #374151;
    }
    .iw-stats b { color: #f9fafb; }
    .iw-phone {
        font-size: 12px;
        color: #9ca3af;
        margin: 6px 0 0;
    }
</style>
@endpush

@section('content')

<div class="space-y-5">

    {{-- Mapa --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div id="mapa-sucursales"></div>
    </div>

    {{-- Lista de sucursales con coordenadas --}}
    @if($sucursales->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($sucursales as $suc)
        <div class="bg-gray-800 rounded-xl border border-gray-700 px-5 py-4 flex items-center gap-4 cursor-pointer hover:border-red-700 transition-colors"
             data-center-lat="{{ $suc->latitud }}" data-center-lng="{{ $suc->longitud }}" data-center-idx="{{ $loop->index }}"
             title="Centrar en mapa">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(215,25,32,.15);">
                <i class="bi bi-geo-alt-fill" style="color:#D71920; font-size:18px;"></i>
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-gray-100 truncate">{{ $suc->nombre }}</p>
                <p class="text-xs text-gray-400 truncate">{{ $suc->ciudad }} · {{ $suc->mecanicos_activos }} mecánicos · {{ $suc->ordenes_activas }} órdenes</p>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-gray-800 rounded-xl border border-gray-700 py-16 text-center">
        <i class="bi bi-geo text-gray-600" style="font-size:48px;"></i>
        <p class="mt-3 text-gray-500">No hay sucursales con coordenadas registradas.</p>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('sucursales.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white"
           style="background:#D71920;">
            <i class="bi bi-plus-lg"></i> Agregar sucursal
        </a>
        @endif
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script @nonce>
const SUCURSALES = @json($sucursales);

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

let map, markers = [], openInfoWindow = null;

function initMap() {
    const defaultCenter = SUCURSALES.length
        ? { lat: parseFloat(SUCURSALES[0].latitud), lng: parseFloat(SUCURSALES[0].longitud) }
        : { lat: -17.7833, lng: -63.1821 };

    map = new google.maps.Map(document.getElementById('mapa-sucursales'), {
        center: defaultCenter,
        zoom: SUCURSALES.length > 1 ? 11 : 14,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        styles: MAP_STYLES,
    });

    const bounds = new google.maps.LatLngBounds();

    SUCURSALES.forEach((suc, i) => {
        const pos = { lat: parseFloat(suc.latitud), lng: parseFloat(suc.longitud) };
        bounds.extend(pos);

        const marker = new google.maps.Marker({
            map,
            position: pos,
            title: suc.nombre,
            animation: google.maps.Animation.DROP,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 11,
                fillColor: '#D71920',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2.5,
            },
        });

        const infoContent = `
            <div class="iw-body">
                <p class="iw-title">${suc.nombre}</p>
                ${suc.ciudad   ? `<p class="iw-sub">${suc.ciudad}</p>` : ''}
                ${suc.direccion ? `<p class="iw-sub">${suc.direccion}</p>` : ''}
                <div class="iw-stats">
                    <span><b>${suc.mecanicos_activos}</b> mecánicos</span>
                    <span><b>${suc.ordenes_activas}</b> órdenes</span>
                </div>
                ${suc.telefono ? `<p class="iw-phone">${suc.telefono}</p>` : ''}
            </div>`;

        const infoWindow = new google.maps.InfoWindow({ content: infoContent });

        marker.addListener('click', () => {
            if (openInfoWindow) openInfoWindow.close();
            infoWindow.open(map, marker);
            openInfoWindow = infoWindow;
        });

        markers.push({ marker, infoWindow });
    });

    if (SUCURSALES.length > 1) {
        map.fitBounds(bounds, { padding: 60 });
    }
}

function centerMap(lat, lng, index) {
    map.panTo({ lat: parseFloat(lat), lng: parseFloat(lng) });
    map.setZoom(16);
    if (openInfoWindow) openInfoWindow.close();
    if (markers[index]) {
        markers[index].infoWindow.open(map, markers[index].marker);
        openInfoWindow = markers[index].infoWindow;
    }
}
</script>
<script @nonce>
document.addEventListener('click', function (e) {
    const card = e.target.closest('[data-center-lat]');
    if (card) {
        centerMap(
            parseFloat(card.dataset.centerLat),
            parseFloat(card.dataset.centerLng),
            parseInt(card.dataset.centerIdx)
        );
    }
});
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initMap" async defer></script>
@endpush
