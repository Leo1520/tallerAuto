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
                'cambios'        => $cambios,
                'ip'             => request()->ip(),
                'user_agent'     => request()->userAgent(),
            ]);
        }
    }
}
