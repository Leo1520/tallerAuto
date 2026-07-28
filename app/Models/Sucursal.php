<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    protected $fillable = ['nombre', 'direccion', 'ciudad', 'telefono', 'email', 'latitud', 'longitud', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function mecanicos(): HasMany
    {
        return $this->hasMany(Mecanico::class);
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(OrdenServicio::class);
    }
}
