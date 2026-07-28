<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use Auditable;

    protected $fillable = [
        'orden_id', 'metodo_pago_id', 'user_id', 'monto', 'moneda',
        'estado', 'referencia', 'transaccion_externa', 'webhook_verificado',
        'fecha_confirmacion', 'observaciones',
    ];

    protected $casts = [
        'webhook_verificado' => 'boolean',
        'fecha_confirmacion' => 'datetime',
        'monto'              => 'decimal:2',
    ];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenServicio::class);
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function esStripe(): bool
    {
        return str_contains(strtolower($this->metodoPago?->nombre ?? ''), 'stripe')
            || str_contains(strtolower($this->metodoPago?->nombre ?? ''), 'tarjeta');
    }

    public function estaConfirmado(): bool
    {
        return $this->estado === 'Confirmado';
    }
}
