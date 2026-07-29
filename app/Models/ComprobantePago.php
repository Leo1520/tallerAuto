<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ComprobantePago extends Model
{
    public $timestamps = false;
    protected $table   = 'comprobantes_pago';

    protected $fillable = [
        'pago_id', 'ruta', 'nombre_original', 'tipo_mime', 'tamano',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'tamano'     => 'integer',
    ];

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->ruta);
    }

    public function esImagen(): bool
    {
        return str_starts_with($this->tipo_mime ?? '', 'image/');
    }

    public function tamanoLegible(): string
    {
        $bytes = $this->tamano ?? 0;
        if ($bytes < 1024)       return "{$bytes} B";
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
