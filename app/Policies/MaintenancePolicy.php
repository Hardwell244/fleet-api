<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Maintenance;

class MaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Maintenance $maintenance): bool
    {
        return $user->company_id === $maintenance->company_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->company_id === $maintenance->company_id;
    }

    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->company_id === $maintenance->company_id;
    }
}
