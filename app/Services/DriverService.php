<?php

namespace App\Services;

use App\DTOs\DriverDTO;
use App\Models\Driver;
use App\Repositories\DriverRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DriverService
{
    public function __construct(
        private DriverRepository $repository
    ) {}

    /**
     * Listar motoristas
     */
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Buscar motorista por ID
     */
    public function findById(int $id): ?Driver
    {
        return $this->repository->findById($id);
    }

    /**
     * Buscar motorista por ID sem scope de company
     */
    public function findByIdWithoutScope(int $id): ?Driver
    {
        return $this->repository->findByIdWithoutScope($id);
    }

    /**
     * Criar novo motorista
     */
    public function create(DriverDTO $dto): Driver
    {
        // Validar CPF único
        if ($this->repository->cpfExists($dto->cpf)) {
            throw ValidationException::withMessages([
                'cpf' => ['Este CPF já está cadastrado.']
            ]);
        }

        // Validar CNH única
        if ($this->repository->cnhExists($dto->cnh)) {
            throw ValidationException::withMessages([
                'cnh' => ['Esta CNH já está cadastrada.']
            ]);
        }

        return DB::transaction(function () use ($dto) {
            return $this->repository->create($dto);
        });
    }

    /**
     * Atualizar motorista
     */
    public function update(int $id, DriverDTO $dto): bool
    {
        // Validar CPF único (exceto o próprio registro)
        if ($this->repository->cpfExists($dto->cpf, $id)) {
            throw ValidationException::withMessages([
                'cpf' => ['Este CPF já está cadastrado.']
            ]);
        }

        // Validar CNH única (exceto o próprio registro)
        if ($this->repository->cnhExists($dto->cnh, $id)) {
            throw ValidationException::withMessages([
                'cnh' => ['Esta CNH já está cadastrada.']
            ]);
        }

        return DB::transaction(function () use ($id, $dto) {
            return $this->repository->update($id, $dto);
        });
    }

    /**
     * Deletar motorista
     */
    public function delete(int $id): bool
    {
        $driver = $this->repository->findById($id);

        if (!$driver) {
            throw ValidationException::withMessages([
                'id' => ['Motorista não encontrado.']
            ]);
        }

        // Verificar se tem entregas ativas
        if ($driver->deliveries()->whereIn('status', ['pending', 'in_transit'])->exists()) {
            throw ValidationException::withMessages([
                'driver' => ['Não é possível excluir este motorista pois ele possui entregas ativas.']
            ]);
        }

        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }

    /**
     * Listar motoristas disponíveis
     */
    public function getAvailable(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getAvailable();
    }
}
