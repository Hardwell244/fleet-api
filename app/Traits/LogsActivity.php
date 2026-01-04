<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        // Log quando criar
        static::created(function ($model) {
            $user = auth()->user();

            Log::channel('audit')->info('CREATED', [
                'model' => class_basename($model),
                'id' => $model->id,
                'company_id' => $model->company_id ?? null,
                'user_id' => $user?->id,
                'user_email' => $user?->email,
                'ip' => request()->ip(),
                'data' => $model->toArray(),
            ]);
        });

        // Log quando atualizar
        static::updated(function ($model) {
            $user = auth()->user();

            Log::channel('audit')->info('UPDATED', [
                'model' => class_basename($model),
                'id' => $model->id,
                'company_id' => $model->company_id ?? null,
                'user_id' => $user?->id,
                'user_email' => $user?->email,
                'ip' => request()->ip(),
                'changes' => $model->getChanges(),
                'original' => $model->getOriginal(),
            ]);
        });

        // Log quando deletar
        static::deleted(function ($model) {
            $user = auth()->user();

            Log::channel('audit')->warning('DELETED', [
                'model' => class_basename($model),
                'id' => $model->id,
                'company_id' => $model->company_id ?? null,
                'user_id' => $user?->id,
                'user_email' => $user?->email,
                'ip' => request()->ip(),
                'data' => $model->toArray(),
            ]);
        });
    }
}
