<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    use Auditable;

    protected $fillable = ['orden_id', 'numero', 'fecha_emision', 'subtotal', 'iva', 'total', 'estado', 'observaciones'];

    public function estaEmitida(): bool  { return $this->estado === 'Emitida'; }
    public function estaAnulada(): bool  { return $this->estado === 'Anulada'; }

    protected $casts = ['fecha_emision' => 'datetime'];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenServicio::class);
    }
}
