<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pago extends Model
{
    use Auditable;

    protected $fillable = [
        'orden_id', 'metodo_pago_id', 'user_id', 'monto', 'moneda',
        'estado', 'referencia', 'transaccion_externa', 'webhook_verificado',
        'fecha_confirmacion', 'observaciones',
        'confirmado_por_id', 'confirmado_ip', 'metodo_confirmacion',
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

    public function comprobante(): HasOne
    {
        return $this->hasOne(ComprobantePago::class);
    }

    public function movimientoCaja(): HasOne
    {
        return $this->hasOne(MovimientoCaja::class);
    }

    public function confirmadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmado_por_id');
    }

    public function esQr(): bool
    {
        return str_contains(strtolower($this->metodoPago?->nombre ?? ''), 'qr');
    }

    public function esEfectivo(): bool
    {
        return strtolower($this->metodoPago?->nombre ?? '') === 'efectivo';
    }

    public function estaConfirmado(): bool
    {
        return $this->estado === 'Confirmado';
    }

    public function estaEnRevision(): bool
    {
        return $this->estado === 'En revisión';
    }

    public function estaRechazado(): bool
    {
        return $this->estado === 'Rechazado';
    }
}
