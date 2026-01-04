<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getCnpjFormattedAttribute(): string
    {
        $cnpj = $this->cnpj;
        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($cnpj, 0, 2),
            substr($cnpj, 2, 3),
            substr($cnpj, 5, 3),
            substr($cnpj, 8, 4),
            substr($cnpj, 12, 2)
        );
    }

    // Cache Methods
    public static function isCompanyActive(int $companyId): bool
    {
        return Cache::remember("company.{$companyId}.active", 3600, function () use ($companyId) {
            return self::where('id', $companyId)
                ->where('is_active', true)
                ->exists();
        });
    }

    public static function clearCompanyCache(int $companyId): void
    {
        Cache::forget("company.{$companyId}.active");
    }

    // Events
    protected static function booted(): void
    {
        static::updated(function (Company $company) {
            self::clearCompanyCache($company->id);
        });

        static::deleted(function (Company $company) {
            self::clearCompanyCache($company->id);
        });
    }
}
