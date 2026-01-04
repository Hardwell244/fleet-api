<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    /**
     * Determina se o usuário pode visualizar qualquer veículo
     */
    public function viewAny(User $user): bool
    {
        return true; // Usuário autenticado pode listar
    }

    /**
     * Determina se o usuário pode visualizar o veículo
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->company_id === $vehicle->company_id;
    }

    /**
     * Determina se o usuário pode criar veículos
     */
    public function create(User $user): bool
    {
        return true; // Usuário autenticado pode criar
    }

    /**
     * Determina se o usuário pode atualizar o veículo
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->company_id === $vehicle->company_id;
    }

    /**
     * Determina se o usuário pode deletar o veículo
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->company_id === $vehicle->company_id;
    }
}
