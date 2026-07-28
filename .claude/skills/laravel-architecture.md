# Skill: laravel-architecture

Patrones de arquitectura Laravel para el proyecto Taller Pro.

## Estructura recomendada

```
app/
├── Actions/          # Acciones de negocio (una responsabilidad)
├── DTOs/             # Data Transfer Objects
├── Services/         # Lógica de dominio reutilizable
├── Repositories/     # Abstracción de acceso a datos (opcional)
├── Http/
│   ├── Controllers/  # Delgados — solo coordinan
│   ├── Requests/     # Validación con FormRequest
│   └── Resources/    # API Resources para JSON
├── Models/           # Eloquent + relaciones + scopes
├── Policies/         # Autorización por modelo
└── Jobs/             # Tareas asíncronas (queues)
```

## Reglas

### Controladores delgados
- El controlador solo recibe la request, llama un Action/Service, devuelve respuesta
- Sin lógica de negocio directa en controladores

### Actions (patrón preferido)
```php
// app/Actions/Ordenes/CrearOrdenAction.php
class CrearOrdenAction
{
    public function execute(CrearOrdenDTO $dto): OrdenServicio
    {
        return DB::transaction(function () use ($dto) {
            // lógica aquí
        });
    }
}
```

### Evitar N+1
- Siempre eager load relaciones conocidas: `->with(['vehiculo.cliente', 'mecanico'])`
- Usar `->withCount()` en lugar de contar en PHP
- Instalar Laravel Debugbar en desarrollo para detectar N+1

### Transacciones
- Envolver en `DB::transaction()` toda operación que modifique múltiples tablas
- Especialmente: crear orden + detalle + movimiento inventario + pago

### Cache
- Cachear catálogos estáticos: marcas, modelos, tipos_servicio, especialidades
- Redis para sesiones y cache en producción
- Invalidar cache al actualizar registros

### Jobs/Queues
- Envío de emails → Job
- Generación de reportes PDF → Job
- Notificaciones de mantenimiento preventivo → Job programado

## Comandos

```bash
php artisan make:action Ordenes/CrearOrdenAction
php artisan make:job EnviarNotificacionMantenimiento
php artisan make:resource OrdenServicioResource
```
