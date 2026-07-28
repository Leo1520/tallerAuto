<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mecanico extends Model
{
    protected $fillable = ['persona_id', 'sucursal_id', 'especialidad_id', 'cedula', 'fecha_ingreso', 'salario', 'activo'];

    protected $casts = ['activo' => 'boolean', 'fecha_ingreso' => 'date'];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(OrdenServicio::class);
    }
}
