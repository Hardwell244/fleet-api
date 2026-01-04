<?php

namespace App\Repositories;

use App\Models\DeliveryEvent;
use App\Repositories\Contracts\DeliveryEventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DeliveryEventRepository implements DeliveryEventRepositoryInterface
{
    public function create(array $data): DeliveryEvent
    {
        return DeliveryEvent::create($data);
    }

    public function getByDelivery(int $deliveryId): Collection
    {
        return DeliveryEvent::where('delivery_id', $deliveryId)
                           ->orderBy('created_at', 'desc')
                           ->get();
    }

    public function getRecent(int $hours = 24): Collection
    {
        return DeliveryEvent::with(['delivery'])
                           ->where('created_at', '>=', now()->subHours($hours))
                           ->orderBy('created_at', 'desc')
                           ->get();
    }
}
