<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repuesto extends Model
{
    protected $fillable = ['proveedor_id', 'nombre', 'codigo', 'descripcion', 'precio_compra', 'precio_venta', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(InventarioSucursal::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function stockEn(int $sucursalId): int
    {
        return $this->inventarios()->where('sucursal_id', $sucursalId)->value('stock') ?? 0;
    }
}
