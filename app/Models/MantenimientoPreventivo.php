<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantenimientoPreventivo extends Model
{
    protected $table = 'mantenimientos_preventivos';
    protected $fillable = [
        'vehiculo_id', 'tipo', 'fecha_ultimo', 'proxima_fecha',
        'kilometraje_ultimo', 'kilometraje_proximo', 'notificar', 'estado', 'observaciones',
    ];

    protected $casts = [
        'fecha_ultimo'  => 'date',
        'proxima_fecha' => 'date',
        'notificar'     => 'boolean',
    ];

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
