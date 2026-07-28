<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleOrdenServicio extends Model
{
    public $timestamps = false;
    protected $table = 'detalle_orden_servicio';
    protected $fillable = ['orden_id', 'servicio_id', 'cantidad', 'precio_unitario', 'subtotal', 'estado', 'observaciones'];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenServicio::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}
