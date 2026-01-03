<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Delivery extends Model
{
    use HasFactory, SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'driver_id',
        'vehicle_id',
        'tracking_code',
        'status',
        'origin_address',
        'origin_lat',
        'origin_lng',
        'destination_address',
        'destination_lat',
        'destination_lng',
        'distance_km',
        'estimated_time_minutes',
        'recipient_name',
        'recipient_phone',
        'assigned_at',
        'picked_up_at',
        'delivered_at',
        'delivery_notes',
        'signature_url',
        'photo_url',
    ];

    protected $casts = [
        'origin_lat' => 'decimal:7',
        'origin_lng' => 'decimal:7',
        'destination_lat' => 'decimal:7',
        'destination_lng' => 'decimal:7',
        'distance_km' => 'decimal:2',
        'estimated_time_minutes' => 'integer',
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($delivery) {
            if (!$delivery->tracking_code) {
                $delivery->tracking_code = self::generateTrackingCode();
            }
        });
    }

    // Relationships
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(DeliveryEvent::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeByTrackingCode($query, string $code)
    {
        return $query->where('tracking_code', $code);
    }

    // Accessors
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsInTransitAttribute(): bool
    {
        return $this->status === 'in_transit';
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->status === 'delivered';
    }

    // Business Logic
    public static function generateTrackingCode(): string
    {
        do {
            $code = 'FLT' . strtoupper(Str::random(12));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    public function assignToDriver(int $driverId, int $vehicleId): void
    {
        $this->update([
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        $this->addEvent('assigned', null, null, 'Entrega atribuída ao motorista');
    }

    public function markAsPickedUp(?float $lat = null, ?float $lng = null): void
    {
        $this->update([
            'status' => 'in_transit',
            'picked_up_at' => now(),
        ]);

        $this->addEvent('picked_up', $lat, $lng, 'Pedido coletado');
    }

    public function markAsDelivered(?float $lat = null, ?float $lng = null, ?string $notes = null): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'delivery_notes' => $notes,
        ]);

        $this->addEvent('delivered', $lat, $lng, 'Entrega concluída');
    }

    public function addEvent(string $status, ?float $lat, ?float $lng, ?string $observation = null): void
    {
        $this->events()->create([
            'status' => $status,
            'latitude' => $lat,
            'longitude' => $lng,
            'observation' => $observation,
            'created_at' => now(),
        ]);
    }

    // Cálculo de distância (fórmula Haversine)
    public function calculateDistance(): float
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->origin_lat);
        $lngFrom = deg2rad($this->origin_lng);
        $latTo = deg2rad($this->destination_lat);
        $lngTo = deg2rad($this->destination_lng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)
        ));

        return round($earthRadius * $angle, 2);
    }
}
