<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Driver;

class DriverPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Driver $driver): bool
    {
        return $user->company_id === $driver->company_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Driver $driver): bool
    {
        return $user->company_id === $driver->company_id;
    }

    public function delete(User $user, Driver $driver): bool
    {
        return $user->company_id === $driver->company_id;
    }
}
