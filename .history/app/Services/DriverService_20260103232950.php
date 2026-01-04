<?php

namespace App\Services;

use App\DTOs\DriverDTO;
use App\Models\Driver;
use App\Repositories\DriverRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverService
{
    public function __construct(
        private DriverRepository $repository
    ) {}

    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function findById(int $id): ?Driver
    {
        return $this->repository->findById($id);
    }

    public function create(DriverDTO $dto): Driver
    {
        return $this->repository->create($dto);
    }

    public function update(int $id, DriverDTO $dto): bool
    {
        return $this->repository->update($id, $dto);
    }

    public function delete(int $id): bool
    {
        $driver = $this->repository->findById($id);

        if (!$driver) {
            return false;
        }

        // Verificar se tem entregas ativas
        if ($driver->deliveries()->whereIn('status', ['pending', 'in_transit'])->exists()) {
            throw new \Exception('Não é possível excluir este motorista pois ele possui entregas ativas.');
        }

        return $this->repository->delete($id);
    }

    public function getAvailable(): Collection
    {
        return $this->repository->getAvailable();
    }
}
