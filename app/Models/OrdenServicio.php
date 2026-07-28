<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrdenServicio extends Model
{
    protected $table = 'ordenes_servicio';

    protected $fillable = [
        'numero', 'vehiculo_id', 'sucursal_id', 'mecanico_id',
        'fecha_ingreso', 'fecha_entrega_estimada', 'fecha_entrega_real',
        'estado', 'prioridad', 'subtotal', 'descuento', 'impuestos', 'total', 'observaciones',
    ];

    protected $casts = [
        'fecha_ingreso'           => 'datetime',
        'fecha_entrega_estimada'  => 'datetime',
        'fecha_entrega_real'      => 'datetime',
    ];

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(Mecanico::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleOrdenServicio::class, 'orden_id');
    }

    public function repuestos(): HasMany
    {
        return $this->hasMany(DetalleOrdenRepuesto::class, 'orden_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'orden_id');
    }

    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class, 'orden_id');
    }
}
