<?php

namespace App\DTOs;

class VehicleDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $company_id,
        public readonly string $plate,
        public readonly string $brand,
        public readonly string $model,
        public readonly int $year,
        public readonly string $type,
        public readonly string $status,
        public readonly ?float $fuel_capacity,
        public readonly float $current_km,
    ) {}

    public static function fromRequest(array $data, int $companyId, ?int $id = null): self
    {
        return new self(
            id: $id,
            company_id: $companyId,
            plate: strtoupper($data['plate']),
            brand: $data['brand'],
            model: $data['model'],
            year: (int) $data['year'],
            type: $data['type'],
            status: $data['status'] ?? 'available',
            fuel_capacity: isset($data['fuel_capacity']) ? (float) $data['fuel_capacity'] : null,
            current_km: isset($data['current_km']) ? (float) $data['current_km'] : 0.0,
        );
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->company_id,
            'plate' => $this->plate,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'type' => $this->type,
            'status' => $this->status,
            'fuel_capacity' => $this->fuel_capacity,
            'current_km' => $this->current_km,
        ];
    }
}
