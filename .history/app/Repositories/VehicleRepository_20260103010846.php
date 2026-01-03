<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Illuminate\Pagination\LengthAwarePaginator;

class VehicleRepository
{
    public function __construct(
        private Vehicle $model
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->with('company');

        // Filtros
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('plate', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('brand', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('model', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Vehicle
    {
        return $this->model->with('company')->find($id);
    }

    public function create(array $data): Vehicle
    {
        return $this->model->create($data);
    }

    public function update(Vehicle $vehicle, array $data): bool
    {
        return $vehicle->update($data);
    }

    public function delete(Vehicle $vehicle): bool
    {
        return $vehicle->delete();
    }

    public function getAvailable(): array
    {
        return $this->model->available()->get()->toArray();
    }
}
