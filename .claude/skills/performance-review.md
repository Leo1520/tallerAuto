# Skill: performance-review

Revisión de rendimiento para el proyecto Taller Pro.

## Checklist N+1

Antes de entregar cualquier listado, verificar:
- [ ] Todas las relaciones usadas en Blade están en `->with()`
- [ ] Usar `->withCount()` en lugar de `->detalles->count()` en PHP
- [ ] No llamar métodos de relación sin `->` en loops

Señales de N+1 en Blade:
```blade
{{-- ❌ N+1 --}}
@foreach($ordenes as $orden)
    {{ $orden->vehiculo->cliente->persona->nombre }}
@endforeach

{{-- ✅ Correcto (eager loaded) --}}
{{-- En controller: OrdenServicio::with('vehiculo.cliente.persona')->paginate() --}}
@foreach($ordenes as $orden)
    {{ $orden->vehiculo->cliente->persona->nombre }}
@endforeach
```

## Índices — verificar en migraciones

Columnas que DEBEN tener índice en `db_taller`:
- `ordenes_servicio.estado`, `ordenes_servicio.fecha_ingreso`
- `pagos.estado`, `pagos.transaccion_externa`
- `vehiculos.placa`, `vehiculos.vin`
- `repuestos.codigo`

## Cache de catálogos

```php
// Cachear catálogos estáticos por 24h
$marcas = Cache::remember('marcas_activas', 86400, fn() =>
    Marca::where('activo', true)->orderBy('nombre')->get()
);

// Invalidar al actualizar
Cache::forget('marcas_activas');
```

## Paginación

- Siempre `->paginate(15)` o `->paginate(25)` en listados, nunca `->get()` sin límite
- Usar `->simplePaginate()` si no se necesita número total de páginas

## Queries lentas

Instalar en desarrollo:
```bash
composer require barryvdh/laravel-debugbar --dev
```

Revisar en producción via logs:
```php
// En AppServiceProvider::boot()
if (config('app.debug')) {
    DB::listen(fn($q) => logger($q->sql, $q->bindings));
}
```
