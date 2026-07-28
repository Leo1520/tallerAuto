# Skill: taller-db-patterns

Patrones de base de datos específicos para Taller Pro (`db_taller`).

## Tablas clave y sus relaciones

### Jerarquía principal
```
persona → users (1:1)
persona → clientes (1:1)
persona → mecanicos (1:1)
clientes → vehiculos (1:N)
vehiculos → ordenes_servicio (1:N)
ordenes_servicio → detalle_orden_servicio (1:N)
ordenes_servicio → detalle_orden_repuesto (1:N)
ordenes_servicio → pagos (1:N)
ordenes_servicio → facturas (1:1)
sucursales → mecanicos (1:N)
sucursales → inventario_sucursal (1:N)
```

## Eager loading estándar

### Orden con todo
```php
OrdenServicio::with([
    'vehiculo.cliente.persona',
    'vehiculo.modelo.marca',
    'mecanico.persona',
    'sucursal',
    'detalles.servicio',
    'repuestos.repuesto',
    'pagos.metodoPago',
    'factura',
])->findOrFail($id);
```

### Lista de órdenes (paginada)
```php
OrdenServicio::with(['vehiculo.cliente.persona', 'mecanico.persona', 'sucursal'])
    ->latest('fecha_ingreso')
    ->paginate(15);
```

## Migraciones — convenciones

- `activo boolean default true` en catálogos (no borrar, desactivar)
- `softDeletes()` solo en modelos que lo justifiquen
- Siempre agregar índices en FKs y columnas de búsqueda frecuente
- Columnas de estado con `enum` o `varchar(30)` con valores documentados

### Estados de orden_servicio
`Recibido | En diagnóstico | En reparación | Esperando repuestos | Listo | Entregado | Cancelado`

### Estados de pago
`Pendiente | Confirmado | Rechazado | Reembolsado`

## Seeders recomendados

```bash
php artisan make:seeder RolesPermissionsSeeder
php artisan make:seeder TiposServicioSeeder
php artisan make:seeder MetodosPagoSeeder
php artisan make:seeder MarcasModelosSeeder
php artisan make:seeder SucursalesSeeder
```

## Factories

```bash
php artisan make:factory PersonaFactory
php artisan make:factory ClienteFactory
php artisan make:factory VehiculoFactory
php artisan make:factory OrdenServicioFactory
```
