<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        // Log quando criar
        static::created(function ($model) {
            Log::channel('audit')->info('CREATED', [
                'model' => class_basename($model),
                'id' => $model->id,
                'company_id' => $model->company_id ?? null,
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
                'ip' => request()->ip(),
                'data' => $model->toArray(),
            ]);
        });

        // Log quando atualizar
        static::updated(function ($model) {
            Log::channel('audit')->info('UPDATED', [
                'model' => class_basename($model),
                'id' => $model->id,
                'company_id' => $model->company_id ?? null,
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
                'ip' => request()->ip(),
                'changes' => $model->getChanges(),
                'original' => $model->getOriginal(),
            ]);
        });

        // Log quando deletar
        static::deleted(function ($model) {
            Log::channel('audit')->warning('DELETED', [
                'model' => class_basename($model),
                'id' => $model->id,
                'company_id' => $model->company_id ?? null,
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
                'ip' => request()->ip(),
                'data' => $model->toArray(),
            ]);
        });
    }
}
