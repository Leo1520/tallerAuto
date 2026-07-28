<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPago extends Model
{
    public $timestamps = false;
    protected $table = 'metodos_pago';
    protected $fillable = ['nombre', 'requiere_referencia', 'activo', 'comision'];
    protected $casts = ['requiere_referencia' => 'boolean', 'activo' => 'boolean'];

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}
