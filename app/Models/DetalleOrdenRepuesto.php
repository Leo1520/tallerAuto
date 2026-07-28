<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleOrdenRepuesto extends Model
{
    public $timestamps = false;
    protected $table = 'detalle_orden_repuesto';
    protected $fillable = ['orden_id', 'repuesto_id', 'cantidad', 'precio_unitario', 'subtotal'];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenServicio::class);
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }
}
