<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    public $timestamps = false;
    protected $table = 'movimientos_inventario';

    protected $fillable = ['repuesto_id', 'sucursal_id', 'user_id', 'tipo', 'cantidad', 'motivo', 'referencia'];

    protected $casts = ['created_at' => 'datetime'];

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
