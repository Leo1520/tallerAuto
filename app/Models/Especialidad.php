<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    public $timestamps = false;
    protected $fillable = ['nombre', 'descripcion'];

    public function mecanicos(): HasMany
    {
        return $this->hasMany(Mecanico::class);
    }
}
