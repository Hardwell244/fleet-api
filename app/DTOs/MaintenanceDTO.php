<?php

namespace App\DTOs;

class MaintenanceDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $company_id,
        public readonly int $vehicle_id,
        public readonly string $type,
        public readonly string $description,
        public readonly string $scheduled_date,
        public readonly ?string $completed_date,
        public readonly ?float $cost,
        public readonly string $status,
        public readonly ?string $notes,
    ) {}

    public static function fromRequest(array $data, int $companyId, ?int $id = null): self
    {
        return new self(
            id: $id,
            company_id: $companyId,
            vehicle_id: (int) $data['vehicle_id'],
            type: $data['type'],
            description: $data['description'],
            scheduled_date: $data['scheduled_date'],
            completed_date: $data['completed_date'] ?? null,
            cost: isset($data['cost']) ? (float) $data['cost'] : null,
            status: $data['status'] ?? 'scheduled',
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->company_id,
            'vehicle_id' => $this->vehicle_id,
            'type' => $this->type,
            'description' => $this->description,
            'scheduled_date' => $this->scheduled_date,
            'completed_date' => $this->completed_date,
            'cost' => $this->cost,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}
