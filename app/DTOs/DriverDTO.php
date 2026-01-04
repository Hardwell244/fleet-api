<?php

namespace App\DTOs;

use App\Models\Driver;

readonly class DriverDTO
{
    public function __construct(
        public ?int $id,
        public int $company_id,
        public string $name,
        public string $cpf,
        public string $cnh,
        public string $cnh_category,
        public string $cnh_expiry,
        public ?string $phone,
        public string $status,
    ) {}

    public static function fromRequest(array $data, int $companyId, ?int $id = null): self
    {
        return new self(
            id: $id,
            company_id: $companyId,
            name: $data['name'],
            cpf: preg_replace('/\D/', '', $data['cpf']), // Remove formatação
            cnh: $data['cnh'],
            cnh_category: $data['cnh_category'],
            cnh_expiry: $data['cnh_expiry'],
            phone: $data['phone'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }

    public static function fromModel(Driver $driver): self
    {
        return new self(
            id: $driver->id,
            company_id: $driver->company_id,
            name: $driver->name,
            cpf: $driver->cpf,
            cnh: $driver->cnh,
            cnh_category: $driver->cnh_category,
            cnh_expiry: $driver->cnh_expiry,
            phone: $driver->phone,
            status: $driver->status,
        );
    }

    public function toArray(): array
    {
        $data = [
            'company_id' => $this->company_id,
            'name' => $this->name,
            'cpf' => $this->cpf,
            'cnh' => $this->cnh,
            'cnh_category' => $this->cnh_category,
            'cnh_expires_at' => $this->cnh_expiry,
            'status' => $this->status,
        ];

        if ($this->phone !== null) {
            $data['phone'] = $this->phone;
        }

        return $data;
    }
}
