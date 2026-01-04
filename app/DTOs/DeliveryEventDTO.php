<?php

namespace App\DTOs;

class DeliveryEventDTO
{
    public function __construct(
        public readonly int $delivery_id,
        public readonly string $status,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?string $observation,
    ) {}

    public static function fromRequest(array $data, int $deliveryId): self
    {
        return new self(
            delivery_id: $deliveryId,
            status: $data['status'],
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            observation: $data['observation'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'delivery_id' => $this->delivery_id,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'observation' => $this->observation,
            'created_at' => now(),
        ];
    }
}
