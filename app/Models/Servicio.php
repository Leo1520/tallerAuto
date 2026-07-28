<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Servicio extends Model
{
    protected $fillable = ['tipo_servicio_id', 'nombre', 'descripcion', 'precio', 'tiempo_estimado', 'requiere_repuestos', 'activo'];

    protected $casts = ['requiere_repuestos' => 'boolean', 'activo' => 'boolean'];

    public function tipoServicio(): BelongsTo
    {
        return $this->belongsTo(TipoServicio::class);
    }
}
