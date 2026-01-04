<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes, BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'name',
        'cpf',
        'cnh',
        'cnh_category',
        'cnh_expires_at',
        'phone',
        'status',
    ];

    protected $casts = [
        'cnh_expires_at' => 'date',
        'is_available' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'cpf',
        'deleted_at',
    ];

    // Relationships
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                     ->where('cnh_expires_at', '>', now());
    }

    public function scopeCnhExpiring($query, int $days = 30)
    {
        return $query->whereBetween('cnh_expires_at', [
            now(),
            now()->addDays($days)
        ]);
    }

    // Accessors
    public function getCpfFormattedAttribute(): string
    {
        $cpf = $this->cpf;
        return sprintf(
            '%s.%s.%s-%s',
            substr($cpf, 0, 3),
            substr($cpf, 3, 3),
            substr($cpf, 6, 3),
            substr($cpf, 9, 2)
        );
    }

    public function getCnhExpiryAttribute()
    {
        return $this->cnh_expires_at?->format('Y-m-d');
    }

    public function getIsCnhValidAttribute(): bool
    {
        return $this->cnh_expires_at->isFuture();
    }

    // Mutators
    public function setCnhExpiryAttribute($value)
    {
        $this->attributes['cnh_expires_at'] = $value;
    }

    // Business Logic
    public function markAsAvailable(): void
    {
        $this->update(['is_available' => true]);
    }

    public function markAsUnavailable(): void
    {
        $this->update(['is_available' => false]);
    }
}
