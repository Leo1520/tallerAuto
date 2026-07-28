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

@section('content')

<div class="space-y-5">

    {{-- Mapa --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div id="mapa-sucursales" style="height:500px; width:100%;"></div>
    </div>

    {{-- Lista de sucursales con coordenadas --}}
    @if($sucursales->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($sucursales as $suc)
        <div class="bg-gray-800 rounded-xl border border-gray-700 px-5 py-4 flex items-center gap-4 cursor-pointer hover:border-red-700 transition-colors"
             onclick="centerMap({{ $suc->latitud }}, {{ $suc->longitud }}, '{{ addslashes($suc->nombre) }}')"
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

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const sucursales = @json($sucursales);

const map = L.map('mapa-sucursales', {
    center: sucursales.length
        ? [sucursales[0].latitud, sucursales[0].longitud]
        : [-16.5, -68.15],
    zoom: sucursales.length > 1 ? 8 : 13,
    zoomControl: true,
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

const redIcon = L.divIcon({
    className: '',
    html: `<div style="
        width:32px; height:32px; border-radius:50% 50% 50% 0; transform:rotate(-45deg);
        background:#D71920; border:3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.4);
    "></div>`,
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
});

const markers = [];

sucursales.forEach(suc => {
    const marker = L.marker([suc.latitud, suc.longitud], { icon: redIcon })
        .addTo(map)
        .bindPopup(`
            <div style="min-width:180px; font-family:sans-serif;">
                <p style="font-weight:700; font-size:14px; margin:0 0 4px;">${suc.nombre}</p>
                ${suc.ciudad ? `<p style="color:#6b7280; font-size:12px; margin:0 0 4px;">${suc.ciudad}</p>` : ''}
                ${suc.direccion ? `<p style="color:#6b7280; font-size:12px; margin:0 0 6px;">${suc.direccion}</p>` : ''}
                <div style="display:flex; gap:12px; font-size:12px;">
                    <span><b>${suc.mecanicos_activos}</b> mecánicos</span>
                    <span><b>${suc.ordenes_activas}</b> órdenes</span>
                </div>
                ${suc.telefono ? `<p style="margin:6px 0 0; font-size:12px; color:#374151;">${suc.telefono}</p>` : ''}
            </div>
        `);
    markers.push(marker);
});

if (sucursales.length > 1) {
    const group = L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.15));
}

// Forzar re-render por si el contenedor no tenía dimensiones al inicializar
setTimeout(() => map.invalidateSize(), 200);

function centerMap(lat, lng, nombre) {
    map.setView([lat, lng], 14, { animate: true });
    markers.forEach(m => {
        if (Math.abs(m.getLatLng().lat - lat) < 0.0001 && Math.abs(m.getLatLng().lng - lng) < 0.0001) {
            m.openPopup();
        }
    });
}
</script>
@endpush
