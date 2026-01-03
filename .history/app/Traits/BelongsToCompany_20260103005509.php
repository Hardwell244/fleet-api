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
            if (Auth::check() && Auth::user()->company_id) {
                $builder->where($builder->getModel()->getTable() . '.company_id', Auth::user()->company_id);
            }
        });

        // Auto-preenche company_id ao criar
        static::creating(function ($model) {
            if (Auth::check() && !$model->company_id) {
                $model->company_id = Auth::user()->company_id;
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
