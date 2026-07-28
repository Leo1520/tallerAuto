<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'orden_id', 'metodo_pago_id', 'user_id', 'monto', 'moneda',
        'estado', 'referencia', 'transaccion_externa', 'webhook_verificado',
        'fecha_confirmacion', 'observaciones',
    ];

    protected $casts = [
        'webhook_verificado'  => 'boolean',
        'fecha_confirmacion'  => 'datetime',
    ];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenServicio::class);
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class);
    }
}
