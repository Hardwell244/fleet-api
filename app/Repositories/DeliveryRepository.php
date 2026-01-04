<?php

namespace App\Repositories;

use App\Models\Delivery;
use App\Repositories\Contracts\DeliveryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DeliveryRepository implements DeliveryRepositoryInterface
{
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Delivery::with(['vehicle', 'driver', 'company']);

        // Filtro por status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtro por veículo
        if (isset($filters['vehicle_id']) && !empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        // Filtro por motorista
        if (isset($filters['driver_id']) && !empty($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        // Busca por endereços, tracking code ou destinatário
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('origin_address', 'like', "%{$search}%")
                  ->orWhere('destination_address', 'like', "%{$search}%")
                  ->orWhere('tracking_code', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    public function findById(int $id): ?Delivery
    {
        return Delivery::with(['vehicle', 'driver', 'company', 'events'])->find($id);
    }

    public function findByIdWithoutScope(int $id): ?Delivery
    {
        return Delivery::withoutGlobalScopes()->with(['vehicle', 'driver', 'company', 'events'])->find($id);
    }

    public function findByTrackingCode(string $trackingCode): ?Delivery
    {
        return Delivery::with(['vehicle', 'driver', 'events'])
                      ->where('tracking_code', $trackingCode)
                      ->first();
    }

    public function create(array $data): Delivery
    {
        return Delivery::create($data);
    }

    public function update(Delivery $delivery, array $data): bool
    {
        return $delivery->update($data);
    }

    public function delete(Delivery $delivery): bool
    {
        return $delivery->delete();
    }

    public function getByStatus(string $status): Collection
    {
        return Delivery::with(['vehicle', 'driver'])
                      ->where('status', $status)
                      ->orderBy('created_at', 'desc')
                      ->get();
    }

    public function getByVehicle(int $vehicleId): Collection
    {
        return Delivery::with(['driver'])
                      ->where('vehicle_id', $vehicleId)
                      ->orderBy('created_at', 'desc')
                      ->get();
    }

    public function getByDriver(int $driverId): Collection
    {
        return Delivery::with(['vehicle'])
                      ->where('driver_id', $driverId)
                      ->orderBy('created_at', 'desc')
                      ->get();
    }

    public function getInTransit(): Collection
    {
        return Delivery::with(['vehicle', 'driver'])
                      ->where('status', 'in_transit')
                      ->orderBy('picked_up_at', 'asc')
                      ->get();
    }
}
