<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'type',
        'description',
        'cost',
        'scheduled_date',
        'completed_at',
        'workshop',
        'notes',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereNull('completed_at');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeScheduledBetween($query, $start, $end)
    {
        return $query->whereBetween('scheduled_date', [$start, $end]);
    }

    // Accessors
    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->completed_at);
    }

    public function getIsOverdueAttribute(): bool
    {
        return is_null($this->completed_at)
               && $this->scheduled_date->isPast();
    }

    // Business Logic
    public function markAsCompleted(): void
    {
        $this->update(['completed_at' => now()]);
    }
}
