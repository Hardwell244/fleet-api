<?php

namespace App\Repositories\Contracts;

use App\Models\DeliveryEvent;
use Illuminate\Database\Eloquent\Collection;

interface DeliveryEventRepositoryInterface
{
    public function create(array $data): DeliveryEvent;
    public function getByDelivery(int $deliveryId): Collection;
    public function getRecent(int $hours = 24): Collection;
}
