<?php

namespace App\Repositories\Contracts;

use App\Models\Delivery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DeliveryRepositoryInterface
{
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Delivery;
    public function findByIdWithoutScope(int $id): ?Delivery;
    public function findByTrackingCode(string $trackingCode): ?Delivery;
    public function create(array $data): Delivery;
    public function update(Delivery $delivery, array $data): bool;
    public function delete(Delivery $delivery): bool;
    public function getByStatus(string $status): Collection;
    public function getByVehicle(int $vehicleId): Collection;
    public function getByDriver(int $driverId): Collection;
    public function getInTransit(): Collection;
}
