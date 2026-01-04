<?php

namespace App\Repositories;

use App\DTOs\DriverDTO;
use App\Models\Driver;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverRepository
{
    /**
     * Listar motoristas com paginação
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Driver::with('company');

        // Filtro por status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtro por busca (nome, CPF, CNH)
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%")
                  ->orWhere('cnh', 'like', "%{$search}%");
            });
        }

        // Filtro por categoria de CNH
        if (isset($filters['cnh_category'])) {
            $query->where('cnh_category', $filters['cnh_category']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    /**
     * Buscar motorista por ID
     */
    public function findById(int $id): ?Driver
    {
        return Driver::with('company')->find($id);
    }

    /**
     * Buscar motorista por ID sem scope de company
     */
    public function findByIdWithoutScope(int $id): ?Driver
    {
        return Driver::withoutGlobalScopes()->with('company')->find($id);
    }

    /**
     * Criar novo motorista
     */
    public function create(DriverDTO $dto): Driver
    {
        return Driver::create($dto->toArray());
    }

    /**
     * Atualizar motorista
     */
    public function update(int $id, DriverDTO $dto): bool
    {
        $driver = Driver::findOrFail($id);
        return $driver->update($dto->toArray());
    }

    /**
     * Deletar motorista
     */
    public function delete(int $id): bool
    {
        $driver = Driver::findOrFail($id);
        return $driver->delete();
    }

    /**
     * Buscar motoristas disponíveis
     */
    public function getAvailable(): \Illuminate\Database\Eloquent\Collection
    {
        return Driver::where('status', 'active')
            ->with('company')
            ->orderBy('name')
            ->get();
    }

    /**
     * Verificar se CPF já existe
     */
    public function cpfExists(string $cpf, ?int $excludeId = null): bool
    {
        $query = Driver::where('cpf', $cpf);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Verificar se CNH já existe
     */
    public function cnhExists(string $cnh, ?int $excludeId = null): bool
    {
        $query = Driver::where('cnh', $cnh);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
