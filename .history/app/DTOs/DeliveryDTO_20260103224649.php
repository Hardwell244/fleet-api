<?php

namespace App\DTOs;

class DeliveryDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $company_id,
        public readonly ?int $driver_id,
        public readonly ?int $vehicle_id,
        public readonly ?string $tracking_code,
        public readonly string $status,
        public readonly string $origin_address,
        public readonly float $origin_lat,
        public readonly float $origin_lng,
        public readonly string $destination_address,
        public readonly float $destination_lat,
        public readonly float $destination_lng,
        public readonly ?float $distance_km,
        public readonly ?int $estimated_time_minutes,
        public readonly string $recipient_name,
        public readonly string $recipient_phone,
        public readonly ?string $assigned_at,
        public readonly ?string $picked_up_at,
        public readonly ?string $delivered_at,
        public readonly ?string $delivery_notes,
        public readonly ?string $signature_url,
        public readonly ?string $photo_url,
    ) {}

    public static function fromRequest(array $data, int $companyId, ?int $id = null): self
    {
        return new self(
            id: $id,
            company_id: $companyId,
            driver_id: isset($data['driver_id']) ? (int) $data['driver_id'] : null,
            vehicle_id: isset($data['vehicle_id']) ? (int) $data['vehicle_id'] : null,
            tracking_code: $data['tracking_code'] ?? null,
            status: $data['status'] ?? 'pending',
            origin_address: $data['origin_address'],
            origin_lat: (float) $data['origin_lat'],
            origin_lng: (float) $data['origin_lng'],
            destination_address: $data['destination_address'],
            destination_lat: (float) $data['destination_lat'],
            destination_lng: (float) $data['destination_lng'],
            distance_km: isset($data['distance_km']) ? (float) $data['distance_km'] : null,
            estimated_time_minutes: isset($data['estimated_time_minutes']) ? (int) $data['estimated_time_minutes'] : null,
            recipient_name: $data['recipient_name'],
            recipient_phone: $data['recipient_phone'],
            assigned_at: $data['assigned_at'] ?? null,
            picked_up_at: $data['picked_up_at'] ?? null,
            delivered_at: $data['delivered_at'] ?? null,
            delivery_notes: $data['delivery_notes'] ?? null,
            signature_url: $data['signature_url'] ?? null,
            photo_url: $data['photo_url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->company_id,
            'driver_id' => $this->driver_id,
            'vehicle_id' => $this->vehicle_id,
            'tracking_code' => $this->tracking_code,
            'status' => $this->status,
            'origin_address' => $this->origin_address,
            'origin_lat' => $this->origin_lat,
            'origin_lng' => $this->origin_lng,
            'destination_address' => $this->destination_address,
            'destination_lat' => $this->destination_lat,
            'destination_lng' => $this->destination_lng,
            'distance_km' => $this->distance_km,
            'estimated_time_minutes' => $this->estimated_time_minutes,
            'recipient_name' => $this->recipient_name,
            'recipient_phone' => $this->recipient_phone,
            'assigned_at' => $this->assigned_at,
            'picked_up_at' => $this->picked_up_at,
            'delivered_at' => $this->delivered_at,
            'delivery_notes' => $this->delivery_notes,
            'signature_url' => $this->signature_url,
            'photo_url' => $this->photo_url,
        ];
    }
}
