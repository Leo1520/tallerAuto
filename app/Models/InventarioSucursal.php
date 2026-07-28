<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioSucursal extends Model
{
    public $timestamps = false;
    protected $table = 'inventario_sucursal';

    protected $fillable = ['sucursal_id', 'repuesto_id', 'stock', 'stock_minimo', 'updated_at'];

    protected $casts = ['updated_at' => 'datetime'];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function bajoStock(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}
