<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    protected $fillable = ['orden_id', 'numero', 'fecha_emision', 'subtotal', 'iva', 'total', 'estado', 'observaciones'];

    protected $casts = ['fecha_emision' => 'datetime'];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenServicio::class);
    }
}
