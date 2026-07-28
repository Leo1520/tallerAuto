# Skill: audit-logging

Auditoría y trazabilidad para operaciones críticas en Taller Pro.

## Modelo Auditoria

```php
// app/Models/Auditoria.php
class Auditoria extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'tipo_operacion', 'tabla', 'registro_id', 'cambios', 'ip', 'user_agent'];
    protected $casts = ['cambios' => 'array'];
}
```

## Trait AuditableModel (para agregar a modelos críticos)

```php
// app/Traits/Auditable.php
trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::updated(function ($model) {
            Auditoria::create([
                'user_id'        => auth()->id(),
                'tipo_operacion' => 'UPDATE',
                'tabla'          => $model->getTable(),
                'registro_id'    => $model->getKey(),
                'cambios'        => ['antes' => $model->getOriginal(), 'despues' => $model->getDirty()],
                'ip'             => request()->ip(),
                'user_agent'     => request()->userAgent(),
            ]);
        });

        static::deleted(function ($model) {
            Auditoria::create([
                'user_id'        => auth()->id(),
                'tipo_operacion' => 'DELETE',
                'tabla'          => $model->getTable(),
                'registro_id'    => $model->getKey(),
                'cambios'        => $model->toArray(),
                'ip'             => request()->ip(),
                'user_agent'     => request()->userAgent(),
            ]);
        });
    }
}
```

## Uso en modelos

```php
// En modelos críticos: OrdenServicio, Pago, Factura, Inventario
use App\Traits\Auditable;

class OrdenServicio extends Model
{
    use Auditable;
    // ...
}
```

## Operaciones que SIEMPRE deben auditarse
- Cambio de estado de orden de servicio
- Registro y confirmación de pagos
- Movimientos de inventario (entrada/salida)
- Cambio de roles/permisos de usuarios
- Emisión/anulación de facturas
- Login/logout de usuarios

## Consulta de auditoría

```php
Auditoria::with('user.persona')
    ->where('tabla', 'ordenes_servicio')
    ->where('registro_id', $ordenId)
    ->latest('created_at')
    ->get();
```
