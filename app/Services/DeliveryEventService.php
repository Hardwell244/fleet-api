<?php

namespace App\Services;

use App\DTOs\DeliveryEventDTO;
use App\Models\DeliveryEvent;
use App\Repositories\Contracts\DeliveryEventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DeliveryEventService
{
    public function __construct(
        private DeliveryEventRepositoryInterface $repository
    ) {}

    public function create(DeliveryEventDTO $dto): DeliveryEvent
    {
        return $this->repository->create($dto->toArray());
    }

    public function getByDelivery(int $deliveryId): Collection
    {
        return $this->repository->getByDelivery($deliveryId);
    }

    public function getRecent(int $hours = 24): Collection
    {
        return $this->repository->getRecent($hours);
    }
}
