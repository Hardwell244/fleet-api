<?php

namespace App\Services;

use App\DTOs\MaintenanceDTO;
use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MaintenanceService
{
    public function __construct(
        private MaintenanceRepositoryInterface $repository
    ) {}

    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->list($perPage, $filters);
    }

    public function findById(int $id): ?Maintenance
    {
        return $this->repository->findById($id);
    }

    public function findByIdWithoutScope(int $id): ?Maintenance
    {
        return $this->repository->findByIdWithoutScope($id);
    }

    public function create(MaintenanceDTO $dto): Maintenance
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, MaintenanceDTO $dto): bool
    {
        $maintenance = $this->repository->findById($id);

        if (!$maintenance) {
            return false;
        }

        return $this->repository->update($maintenance, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        $maintenance = $this->repository->findById($id);

        if (!$maintenance) {
            return false;
        }

        return $this->repository->delete($maintenance);
    }

    public function getByVehicle(int $vehicleId): Collection
    {
        return $this->repository->getByVehicle($vehicleId);
    }

    public function getPending(): Collection
    {
        return $this->repository->getPending();
    }
}
