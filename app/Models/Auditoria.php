<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    public $timestamps = false;
    protected $table   = 'auditorias';

    protected $fillable = [
        'user_id', 'tipo_operacion', 'tabla',
        'registro_id', 'cambios', 'ip', 'user_agent',
    ];

    protected $casts = [
        'cambios'    => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
