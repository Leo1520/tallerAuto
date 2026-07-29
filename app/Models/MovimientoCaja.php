<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoCaja extends Model
{
    public $timestamps = false;
    protected $table   = 'movimientos_caja';

    protected $fillable = [
        'pago_id', 'user_id', 'tipo', 'concepto', 'monto', 'referencia',
    ];

    protected $casts = [
        'monto'      => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
