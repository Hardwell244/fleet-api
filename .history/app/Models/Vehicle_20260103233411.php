<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'plate',
        'brand',
        'model',
        'year',
        'type',
        'status',
        'fuel_capacity',
        'current_km',
    ];

    protected $casts = [
        'year' => 'integer',
        'fuel_capacity' => 'decimal:2',
        'current_km' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getPlateFormattedAttribute(): string
    {
        return strtoupper($this->plate);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available';
    }

    // Business Logic
    public function markAsInUse(): void
    {
        $this->update(['status' => 'in_use']);
    }

    public function markAsAvailable(): void
    {
        $this->update(['status' => 'available']);
    }
}
