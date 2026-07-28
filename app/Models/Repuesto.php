<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repuesto extends Model
{
    protected $fillable = ['proveedor_id', 'nombre', 'codigo', 'descripcion', 'precio_compra', 'precio_venta', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }
}
