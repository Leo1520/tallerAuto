<?php

namespace App\Models;

use App\Notifications\Auth\VerifyEmailNotification;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    protected $fillable = ['persona_id', 'email', 'password', 'ultimo_acceso'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_acceso'     => 'datetime',
        'password'          => 'hashed',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // ─── Helpers de roles y permisos ─────────────────────────

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return $this->roles->whereIn('nombre', $roles)->isNotEmpty();
    }

    public function hasPermission(string $permiso): bool
    {
        return $this->getAllPermissions()->contains('nombre', $permiso);
    }

    public function getAllPermissions(): Collection
    {
        return $this->roles
            ->load('permissions')
            ->flatMap(fn($role) => $role->permissions)
            ->unique('id');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    // ─── Accessor: nombre desde persona ───────────────────────

    public function getNombreAttribute(): string
    {
        return $this->persona?->nombre ?? $this->email;
    }
}
