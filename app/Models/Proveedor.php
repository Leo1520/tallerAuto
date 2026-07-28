<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = ['nombre', 'telefono', 'email', 'direccion', 'ciudad', 'nit', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function repuestos(): HasMany
    {
        return $this->hasMany(Repuesto::class);
    }
}
