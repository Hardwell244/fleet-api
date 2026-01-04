<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToCompany(): void
    {
        // Auto-escopo global para garantir isolamento de dados
        static::addGlobalScope('company', function (Builder $builder) {
            $user = Auth::user();

            if ($user && $user->company_id) {
                $builder->where($builder->getModel()->getTable() . '.company_id', $user->company_id);
            }
        });

        // Auto-preenche company_id ao criar
        static::creating(function ($model) {
            $user = Auth::user();

            if ($user && !$model->company_id) {
                $model->company_id = $user->company_id;
            }
        });
    }

    /**
     * Relationship com Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope para buscar sem filtro de company
     */
    public function scopeWithoutCompanyScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('company');
    }
}
