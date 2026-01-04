<?php

namespace App\Repositories\Contracts;

use App\Models\Maintenance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MaintenanceRepositoryInterface
{
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Maintenance;
    public function create(array $data): Maintenance;
    public function update(Maintenance $maintenance, array $data): bool;
    public function delete(Maintenance $maintenance): bool;
    public function getByVehicle(int $vehicleId): Collection;
    public function getPending(): Collection;
}
