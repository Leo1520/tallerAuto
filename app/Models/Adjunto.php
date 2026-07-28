<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adjunto extends Model
{
    public $timestamps = false;

    protected $fillable = ['orden_id', 'user_id', 'nombre', 'ruta', 'tipo', 'tamano'];

    protected $casts = ['created_at' => 'datetime'];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function esImagen(): bool
    {
        return str_starts_with($this->tipo ?? '', 'image/');
    }

    public function tamanoFormateado(): string
    {
        $bytes = $this->tamano ?? 0;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
