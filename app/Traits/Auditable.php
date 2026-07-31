<?php

namespace App\Traits;

use App\Models\Auditoria;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::registrarAuditoria('CREATE', $model, $model->toArray());
        });

        static::updated(function ($model) {
            static::registrarAuditoria('UPDATE', $model, [
                'antes'   => $model->getOriginal(),
                'despues' => $model->getChanges(),
            ]);
        });

        static::deleted(function ($model) {
            static::registrarAuditoria('DELETE', $model, $model->toArray());
        });
    }

    private static function registrarAuditoria(string $tipo, $model, array $cambios): void
    {
        if (!app()->runningInConsole()) {
            Auditoria::create([
                'user_id'        => auth()->id(),
                'tipo_operacion' => $tipo,
                'tabla'          => $model->getTable(),
                'registro_id'    => $model->getKey(),
                'cambios'        => static::sanitizarCambios($cambios),
                'ip'             => request()->ip(),
                'user_agent'     => request()->userAgent(),
            ]);
        }
    }

    // Elimina claves sensibles antes de persistir el JSON de cambios.
    private static function sanitizarCambios(array $cambios): array
    {
        static $sensibles = [
            'password', 'remember_token', 'token', 'api_key', 'secret',
            '_token', 'hash', 'api_secret', 'private_key',
        ];

        foreach ($cambios as $key => $valor) {
            if (in_array(strtolower((string) $key), $sensibles, true)) {
                unset($cambios[$key]);
            } elseif (is_array($valor)) {
                $cambios[$key] = static::sanitizarCambios($valor);
            }
        }

        return $cambios;
    }
}
