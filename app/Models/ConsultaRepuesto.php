<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultaRepuesto extends Model
{
    public $timestamps = false;
    protected $table   = 'consultas_repuesto';

    protected $fillable = [
        'repuesto_id', 'cliente_id', 'nombre', 'telefono',
        'email', 'cantidad', 'notas', 'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'cantidad'   => 'integer',
    ];

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
