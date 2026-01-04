<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'vehicle_id',
        'type',
        'description',
        'scheduled_date',
        'completed_date',
        'cost',
        'status',
        'notes',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
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
        return $this->status === 'completed';
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['scheduled', 'in_progress'])
               && $this->scheduled_date->isPast();
    }

    // Business Logic
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_date' => now()->format('Y-m-d')
        ]);
    }
}
