<?php

namespace App\Repositories;

use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MaintenanceRepository implements MaintenanceRepositoryInterface
{
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Maintenance::with(['vehicle', 'company']);

        // Filtro por status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtro por tipo
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filtro por veículo
        if (isset($filters['vehicle_id']) && !empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        // Busca por descrição ou notas
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('scheduled_date', 'desc')
                    ->paginate($perPage);
    }

    public function findById(int $id): ?Maintenance
    {
        return Maintenance::with(['vehicle', 'company'])->find($id);
    }

    public function findByIdWithoutScope(int $id): ?Maintenance
    {
        return Maintenance::withoutGlobalScopes()->with(['vehicle', 'company'])->find($id);
    }

    public function create(array $data): Maintenance
    {
        return Maintenance::create($data);
    }

    public function update(Maintenance $maintenance, array $data): bool
    {
        return $maintenance->update($data);
    }

    public function delete(Maintenance $maintenance): bool
    {
        return $maintenance->delete();
    }

    public function getByVehicle(int $vehicleId): Collection
    {
        return Maintenance::where('vehicle_id', $vehicleId)
                         ->orderBy('scheduled_date', 'desc')
                         ->get();
    }

    public function getPending(): Collection
    {
        return Maintenance::with(['vehicle'])
                         ->whereIn('status', ['scheduled', 'in_progress'])
                         ->orderBy('scheduled_date', 'asc')
                         ->get();
    }
}
