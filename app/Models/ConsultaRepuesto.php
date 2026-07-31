<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultaRepuesto extends Model
{
    public $timestamps = false;
    protected $table   = 'consultas_repuesto';

    protected $fillable = [
        'repuesto_id', 'cliente_id', 'nombre', 'telefono',
        'email', 'cantidad', 'monto', 'notas', 'estado',
        'token', 'qr_path', 'comprobante_path', 'pago_estado', 'pago_notas',
        'pago_confirmado_at',
    ];

    protected $casts = [
        'created_at'         => 'datetime',
        'pago_confirmado_at' => 'datetime',
        'cantidad'           => 'integer',
        'monto'              => 'decimal:2',
    ];

    public function montoTotal(): float
    {
        return (float) ($this->monto ?? ($this->cantidad * ($this->repuesto->precio_venta ?? 0)));
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
