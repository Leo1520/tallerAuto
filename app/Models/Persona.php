<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Model
{
    use HasFactory;
    protected $table = 'persona';

    protected $fillable = ['nombre', 'telefono', 'email', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class);
    }

    public function mecanico(): HasOne
    {
        return $this->hasOne(Mecanico::class);
    }
}
