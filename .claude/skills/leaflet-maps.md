# Skill: leaflet-maps

Integración de Leaflet + OpenStreetMap para mostrar sucursales en Taller Pro.

## Setup básico en Blade

```html
{{-- En el <head> --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- Contenedor del mapa --}}
<div id="mapa-sucursales" style="height: 450px; border-radius: 0.5rem;"></div>

<script>
const sucursales = @json($sucursales); // desde el controlador

const mapa = L.map('mapa-sucursales').setView([-17.3935, -66.1570], 12); // Bolivia por defecto

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(mapa);

sucursales.forEach(s => {
    if (s.latitud && s.longitud) {
        L.marker([s.latitud, s.longitud])
            .addTo(mapa)
            .bindPopup(`
                <strong>${s.nombre}</strong><br>
                ${s.direccion}<br>
                Tel: ${s.telefono}
            `);
    }
});
</script>
```

## Controlador

```php
public function index()
{
    $sucursales = Sucursal::where('activo', true)
        ->select('id', 'nombre', 'direccion', 'ciudad', 'telefono', 'latitud', 'longitud')
        ->get();

    return view('mapa.index', compact('sucursales'));
}
```

## Geolocalización del usuario

```js
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
        const { latitude, longitude } = pos.coords;
        L.marker([latitude, longitude], {
            icon: L.divIcon({ className: 'user-location-icon', html: '📍' })
        }).addTo(mapa).bindPopup('Tu ubicación');
        mapa.setView([latitude, longitude], 13);
    });
}
```

## Columnas en tabla sucursales
- `latitud decimal(10,8)` — precisión suficiente para GPS
- `longitud decimal(11,8)` — longitud puede ser negativa (oeste)
