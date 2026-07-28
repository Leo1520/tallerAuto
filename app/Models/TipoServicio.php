<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoServicio extends Model
{
    public $timestamps = false;
    protected $table = 'tipos_servicio';
    protected $fillable = ['nombre', 'descripcion', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }
}
