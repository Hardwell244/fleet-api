<?php

namespace App\Services;

use App\DTOs\VehicleDTO;
use App\Models\Vehicle;
use App\Repositories\VehicleRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class VehicleService
{
    public function __construct(
        private VehicleRepository $repository
    ) {}

    public function listPaginated(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function findById(int $id): ?Vehicle
    {
        return $this->repository->findById($id);
    }

    public function create(VehicleDTO $dto): Vehicle
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(Vehicle $vehicle, VehicleDTO $dto): bool
    {
        return $this->repository->update($vehicle, $dto->toArray());
    }

    public function delete(Vehicle $vehicle): bool
    {
        // Verificar se pode deletar (ex: não tem entregas ativas)
        if ($vehicle->deliveries()->whereIn('status', ['pending', 'assigned', 'in_transit'])->exists()) {
            throw new \Exception('Não é possível deletar veículo com entregas ativas.');
        }

        return $this->repository->delete($vehicle);
    }

    public function getAvailable(): array
    {
        return $this->repository->getAvailable();
    }
}
