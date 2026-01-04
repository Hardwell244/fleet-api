<?php

namespace App\Services;

use App\DTOs\DeliveryDTO;
use App\Models\Delivery;
use App\Repositories\Contracts\DeliveryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DeliveryService
{
    public function __construct(
        private DeliveryRepositoryInterface $repository
    ) {}

    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->list($perPage, $filters);
    }

    public function findById(int $id): ?Delivery
    {
        return $this->repository->findById($id);
    }

    public function findByIdWithoutScope(int $id): ?Delivery
    {
        return $this->repository->findByIdWithoutScope($id);
    }

    public function findByTrackingCode(string $trackingCode): ?Delivery
    {
        return $this->repository->findByTrackingCode($trackingCode);
    }

    public function create(DeliveryDTO $dto): Delivery
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, DeliveryDTO $dto): bool
    {
        $delivery = $this->repository->findById($id);

        if (!$delivery) {
            return false;
        }

        return $this->repository->update($delivery, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        $delivery = $this->repository->findById($id);

        if (!$delivery) {
            return false;
        }

        return $this->repository->delete($delivery);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->repository->getByStatus($status);
    }

    public function getByVehicle(int $vehicleId): Collection
    {
        return $this->repository->getByVehicle($vehicleId);
    }

    public function getByDriver(int $driverId): Collection
    {
        return $this->repository->getByDriver($driverId);
    }

    public function getInTransit(): Collection
    {
        return $this->repository->getInTransit();
    }
}
