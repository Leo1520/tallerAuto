# Skill: tailwind-automotive-ui

Guía de UI con Tailwind CSS para el dashboard de Taller Pro.

## Componentes clave

### Badge de estado de orden
```html
@php
$colores = [
    'Recibido'             => 'bg-blue-100 text-blue-800',
    'En diagnóstico'       => 'bg-yellow-100 text-yellow-800',
    'En reparación'        => 'bg-orange-100 text-orange-800',
    'Esperando repuestos'  => 'bg-purple-100 text-purple-800',
    'Listo'                => 'bg-green-100 text-green-800',
    'Entregado'            => 'bg-gray-100 text-gray-800',
    'Cancelado'            => 'bg-red-100 text-red-800',
];
@endphp
<span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colores[$orden->estado] ?? 'bg-gray-100 text-gray-600' }}">
    {{ $orden->estado }}
</span>
```

### Tarjeta de estadística (dashboard)
```html
<div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
    <div class="p-3 bg-blue-50 rounded-lg">
        <svg class="w-6 h-6 text-blue-600"><!-- icono --></svg>
    </div>
    <div>
        <p class="text-sm text-gray-500">Órdenes activas</p>
        <p class="text-2xl font-bold text-gray-900">{{ $totalOrdenes }}</p>
    </div>
</div>
```

### Tabla responsive
```html
<div class="overflow-x-auto rounded-xl shadow">
    <table class="min-w-full divide-y divide-gray-200 bg-white">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Orden</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($ordenes as $orden)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $orden->numero }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $orden->vehiculo->cliente->persona->nombre }}</td>
                <td class="px-6 py-4"><!-- badge estado --></td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-900">Bs {{ number_format($orden->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

## Layout del dashboard
- Sidebar fijo (`w-64`) + contenido principal (`flex-1 overflow-y-auto`)
- Responsive: sidebar colapsable en móvil con Alpine.js
- Paleta: azul oscuro para sidebar, blanco para cards, gris claro para fondo
